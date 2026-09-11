<div x-data="cookieConsentManager()" 
     x-show="showBanner" 
     x-cloak 
     x-transition:enter="transition ease-out duration-300 transform"
     x-transition:enter-start="translate-y-full opacity-0"
     x-transition:enter-end="translate-y-0 opacity-100"
     x-transition:leave="transition ease-in duration-200 transform"
     x-transition:leave-start="translate-y-0 opacity-100"
     x-transition:leave-end="translate-y-full opacity-0"
     class="fixed bottom-4 right-4 left-4 sm:left-auto sm:max-w-md z-50 bg-slate-900/95 backdrop-blur-md text-white p-5 rounded-2xl shadow-2xl border border-slate-700/80">
    
    <div class="flex items-start gap-3">
        <div class="p-2 bg-emerald-500/20 text-emerald-400 rounded-xl shrink-0 mt-0.5">
            <i class="fa-solid fa-cookie-bite text-xl"></i>
        </div>
        <div class="space-y-1">
            <h4 class="text-sm font-bold text-white flex items-center justify-between">
                <span>Preferências de Cookies & Privacidade</span>
                <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-emerald-950 text-emerald-400 border border-emerald-800">RGPD / MINEDH</span>
            </h4>
            <p class="text-xs text-slate-300 leading-relaxed">
                Utilizamos cookies essenciais para autenticação multi-escola, preferências de sessão e otimização de performance.
            </p>
        </div>
    </div>

    <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-end gap-2">
        <button type="button" @click="acceptEssential()" class="px-3.5 py-1.5 rounded-xl border border-slate-700 hover:bg-slate-800 text-slate-300 text-xs font-semibold transition">
            Apenas Essenciais
        </button>
        <button type="button" @click="acceptAll()" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-sm transition">
            Aceitar Todos
        </button>
    </div>
</div>

<script>
function cookieConsentManager() {
    return {
        showBanner: false,
        init() {
            const consent = this.getCookie('zamedu_cookie_consent') || localStorage.getItem('zamedu_cookie_consent');
            if (!consent) {
                setTimeout(() => { this.showBanner = true; }, 1000);
            }
        },
        acceptAll() {
            this.setCookie('zamedu_cookie_consent', 'accepted', 365);
            localStorage.setItem('zamedu_cookie_consent', 'accepted');
            this.showBanner = false;
        },
        acceptEssential() {
            this.setCookie('zamedu_cookie_consent', 'essential_only', 365);
            localStorage.setItem('zamedu_cookie_consent', 'essential_only');
            this.showBanner = false;
        },
        setCookie(name, value, days) {
            let expires = "";
            if (days) {
                const date = new Date();
                date.setTime(date.getTime() + (days*24*60*60*1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + (value || "")  + expires + "; path=/; SameSite=Lax";
        },
        getCookie(name) {
            const nameEQ = name + "=";
            const ca = document.cookie.split(';');
            for(let i=0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0)==' ') c = c.substring(1,c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
            }
            return null;
        }
    };
}
</script>
