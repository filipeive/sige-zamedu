{{-- ============================================================
     FLOATING AI ASSISTANT WIDGET (Clean Modern UI/UX)
     ============================================================ --}}
@auth
<div x-data="floatingAiWidget()"
     @keydown.escape.window="openAiWidget = false"
     class="fixed bottom-5 right-5 z-40"
     x-cloak>

    {{-- Trigger Button --}}
    <button type="button"
        @click.stop="toggleWidget()"
        class="relative flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-tr from-emerald-700 to-teal-500 text-white shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 transition-all duration-200 border-2 border-white/20 cursor-pointer"
        title="Assistente IA">
        <i class="fas fa-robot text-lg transition-transform duration-200 group-hover:rotate-12"></i>
        <span class="absolute -top-0.5 -right-0.5 w-3 h-3 bg-amber-400 border-2 border-white rounded-full animate-pulse" x-show="!openAiWidget"></span>
    </button>

    {{-- Chat Panel Popup --}}
    <div x-show="openAiWidget"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        class="absolute bottom-16 right-0 w-80 sm:w-96 h-[460px] max-h-[calc(100vh-6rem)] bg-white rounded-2xl shadow-2xl border border-slate-200 flex flex-col overflow-hidden text-xs z-40"
        @click.outside="openAiWidget = false">

        {{-- Header --}}
        <div class="px-4 py-3 bg-slate-900 text-white flex items-center justify-between shrink-0 border-b border-slate-800">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm border border-emerald-500/30">
                    <i class="fas fa-robot"></i>
                </div>
                <div>
                    <h4 class="font-bold text-xs text-white leading-none">Assistente IA</h4>
                    <span class="text-[10px] text-slate-400 font-medium">Visionários School System</span>
                </div>
            </div>
            <div class="flex items-center gap-1">
                <button type="button" @click.stop="clearChat()" class="p-1.5 text-slate-400 hover:text-slate-200 transition rounded" title="Limpar conversa">
                    <i class="fas fa-trash-can text-xs"></i>
                </button>
                <button type="button" @click.stop="openAiWidget = false" class="p-1.5 text-slate-400 hover:text-white transition rounded" title="Fechar">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>

        {{-- Chat Body --}}
        <div class="flex-1 p-3 overflow-y-auto bg-slate-50 space-y-3" id="floating-chat-box">
            <template x-for="(m, i) in aiMessages" :key="i">
                <div class="flex gap-2" :class="m.role === 'user' ? 'justify-end' : 'justify-start'">
                    <template x-if="m.role === 'assistant'">
                        <div class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0 text-[10px] shadow-2xs mt-0.5">
                            <i class="fas fa-robot"></i>
                        </div>
                    </template>

                    <div class="max-w-[85%] rounded-2xl px-3.5 py-2 text-xs shadow-2xs space-y-0.5"
                         :class="m.role === 'user'
                            ? 'bg-emerald-600 text-white rounded-br-xs'
                            : 'bg-white text-slate-800 border border-slate-200/80 rounded-bl-xs'">
                        <div class="text-[9px] font-extrabold uppercase tracking-wider opacity-75"
                             :class="m.role === 'user' ? 'text-emerald-100' : 'text-slate-400'"
                             x-text="m.role === 'user' ? 'Tu' : 'Assistente IA'"></div>
                        <div class="leading-relaxed" x-html="formatMessage(m.content)"></div>
                    </div>
                </div>
            </template>

            <div class="flex items-center gap-2 text-slate-400 italic text-[11px] p-1" x-show="aiLoading" x-cloak>
                <i class="fas fa-circle-notch fa-spin text-emerald-600"></i> Assistente IA a pensar...
            </div>
        </div>

        {{-- Footer Input --}}
        <form class="p-2.5 bg-white border-t border-slate-100 flex items-center gap-2 shrink-0" @submit.prevent="sendMessage()">
            <input type="text" x-model="aiInput" placeholder="Pergunte ao assistente..." :disabled="aiLoading" autocomplete="off"
                   class="flex-1 rounded-xl border-slate-200 bg-slate-50 text-slate-800 py-1.5 px-3 text-xs font-medium focus:bg-white focus:border-emerald-500 focus:ring-emerald-500">
            <button type="submit" :disabled="aiLoading || !aiInput.trim()"
                    class="w-8 h-8 rounded-xl bg-emerald-600 hover:bg-emerald-700 disabled:opacity-40 disabled:hover:bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-2xs transition">
                <i class="fas fa-paper-plane text-xs"></i>
            </button>
        </form>
    </div>
</div>

<script>
function floatingAiWidget() {
    const STORAGE_KEY = 'visionarios_ai_chat_history';
    const defaultGreeting = {
        role: 'assistant',
        meta: 'greeting',
        content: 'Olá! Sou o Assistente IA do Visionários. Como posso ajudar com alunos, relatórios ou dúvidas do sistema?'
    };

    let saved = [];
    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (raw) saved = JSON.parse(raw);
    } catch (e) { saved = []; }

    return {
        openAiWidget: false,
        aiInput: '',
        aiLoading: false,
        aiMessages: (Array.isArray(saved) && saved.length > 0) ? saved : [defaultGreeting],

        toggleWidget() {
            this.openAiWidget = !this.openAiWidget;
            if (this.openAiWidget) {
                setTimeout(() => this.scrollToBottom(), 200);
            }
        },

        saveHistory() {
            try {
                localStorage.setItem(STORAGE_KEY, JSON.stringify(this.aiMessages));
            } catch (e) {}
        },

        async sendMessage() {
            const txt = this.aiInput.trim();
            if (!txt || this.aiLoading) return;

            const realHistory = this.aiMessages.filter(m => m.meta !== 'greeting');
            const historyContext = realHistory.slice(-8);

            this.aiMessages.push({ role: 'user', content: txt });
            this.saveHistory();
            this.aiInput = '';
            this.aiLoading = true;
            this.scrollToBottom();

            try {
                const response = await fetch('{{ route('ai.widget.chat') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        prompt: txt,
                        history: historyContext
                    })
                });

                const data = await response.json();
                if (data.success) {
                    this.aiMessages.push({ role: 'assistant', content: data.response });
                } else {
                    this.aiMessages.push({ role: 'assistant', content: '⚠️ ' + (data.message || 'Erro ao consultar IA') });
                }
            } catch (e) {
                this.aiMessages.push({ role: 'assistant', content: '❌ Erro de ligação com o servidor de IA.' });
            } finally {
                this.aiLoading = false;
                this.saveHistory();
                this.scrollToBottom();
            }
        },

        async clearChat() {
            this.aiMessages = [];
            try { localStorage.removeItem(STORAGE_KEY); } catch (e) {}
            this.aiMessages.push({
                role: 'assistant',
                meta: 'greeting',
                content: '✨ Conversa limpa. Como posso ajudar agora?'
            });
            this.saveHistory();
            this.scrollToBottom();

            try {
                await fetch('{{ route('ai.widget.reset') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
            } catch (e) {}
        },

        formatMessage(text) {
            if (!text) return '';
            return text
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                .replace(/`(.*?)`/g, '<code>$1</code>')
                .replace(/\n/g, '<br>');
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const cb = document.getElementById('floating-chat-box');
                if (cb) {
                    cb.scrollTop = cb.scrollHeight;
                }
            });
        }
    };
}
</script>
@endauth
