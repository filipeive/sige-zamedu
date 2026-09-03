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
                // Ignora a saudação inicial artificial do front-end
                if (($h['role'] ?? '') === 'assistant' && ($h['meta'] ?? null) === 'greeting') {
                    continue;
                }
                $history[] = ['role' => $h['role'], 'content' => $h['content']];
            }
        }
        // Garante que o prompt actual entra no histórico
        $history[] = ['role' => 'user', 'content' => $prompt];

        $response = $this->ai->ask($history, ['temperature' => 0.6, 'max_tokens' => 800]);

        if (!($response['success'] ?? false)) {
            return response()->json([
                'success' => false,
                'message' => $response['error'] ?? 'Erro ao consultar IA',
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
        return "És o assistente IA do sistema escolar ".config('app.name').".
Ajuda professores, secretários, directores e encarregados de educação com:
- Análise de desempenho académico e frequência de alunos.
- Geração de mensagens para encarregados (educadas e em português).
- Sugestões pedagógicas, relatórios e respostas a perguntas do quotidiano escolar.
Responde sempre em português de Moçambique, claro, prático e respeitoso.
Quando gerares mensagens curtas para enviar por SMS ao encarregado, mantém <300 caracteres.";
    }
}