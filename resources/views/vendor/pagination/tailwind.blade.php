@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between py-2 px-1">
        <!-- Mobile View (Previous / Next Buttons) -->
        <div class="flex flex-1 justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-400 cursor-not-allowed">
                    <i class="fas fa-chevron-left me-1.5 text-[10px]"></i> Anterior
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="no-underline relative inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-emerald-700 transition shadow-2xs">
                    <i class="fas fa-chevron-left me-1.5 text-[10px]"></i> Anterior
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="no-underline relative inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-emerald-700 transition shadow-2xs">
                    Próximo <i class="fas fa-chevron-right ms-1.5 text-[10px]"></i>
                </a>
            @else
                <span class="relative inline-flex items-center rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-400 cursor-not-allowed">
                    Próximo <i class="fas fa-chevron-right ms-1.5 text-[10px]"></i>
                </span>
            @endif
        </div>

        <!-- Desktop View (Full Pagination) -->
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-xs font-semibold text-slate-500">
                    Mostrando
                    @if ($paginator->firstItem())
                        <span class="font-extrabold text-slate-800">{{ $paginator->firstItem() }}</span>
                        a
                        <span class="font-extrabold text-slate-800">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    de
                    <span class="font-extrabold text-slate-800">{{ $paginator->total() }}</span>
                    resultados
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex items-center gap-1 rounded-xl bg-slate-50/70 p-1 border border-slate-200/80 shadow-2xs">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span class="relative inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-300 bg-slate-100 cursor-not-allowed" aria-hidden="true">
                                <i class="fas fa-chevron-left text-[11px]"></i>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="no-underline relative inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-600 bg-white hover:bg-slate-100 hover:text-emerald-700 transition shadow-2xs font-bold" aria-label="{{ __('pagination.previous') }}">
                            <i class="fas fa-chevron-left text-[11px]"></i>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="relative inline-flex items-center justify-center w-8 h-8 text-xs font-extrabold text-slate-400">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="relative inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-black text-white bg-emerald-700 shadow-xs border border-emerald-800/20">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="no-underline relative inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold text-slate-700 bg-white hover:bg-slate-100 hover:text-emerald-700 transition shadow-2xs" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="no-underline relative inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-600 bg-white hover:bg-slate-100 hover:text-emerald-700 transition shadow-2xs font-bold" aria-label="{{ __('pagination.next') }}">
                            <i class="fas fa-chevron-right text-[11px]"></i>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span class="relative inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-300 bg-slate-100 cursor-not-allowed" aria-hidden="true">
                                <i class="fas fa-chevron-right text-[11px]"></i>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
