@extends('layouts.portal')

@section('title', 'Pré-Inscrição Submetida com Sucesso — MINEDH | ZAMEDU')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <!-- Top Breadcrumb -->
    <div class="flex items-center justify-between flex-wrap gap-3 mb-6">
        <div class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500">
            <a href="{{ route('welcome') }}" class="hover:text-emerald-700 transition">Início</a>
            <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
            <a href="{{ route('public.pre-enrollment') }}" class="hover:text-emerald-700 transition">Pré-Inscrição</a>
            <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
            <span class="text-emerald-800 font-bold">Confirmação de Candidatura</span>
        </div>
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-900 text-xs font-bold border border-emerald-200 shadow-xs">
            <i class="fa-solid fa-circle-check text-emerald-700"></i> Candidatura Registada
        </div>
    </div>

    <!-- Main Success Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-md overflow-hidden text-center p-6 sm:p-10 md:p-12">
        
        <!-- Animated Icon & Status -->
        <div class="w-20 h-20 bg-emerald-100 text-emerald-700 rounded-3xl flex items-center justify-center text-4xl mx-auto mb-6 shadow-xs border border-emerald-200">
            <i class="fa-solid fa-circle-check"></i>
        </div>

        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-100 text-amber-950 text-xs font-black uppercase tracking-wider mb-3">
            <i class="fa-solid fa-school text-amber-700"></i> Ano Lectivo {{ $application->academic_year ?? '2027' }}
        </div>

        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mb-2">
            Pré-Inscrição Enviada com Sucesso!
        </h1>

        <p class="text-slate-600 text-sm sm:text-base max-w-xl mx-auto mb-8">
            O pedido de pré-inscrição para o aluno <strong class="text-slate-900">{{ $application->student_data['first_name'] ?? '' }} {{ $application->student_data['last_name'] ?? '' }}</strong> foi submetido no Sistema Nacional de Educação e está sob análise da secretaria da instituição escolar.
        </p>

        <!-- Summary Highlights Box -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8 text-left">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Código de Referência</p>
                <p class="text-base font-black text-emerald-800 mt-0.5">PRE-{{ str_pad($application->id, 5, '0', STR_PAD_LEFT) }}</p>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Escola Selecionada</p>
                <p class="text-sm font-bold text-slate-900 mt-0.5 truncate">{{ $application->school->name ?? 'Escola Territorial' }}</p>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Taxa Estimada</p>
                <p class="text-base font-black text-slate-900 mt-0.5">{{ number_format($application->total_amount, 2, ',', '.') }} MT</p>
            </div>
        </div>

        <!-- Next Steps Box -->
        <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 mb-6 text-left">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i class="fa-solid fa-list-check text-emerald-700"></i>
                Próximas Etapas do Processo de Matrícula:
            </h3>
            <ol class="space-y-3 text-xs sm:text-sm text-slate-700">
                <li class="flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full bg-emerald-700 text-white font-black text-xs flex items-center justify-center shrink-0 mt-0.5">1</span>
                    <div>
                        <strong class="text-slate-900">Validação Documental:</strong> A comissão de matrículas da escola irá verificar a conformidade dos dados submetidos online.
                    </div>
                </li>
                <li class="flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full bg-emerald-700 text-white font-black text-xs flex items-center justify-center shrink-0 mt-0.5">2</span>
                    <div>
                        <strong class="text-slate-900">Notificação ao Encarregado:</strong> Receberá uma mensagem por SMS/WhatsApp com a data para apresentação presencial dos documentos originais.
                    </div>
                </li>
                <li class="flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full bg-emerald-700 text-white font-black text-xs flex items-center justify-center shrink-0 mt-0.5">3</span>
                    <div>
                        <strong class="text-slate-900">Pagamento de Taxas (se aplicável):</strong> O pagamento de <strong>{{ number_format($application->total_amount, 2, ',', '.') }} MT</strong> poderá ser efetuado por depósito bancário ou diretamente na secretaria da escola.
                    </div>
                </li>
            </ol>
        </div>

        <!-- Deposit Details Alert -->
        <div class="p-5 rounded-2xl bg-emerald-50/70 border border-emerald-200 mb-8 text-left">
            <h4 class="text-xs font-black text-emerald-950 uppercase tracking-wider mb-3 flex items-center gap-2">
                <i class="fa-solid fa-building-columns text-emerald-700"></i>
                Coordenadas Bancárias Oficiais para Efetivação:
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                <div class="p-2.5 rounded-xl bg-white border border-emerald-100">
                    <p class="text-[10px] text-slate-500 font-bold uppercase">Banco</p>
                    <p class="font-bold text-slate-900 mt-0.5">Millennium BIM</p>
                </div>
                <div class="p-2.5 rounded-xl bg-white border border-emerald-100">
                    <p class="text-[10px] text-slate-500 font-bold uppercase">Número de Conta</p>
                    <p class="font-bold text-slate-900 mt-0.5 font-mono">123456789</p>
                </div>
                <div class="p-2.5 rounded-xl bg-white border border-emerald-100">
                    <p class="text-[10px] text-slate-500 font-bold uppercase">NIB</p>
                    <p class="font-bold text-slate-900 mt-0.5 font-mono">0001 0000 1234 5678 9012 3</p>
                </div>
                <div class="p-2.5 rounded-xl bg-white border border-emerald-100">
                    <p class="text-[10px] text-slate-500 font-bold uppercase">Referência de Depósito</p>
                    <p class="font-black text-emerald-800 mt-0.5 font-mono">PRE-{{ str_pad($application->id, 5, '0', STR_PAD_LEFT) }}</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap items-center justify-center gap-3">
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs sm:text-sm transition">
                <i class="fa-solid fa-print"></i>
                <span>Imprimir Comprovativo</span>
            </button>

            <a href="{{ route('welcome') }}" class="inline-flex items-center gap-2 px-7 py-3 rounded-xl bg-emerald-800 hover:bg-emerald-900 text-white font-bold text-xs sm:text-sm transition shadow-md">
                <i class="fa-solid fa-house"></i>
                <span>Voltar à Página Inicial</span>
            </a>
        </div>
    </div>
</div>
@endsection