@guest
<div class="fixed bottom-6 right-6 z-[9999]" id="public-ai-widget">
    {{-- Chat Panel Popup --}}
    <div id="public-ai-chat" class="hidden absolute bottom-20 right-0 w-[calc(100vw-3rem)] sm:w-[420px] h-[540px] max-h-[calc(100vh-8rem)] bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200/80 dark:border-slate-800 flex flex-col overflow-hidden text-xs">
        {{-- Header --}}
        <div class="px-5 py-4 bg-slate-900 text-white flex items-center justify-between shrink-0 border-b border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center text-lg shadow-md border border-white/20">
                    <i class="fas fa-robot"></i>
                </div>
                <div>
                    <h4 class="font-bold text-sm text-white leading-tight font-heading">Assistente IA ZamEdu</h4>
                    <span class="text-[11px] text-slate-400 font-medium">Inteligência Pedagógica & Gestão</span>
                </div>
            </div>
            <div class="flex items-center gap-1">
                <button type="button" onclick="closePublicAiWidget()" class="p-2 text-slate-400 hover:text-white hover:bg-slate-800/80 transition rounded-xl" title="Fechar">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>

        {{-- Chat Body --}}
        <div class="flex-1 p-4 overflow-y-auto bg-slate-50/50 dark:bg-slate-950/50 space-y-3.5" id="public-chat-box">
            <div class="flex gap-2.5 justify-start">
                <div class="w-7 h-7 rounded-xl bg-gradient-to-br from-emerald-600 to-teal-700 text-white flex items-center justify-center shrink-0 text-xs shadow-sm mt-0.5">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="max-w-[85%] rounded-2xl px-4 py-2.5 text-xs shadow-sm space-y-1 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 border border-slate-200/80 dark:border-slate-700/80 rounded-bl-xs">
                    <div class="text-[9px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Assistente IA</div>
                    <div class="leading-relaxed whitespace-pre-wrap">Olá! Sou o **Assistente IA do ZamEdu**. Como posso ajudar hoje?</div>
                </div>
            </div>
            <div class="flex gap-2.5 justify-start">
                <div class="w-7 h-7 rounded-xl bg-gradient-to-br from-emerald-600 to-teal-700 text-white flex items-center justify-center shrink-0 text-xs shadow-sm mt-0.5">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="max-w-[85%] rounded-2xl px-4 py-2.5 text-xs shadow-sm space-y-1 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 border border-slate-200/80 dark:border-slate-700/80 rounded-bl-xs">
                    <div class="text-[9px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Assistente IA</div>
                    <div class="leading-relaxed whitespace-pre-wrap">Para uma experiência completa com chat persistente, histórico de conversas e respostas personalizadas, faça login no sistema.</div>
                    <div class="mt-2">
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-sm">
                            <i class="fas fa-sign-in-alt"></i> Fazer Login
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer Input --}}
        <form class="p-3 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 flex items-center gap-2 shrink-0" onsubmit="event.preventDefault(); alert('Faça login para interagir com o assistente IA.');">
            <input type="text" placeholder="Escreva uma pergunta..." disabled autocomplete="off"
                   class="flex-1 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-100 py-2 px-3.5 text-xs font-medium focus:bg-white dark:focus:bg-slate-800 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition placeholder-slate-400 cursor-not-allowed opacity-75">
            <button type="button" disabled
                    class="w-9 h-9 rounded-xl bg-emerald-600 opacity-75 text-white flex items-center justify-center shrink-0 shadow-md cursor-not-allowed">
                <i class="fas fa-paper-plane text-xs"></i>
            </button>
        </form>
    </div>

    {{-- Trigger Button --}}
    <button type="button"
        onclick="togglePublicAiWidget()"
        class="group relative flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-emerald-600 via-teal-600 to-emerald-800 text-white shadow-2xl shadow-emerald-900/30 hover:shadow-emerald-600/40 hover:scale-110 active:scale-95 transition-all duration-300 border-2 border-white/30 cursor-pointer"
        title="Assistente IA ZamEdu">
        <i class="fas fa-robot text-2xl transition-transform duration-300 group-hover:rotate-12 group-hover:scale-110"></i>
        <span class="absolute -bottom-6 right-0 whitespace-nowrap bg-slate-900 text-white text-[10px] font-bold px-2 py-1 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 shadow-lg">
            Assistente IA
        </span>
    </button>
</div>

<script>
    function togglePublicAiWidget() {
        const chat = document.getElementById('public-ai-chat');
        if (chat) {
            chat.classList.toggle('hidden');
            if (!chat.classList.contains('hidden')) {
                chat.classList.add('flex');
            } else {
                chat.classList.remove('flex');
            }
        }
    }
    function closePublicAiWidget() {
        const chat = document.getElementById('public-ai-chat');
        if (chat) {
            chat.classList.add('hidden');
            chat.classList.remove('flex');
        }
    }
</script>
@endguest