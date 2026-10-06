@extends('layouts.portal')

@section('title', 'Consulta de Afetação Oficial SNE 2027 — MINEDH | ZAMEDU')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <!-- Top Breadcrumb & Badge -->
    <div class="flex items-center justify-between flex-wrap gap-3 mb-6">
        <div class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500">
            <a href="{{ route('welcome') }}" class="hover:text-emerald-700 transition">Início</a>
            <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
            <span class="text-emerald-800 font-bold">Afetações Oficiais SNE 2027</span>
        </div>
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-900 text-xs font-bold border border-emerald-200">
            <i class="fa-solid fa-calendar-check text-emerald-700"></i> Ano Lectivo {{ $academicYear }}
        </div>
    </div>

    <!-- Banner Institucional MINEDH -->
    <div class="rounded-3xl shadow-sm overflow-hidden mb-8 border border-emerald-800/20 text-white" style="background: linear-gradient(135deg, #005A2B 0%, #007A3D 55%, #046A38 100%); border-bottom: 4px solid #FCD116;">
        <div class="p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start sm:items-center gap-5">
                <div class="p-2.5 bg-white/10 rounded-2xl backdrop-blur-sm border border-white/20 shrink-0">
                    <img src="{{ asset('images/emblem.png') }}" alt="Emblema Nacional" class="h-16 w-auto drop-shadow-md">
                </div>
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-amber-400 text-slate-900 text-[11px] font-extrabold uppercase tracking-wider mb-2">
                        REPÚBLICA DE MOÇAMBIQUE &bull; MINEDH &bull; SNE
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight leading-tight">
                        Consulta de Afetação Primário &rarr; Secundário
                    </h1>
                    <p class="text-emerald-100 text-xs sm:text-sm mt-1 max-w-2xl leading-relaxed">
                        Verifique a escola secundária pública ou comunitária atribuída aos alunos concluintes da 6ª classe no Ensino Primário (Transição Oficial para a 7ª Classe — ESG1).
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                <a href="{{ route('public.vacancies') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition border border-white/30 backdrop-blur-xs">
                    <i class="fa-solid fa-chair text-amber-300"></i> Ver Vagas 2027
                </a>
                <a href="{{ route('public.pre-enrollment') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-slate-900 font-extrabold text-xs transition shadow-sm">
                    <i class="fa-solid fa-user-plus"></i> Pré-Inscrição Online
                </a>
            </div>
        </div>
    </div>

    <!-- Painel de Pesquisa -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-8">
        <form method="GET" action="{{ route('public.allocations') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4 items-end">
                <div class="lg:col-span-5">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        <i class="fa-solid fa-id-card text-emerald-700 mr-1"></i> Código do Aluno ou Nome Completo:
                    </label>
                    <input type="text" name="search" value="{{ $search }}" class="w-full text-xs sm:text-sm rounded-xl border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 p-2.5 border" placeholder="Ex: SNE-2027-0001 ou Amélia Sitoe...">
                </div>

                <div class="lg:col-span-3">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        <i class="fa-solid fa-map-location-dot text-emerald-700 mr-1"></i> Província:
                    </label>
                    <select name="province_id" id="alloc_province" class="w-full text-xs sm:text-sm rounded-xl border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 p-2.5 border bg-white">
                        <option value="">Todas as Províncias</option>
                        @foreach($provinces as $p)
                            <option value="{{ $p->id }}" {{ $provinceId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-3">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        <i class="fa-solid fa-landmark text-emerald-700 mr-1"></i> Distrito:
                    </label>
                    <select name="district_id" id="alloc_district" class="w-full text-xs sm:text-sm rounded-xl border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 p-2.5 border bg-white">
                        <option value="">Todos os Distritos</option>
                        @foreach($districts as $d)
                            <option value="{{ $d->id }}" {{ $districtId == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-1 flex items-center gap-2">
                    <button type="submit" class="w-full h-[42px] inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-800 hover:bg-emerald-900 text-white font-bold text-xs transition shadow-sm" title="Pesquisar">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                    @if(!empty($search) || !empty($provinceId) || !empty($districtId))
                        <a href="{{ route('public.allocations') }}" class="h-[42px] px-3 inline-flex items-center justify-center rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs border border-slate-300 transition" title="Limpar Filtros">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <div class="mt-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-2 text-xs text-emerald-900">
                <i class="fa-solid fa-shield-halved text-emerald-700 text-sm"></i>
                <span>As vagas de 7ª classe apresentadas abaixo foram previamente reservadas e homologadas pelo SDEJT/ZIP para garantir o acesso equitativo de todos os concluintes do primário.</span>
            </div>
            <span class="text-[11px] font-bold text-emerald-800 bg-white px-2.5 py-1 rounded-md border border-emerald-200">
                <i class="fa-solid fa-lock text-emerald-600 mr-1"></i> Garantia Oficial SNE
            </span>
        </div>
    </div>

    <!-- Resultados -->
    @if($allocations->count() > 0)
        <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
            <div class="text-xs font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-list-check text-emerald-700"></i>
                Alunos Encontrados: <span class="bg-slate-200 text-slate-800 px-2 py-0.5 rounded-md font-mono">{{ $allocations->total() }}</span>
            </div>
            <div class="text-xs text-slate-500">
                Página {{ $allocations->currentPage() }} de {{ $allocations->lastPage() }}
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($allocations as $alloc)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition overflow-hidden flex flex-col justify-between">
                    <!-- Cabeçalho do Card -->
                    <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5 font-mono text-xs font-bold text-slate-800 bg-white px-2.5 py-1 rounded-md border border-slate-300">
                            <i class="fa-solid fa-id-badge text-emerald-700"></i> {{ $alloc->student_code ?? 'SNE-2027' }}
                        </span>

                        @if($alloc->status === 'confirmed')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-sky-100 text-sky-800 text-[11px] font-bold border border-sky-200">
                                <i class="fa-solid fa-check-double"></i> Inscrição Confirmada
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold border border-emerald-200">
                                <i class="fa-solid fa-lock"></i> Vaga Garantida
                            </span>
                        @endif
                    </div>

                    <!-- Corpo do Card -->
                    <div class="p-5 flex-grow">
                        <!-- Identificação do Aluno -->
                        <div class="flex items-center gap-3.5 mb-4">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center text-white text-base font-bold shrink-0 shadow-xs {{ $alloc->gender === 'female' ? 'bg-purple-600' : 'bg-sky-600' }}">
                                <i class="fa-solid {{ $alloc->gender === 'female' ? 'fa-venus' : 'fa-mars' }}"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-slate-900 leading-snug">
                                    {{ $alloc->student_name }}
                                </h3>
                                <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1.5">
                                    <span>{{ $alloc->gender === 'female' ? 'Feminino (Mulher)' : 'Masculino (Homem)' }}</span>
                                    @if($alloc->birth_date)
                                        <span>&bull; Nasc: {{ $alloc->birth_date->format('d/m/Y') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Origem e Destino -->
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                            <!-- Escola Primária de Origem -->
                            <div class="mb-3 flex items-start gap-2 text-slate-600">
                                <i class="fa-solid fa-school text-slate-400 mt-0.5"></i>
                                <div>
                                    <span class="text-[10px] uppercase font-bold text-slate-500 block tracking-wider">Conclusão 6ª Classe (Origem):</span>
                                    <strong class="text-slate-800">{{ $alloc->origin_school_name ?? 'Escola Primária da Região' }}</strong>
                                </div>
                            </div>

                            <!-- Escola Secundária de Destino -->
                            <div class="pt-2.5 border-t border-dashed border-slate-200 flex items-start gap-2">
                                <i class="fa-solid fa-building-columns text-emerald-700 mt-0.5"></i>
                                <div>
                                    <span class="text-[10px] uppercase font-bold text-emerald-800 block tracking-wider">Escola Secundária Afetada:</span>
                                    <strong class="text-xs text-slate-900 block mt-0.5">
                                        {{ $alloc->destinationSchool->name ?? 'Escola Secundária Geral' }}
                                    </strong>
                                    <div class="text-[11px] text-slate-500 mt-1">
                                        <i class="fa-solid fa-map-pin text-emerald-600 mr-1"></i>
                                        {{ $alloc->destinationSchool->district->name ?? ($alloc->district->name ?? 'Distrito') }}, {{ $alloc->destinationSchool->province->name ?? ($alloc->province->name ?? 'Província') }}
                                        &bull; <span class="font-bold text-slate-800">7ª Classe</span> &bull; Turno {{ $alloc->shift_label }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rodapé da Ação -->
                    <div class="p-4 bg-white border-t border-slate-100">
                        @if($alloc->status === 'allocated')
                            <a href="{{ route('public.pre-enrollment', ['allocation_id' => $alloc->id]) }}" class="w-full py-2.5 px-4 rounded-xl bg-emerald-800 hover:bg-emerald-900 text-white font-bold text-xs transition inline-flex items-center justify-center gap-2 shadow-xs">
                                <i class="fa-solid fa-file-signature"></i> Confirmar &amp; Pré-Inscrever
                            </a>
                        @else
                            <div class="w-full py-2.5 px-4 rounded-xl bg-slate-50 text-emerald-800 border border-emerald-200 text-xs font-bold inline-flex items-center justify-center gap-2">
                                <i class="fa-solid fa-check-circle text-emerald-600"></i> Pré-Inscrição Já Registada
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex justify-center mt-6">
            {{ $allocations->links() }}
        </div>
    @else
        <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center shadow-xs">
            <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-2xl mb-4">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 mb-2">
                Nenhum registo de afetação localizado
            </h3>
            <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto mb-6 leading-relaxed">
                Verifique se o código do aluno (ex: <code class="bg-slate-100 px-1.5 py-0.5 rounded text-emerald-800 font-bold">SNE-2027-0001</code>) ou nome foi digitado corretamente. Caso se trate de uma nova matrícula sem afetação automática, consulte as vagas escolares disponíveis.
            </p>
            <div class="flex items-center justify-center gap-3 flex-wrap">
                <a href="{{ route('public.allocations') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs border border-slate-300 transition">
                    <i class="fa-solid fa-rotate-left"></i> Limpar Filtros
                </a>
                <a href="{{ route('public.vacancies') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-800 hover:bg-emerald-900 text-white font-bold text-xs transition shadow-xs">
                    <i class="fa-solid fa-chair text-amber-300"></i> Ver Vagas Escolares 2027
                </a>
            </div>
        </div>
    @endif

</div>
@endsection