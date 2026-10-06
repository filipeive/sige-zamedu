@extends('layouts.app')

@section('title', 'Editar Aluno')
@section('page-title', 'Editar Aluno')
@section('page-title-icon', 'fas fa-user-edit')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}" class="no-underline">Alunos</a></li>
    <li class="breadcrumb-item"><a href="{{ route('students.show', $student) }}" class="no-underline">{{ $student->full_name }}</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Editar</li>
@endsection

@section('title', 'Editar ' . $student->full_name)
@section('content')
<div class="max-w-full mx-auto space-y-4" x-data="{
    specialNeeds: {{ old('has_special_needs', $student->has_special_needs) ? 'true' : 'false' }},
    parentModalOpen: false,
    newParentFirstName: '',
    newParentLastName: '',
    newParentEmail: '',
    newParentPhone: '',
    parentSubmitting: false,
    parentMessage: '',
    async submitQuickParent() {
        if (!this.newParentFirstName || !this.newParentLastName || !this.newParentPhone) {
            this.parentMessage = 'Por favor preencha Nome, Sobrenome e Telefone.';
            return;
        }
        this.parentSubmitting = true;
        this.parentMessage = '';
        try {
            const res = await fetch('{{ route('parents.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    first_name: this.newParentFirstName,
                    last_name: this.newParentLastName,
                    email: this.newParentEmail,
                    phone: this.newParentPhone
                })
            });
            const data = await res.json();
            if (data.success && data.parent) {
                const selectEl = document.getElementById('parent_id');
                const opt = document.createElement('option');
                opt.value = data.parent.id;
                opt.textContent = `${data.parent.full_name} (${data.parent.phone})`;
                opt.selected = true;
                selectEl.appendChild(opt);
                selectEl.value = data.parent.id;

                this.newParentFirstName = '';
                this.newParentLastName = '';
                this.newParentEmail = '';
                this.newParentPhone = '';
                this.parentModalOpen = false;
            } else {
                this.parentMessage = data.message || 'Erro ao cadastrar encarregado.';
            }
        } catch (e) {
            this.parentMessage = 'Erro de ligação ao servidor.';
        } finally {
            this.parentSubmitting = false;
        }
    }
}">

    <!-- Header Row -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-user-edit text-amber-600"></i> Editar Aluno: {{ $student->full_name }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Atualize os dados pessoais, estado de matrícula e encarregado de educação.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('students.show', $student) }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                <i class="fas fa-eye text-emerald-600"></i> Ver Perfil
            </a>
            <a href="{{ route('students.index') }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <!-- Form Container Card with Amber Accent -->
    <div class="bg-white rounded-xl border border-slate-200 border-t-4 border-t-amber-500 shadow-2xs p-5">
        <form method="POST" action="{{ route('students.update', $student) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Informações Pessoais -->
                <div class="space-y-4">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2 pb-2 border-b border-slate-100">
                        <i class="fas fa-user text-amber-500"></i> Informações Pessoais
                    </h3>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="first_name" class="block text-xs font-bold text-slate-700 mb-1">Nome *</label>
                            <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $student->first_name) }}" required
                                   class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500">
                        </div>
                        <div>
                            <label for="last_name" class="block text-xs font-bold text-slate-700 mb-1">Sobrenome *</label>
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $student->last_name) }}" required
                                   class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="gender" class="block text-xs font-bold text-slate-700 mb-1">Gênero *</label>
                            <select id="gender" name="gender" required
                                    class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500">
                                <option value="male" {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>Masculino</option>
                                <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>Feminino</option>
                            </select>
                        </div>
                        <div>
                            <label for="birthdate" class="block text-xs font-bold text-slate-700 mb-1">Data de Nascimento *</label>
                            <input type="date" id="birthdate" name="birthdate" value="{{ old('birthdate', $student->birthdate?->format('Y-m-d')) }}" required
                                   class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500">
                        </div>
                    </div>

                    <div>
                        <label for="birth_place" class="block text-xs font-bold text-slate-700 mb-1">Naturalidade / Local de Nascimento</label>
                        <input type="text" id="birth_place" name="birth_place" value="{{ old('birth_place', $student->birth_place ?? 'Maputo') }}"
                               class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('birth_place') border-rose-500 @enderror">
                        @error('birth_place')
                            <span class="text-[10px] text-rose-600 font-semibold">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="status" class="block text-xs font-bold text-slate-700 mb-1">Status do Aluno *</label>
                            <select id="status" name="status" required
                                    class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500">
                                <option value="active" {{ old('status', $student->status) == 'active' ? 'selected' : '' }}>Ativo</option>
                                <option value="inactive" {{ old('status', $student->status) == 'inactive' ? 'selected' : '' }}>Inativo</option>
                                <option value="transferred" {{ old('status', $student->status) == 'transferred' ? 'selected' : '' }}>Transferido</option>
                                <option value="graduated" {{ old('status', $student->status) == 'graduated' ? 'selected' : '' }}>Formado</option>
                            </select>
                        </div>
                        <div>
                            <label for="student_number" class="block text-xs font-bold text-slate-700 mb-1">Número de Estudante</label>
                            <input type="text" id="student_number" name="student_number" value="{{ old('student_number', $student->student_number) }}" readonly
                                   class="w-full rounded-lg border border-slate-200 bg-slate-100 text-slate-500 text-xs font-mono font-bold py-2 px-3">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="passport_photo" class="block text-xs font-bold text-slate-700 mb-1">Fotografia Passe</label>
                            <div class="flex items-center gap-2">
                                @if($student->passport_photo)
                                    <img src="{{ Storage::url($student->passport_photo) }}" alt="{{ $student->full_name }}" class="h-8 w-8 rounded-lg object-cover ring-1 ring-slate-200 shrink-0">
                                @endif
                                <input type="file" id="passport_photo" name="passport_photo" accept="image/*"
                                       class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-1 px-2 text-xs font-semibold focus:border-amber-500">
                            </div>
                        </div>
                        <div>
                            <label for="medical_certificate" class="block text-xs font-bold text-slate-700 mb-1">Atestado Médico (Substituir)</label>
                            <input type="file" id="medical_certificate" name="medical_certificate" accept=".pdf,.png,.jpg,.jpeg"
                                   class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-1 px-2 text-xs font-semibold focus:border-amber-500">
                        </div>
                    </div>
                </div>

                <!-- Encarregado de Educação & Contacto de Emergência -->
                <div class="space-y-4">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2 pb-2 border-b border-slate-100">
                        <i class="fas fa-users-cog text-amber-500"></i> Encarregado & Contactos
                    </h3>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="parent_id" class="block text-xs font-bold text-slate-700">Encarregado de Educação</label>
                            <button type="button" @click.prevent="parentModalOpen = true" class="no-underline text-[11px] font-bold text-amber-700 hover:text-amber-800 flex items-center gap-1 bg-amber-50 hover:bg-amber-100 px-2 py-1 rounded-lg border border-amber-200 shadow-2xs transition cursor-pointer">
                                <i class="fas fa-user-plus"></i> + Novo Encarregado
                            </button>
                        </div>
                        <select id="parent_id" name="parent_id"
                                class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('parent_id') border-rose-500 @enderror">
                            <option value="">Nenhum Encarregado Atribuído</option>
                            @foreach($parents as $parent)
                                <option value="{{ $parent->user_id }}" {{ (string) old('parent_id', $student->parent_id) === (string) $parent->user_id ? 'selected' : '' }}>
                                    {{ $parent->full_name }} ({{ $parent->phone ?? 'Sem Telefone' }})
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <span class="text-[10px] text-rose-600 font-semibold">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="address" class="block text-xs font-bold text-slate-700 mb-1">Endereço / Bairro</label>
                        <input type="text" id="address" name="address" value="{{ old('address', $student->address) }}"
                               class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('address') border-rose-500 @enderror">
                        @error('address')
                            <span class="text-[10px] text-rose-600 font-semibold">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="emergency_contact" class="block text-xs font-bold text-slate-700 mb-1">Contacto de Emergência (Nome)</label>
                            <input type="text" id="emergency_contact" name="emergency_contact" value="{{ old('emergency_contact', $student->emergency_contact) }}"
                                   class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('emergency_contact') border-rose-500 @enderror">
                            @error('emergency_contact')
                                <span class="text-[10px] text-rose-600 font-semibold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="emergency_phone" class="block text-xs font-bold text-slate-700 mb-1">Telefone de Emergência</label>
                            <input type="text" id="emergency_phone" name="emergency_phone" value="{{ old('emergency_phone', $student->emergency_phone) }}"
                                   class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('emergency_phone') border-rose-500 @enderror">
                            @error('emergency_phone')
                                <span class="text-[10px] text-rose-600 font-semibold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    @if(is_private_school())
                        <div>
                            <label for="monthly_fee" class="block text-xs font-bold text-slate-700 mb-1">Mensalidade (MT)</label>
                            <input type="number" step="0.01" id="monthly_fee" name="monthly_fee" value="{{ old('monthly_fee', $student->monthly_fee) }}"
                                   class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500">
                        </div>
                    @endif
                </div>
            </div>

            <!-- Necessidades Especiais & Observações -->
            <div class="pt-4 border-t border-slate-100 space-y-3">
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="has_special_needs" name="has_special_needs" value="1" x-model="specialNeeds"
                           class="rounded border-slate-300 text-amber-500 focus:ring-amber-500">
                    <label for="has_special_needs" class="text-xs font-bold text-slate-700 cursor-pointer">Aluno possui necessidades educativas especiais (NEE)</label>
                </div>

                <div x-show="specialNeeds" x-transition>
                    <label for="special_needs_description" class="block text-xs font-bold text-slate-700 mb-1">Descrição das Necessidades Especiais</label>
                    <textarea id="special_needs_description" name="special_needs_description" rows="2"
                              class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500"
                              placeholder="Descreva as adaptações pedagógicas ou necessidades do aluno.">{{ old('special_needs_description', $student->special_needs_description) }}</textarea>
                </div>

                <div>
                    <label for="observations" class="block text-xs font-bold text-slate-700 mb-1">Observações Gerais</label>
                    <textarea id="observations" name="observations" rows="2"
                              class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500"
                              placeholder="Outras informações relevantes sobre o aluno.">{{ old('observations', $student->observations) }}</textarea>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                <a href="{{ route('students.show', $student) }}" class="no-underline inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                    Cancelar
                </a>
                <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-xs font-bold text-white shadow-sm transition">
                    <i class="fas fa-save"></i> Guardar Alterações
                </button>
            </div>
        </form>
    </div>

    <!-- Quick Register Guardian Modal (Absolute Top Teleport) -->
    <template x-teleport="body">
        <div x-show="parentModalOpen" x-transition.opacity
             style="position: fixed; inset: 0; z-index: 999999 !important; display: none;"
             class="flex items-center justify-center bg-slate-900/70 backdrop-blur-xs p-4">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl space-y-4 text-left border border-slate-100" @click.outside="parentModalOpen = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-user-plus text-amber-600"></i> Cadastrar Encarregado Rápido
                    </h3>
                    <button type="button" @click="parentModalOpen = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold leading-none">&times;</button>
                </div>

                <template x-if="parentMessage">
                    <div class="p-2.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold" x-text="parentMessage"></div>
                </template>

                <div class="space-y-3 text-xs">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Primeiro Nome *</label>
                            <input type="text" x-model="newParentFirstName" class="w-full rounded-lg border-slate-200 py-1.5 px-2.5 text-xs font-semibold focus:ring-amber-500 focus:border-amber-500" placeholder="Ex: João">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Sobrenome *</label>
                            <input type="text" x-model="newParentLastName" class="w-full rounded-lg border-slate-200 py-1.5 px-2.5 text-xs font-semibold focus:ring-amber-500 focus:border-amber-500" placeholder="Ex: Sitoe">
                        </div>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Telefone / WhatsApp *</label>
                        <input type="text" x-model="newParentPhone" class="w-full rounded-lg border-slate-200 py-1.5 px-2.5 text-xs font-semibold focus:ring-amber-500 focus:border-amber-500" placeholder="Ex: 841234567">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">E-mail (Opcional - Gerado Automático)</label>
                        <input type="email" x-model="newParentEmail" class="w-full rounded-lg border-slate-200 py-1.5 px-2.5 text-xs font-semibold focus:ring-amber-500 focus:border-amber-500" placeholder="Deixe em branco para automático">
                    </div>
                    <div class="p-2 rounded-lg bg-amber-50/80 border border-amber-100 text-[11px] text-amber-800 font-medium flex items-center gap-1.5">
                        <i class="fas fa-shield-alt text-amber-600"></i> A palavra-passe de acesso ao portal é gerada automaticamente pelo sistema.
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="parentModalOpen = false" class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition">Cancelar</button>
                    <button type="button" @click="submitQuickParent()" :disabled="parentSubmitting" class="px-4 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-xs font-bold text-white shadow-2xs transition flex items-center gap-1.5">
                        <i class="fas fa-save"></i>
                        <span x-text="parentSubmitting ? 'A guardar...' : 'Guardar Encarregado'"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>

</div>
@endsection
