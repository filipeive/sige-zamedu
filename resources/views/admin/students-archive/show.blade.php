@extends('layouts.app')

@section('title', 'Arquivo - ' . $student->full_name)
@section('page-title', 'Arquivo: ' . $student->full_name)

@section('content')
<div class="w-full px-4 py-6" x-data="{ reactivateModal: false }">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Sidebar Info -->
        <div class="lg:col-span-4">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 text-center">
                @if ($student->passport_photo)
                    <img src="{{ Storage::url($student->passport_photo) }}" alt="{{ $student->full_name }}"
                        class="w-28 h-28 rounded-full shadow-md border-4 border-white mx-auto object-cover mb-4">
                @else
                    <div class="w-28 h-28 rounded-full bg-slate-200 text-slate-700 font-black text-3xl flex items-center justify-center mx-auto shadow-md mb-4 border-4 border-white">
                        {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                    </div>
                @endif
                <h4 class="text-xl font-extrabold text-slate-900 tracking-tight mb-1">{{ $student->full_name }}</h4>
                <p class="text-xs font-mono text-slate-500 mb-3">{{ $student->student_number }}</p>

                @php
                    $statusColors = [
                        'transferred' => 'bg-sky-100 text-sky-800 border-sky-200',
                        'graduated' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                        'inactive' => 'bg-slate-100 text-slate-700 border-slate-200',
                    ];
                    $statusLabels = [
                        'transferred' => 'Transferido',
                        'graduated' => 'Formado',
                        'inactive' => 'Desistente',
                    ];
                @endphp
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full border text-xs font-extrabold {{ $statusColors[$student->status] ?? 'bg-slate-100 text-slate-700 border-slate-200' }}">
                    <i class="fa-solid fa-box-archive"></i> {{ $statusLabels[$student->status] ?? ucfirst($student->status) }}
                </span>

                <div class="border-t border-slate-200 my-5"></div>

                <div class="text-left text-xs space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 font-medium">Idade:</span>
                        <strong class="text-slate-800 font-bold">{{ $student->age ?? 'N/A' }} anos</strong>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 font-medium">Gênero:</span>
                        <strong class="text-slate-800 font-bold">{{ $student->gender === 'M' ? 'Masculino' : ($student->gender === 'F' ? 'Feminino' : 'N/A') }}</strong>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 font-medium">Contacto:</span>
                        <strong class="font-mono text-slate-800 font-bold">{{ $student->emergency_phone ?? 'N/A' }}</strong>
                    </div>
                    @if ($student->parent)
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-medium">Encarregado:</span>
                            <strong class="text-slate-800 font-bold">{{ $student->parent->first_name }} {{ $student->parent->last_name }}</strong>
                        </div>
                    @endif
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 font-medium">Arquivado em:</span>
                        <strong class="text-slate-800 font-bold">{{ $student->updated_at?->format('d/m/Y') ?? 'N/A' }}</strong>
                    </div>
                </div>

                <div class="space-y-2.5 mt-6">
                    <button type="button" @click="reactivateModal = true" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition inline-flex items-center justify-center gap-2">
                        <i class="fa-solid fa-rotate-right"></i> Reativar Aluno
                    </button>
                    <a href="{{ route('students.support.index', $student) }}" class="w-full py-2.5 px-4 rounded-xl border border-amber-300 bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold text-xs transition inline-flex items-center justify-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation"></i> Ver Histórico / Ocorrências
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Histórico de Matrículas -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 md:p-6 bg-slate-50/50 border-b border-slate-200">
                    <h5 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-school text-emerald-600"></i> Histórico de Matrículas
                    </h5>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left text-slate-600 divide-y divide-slate-200">
                        <thead class="bg-slate-50 uppercase font-semibold text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Ano Lectivo</th>
                                <th class="px-4 py-3">Turma</th>
                                <th class="px-4 py-3">Estado</th>
                                <th class="px-4 py-3">Data Matrícula</th>
                                <th class="px-4 py-3">Mensalidade</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 font-medium">
                            @forelse($student->enrollments as $enrollment)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-4 py-3 font-bold text-slate-900">{{ $enrollment->school_year }}</td>
                                    <td class="px-4 py-3 font-semibold text-slate-800">{{ $enrollment->class->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3">
                                        @php
                                            $enrollStatus = [
                                                'active' => 'bg-emerald-100 text-emerald-800',
                                                'pending' => 'bg-amber-100 text-amber-800',
                                                'completed' => 'bg-sky-100 text-sky-800',
                                                'cancelled' => 'bg-rose-100 text-rose-800',
                                                'transferred' => 'bg-sky-100 text-sky-800',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $enrollStatus[$enrollment->status] ?? 'bg-slate-100 text-slate-600' }}">
                                            {{ ucfirst($enrollment->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-slate-500">{{ $enrollment->enrollment_date?->format('d/m/Y') ?? '-' }}</td>
                                    <td class="px-4 py-3 font-bold text-slate-800">{{ number_format($enrollment->monthly_fee ?? 0, 2, ',', '.') }} MT</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-8 text-slate-400">
                                        <i class="fa-solid fa-folder-open text-3xl mb-2 block"></i>
                                        <p>Nenhuma matrícula registada.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Observações Registadas -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 md:p-6 bg-slate-50/50 border-b border-slate-200">
                    <h5 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-comments text-sky-600"></i> Observações Registadas
                    </h5>
                </div>
                <div class="p-6 space-y-3">
                    @forelse($student->observations()->with('creator')->latest()->limit(10)->get() as $observation)
                        <div class="rounded-2xl border border-slate-200 p-4 bg-slate-50/40">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs text-slate-400">
                                    {{ $observation->created_at->format('d/m/Y H:i') }} por {{ $observation->creator->name ?? 'Sistema' }}
                                </span>
                                @if ($observation->special_needs)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold">Nec. especial</span>
                                @endif
                            </div>
                            <p class="text-xs font-medium text-slate-700">{{ $observation->observations }}</p>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">Nenhuma observação registada.</p>
                    @endforelse
                </div>
            </div>

            <!-- Registos / Ocorrências -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 md:p-6 bg-slate-50/50 border-b border-slate-200">
                    <h5 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-folder-open text-emerald-600"></i> Registos / Ocorrências
                    </h5>
                </div>
                <div class="p-6 space-y-3">
                    @forelse($student->studentRecords()->with('creator')->latest('record_date')->limit(10)->get() as $record)
                        <div class="rounded-2xl border border-slate-200 p-4 bg-slate-50/40">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs text-slate-400">
                                    {{ \Carbon\Carbon::parse($record->record_date)->format('d/m/Y') }} por {{ $record->creator->name ?? 'Sistema' }}
                                </span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-sky-100 text-sky-800 text-[10px] font-bold">{{ $record->record_type_name }}</span>
                            </div>
                            <p class="text-xs font-medium text-slate-700">{{ $record->record_details }}</p>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">Nenhuma ocorrência/registo encontrado.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Reativação Alpine.js -->
    <div x-show="reactivateModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.away="reactivateModal = false" class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-md w-full overflow-hidden text-left">
            <form action="{{ route('admin.students-archive.reactivate', $student) }}" method="POST">
                @csrf
                <div class="p-5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <h5 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-rotate-right text-emerald-600"></i> Reativar: {{ $student->full_name }}
                    </h5>
                    <button type="button" @click="reactivateModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="bg-amber-50 border border-amber-200 text-amber-900 rounded-xl p-3.5 text-xs flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation text-amber-600 text-base"></i>
                        <span>O aluno será reativado no ano letivo <strong>{{ current_school_year() }}</strong>.</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Nova Turma *</label>
                        <select name="class_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 bg-white" required>
                            <option value="">Selecione...</option>
                            @foreach(\App\Models\ClassRoom::where('school_year', current_school_year())->where('is_active', true)->get() as $class)
                                <option value="{{ $class->id }}">
                                    {{ $class->name }} ({{ $class->grade_level_name }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Mensalidade (MT) *</label>
                            <input type="number" name="monthly_fee" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 bg-white"
                                value="{{ $student->monthly_fee ?? 0 }}" step="0.01" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Observações</label>
                            <input type="text" name="reason" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 bg-white"
                                placeholder="Motivo da reativação">
                        </div>
                    </div>
                </div>
                <div class="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-2">
                    <button type="button" @click="reactivateModal = false" class="px-4 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-sm transition">Cancelar</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-check"></i> Confirmar Reativação
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection