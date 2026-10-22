@extends('layouts.app')

@section('title', 'Lista de Materiais Escolares')
@section('page-title', 'Lista de Materiais Escolares')
@section('page-title-icon', 'fas fa-folder-open')

@section('breadcrumbs')
    <li class="breadcrumb-item text-slate-400">Portal Público</li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Materiais Escolares</li>
@endsection

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    <!-- Hero Card -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 p-6 text-white shadow-xl">
        <div class="flex items-center gap-3.5">
            <span class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-emerald-300 text-xl">
                <i class="fas fa-book-open"></i>
            </span>
            <div>
                <h1 class="text-xl font-bold text-white">Lista de Materiais Escolares</h1>
                <p class="text-xs text-slate-300 mt-0.5">Ano Letivo {{ next_school_year() }} &bull; Itens recomendados por ciclo</p>
            </div>
        </div>
    </div>

    <!-- Grid of Material Lists -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($materialLists as $list)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                        <h2 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <i class="fas fa-pencil-alt text-xs"></i>
                            </span>
                            {{ $list->grade_level === 'pre-school' ? 'Pré-Escolar' : 'Ensino Primário (1ª - 6ª Classe)' }}
                        </h2>
                    </div>

                    <div class="p-5">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-100 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                        <th class="pb-2">Item Requerido</th>
                                        <th class="pb-2 text-right">Qtd</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    @foreach($list->items as $item)
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="py-2 text-slate-700 font-medium">{{ $item['name'] }}</td>
                                            <td class="py-2 text-right font-mono font-bold text-slate-800">{{ $item['quantity'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($list->notes)
                            <div class="mt-4 p-3 rounded-xl bg-slate-50 border border-slate-100 text-[11px] text-slate-600">
                                <strong class="text-slate-700 block mb-0.5 font-semibold">Observações:</strong>
                                {{ $list->notes }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="p-4 bg-slate-50/60 border-t border-slate-100 flex justify-end">
                    <button class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 shadow-2xs transition-colors">
                        <i class="fas fa-download text-emerald-600 text-xs"></i>
                        Baixar PDF
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-400">
                <i class="fas fa-folder-open text-3xl mb-2 text-slate-300"></i>
                <p class="text-xs font-medium text-slate-500">Nenhuma lista de materiais cadastrada no momento.</p>
            </div>
        @endforelse
    </div>

    <!-- Footer Note -->
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs flex items-center justify-center gap-2 text-center">
        <i class="fas fa-info-circle text-emerald-600"></i>
        <span>Os manuais escolares e uniformes podem ser adquiridos diretamente na secretaria da escola.</span>
    </div>

</div>
@endsection
