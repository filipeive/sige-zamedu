<nav class="mb-3" aria-label="breadcrumb">
    <ol class="flex flex-wrap items-center gap-1.5 text-xs text-slate-500 font-medium">
        <li class="inline-flex items-center">
            @auth
                <a href="{{ route('dashboard') }}" class="no-underline text-slate-500 hover:text-emerald-600 transition inline-flex items-center gap-1">
                    <i class="fas fa-home"></i> Início
                </a>
            @else
                <a href="{{ route('welcome') }}" class="no-underline text-slate-500 hover:text-emerald-600 transition inline-flex items-center gap-1">
                    <i class="fas fa-home"></i> Início
                </a>
            @endauth
        </li>
        @yield('breadcrumbs')
    </ol>
</nav>

