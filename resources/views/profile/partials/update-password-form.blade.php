<form method="POST" action="{{ route('password.update') }}" class="space-y-5">
    @csrf
    @method('PUT')

    <div>
        <label for="current_password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
            Senha Atual <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i class="fas fa-lock text-sm"></i>
            </div>
            <input type="password" 
                   id="current_password" 
                   name="current_password" 
                   required
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border {{ $errors->has('current_password') ? 'border-rose-400 focus:ring-rose-400' : 'border-slate-200 focus:ring-emerald-500' }} bg-white text-slate-900 text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all shadow-2xs">
        </div>
        @error('current_password')
            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
            Nova Senha <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i class="fas fa-key text-sm"></i>
            </div>
            <input type="password" 
                   id="password" 
                   name="password" 
                   required
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border {{ $errors->has('password') ? 'border-rose-400 focus:ring-rose-400' : 'border-slate-200 focus:ring-emerald-500' }} bg-white text-slate-900 text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all shadow-2xs">
        </div>
        @error('password')
            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
            Confirmar Nova Senha <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i class="fas fa-check-double text-sm"></i>
            </div>
            <input type="password" 
                   id="password_confirmation" 
                   name="password_confirmation" 
                   required
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 focus:ring-emerald-500 bg-white text-slate-900 text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all shadow-2xs">
        </div>
    </div>

    <div class="pt-2">
        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold shadow-xs hover:shadow-md transition-all">
            <i class="fas fa-shield-alt"></i>
            Atualizar Senha
        </button>
    </div>
</form>
