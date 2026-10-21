<x-guest-layout>
    <div class="min-h-screen w-full flex flex-col items-center justify-between p-4 sm:p-6 lg:p-8 relative bg-gradient-to-br from-[#023318] via-[#00572b] to-[#034522] selection:bg-[#FCD116] selection:text-slate-950 overflow-y-auto">
        
        <!-- Ambient Glowing Lights Background Elements -->
        <div class="absolute top-[-100px] left-[-100px] w-[500px] h-[500px] bg-emerald-400/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-[-100px] right-[-100px] w-[600px] h-[600px] bg-amber-400/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-emerald-600/10 rounded-full blur-[120px] pointer-events-none"></div>

        <!-- Top Navigation Header -->
        <div class="w-full max-w-md sm:max-w-lg flex items-center justify-between z-10 pt-2 pb-4">
            <a href="{{ route('login') }}" class="no-underline group inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/15 text-white text-xs font-bold transition-all duration-300 backdrop-blur-md shadow-sm">
                <i class="fas fa-arrow-left text-amber-300 group-hover:-translate-x-1 transition-transform duration-200"></i>
                <span>Voltar ao Login</span>
            </a>

            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-400/15 border border-amber-400/30 text-amber-300 text-[11px] font-extrabold tracking-wider uppercase backdrop-blur-md">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                <span>MINEDH · SIGE</span>
            </div>
        </div>

        <!-- CENTERED FORGOT PASSWORD CARD -->
        <div class="w-full max-w-md sm:max-w-lg z-10 my-auto" x-data="{ method: '{{ old('method', 'email') }}' }">
            <div class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-2xl border border-white/40 dark:border-slate-800 rounded-[28px] shadow-2xl p-6 sm:p-9 text-slate-900 dark:text-white transition-all duration-300">
                
                <!-- Card Header with Emblem Seal -->
                <div class="text-center mb-6">
                    <div class="inline-flex p-3 rounded-full bg-emerald-50 dark:bg-emerald-950/60 border-2 border-dashed border-[#007A3D] shadow-lg shadow-emerald-900/10 mb-3">
                        <img src="{{ asset('images/logo-visionarios.png') }}" alt="Escola dos Visionários" class="h-14 w-auto object-contain drop-shadow-sm">
                    </div>

                    <div class="space-y-1">
                        <span class="inline-block px-3 py-0.5 rounded-full bg-amber-100 dark:bg-amber-950/50 text-amber-800 dark:text-amber-300 text-[10px] font-black uppercase tracking-widest">
                            Recuperação de Acesso
                        </span>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900 dark:text-white font-heading mt-1">
                            Esqueceu a Senha?
                        </h1>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                            Escolha o método preferido para redefinir a sua palavra-passe com segurança.
                        </p>
                    </div>
                </div>

                <!-- Session Status Alert -->
                @if (session('status'))
                    <div class="p-4 mb-5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200 text-xs font-bold flex items-center gap-2.5">
                        <i class="fas fa-circle-check text-emerald-600 text-base shrink-0"></i>
                        <div>{{ session('status') }}</div>
                    </div>
                @endif

                <!-- Validation Errors Alert -->
                @if ($errors->any())
                    <div class="p-4 mb-5 rounded-2xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-rose-900 dark:text-rose-200 text-xs font-semibold">
                        <div class="font-extrabold flex items-center gap-2 mb-1 text-rose-700 dark:text-rose-300">
                            <i class="fas fa-triangle-exclamation"></i>
                            <span>Atenção:</span>
                        </div>
                        <ul class="list-disc list-inside space-y-0.5 text-[11px] text-rose-800 dark:text-rose-300">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- METHOD SWITCHER TABS -->
                <div class="grid grid-cols-2 gap-2 p-1.5 mb-6 bg-slate-100 dark:bg-slate-800 rounded-2xl border border-slate-200/60 dark:border-slate-700/60">
                    <button type="button" @click="method = 'email'"
                        :class="method === 'email' ? 'bg-white dark:bg-slate-900 text-[#007A3D] dark:text-emerald-400 shadow-sm border border-slate-200/80 dark:border-slate-700' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'"
                        class="py-2.5 px-3 rounded-xl text-xs font-extrabold transition-all duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-envelope"></i>
                        <span>Por E-mail</span>
                    </button>

                    <button type="button" @click="method = 'sms'"
                        :class="method === 'sms' ? 'bg-white dark:bg-slate-900 text-[#007A3D] dark:text-emerald-400 shadow-sm border border-slate-200/80 dark:border-slate-700' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'"
                        class="py-2.5 px-3 rounded-xl text-xs font-extrabold transition-all duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-comment-sms text-amber-500"></i>
                        <span>SMS / OTP</span>
                    </button>
                </div>

                <!-- METHOD 1: EMAIL LINK FORM -->
                <form method="POST" action="{{ route('password.email') }}" x-show="method === 'email'" class="space-y-4">
                    @csrf
                    <input type="hidden" name="method" value="email">

                    <div>
                        <label for="email" class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5 flex items-center gap-1.5">
                            <i class="fas fa-envelope text-[#007A3D]"></i> Endereço de E-mail Registado
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fas fa-at text-xs"></i>
                            </div>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                                placeholder="utilizador@escola.gov.mz"
                                class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-bold focus:bg-white dark:focus:bg-slate-900 focus:border-[#007A3D] focus:ring-4 focus:ring-emerald-500/15 outline-none transition-all duration-200">
                        </div>
                        <p class="mt-1.5 text-[11px] text-slate-500 dark:text-slate-400">
                            Enviaremos um hiperlink seguro para a sua caixa de entrada para definir uma nova palavra-passe.
                        </p>
                    </div>

                    <button type="submit" class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-[#007A3D] to-[#034522] hover:from-[#059669] hover:to-[#007A3D] text-white font-extrabold text-xs uppercase tracking-wider shadow-lg shadow-emerald-900/25 hover:shadow-xl hover:shadow-emerald-800/35 transform hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 flex items-center justify-center gap-2 group">
                        <span>Enviar Link por E-mail</span>
                        <i class="fas fa-paper-plane text-amber-300 group-hover:translate-x-1 transition-transform duration-200"></i>
                    </button>
                </form>

                <!-- METHOD 2: SMS OTP FORM -->
                <form method="POST" action="{{ route('password.email') }}" x-show="method === 'sms'" class="space-y-4" style="display: none;">
                    @csrf
                    <input type="hidden" name="method" value="sms">

                    <div>
                        <label for="phone" class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5 flex items-center gap-1.5">
                            <i class="fas fa-mobile-screen text-[#007A3D]"></i> Número de Telemóvel Registado
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 font-bold text-xs">
                                +258
                            </div>
                            <input id="phone" type="text" name="phone" value="{{ old('phone') }}" placeholder="84 123 4567"
                                class="w-full pl-14 pr-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-bold focus:bg-white dark:focus:bg-slate-900 focus:border-[#007A3D] focus:ring-4 focus:ring-emerald-500/15 outline-none transition-all duration-200">
                        </div>
                        <p class="mt-1.5 text-[11px] text-slate-500 dark:text-slate-400">
                            Dispararemos um código de verificação OTP de 6 dígitos via SMS para o seu telemóvel.
                        </p>
                    </div>

                    <button type="submit" class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-[#007A3D] to-[#034522] hover:from-[#059669] hover:to-[#007A3D] text-white font-extrabold text-xs uppercase tracking-wider shadow-lg shadow-emerald-900/25 hover:shadow-xl hover:shadow-emerald-800/35 transform hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 flex items-center justify-center gap-2 group">
                        <span>Enviar Código OTP por SMS</span>
                        <i class="fas fa-comment-sms text-amber-300 group-hover:translate-x-1 transition-transform duration-200"></i>
                    </button>
                </form>

                <!-- Footer Navigation -->
                <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800 text-center">
                    <a href="{{ route('login') }}" class="no-underline text-xs font-bold text-[#007A3D] dark:text-emerald-400 hover:underline inline-flex items-center gap-1.5">
                        <i class="fas fa-arrow-left"></i> Lembra-se da sua palavra-passe? Voltar ao Login
                    </a>
                </div>

            </div>
        </div>

        <!-- FOOTER -->
        <div class="w-full max-w-md text-center z-10 pt-4 pb-2">
            <p class="text-[11px] text-emerald-100/70 font-medium">
                &copy; {{ date('Y') }} <strong>ZAMEDU - SIGE</strong> · MINEDH Moçambique · Lei 18/2018 SNE
            </p>
        </div>

    </div>
</x-guest-layout>
