@php
    $month = (int) ($calendarMonth ?? now()->month);
    $year = (int) ($calendarYear ?? now()->year);
    $startOfMonth = \Carbon\Carbon::create($year, $month, 1);
    $endOfMonth = $startOfMonth->copy()->endOfMonth();
    $daysInMonth = $endOfMonth->day;
    $firstDayOfWeek = $startOfMonth->dayOfWeek; // 0=Dom
    $today = now();

    $eventsByDay = $monthEvents ?? collect();
    $birthdaysByDay = $monthBirthdays ?? collect();

    $selectedDay = request('calendar_day');
    $selectedDate = $selectedDay ? \Carbon\Carbon::create($year, $month, (int) $selectedDay) : null;
    $selectedEvents = $selectedDate && $eventsByDay->has($selectedDay) ? $eventsByDay[$selectedDay]['events'] : collect();
    $selectedBirthdays = $selectedDate && $birthdaysByDay->has($selectedDay) ? $birthdaysByDay[$selectedDay]['students'] : collect();
@endphp

<div class="dash-card-flat h-full bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-xs"></i>
                </span>
                Calendário Escolar
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">Eventos e aniversariantes do mês</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="?calendar_month={{ $startOfMonth->copy()->subMonth()->month }}&calendar_year={{ $startOfMonth->copy()->subMonth()->year }}" 
               class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center justify-center text-xs transition">
                <i class="fas fa-chevron-left"></i>
            </a>
            <span class="font-bold text-xs text-slate-800 min-w-[110px] text-center capitalize">
                {{ $startOfMonth->translatedFormat('F Y') }}
            </span>
            <a href="?calendar_month={{ $startOfMonth->copy()->addMonth()->month }}&calendar_year={{ $startOfMonth->copy()->addMonth()->year }}" 
               class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center justify-center text-xs transition">
                <i class="fas fa-chevron-right"></i>
            </a>
        </div>
    </div>

    <div class="p-5">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            <!-- Calendar Grid (7 cols) -->
            <div class="lg:col-span-7">
                <div class="rounded-xl border border-slate-100 bg-white p-2.5">
                    <table class="w-full text-center border-collapse">
                        <thead>
                            <tr class="bg-slate-50/75 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                <th class="py-2">Dom</th>
                                <th class="py-2">Seg</th>
                                <th class="py-2">Ter</th>
                                <th class="py-2">Qua</th>
                                <th class="py-2">Qui</th>
                                <th class="py-2">Sex</th>
                                <th class="py-2">Sáb</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @php
                                $day = 1;
                                $week = 0;
                            @endphp
                            @while($day <= $daysInMonth)
                                <tr>
                                    @for($col = 0; $col < 7; $col++)
                                        @php
                                            if ($week == 0 && $col < $firstDayOfWeek) {
                                                echo '<td class="py-2 px-1 text-slate-300"></td>';
                                            } elseif ($day > $daysInMonth) {
                                                echo '<td class="py-2 px-1 text-slate-300"></td>';
                                            } else {
                                                $date = \Carbon\Carbon::create($year, $month, $day);
                                                $isToday = $date->isToday();
                                                $isSelected = $selectedDate && $date->eq($selectedDate);
                                                $hasEvent = $eventsByDay->has($day);
                                                $hasBirthday = $birthdaysByDay->has($day);

                                                $cellClass = 'text-slate-700 hover:bg-slate-100';
                                                if ($isToday) {
                                                    $cellClass = 'bg-emerald-600 text-white font-bold rounded-lg shadow-2xs';
                                                } elseif ($isSelected) {
                                                    $cellClass = 'bg-emerald-100 text-emerald-900 font-bold rounded-lg ring-2 ring-emerald-500';
                                                } elseif ($hasEvent || $hasBirthday) {
                                                    $cellClass = 'bg-emerald-50/70 text-emerald-800 font-semibold rounded-lg';
                                                }
                                        @endphp
                                        <td class="py-1 px-1">
                                            <a href="?calendar_month={{ $month }}&calendar_year={{ $year }}&calendar_day={{ $day }}" 
                                               class="w-full py-1.5 flex flex-col items-center justify-center text-xs transition {{ $cellClass }}">
                                                <span>{{ $day }}</span>
                                                @if($hasEvent || $hasBirthday)
                                                    <span class="w-1 h-1 rounded-full {{ ($isToday || $isSelected) ? 'bg-white' : 'bg-emerald-600' }} mt-0.5"></span>
                                                @endif
                                            </a>
                                        </td>
                                        @php
                                                $day++;
                                            }
                                        @endphp
                                    @endfor
                                </tr>
                                @php $week++; @endphp
                            @endwhile
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Details on Selected Day (5 cols) -->
            <div class="lg:col-span-5">
                <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 h-full flex flex-col justify-between">
                    @if($selectedDate)
                        <div class="space-y-4">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                                <span class="font-bold text-xs uppercase tracking-wider text-slate-800 capitalize">
                                    {{ $selectedDate->translatedFormat('d \\d\\e F, l') }}
                                </span>
                            </div>

                            @if($selectedEvents->count() > 0)
                                <div class="space-y-2">
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Eventos do Dia</div>
                                    @foreach($selectedEvents as $event)
                                        <a href="{{ route('events.show', $event) }}" class="flex items-start gap-3 p-2.5 rounded-xl bg-white border border-slate-200 hover:border-emerald-300 transition text-slate-800 shadow-2xs">
                                            <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-700 flex flex-col items-center justify-center shrink-0">
                                                <span class="text-[9px] font-bold uppercase">{{ $event->event_date->format('M') }}</span>
                                                <span class="text-xs font-extrabold leading-none">{{ $event->event_date->format('d') }}</span>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-bold text-xs text-slate-800 truncate">{{ $event->title }}</div>
                                                <div class="text-[10px] text-slate-400 mt-0.5">
                                                    <i class="far fa-clock mr-1"></i>
                                                    {{ $event->start_time?->format('H:i') ?? '' }} {{ $event->start_time && $event->end_time ? '- ' . $event->end_time->format('H:i') : '' }}
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @endif

                            @if($selectedBirthdays->count() > 0)
                                <div class="space-y-2">
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-amber-700">Aniversariantes</div>
                                    @foreach($selectedBirthdays as $birthday)
                                        <div class="flex items-center justify-between gap-2 p-2.5 rounded-xl bg-amber-50/70 border border-amber-200 text-xs">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-7 h-7 rounded-full bg-amber-200 text-amber-800 flex items-center justify-center font-bold text-xs shrink-0">
                                                    {{ substr($birthday->first_name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="font-bold text-slate-800">{{ $birthday->first_name }} {{ $birthday->last_name }}</div>
                                                    <div class="text-[10px] text-slate-500">Nascimento: {{ $birthday->birth_date }}</div>
                                                </div>
                                            </div>
                                            <a href="{{ route('students.show', $birthday->id) }}" class="px-2.5 py-1 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-semibold text-[10px] transition shrink-0">
                                                Perfil
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if(!$selectedEvents->count() && !$selectedBirthdays->count())
                                <div class="py-8 text-center text-slate-400">
                                    <i class="fas fa-calendar-day text-2xl mb-1 text-slate-300"></i>
                                    <p class="text-xs">Nenhum evento ou aniversariante neste dia.</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="py-12 text-center text-slate-400 my-auto">
                            <i class="fas fa-mouse-pointer text-2xl mb-2 text-slate-300"></i>
                            <p class="text-xs font-medium text-slate-600">Selecione um dia no calendário</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">Clique numa data para visualizar eventos e aniversários agendados.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
