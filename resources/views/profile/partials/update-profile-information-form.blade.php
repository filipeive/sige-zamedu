<form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
    @csrf
    @method('PATCH')

    <div>
        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
            Nome Completo <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i class="fas fa-user text-sm"></i>
            </div>
            <input type="text" 
                   id="name" 
                   name="name" 
                   value="{{ old('name', auth()->user()->name) }}" 
                   required
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border {{ $errors->has('name') ? 'border-rose-400 focus:ring-rose-400' : 'border-slate-200 focus:ring-emerald-500' }} bg-white text-slate-900 text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all shadow-2xs">
        </div>
        @error('name')
            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
            Email Institucional / Pessoal <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i class="fas fa-envelope text-sm"></i>
            </div>
            <input type="email" 
                   id="email" 
                   name="email" 
                   value="{{ old('email', auth()->user()->email) }}" 
                   required
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border {{ $errors->has('email') ? 'border-rose-400 focus:ring-rose-400' : 'border-slate-200 focus:ring-emerald-500' }} bg-white text-slate-900 text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all shadow-2xs">
        </div>
        @error('email')
            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
            Contacto Telefónico
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i class="fas fa-phone text-sm"></i>
            </div>
            <input type="text" 
                   id="phone" 
                   name="phone" 
                   value="{{ old('phone', auth()->user()->phone) }}" 
                   placeholder="+258 84 XXX XXXX"
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border {{ $errors->has('phone') ? 'border-rose-400 focus:ring-rose-400' : 'border-slate-200 focus:ring-emerald-500' }} bg-white text-slate-900 text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all shadow-2xs">
        </div>
        @error('phone')
            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <div class="pt-2">
        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow-xs hover:shadow-md transition-all">
            <i class="fas fa-save"></i>
            Salvar Alterações
        </button>
    </div>
</form>
