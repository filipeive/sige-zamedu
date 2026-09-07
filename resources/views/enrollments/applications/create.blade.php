@extends('layouts.app')

@section('title', 'Pré-Inscrição Online')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="school-card shadow-lg border-0">
                    <div class="school-card-header text-white p-4" style="background: linear-gradient(135deg, #007A3D 0%, #046A38 100%); border-bottom: 4px solid #FCD116;">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ asset('images/emblem.png') }}" alt="Emblema da República de Moçambique" style="height: 60px; width: auto; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));">
                            <div>
                                <span class="badge bg-warning text-dark font-bold text-uppercase px-2 py-1 mb-1">REPÚBLICA DE MOÇAMBIQUE · MINEDH</span>
                                <h2 class="mb-0 font-extrabold"><i class="fas fa-user-plus me-2"></i> Pré-Inscrição Online — Ano Letivo {{ $academicYear }}</h2>
                                <p class="mb-0 mt-1 opacity-90 small">Selecione a região/escola pretendida e preencha os dados do candidato.</p>
                            </div>
                        </div>
                    </div>
                    <div class="school-card-body p-5">
                        <form action="{{ route('public.pre-enrollment.store') }}" method="POST"
                            enctype="multipart/form-data" id="enrollmentForm">
                            @csrf

                            <!-- Step 1: Student Data & Territorial Selection -->
                            <div class="form-step" id="step1">
                                <h4 class="border-bottom pb-2 mb-4 text-emerald-800 font-extrabold"><i class="fas fa-map-marker-alt me-2 text-warning"></i>1. Seleção da Escola por Região / Zona & Dados do Aluno</h4>
                                
                                <!-- Territorial School Selector -->
                                <div class="p-4 mb-4 rounded-3 border bg-light" style="border-left: 5px solid #007A3D !important;">
                                    <h6 class="text-emerald-900 font-bold mb-3"><i class="fas fa-landmark me-2 text-emerald-700"></i>Escolha a Escola de acordo com a sua Província / Distrito:</h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label font-semibold">Província <span class="text-danger">*</span></label>
                                            <select id="province_select" class="form-select" onchange="filterTerritoryOptions()">
                                                <option value="">Todas as Províncias</option>
                                                @if(isset($provinces))
                                                    @foreach($provinces as $p)
                                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label font-semibold">Distrito <span class="text-danger">*</span></label>
                                            <select id="district_select" class="form-select" onchange="filterTerritoryOptions()">
                                                <option value="">Todos os Distritos</option>
                                                @if(isset($provinces))
                                                    @foreach($provinces as $p)
                                                        @foreach($p->districts as $d)
                                                            <option value="{{ $d->id }}" data-province="{{ $p->id }}">{{ $d->name }} ({{ $p->name }})</option>
                                                        @endforeach
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label font-semibold">Escola Pretendida <span class="text-danger">*</span></label>
                                            <select name="school_id" id="school_select" class="form-select" required>
                                                <option value="">Selecione a Escola...</option>
                                                @if(isset($schools))
                                                    @foreach($schools as $s)
                                                        <option value="{{ $s->id }}" data-province="{{ $s->province_id }}" data-district="{{ $s->district_id }}">
                                                            {{ $s->name }} ({{ strtoupper($s->type === 'public' ? 'Pública' : ($s->type === 'private' ? 'Privada' : 'Comunitária')) }})
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nome Próprio <span class="text-danger">*</span></label>
                                        <input type="text" name="student[first_name]" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Apelido <span class="text-danger">*</span></label>
                                        <input type="text" name="student[last_name]" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Data de Nascimento <span
                                                class="text-danger">*</span></label>
                                        <input type="date" name="student[birth_date]" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Sexo <span class="text-danger">*</span></label>
                                        <select name="student[gender]" class="form-select" required>
                                            <option value="">Selecione...</option>
                                            <option value="male">Masculino</option>
                                            <option value="female">Feminino</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Classe Pretendida <span
                                                class="text-danger">*</span></label>
                                        <select name="student[grade_level]" class="form-select" required>
                                            <option value="">Selecione...</option>
                                            <option value="pre-school">Pré-Escolar</option>
                                            <option value="primary">1ª à 6ª Classe</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Endereço Residencial <span
                                                class="text-danger">*</span></label>
                                        <textarea name="student[address]" class="form-control" rows="2" required
                                            placeholder="Rua, Bairro, Cidade..."></textarea>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Necessidades Especiais?</label>
                                        <textarea name="student[special_needs_description]" class="form-control" rows="2"
                                            placeholder="Se sim, descreva aqui..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 2: Parent Data -->
                            <div class="form-step d-none" id="step2">
                                <h4 class="border-bottom pb-2 mb-4 text-primary-school">Passo 2: Dados do Encarregado</h4>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nome do Encarregado <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="parent[first_name]" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Apelido <span class="text-danger">*</span></label>
                                        <input type="text" name="parent[last_name]" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Telefone / WhatsApp <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="parent[phone]" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">E-mail</label>
                                        <input type="email" name="parent[email]" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Grau de Parentesco <span
                                                class="text-danger">*</span></label>
                                        <select name="parent[relationship]" class="form-select" required>
                                            <option value="pai">Pai</option>
                                            <option value="mae">Mãe</option>
                                            <option value="outro">Outro</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Contacto de Emergência <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="student[emergency_contact]" class="form-control" required
                                            placeholder="Nome do contacto">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Telefone de Emergência <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="student[emergency_phone]" class="form-control" required
                                            placeholder="Telefone do contacto">
                                    </div>
                                </div>
                            </div>

                            <!-- Step 3: Documents -->
                            <div class="form-step d-none" id="step3">
                                <h4 class="border-bottom pb-2 mb-4 text-primary-school">Passo 3: Documentos Digitais</h4>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i> Faça o upload dos documentos obrigatórios (PDF
                                    ou Imagem).
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">BI / Certidão de Nascimento</label>
                                        <input type="file" name="documents[id_card]" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Foto Tipo Passe</label>
                                        <input type="file" name="documents[photo]" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Boletim Anterior (se aplicável)</label>
                                        <input type="file" name="documents[report_card]" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Atestado Médico</label>
                                        <input type="file" name="documents[medical]" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <!-- Step 4: Terms and Fees -->
                            <div class="form-step d-none" id="step4">
                                <h4 class="border-bottom pb-2 mb-4 text-primary-school">Passo 4: Termos e Taxas</h4>

                                <div class="card bg-light mb-4">
                                    <div class="card-body">
                                        <h5 class="card-title">Resumo de Taxas (Estimativa)</h5>
                                        <ul class="list-group list-group-flush" id="feeSummary">
                                            <!-- Will be populated by JS -->
                                        </ul>
                                        <div class="mt-3 text-end">
                                            <h4 class="fw-bold">Total: <span id="totalAmount">0.00</span> MT</h4>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="terms[responsibility]"
                                            required id="term1">
                                        <label class="form-check-label" for="term1">
                                            Li e aceito o <strong>Termo de Responsabilidade</strong>.
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="terms[image_consent]"
                                            id="term2">
                                        <label class="form-check-label" for="term2">
                                            Autorizo o uso de imagem para fins pedagógicos.
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="terms[rules]" required
                                            id="term3">
                                        <label class="form-check-label" for="term3">
                                            Comprometo-me a respeitar as regras da escola.
                                        </label>
                                    </div>
                                </div>

                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Atenção:</strong> O pagamento deve ser feito via depósito bancário.
                                    Após a submissão, você receberá as instruções de pagamento.
                                </div>
                            </div>

                            <div class="mt-5 d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary px-4 d-none" id="prevBtn">Anterior</button>
                                <button type="button" class="btn btn-primary-school px-4" id="nextBtn">Próximo</button>
                                <button type="submit" class="btn btn-success px-4 d-none" id="submitBtn">Enviar
                                    Pré-Inscrição</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function filterTerritoryOptions() {
                const provId = document.getElementById('province_select').value;
                const distId = document.getElementById('district_select').value;

                const distOptions = document.querySelectorAll('#district_select option[data-province]');
                distOptions.forEach(opt => {
                    if (!provId || opt.getAttribute('data-province') === provId) {
                        opt.style.display = '';
                    } else {
                        opt.style.display = 'none';
                    }
                });

                const schoolOptions = document.querySelectorAll('#school_select option[data-province]');
                schoolOptions.forEach(opt => {
                    const schoolProv = opt.getAttribute('data-province');
                    const schoolDist = opt.getAttribute('data-district');

                    let show = true;
                    if (provId && schoolProv !== provId) show = false;
                    if (distId && schoolDist !== distId) show = false;

                    opt.style.display = show ? '' : 'none';
                });
            }

            const steps = ['step1', 'step2', 'step3', 'step4'];
            let currentStep = 0;

            const fees = @json($fees);

            function updateStep() {
                steps.forEach((step, index) => {
                    document.getElementById(step).classList.toggle('d-none', index !== currentStep);
                });

                document.getElementById('prevBtn').classList.toggle('d-none', currentStep === 0);
                document.getElementById('nextBtn').classList.toggle('d-none', currentStep === steps.length - 1);
                document.getElementById('submitBtn').classList.toggle('d-none', currentStep !== steps.length - 1);

                if (currentStep === 3) {
                    calculateFees();
                }
            }

            function calculateFees() {
                const gradeLevel = document.querySelector('select[name="student[grade_level]"]').value;
                const summary = document.getElementById('feeSummary');
                let total = 0;

                summary.innerHTML = '';

                fees.forEach(fee => {
                    if (!fee.grade_level || fee.grade_level === gradeLevel) {
                        const li = document.createElement('li');
                        li.className = 'list-group-item d-flex justify-content-between align-items-center bg-transparent';
                        li.innerHTML = `${fee.name} <span>${parseFloat(fee.amount).toLocaleString('pt-MZ', { minimumFractionDigits: 2 })} MT</span>`;
                        summary.appendChild(li);
                        total += parseFloat(fee.amount);
                    }
                });

                document.getElementById('totalAmount').innerText = total.toLocaleString('pt-MZ', { minimumFractionDigits: 2 });
            }

            document.getElementById('nextBtn').addEventListener('click', () => {
                if (currentStep < steps.length - 1) {
                    currentStep++;
                    updateStep();
                }
            });

            document.getElementById('prevBtn').addEventListener('click', () => {
                if (currentStep > 0) {
                    currentStep--;
                    updateStep();
                }
            });

            updateStep();
        </script>
    @endpush
@endsection