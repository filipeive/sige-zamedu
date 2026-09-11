@hasanyrole('super_admin|province_admin|province_inspector|district_admin|district_inspector|zip_coordinator|zip_admin')
<div class="bg-emerald-50/80 border border-emerald-200/80 rounded-xl p-3 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 text-xs">
    <div class="flex items-center gap-2">
        <span class="px-2 py-0.5 rounded-full bg-emerald-700 text-white font-extrabold text-[10px] uppercase tracking-wider">Modo Super Admin</span>
        <span class="font-bold text-slate-700">Visualização Multi-Escola Ativa. Seleccione uma escola no filtro para restringir a consulta.</span>
    </div>
    @if(request()->filled('school_id') && isset($schools))
        @php $selectedSchool = $schools->firstWhere('id', request('school_id')); @endphp
        @if($selectedSchool)
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white border border-emerald-300 font-extrabold text-emerald-800 text-[11px] shadow-2xs">
                <i class="fas fa-school text-emerald-600"></i> {{ $selectedSchool->name }}
            </span>
        @endif
    @endif
</div>
@endhasanyrole