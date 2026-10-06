@auth
<script>
(function() {
    const STORAGE_KEY = 'zamedu_ai_chat_history';
    const defaultGreeting = {
        role: 'assistant',
        meta: 'greeting',
        content: 'Olá! Sou o **Assistente IA do ZamEdu**. Como posso ajudar hoje com alunos, pautas MINEDH, relatórios ou gestão da escola?'
    };

    function getSavedMessages() {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            return Array.isArray(JSON.parse(raw)) ? JSON.parse(raw) : [];
        } catch (e) { return []; }
    }

    function saveMessages(messages) {
        try { localStorage.setItem(STORAGE_KEY, JSON.stringify(messages)); } catch (e) {}
    }

    function stripImageRefs(text) {
        if (!text) return text;
        return text
            .replace(/!\[.*?\]\(.*?\)/g, '[Imagem não suportada pelo assistente]')
            .replace(/<img[^>]*>/g, '[Imagem não suportada pelo assistente]');
    }

    function formatMessage(text) {
        if (!text) return '';
        const safe = stripImageRefs(text);
        return safe
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/`(.*?)`/g, '<code class="px-1 py-0.5 bg-slate-100 dark:bg-slate-700 rounded text-emerald-600 dark:text-emerald-400 font-mono">$1</code>')
            .replace(/\n/g, '<br>');
    }

    function getChatBody() {
        const chatBox = document.getElementById('floating-chat-box');
        return chatBox ? chatBox.querySelector('.flex-1') : null;
    }

    function scrollToBottom() {
        const body = getChatBody();
        if (body) body.scrollTop = body.scrollHeight;
    }

    function sendQuickPrompt(text) {
        const panel = document.getElementById('floating-chat-box');
        if (panel && panel.classList.contains('hidden')) {
            window.floatingAiWidget.toggle();
        }
        const input = document.getElementById('floating-ai-input');
        if (input) {
            input.value = text;
            sendMessage(input);
        }
    }

    function appendMessage(container, role, content) {
        const row = document.createElement('div');
        row.className = 'flex gap-2.5 ' + (role === 'user' ? 'justify-end' : 'justify-start');
        
        if (role === 'assistant') {
            const avatar = document.createElement('div');
            avatar.className = 'w-7 h-7 rounded-xl bg-gradient-to-br from-emerald-600 to-teal-700 text-white flex items-center justify-center shrink-0 text-xs shadow-sm mt-0.5';
            avatar.innerHTML = '<i class="fas fa-robot"></i>';
            row.appendChild(avatar);
        }
        
        const bubble = document.createElement('div');
        bubble.className = 'max-w-[85%] rounded-2xl px-4 py-2.5 text-xs shadow-sm space-y-1 ' + 
            (role === 'user' 
                ? 'bg-emerald-700 text-white rounded-br-xs' 
                : 'bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 border border-slate-200/80 dark:border-slate-700/80 rounded-bl-xs');
        
        const label = document.createElement('div');
        label.className = 'text-[9px] font-bold uppercase tracking-wider opacity-75 ' + (role === 'user' ? 'text-emerald-100' : 'text-emerald-600 dark:text-emerald-400');
        label.textContent = role === 'user' ? 'Tu' : 'Assistente IA';
        
        const body = document.createElement('div');
        body.className = 'leading-relaxed whitespace-pre-wrap';
        body.innerHTML = formatMessage(content);
        
        bubble.appendChild(label);
        bubble.appendChild(body);
        row.appendChild(bubble);
        container.appendChild(row);
    }

    function showLoading(container) {
        const loader = document.createElement('div');
        loader.id = 'ai-loading-indicator';
        loader.className = 'flex items-center gap-2.5 text-slate-500 dark:text-slate-400 text-xs p-2 bg-white/60 dark:bg-slate-800/60 rounded-xl border border-slate-200/50 dark:border-slate-700/50';
        loader.innerHTML = '<i class="fas fa-circle-notch fa-spin text-emerald-600 text-sm"></i><span class="font-medium">Assistente IA a analisar dados...</span>';
        container.appendChild(loader);
        scrollToBottom();
    }

    function removeLoading() {
        const loader = document.getElementById('ai-loading-indicator');
        if (loader) loader.remove();
    }

    async function sendMessage(input) {
        const txt = input.value.trim();
        if (!txt) return;
        
        const quickPrompts = document.getElementById('quick-prompts');
        if (quickPrompts) {
            quickPrompts.style.display = 'none';
        }
        
        const chatBody = getChatBody();
        if (!chatBody) return;

        input.value = '';
        appendMessage(chatBody, 'user', txt);
        scrollToBottom();
        showLoading(chatBody);
        
        const history = getSavedMessages().filter(m => m.meta !== 'greeting').slice(-8);
        
        try {
            const response = await fetch('{{ route('ai.widget.chat') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ prompt: txt, history: history })
            });
            
            const data = await response.json();
            removeLoading();
            
            if (data.success) {
                appendMessage(chatBody, 'assistant', data.response);
            } else {
                const errMsg = (data.message || 'Erro ao consultar IA');
                appendMessage(chatBody, 'assistant', '<i class="fas fa-exclamation-triangle text-amber-500 me-1"></i> ' + errMsg);
                if (errMsg.toLowerCase().includes('image') || errMsg.toLowerCase().includes('imagem')) {
                    appendMessage(chatBody, 'assistant', '<i class="fas fa-circle-info text-sky-500 me-1"></i> <strong>Nota:</strong> Este assistente não suporta envio de imagens. Envie apenas texto para receber apoio pedagógico e administrativo.');
                }
            }
        } catch (e) {
            removeLoading();
            appendMessage(chatBody, 'assistant', '<i class="fas fa-times-circle text-rose-500 me-1"></i> Erro de ligação com o servidor de IA.');
        }
        
        const messages = getSavedMessages();
        messages.push({ role: 'user', content: txt });
        saveMessages(messages);
        scrollToBottom();
    }

    window.floatingAiWidget = {
        init() {
            this.chatBox = document.getElementById('floating-chat-box');
            this.input = document.getElementById('floating-ai-input');
            this.sendBtn = document.getElementById('floating-ai-send');
            this.clearBtn = document.getElementById('floating-ai-clear');
            this.triggerBtn = document.getElementById('floating-ai-trigger');
            this.panel = document.getElementById('floating-ai-panel');
            this.chatBody = getChatBody();
            
            if (this.triggerBtn) {
                this.triggerBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.toggle();
                });
            }
            
            if (this.sendBtn && this.input) {
                this.sendBtn.addEventListener('click', () => sendMessage(this.input));
            }
            
            if (this.input) {
                this.input.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        sendMessage(this.input);
                    }
                });
            }
            
            if (this.clearBtn) {
                this.clearBtn.addEventListener('click', () => {
                    saveMessages([]);
                    if (this.chatBody) this.chatBody.innerHTML = '';
                    const quickPrompts = document.getElementById('quick-prompts');
                    if (quickPrompts) quickPrompts.style.display = '';
                    appendMessage(getChatBody(), 'assistant', 'Conversa limpa. Como posso ajudar agora?');
                });
            }
            
            document.addEventListener('click', (e) => {
                if (this.panel && !this.panel.contains(e.target) && this.triggerBtn && e.target !== this.triggerBtn) {
                    this.close();
                }
            });
            
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.chatBox && !this.chatBox.classList.contains('hidden')) {
                    this.close();
                }
            });
            
            const saved = getSavedMessages();
            if (saved.length > 0) {
                const quickPrompts = document.getElementById('quick-prompts');
                if (quickPrompts) quickPrompts.style.display = 'none';
                saved.forEach(m => appendMessage(getChatBody(), m.role, m.content));
            } else {
                appendMessage(getChatBody(), 'assistant', defaultGreeting.content);
            }
            scrollToBottom();
        },
        toggle() {
            const panel = document.getElementById('floating-chat-box');
            if (!panel) return;
            panel.classList.toggle('hidden');
            if (!panel.classList.contains('hidden')) {
                this.chatBody = getChatBody();
                setTimeout(() => scrollToBottom(), 200);
            }
        },
        close() {
            const panel = document.getElementById('floating-chat-box');
            if (panel) panel.classList.add('hidden');
        }
    };

    document.addEventListener('DOMContentLoaded', () => {
        window.floatingAiWidget.init();
    });
})();
</script>

<div id="floating-ai-panel" class="fixed bottom-6 right-6 z-[9999]">
    {{-- Chat Panel Popup --}}
    <div id="floating-chat-box" class="hidden absolute bottom-20 right-0 w-[calc(100vw-3rem)] sm:w-[420px] h-[540px] max-h-[calc(100vh-8rem)] bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200/80 dark:border-slate-800 flex flex-col overflow-hidden text-xs">
        {{-- Header --}}
        <div class="px-5 py-4 bg-slate-900 text-white flex items-center justify-between shrink-0 border-b border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center text-lg shadow-md border border-white/20">
                    <i class="fas fa-robot"></i>
                </div>
                <div>
                    <h4 class="font-bold text-sm text-white leading-tight flex items-center gap-1.5 font-heading">
                        Assistente IA ZamEdu
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">v2.0</span>
                    </h4>
                    <span class="text-[11px] text-slate-400 font-medium">Inteligência Pedagógica & Gestão</span>
                </div>
            </div>
            <div class="flex items-center gap-1">
                <button type="button" id="floating-ai-clear" class="p-2 text-slate-400 hover:text-red-400 hover:bg-slate-800/80 transition rounded-xl" title="Limpar histórico">
                    <i class="fas fa-trash-can text-xs"></i>
                </button>
                <button type="button" onclick="window.floatingAiWidget.close()" class="p-2 text-slate-400 hover:text-white hover:bg-slate-800/80 transition rounded-xl" title="Fechar">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>

        {{-- Chat Body --}}
        <div class="flex-1 p-4 overflow-y-auto bg-slate-50/50 dark:bg-slate-950/50 space-y-3.5">
            {{-- Quick Prompts (visible on initial load) --}}
            <div id="quick-prompts" class="pt-2 space-y-2">
                <p class="text-[11px] text-slate-400 dark:text-slate-500 font-semibold uppercase tracking-wider px-1">Sugestões rápidas:</p>
                <div class="flex flex-col gap-1.5">
                    <button type="button" onclick="sendQuickPrompt('Como gerar mensagens para encarregados sobre faltas?')"
                            class="text-left p-2.5 rounded-xl bg-white dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-slate-700/60 border border-slate-200/80 dark:border-slate-700/80 text-slate-700 dark:text-slate-200 transition text-xs flex items-center justify-between group">
                        <span><i class="fas fa-comment-dots text-sky-500 me-1"></i> Mensagem para Encarregados</span>
                        <i class="fas fa-chevron-right text-[10px] text-slate-400 group-hover:translate-x-0.5 transition"></i>
                    </button>
                    <button type="button" onclick="sendQuickPrompt('Qual a fórmula oficial do MINEDH para cálculo de NFD em turmas com exame?')"
                            class="text-left p-2.5 rounded-xl bg-white dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-slate-700/60 border border-slate-200/80 dark:border-slate-700/80 text-slate-700 dark:text-slate-200 transition text-xs flex items-center justify-between group">
                        <span><i class="fas fa-calculator text-emerald-500 me-1"></i> Regras de Cálculo MINEDH (Lei 18/2018)</span>
                        <i class="fas fa-chevron-right text-[10px] text-slate-400 group-hover:translate-x-0.5 transition"></i>
                    </button>
                    <button type="button" onclick="sendQuickPrompt('Como registrar novas matrículas e atribuir turmas aos alunos?')"
                            class="text-left p-2.5 rounded-xl bg-white dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-slate-700/60 border border-slate-200/80 dark:border-slate-700/80 text-slate-700 dark:text-slate-200 transition text-xs flex items-center justify-between group">
                        <span><i class="fas fa-user-graduate text-amber-500 me-1"></i> Guia de Matrículas & Turmas</span>
                        <i class="fas fa-chevron-right text-[10px] text-slate-400 group-hover:translate-x-0.5 transition"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Footer Input --}}
        <form class="p-3 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 flex items-center gap-2 shrink-0" onsubmit="event.preventDefault();">
            <input type="text" id="floating-ai-input" placeholder="Escreva uma pergunta ao assistente..." autocomplete="off"
                   class="flex-1 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-100 py-2 px-3.5 text-xs font-medium focus:bg-white dark:focus:bg-slate-800 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition placeholder-slate-400">
            <button type="button" id="floating-ai-send"
                    class="w-9 h-9 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white flex items-center justify-center shrink-0 shadow-md hover:shadow-lg transition cursor-pointer">
                <i class="fas fa-paper-plane text-xs"></i>
            </button>
        </form>
    </div>

    {{-- Trigger Button --}}
    <button type="button" id="floating-ai-trigger"
        class="group relative flex items-center justify-center w-14 h-14 rounded-full bg-gradient-to-br from-emerald-600 via-teal-600 to-emerald-800 text-white shadow-2xl shadow-emerald-900/30 hover:shadow-emerald-600/40 hover:scale-110 active:scale-95 transition-all duration-300 border-2 border-white/30 cursor-pointer"
        title="Assistente IA ZamEdu">
        <i class="fas fa-robot text-xl transition-transform duration-300 group-hover:rotate-12 group-hover:scale-110"></i>
        <span class="absolute -top-0.5 -right-0.5 flex h-4 w-4" id="ai-notification-dot">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-4 w-4 bg-amber-500 border-2 border-white"></span>
        </span>
    </button>
</div>
@endauth
