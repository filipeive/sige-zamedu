@props([
    'school' => null,
    'directorName' => null,
    'date' => null,
    'year' => null,
])

@php
    $displayDirector = $directorName 
        ?? $school?->principal_name 
        ?? setting('director_name', 'PROF. MARIA NHABINDE');
    $districtName = $school?->district?->name ?? 'DISTRITO';
    $currentDate = $date ?? (date('d') . ' de ' . \Carbon\Carbon::now()->translatedFormat('F') . ' de ' . ($year ?? date('Y')));
@endphp

<div class="mt-8 pt-6 border-t border-slate-200 text-xs">
    <div class="text-right mb-6 font-semibold text-slate-700">
        {{ $districtName }}, {{ $currentDate }}
    </div>

    <div class="grid grid-cols-2 gap-8 text-center items-end">
        <!-- Lado Esquerdo: DAP -->
        <div class="flex flex-col items-center">
            <p class="font-extrabold uppercase text-slate-900 text-xs mb-8">O Diretor Adjunto Pedagógico (DAP)</p>
            <div class="w-64 border-b-2 border-slate-900 mb-1"></div>
            <p class="text-2xs font-semibold text-slate-500">(Assinatura e Carimbo Oficial)</p>
        </div>

        <!-- Lado Direito: DAE -->
        <div class="flex flex-col items-center">
            <p class="font-extrabold uppercase text-slate-900 text-xs mb-1">O Diretor da Escola (DAE)</p>
            <p class="font-black text-slate-900 text-xs tracking-wide uppercase mb-6">{{ $displayDirector }}</p>
            <div class="w-64 border-b-2 border-slate-900 mb-1"></div>
            <p class="text-2xs font-semibold text-slate-500">(Assinatura e Carimbo Oficial)</p>
        </div>
    </div>
</div>
