@extends('layouts.app')

@section('title', 'Resultados da Pesquisa')
@section('page-title', 'Resultados da Pesquisa')

@php
    $titleIcon = 'fas fa-search';
@endphp

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="hover:text-emerald-600 transition">Dashboard</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Pesquisa: "{{ $query }}"</li>
@endsection

@section('page-actions')
    <div class="flex items-center gap-2">
        <button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition" onclick="window.history.back()">
            <i class="fas fa-arrow-left"></i> Voltar
        </button>
        <a href="{{ route('dashboard') }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition shadow-2xs">
            <i class="fas fa-home"></i> Dashboard
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-4">
    <!-- Estatísticas da Pesquisa -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-base font-extrabold text-slate-800">Resultados para: <strong class="text-emerald-700">"{{ $query }}"</strong></h2>
                <p class="text-xs text-slate-500 mt-0.5">Encontrados {{ $totalResults }} resultados</p>
            </div>
            <div>
                <form action="{{ route('search') }}" method="GET" class="flex items-center gap-2">
                    <input type="text" name="q" value="{{ $query }}" 
                           class="rounded-xl border border-slate-200 text-xs font-semibold py-1.5 px-3 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Nova pesquisa...">
                    <button type="submit" class="inline-flex items-center justify-center p-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs transition">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Alunos -->
    @if($results['students']->count() > 0)
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-user-graduate text-emerald-600"></i> Alunos ({{ $results['students']->count() }})
            </h3>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">Encontrados</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                        <th class="py-2.5 px-4 w-12">Foto</th>
                        <th class="py-2.5 px-3">Nome</th>
                        <th class="py-2.5 px-3">Nº Estudante</th>
                        <th class="py-2.5 px-3">Turma</th>
                        <th class="py-2.5 px-3">Email</th>
                        <th class="py-2.5 px-3">Status</th>
                        <th class="py-2.5 px-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($results['students'] as $student)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-2.5 px-4">
                                <img src="{{ $student->photo_url ?? '/images/avatar-default.png' }}" 
                                     class="w-8 h-8 rounded-full object-cover border border-slate-200" 
                                     alt="{{ $student->full_name }}"
                                     onerror="this.src='/images/avatar-default.png'">
                            </td>
                            <td class="py-2.5 px-3">
                                <div class="font-bold text-slate-800">{{ $student->full_name }}</div>
                                <div class="text-[10px] text-slate-400">{{ $student->gender }} • {{ $student->age }} anos</div>
                            </td>
                            <td class="py-2.5 px-3 font-mono text-slate-600">{{ $student->student_number }}</td>
                            <td class="py-2.5 px-3">
                                @if($student->currentClass)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">{{ $student->currentClass->name }}</span>
                                @else
                                    <span class="text-slate-400 text-[10px]">N/A</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-slate-600">{{ $student->email ?? 'N/A' }}</td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $student->status === 'active' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $student->status === 'active' ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="py-2.5 px-4 text-right">
                                <a href="{{ route('students.show', $student) }}" 
                                   class="no-underline inline-flex items-center justify-center w-7 h-7 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 text-xs transition" title="Ver perfil">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Professores -->
    @if($results['teachers']->count() > 0)
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-chalkboard-teacher text-emerald-600"></i> Professores ({{ $results['teachers']->count() }})
            </h3>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">Encontrados</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                        <th class="py-2.5 px-4">Nome</th>
                        <th class="py-2.5 px-3">Especialização</th>
                        <th class="py-2.5 px-3">Email</th>
                        <th class="py-2.5 px-3">Telefone</th>
                        <th class="py-2.5 px-3">Status</th>
                        <th class="py-2.5 px-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($results['teachers'] as $teacher)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-2.5 px-4">
                                <div class="font-bold text-slate-800">{{ $teacher->full_name }}</div>
                                <div class="text-[10px] text-slate-400">{{ $teacher->gender ?? 'N/A' }}</div>
                            </td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">{{ $teacher->specialization ?? 'N/A' }}</span>
                            </td>
                            <td class="py-2.5 px-3 text-slate-600">{{ $teacher->email }}</td>
                            <td class="py-2.5 px-3 text-slate-600">{{ $teacher->phone ?? 'N/A' }}</td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $teacher->status === 'active' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $teacher->status === 'active' ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="py-2.5 px-4 text-right">
                                <a href="{{ route('teachers.show', $teacher) }}" 
                                   class="no-underline inline-flex items-center justify-center w-7 h-7 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 text-xs transition" title="Ver perfil">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Turmas -->
    @if($results['classes']->count() > 0)
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-chalkboard text-emerald-600"></i> Turmas ({{ $results['classes']->count() }})
            </h3>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">Encontradas</span>
        </div>
        <div class="p-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
            @foreach($results['classes'] as $class)
                <div class="bg-slate-50 rounded-xl border border-slate-200 p-3.5 space-y-2 flex flex-col justify-between">
                    <div>
                        <h4 class="text-xs font-extrabold text-slate-800">{{ $class->name }}</h4>
                        <p class="text-[11px] text-slate-500">
                            <i class="fas fa-users text-emerald-600 mr-1"></i>
                            {{ $class->students_count ?? 0 }} alunos
                        </p>
                        <p class="text-[11px] text-slate-500">
                            <i class="fas fa-user-tie text-emerald-600 mr-1"></i>
                            {{ $class->teacher->full_name ?? 'Sem professor' }}
                        </p>
                    </div>
                    <a href="{{ route('classes.show', $class) }}" 
                       class="no-underline inline-flex justify-center items-center gap-1 w-full py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-bold transition">
                        Ver Turma
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Nenhum Resultado -->
    @if($totalResults == 0)
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-12 text-center space-y-3">
        <i class="fas fa-search text-4xl text-slate-300"></i>
        <h3 class="text-sm font-extrabold text-slate-700">Nenhum resultado encontrado</h3>
        <p class="text-xs text-slate-500">Não foram encontrados resultados para "{{ $query }}"</p>
        <div class="flex justify-center gap-2 pt-2">
            <a href="{{ route('dashboard') }}" class="no-underline inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition shadow-2xs">
                <i class="fas fa-home"></i> Voltar ao Dashboard
            </a>
        </div>
    </div>
    @endif
@endsection

@push('scripts')
<script>
// Destacar termos pesquisados sem destruir nós HTML (botões, links, imagens)
function highlightNode(element, query) {
    if (!query || query.length < 2) return;
    const escapedQuery = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    const regex = new RegExp(`(${escapedQuery})`, 'gi');
    
    function walk(node) {
        if (node.nodeType === 3) { // Nó de texto
            const val = node.nodeValue;
            if (val && regex.test(val)) {
                const span = document.createElement('span');
                span.innerHTML = val.replace(regex, '<mark class="bg-warning text-dark px-1 rounded font-semibold">$1</mark>');
                node.parentNode.replaceChild(span, node);
            }
        } else if (node.nodeType === 1 && !['SCRIPT', 'STYLE', 'INPUT', 'TEXTAREA', 'BUTTON', 'A', 'IMG', 'CODE'].includes(node.tagName)) {
            Array.from(node.childNodes).forEach(walk);
        }
    }
    walk(element);
}

document.addEventListener('DOMContentLoaded', function() {
    const query = @json($query);
    if (query && query.length >= 2) {
        document.querySelectorAll('.school-card, .table-hover').forEach(container => {
            highlightNode(container, query);
        });
    }

    // Pesquisa em tempo real ao digitar (Live Search Filtering)
    let searchDebounce = null;
    const searchInput = document.querySelector('input[name="q"]');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchDebounce);
            const val = e.target.value.trim().toLowerCase();
            searchDebounce = setTimeout(() => {
                if (val.length >= 2) {
                    document.querySelectorAll('tbody tr').forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(val) ? '' : 'none';
                    });
                    document.querySelectorAll('.school-card').forEach(card => {
                        const tableBody = card.querySelector('tbody');
                        if (tableBody) {
                            const visibleRows = tableBody.querySelectorAll('tr:not([style*="display: none"])');
                            card.style.display = visibleRows.length > 0 ? '' : 'none';
                        }
                    });
                } else {
                    document.querySelectorAll('tbody tr, .school-card').forEach(el => el.style.display = '');
                }
            }, 200);
        });
    }
});
</script>
@endpush