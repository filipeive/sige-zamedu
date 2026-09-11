<x-guest-layout>
    <div class="min-h-screen w-full flex flex-col items-center justify-between p-4 sm:p-6 lg:p-8 relative bg-gradient-to-br from-[#023318] via-[#00572b] to-[#034522] selection:bg-[#FCD116] selection:text-slate-950 overflow-y-auto">
        
        <!-- Ambient Glowing Lights Background Elements -->
        <div class="absolute top-[-100px] left-[-100px] w-[500px] h-[500px] bg-emerald-400/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-[-100px] right-[-100px] w-[600px] h-[600px] bg-amber-400/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-emerald-600/10 rounded-full blur-[120px] pointer-events-none"></div>

        <!-- Top Navigation Header -->
        <div class="w-full max-w-md sm:max-w-lg flex items-center justify-between z-10 pt-2 pb-4">
            <a href="{{ route('welcome') }}" class="no-underline group inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/15 text-white text-xs font-bold transition-all duration-300 backdrop-blur-md shadow-sm">
                <i class="fas fa-arrow-left text-amber-300 group-hover:-translate-x-1 transition-transform duration-200"></i>
                <span>Voltar ao Início</span>
            </a>

            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-400/15 border border-amber-400/30 text-amber-300 text-[11px] font-extrabold tracking-wider uppercase backdrop-blur-md">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                <span>MINEDH · SIGE</span>
            </div>
        </div>

        <!-- CENTERED LOGIN CARD -->
        <div class="w-full max-w-md sm:max-w-lg z-10 my-auto">
            <div class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-2xl border border-white/40 dark:border-slate-800 rounded-[28px] shadow-2xl p-6 sm:p-9 text-slate-900 dark:text-white transition-all duration-300">
                
                <!-- Card Header with Emblem Seal -->
                <div class="text-center mb-6">
                    <div class="inline-flex p-3 rounded-full bg-emerald-50 dark:bg-emerald-950/60 border-2 border-dashed border-[#007A3D] shadow-lg shadow-emerald-900/10 mb-3">
                        <img src="{{ asset('images/emblem.png') }}" alt="Emblema da República de Moçambique" class="h-14 w-auto object-contain drop-shadow-sm">
                    </div>

                    <div class="space-y-1">
                        <span class="inline-block px-3 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900/50 text-[#007A3D] dark:text-emerald-300 text-[10px] font-black uppercase tracking-widest">
                            República de Moçambique
                        </span>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900 dark:text-white font-heading mt-1">
                            ZAMEDU - SIGE
                            <br>
                            <span class="text-sm text-[#007A3D]">Entrar na plataforma</span>
                        </h1>
                    </div>
                </div>

                <!-- Session Status Alert -->
                @if (session('status'))
                    <div class="p-3.5 mb-5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200 text-xs font-bold flex items-center gap-2.5">
                        <i class="fas fa-circle-check text-emerald-600 text-base shrink-0"></i>
                        <div>{{ session('status') }}</div>
                    </div>
                @endif

                <!-- Validation Errors Alert -->
                @if ($errors->any())
                    <div class="p-4 mb-5 rounded-2xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-rose-900 dark:text-rose-200 text-xs font-semibold">
                        <div class="font-extrabold flex items-center gap-2 mb-1 text-rose-700 dark:text-rose-300">
                            <i class="fas fa-triangle-exclamation"></i>
                            <span>Erro de Autenticação:</span>
                        </div>
                        <ul class="list-disc list-inside space-y-0.5 text-[11px] text-rose-800 dark:text-rose-300">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- LOGIN FORM -->
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5 flex items-center gap-1.5">
                            <i class="fas fa-envelope text-[#007A3D]"></i>E-mail ou Código
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fas fa-at text-xs"></i>
                            </div>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                                placeholder="utilizador@escola.gov.mz"
                                class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-bold focus:bg-white dark:focus:bg-slate-900 focus:border-[#007A3D] focus:ring-4 focus:ring-emerald-500/15 outline-none transition-all duration-200">
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5 flex items-center gap-1.5">
                            <i class="fas fa-key text-[#007A3D]"></i> Palavra-Passe
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fas fa-lock text-xs"></i>
                            </div>
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                placeholder="••••••••"
                                class="w-full pl-10 pr-10 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-bold focus:bg-white dark:focus:bg-slate-900 focus:border-[#007A3D] focus:ring-4 focus:ring-emerald-500/15 outline-none transition-all duration-200">
                            <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                                <i class="fas fa-eye text-xs" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Options Row: Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between pt-1 text-xs">
                        <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer select-none">
                            <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-[#007A3D] focus:ring-emerald-500 accent-[#007A3D]">
                            <span class="font-semibold text-slate-600 dark:text-slate-400">Lembrar neste dispositivo</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="no-underline font-extrabold text-[#007A3D] hover:text-[#034522] dark:text-emerald-400 hover:underline">
                                Esqueceu a senha?
                            </a>
                        @endif
                    </div>

                    <!-- SUBMIT BUTTON -->
                    <button type="submit" class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-[#007A3D] to-[#034522] hover:from-[#059669] hover:to-[#007A3D] text-white font-extrabold text-xs uppercase tracking-wider shadow-lg shadow-emerald-900/25 hover:shadow-xl hover:shadow-emerald-800/35 transform hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 flex items-center justify-center gap-2 group">
                        <span>Entrar no Sistema</span>
                        <i class="fas fa-arrow-right-to-bracket text-amber-300 group-hover:translate-x-1 transition-transform duration-200"></i>
                    </button>
                </form>

                <!-- DEMO ACCOUNTS QUICK FILL SECTION -->
                <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800">
                    <div class="text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2.5 flex items-center gap-1.5">
                        <i class="fas fa-bolt text-amber-500"></i>
                        <span>Contas de Demonstração (Clique para Preencher):</span>
                    </div>

                    <div class="flex flex-wrap gap-1.5">
                        <button type="button" onclick="fillDemo('superadmin@visionarios.co.mz', 'superadmin123')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-emerald-50 dark:bg-slate-800 dark:hover:bg-emerald-950/50 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-[10px] font-bold transition flex items-center gap-1">
                            <i class="fas fa-crown text-amber-500"></i> Super Admin
                        </button>

                        <button type="button" onclick="fillDemo('inspector.zambezia@minedh.gov.mz', 'provincia123')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-emerald-50 dark:bg-slate-800 dark:hover:bg-emerald-950/50 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-[10px] font-bold transition flex items-center gap-1">
                            <i class="fas fa-building-columns text-sky-600"></i> Insp. Provincial
                        </button>

                        <button type="button" onclick="fillDemo('director.namacurra@minedh.gov.mz', 'distrito123')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-emerald-50 dark:bg-slate-800 dark:hover:bg-emerald-950/50 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-[10px] font-bold transition flex items-center gap-1">
                            <i class="fas fa-map-location-dot text-indigo-600"></i> Dir. Distrital
                        </button>

                        <button type="button" onclick="fillDemo('zip.namacurra@minedh.gov.mz', 'zip123')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-emerald-50 dark:bg-slate-800 dark:hover:bg-emerald-950/50 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-[10px] font-bold transition flex items-center gap-1">
                            <i class="fas fa-sitemap text-teal-600"></i> Coord. ZIP
                        </button>

                        <button type="button" onclick="fillDemo('admin@visionarios.co.mz', 'admin123')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-emerald-50 dark:bg-slate-800 dark:hover:bg-emerald-950/50 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-[10px] font-bold transition flex items-center gap-1">
                            <i class="fas fa-school text-emerald-600"></i> Dir. Escola
                        </button>

                        <button type="button" onclick="fillDemo('pedagogia@visionarios.co.mz', 'pedagogia123')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-emerald-50 dark:bg-slate-800 dark:hover:bg-emerald-950/50 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-[10px] font-bold transition flex items-center gap-1">
                            <i class="fas fa-book-open-reader text-purple-600"></i> Pedagógico
                        </button>

                        <button type="button" onclick="fillDemo('professor@visionarios.co.mz', 'professor123')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-emerald-50 dark:bg-slate-800 dark:hover:bg-emerald-950/50 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-[10px] font-bold transition flex items-center gap-1">
                            <i class="fas fa-chalkboard-user text-blue-600"></i> Professor
                        </button>

                        <button type="button" onclick="fillDemo('secretaria@visionarios.co.mz', 'secretaria123')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-emerald-50 dark:bg-slate-800 dark:hover:bg-emerald-950/50 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-[10px] font-bold transition flex items-center gap-1">
                            <i class="fas fa-id-card text-rose-600"></i> Secretária
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- FOOTER -->
        <div class="w-full max-w-md text-center z-10 pt-4 pb-2">
            <p class="text-[11px] text-emerald-100/70 font-medium">
                &copy; {{ date('Y') }} <strong>ZAMEDU - SIGE</strong> · MINEDH Moçambique · Lei 18/2018 SNE
            </p>
            <p class="text-[10px] text-emerald-200/50 mt-0.5">
                Desenvolvido por <a href="http://146.235.224.99/" target="_blank" class="no-underline text-amber-300 font-bold hover:underline">Filipe dos Santos</a>
            </p>
        </div>

    </div>

    <script>
        function togglePasswordVisibility() {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                pwd.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        function fillDemo(email, pwd) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = pwd;
        }
    </script>
</x-guest-layout>
