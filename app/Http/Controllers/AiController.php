<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Services\Ai\AiManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiController extends Controller
{
    public function __construct(protected AiManager $ai) {}

    /**
     * Mostra a página do assistente.
     */
    public function index()
    {
        $conversations = Auth::user()
            ->conversations()
            ->latest()
            ->limit(10)
            ->get();

        $messages = [];
        if ($conversations->isNotEmpty()) {
            $messages = $conversations->first()->messages()->orderBy('created_at')->get()->toArray();
        }
        return view('ai.index', [
            'conversations' => $conversations,
            'activeConversation' => $conversations->first(),
            'messages' => $messages,
            'enabled' => (bool) config('services.ai.enabled', false),
            'driver' => config('services.ai.driver', 'openrouter'),
            'model' => config('services.ai.'.config('services.ai.driver').'.model'),
        ]);
    }

    /**
     * Inicia uma nova conversa.
     */
    public function newConversation(Request $request)
    {
        $request->validate(['title' => 'nullable|string|max:120']);

        $conversation = Auth::user()->conversations()->create([
            'title' => $request->title ?: 'Nova conversa',
            'driver' => config('services.ai.driver'),
            'model' => config('services.ai.'.config('services.ai.driver').'.model'),
        ]);

        return redirect()->route('ai.show', $conversation);
    }

    /**
     * Mostra uma conversa específica.
     */
    public function show(Conversation $conversation)
    {
        $this->authorizeAccess($conversation);

        $conversations = Auth::user()
            ->conversations()
            ->latest()
            ->limit(10)
            ->get();

        $messages = $conversation->messages()->orderBy('created_at')->get();

        return view('ai.index', [
            'conversations' => $conversations,
            'activeConversation' => $conversation,
            'messages' => $messages,
            'enabled' => (bool) config('services.ai.enabled', false),
            'driver' => $conversation->driver,
            'model' => $conversation->model,
        ]);
    }

    /**
     * Envia uma mensagem do utilizador e recebe a resposta da IA.
     */
    public function chat(Request $request, Conversation $conversation)
    {
        $this->authorizeAccess($conversation);

        $request->validate(['message' => 'required|string|max:4000']);

        $userMessage = $request->message;

        // Persiste a mensagem do utilizador
        Message::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $userMessage,
        ]);

        // Constrói o histórico para a IA
        $history = [
            ['role' => 'system', 'content' => $this->systemPrompt()],
        ];
        foreach ($conversation->messages()->orderBy('created_at')->get() as $msg) {
            $history[] = ['role' => $msg->role, 'content' => $msg->content];
        }

        $response = $this->ai->ask($history, ['temperature' => 0.6, 'max_tokens' => 1500]);

        if (!($response['success'] ?? false)) {
            Message::create([
                'conversation_id' => $conversation->id,
                'role' => 'assistant',
                'content' => '⚠️ Erro: '.($response['error'] ?? 'desconhecido'),
            ]);
            return back()->with('error', 'Erro no assistente: '.($response['error'] ?? ''));
        }

        Message::create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => $response['content'] ?? '',
        ]);

        // Actualiza o título da conversa com base na 1ª mensagem (se for a 2ª msg)
        if ($conversation->title === 'Nova conversa' && $conversation->messages()->count() === 2) {
            $conversation->update([
                'title' => mb_substr($userMessage, 0, 60).(mb_strlen($userMessage) > 60 ? '…' : ''),
            ]);
        }

        $conversation->touch();

        return redirect()->route('ai.show', $conversation);
    }

    /**
     * Apaga uma conversa.
     */
    public function destroy(Conversation $conversation)
    {
        $this->authorizeAccess($conversation);
        $conversation->delete();

        return redirect()->route('ai.index')->with('success', 'Conversa removida.');
    }

    /**
     * Endpoint AJAX usado pelo widget flutuante.
     *
     * Não persiste no servidor (mesmo padrão da maternidade): recebe o
     * `prompt` do utilizador + o histórico recente do chat e devolve a
     * resposta. O histórico fica em localStorage no cliente.
     */
    public function widgetChat(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:4000',
            'history' => 'nullable|array',
        ]);

        $prompt = trim($request->input('prompt'));
        if ($prompt === '') {
            return response()->json(['success' => false, 'message' => 'Mensagem vazia'], 422);
        }

        $clientHistory = $request->input('history', []);
        $history = [['role' => 'system', 'content' => $this->systemPrompt()]];

        if (is_array($clientHistory)) {
            foreach ($clientHistory as $h) {
                if (!isset($h['role'], $h['content'])) {
                    continue;
                }
                if (($h['role'] ?? '') === 'assistant' && ($h['meta'] ?? null) === 'greeting') {
                    continue;
                }
                $history[] = ['role' => $h['role'], 'content' => $this->stripImages($h['content'])];
            }
        }

        $history[] = ['role' => 'user', 'content' => $this->stripImages($prompt)];

        $response = $this->ai->ask($history, ['temperature' => 0.6, 'max_tokens' => 800]);

        if (!($response['success'] ?? false)) {
            $err = $response['error'] ?? 'Erro ao consultar IA';
            $userMessage = match (true) {
                str_contains(strtolower($err), 'image') => 'Este assistente não suporta imagens. Envie apenas texto.',
                default => $err,
            };

            return response()->json([
                'success' => false,
                'message' => $userMessage,
            ], 500);
        }

        return response()->json([
            'success' => true,
            'response' => $response['content'] ?? '',
        ]);
    }

    /**
     * Endpoint mantido por compatibilidade — não persiste nada.
     */
    public function widgetReset()
    {
        return response()->json(['success' => true]);
    }

    protected function authorizeAccess(Conversation $conversation): void
    {
        if ($conversation->user_id !== Auth::id()) {
            abort(403);
        }
    }

    protected function systemPrompt(): string
    {
        $schoolName = setting('school_name', config('app.name', 'ZamEdu'));
        $userName = auth()->user()?->name ?? 'Utilizador';
        $userRole = auth()->user()?->roles->pluck('name')->first() ?? auth()->user()?->role ?? 'Administrador';

        return "És o Assistente de Inteligência Artificial Oficial do sistema de gestão escolar {$schoolName} (ZamEdu / SIGE Moçambique).
Estás a interagir com: {$userName} (Função: {$userRole}).

### 1. CONHECIMENTO DO SISTEMA NACIONAL DE EDUCAÇÃO (MINEDH - LEI 18/2018):
- Escala Oficial de Avaliação: 0.0 a 20.0 valores. Limiar de aprovação: 9.5 / 10.0 valores.
- Classes com Exame Nacional em Moçambique: 9ª, 10ª e 12ª classes.
- Fórmulas Oficiais de Cálculo MINEDH:
  * MACS (Média de Avaliações Contínuas e Sumativas)
  * MAF (Média de Fim de Trimestre/Ano)
  * Para turmas com Exame Nacional: NE_Eficaz = max(NE, ACF) [onde NE é Nota de Exame e ACF é Avaliação Contínua Final], e NFD = round((MAF + NE_Eficaz) / 2).
- Relatórios Pedagógicos e Inspeção: Exigem obrigatoriamente desagregação por género: **HM** (Total Homens e Mulheres) e **M** (Mulheres/Feminino).

### 2. ARQUITECTURA E ESTRUTURA TERRITORIAL MULTI-TENANT:
- Estrutura de Tenancy Hierárquica em Moçambique: Província -> Distrito -> ZIP (Zona de Influência Pedagógica) -> Escola.
- Escopo de Atuação:
  * Nível Escola: CRUD completo de alunos, turmas, notas, matrículas, pagamentos, presenças e certidões.
  * Nível ZIP / Distrito / Província: Agregação estatística, mapas de inspeção, relatórios consolidados e drill-down read-only.

### 3. MÓDULOS DISPONÍVEIS NO ZAMEDU:
- **Alunos & Matrículas**: Inscrições, renovação de matrículas, distribuição por turmas, transferência de alunos, emissão de certidões e cartões digitais de estudantes com QR Code.
- **Organização Académica**: Matriz curricular, turmas por nível, corpo docente, licenciamento de professores e horários.
- **Lançamento de Notas & Pautas**: Pauta trimestral e anual automatizada com motor de cálculo MINEDH, desvio padrão e mapa pedagógico.
- **Finanças & Propinas**: Mensalidades, emolumentos, faturamento, recibos, relatório de devedores e balanço financeiro.
- **Comunicação & Segurança**: Notificações automáticas SMS aos encarregados, canal WhatsApp, controlo de assiduidade e portaria digital com QR Code.

### 4. INSTRUÇÕES DE RESPOSTA E TOM DE VOZ:
- Responde em Português de Moçambique, de forma extremamente profissional, clara, estruturada e cortês.
- Usa formatação Markdown com negritos, tópicos organizados e emojis explicativos quando útil.
- Quando solicitado a gerar uma mensagem SMS para encarregados de educação, mantém o texto direto, cortês e com menos de 300 caracteres.";
    }

    private function stripImages(string $text): string
    {
        return preg_replace('/(?:!\[[^\]]*\]\([^)]*\)|<img[^>]*>)/i', '[Imagem não suportada pelo assistente]', $text) ?? $text;
    }
}