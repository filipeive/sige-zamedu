<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Licença Suspensa - ZamEdu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-lg w-full bg-white rounded-3xl border border-slate-200 shadow-2xl p-8 text-center">
        <div class="w-20 h-20 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
            <i class="fa-solid fa-triangle-exclamation text-4xl"></i>
        </div>
        <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight mb-2">Licença do Sistema Suspensa</h3>
        <p class="text-sm text-slate-600 mb-6 leading-relaxed">
            A subscrição do <strong>ZamEdu</strong> para esta instituição expirou e o período de carência foi ultrapassado.
            <br><span class="text-xs text-slate-400 mt-1 block">Nota: Todos os dados de alunos, turmas e notas permanecem salvaguardados na base de dados.</span>
        </p>

        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 text-left text-xs mb-8 space-y-2">
            <div class="flex justify-between items-center">
                <span class="text-slate-500 font-medium">Cliente:</span>
                <strong class="text-slate-800 font-bold">{{ $license->client_name ?? 'N/A' }}</strong>
            </div>
            <div class="flex justify-between items-center border-t border-slate-200 pt-2">
                <span class="text-slate-500 font-medium">Data de Expiração:</span>
                <strong class="text-slate-800 font-bold">{{ $license?->expires_at ? $license->expires_at->format('d/m/Y') : 'N/A' }}</strong>
            </div>
            <div class="flex justify-between items-center border-t border-slate-200 pt-2">
                <span class="text-slate-500 font-medium">Suporte FDS Software:</span>
                <strong class="text-emerald-700 font-bold">suporte@fdssoftware.co.mz</strong>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            @auth
                <form action="{{ route('logout') }}" method="POST" class="w-full sm:w-auto">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs transition inline-flex items-center justify-center gap-2 shadow-sm">
                        <i class="fa-solid fa-right-from-bracket"></i> Sair
                    </button>
                </form>
                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin'))
                    <a href="{{ route('admin.license') }}" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition inline-flex items-center justify-center gap-2">
                        <i class="fa-solid fa-key"></i> Renovar Licença
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition inline-flex items-center justify-center gap-2">
                    <i class="fa-solid fa-right-to-bracket"></i> Entrar como Administrador
                </a>
            @endauth
        </div>
    </div>
</body>
</html>

