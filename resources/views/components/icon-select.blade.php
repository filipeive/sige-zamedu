@props([
    'name',
    'value' => '',
    'placeholder' => 'Selecione...',
    'groups' => [],
    'required' => false,
    'errorKey' => null,
    'iconClass' => 'w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold',
])

@php
    $errorKey = $errorKey ?? $name;
    $hasError = $errors->has($errorKey);
    $currentLabel = null;
    $currentIcon = null;
    $currentGroup = null;

    foreach ($groups as $group) {
        foreach ($group['options'] as $option) {
            if ((string) $option['value'] === (string) $value) {
                $currentLabel = $option['label'];
                $currentIcon = $option['icon'] ?? null;
                $currentGroup = $group['label'];
                break 2;
            }
        }
    }

    $flatOptions = [];
    foreach ($groups as $group) {
        foreach ($group['options'] as $option) {
            $flatOptions[] = [
                'value' => (string) $option['value'],
                'label' => $option['label'],
                'icon' => $option['icon'] ?? '',
                'group' => $group['label'],
            ];
        }
    }
@endphp

<div class="relative" x-data="{
        open: false,
        selected: @js((string) $value),
        currentLabel: @js($currentLabel),
        currentIcon: @js($currentIcon ?? ''),
        options: @js($flatOptions),
        term: '',

        choose(value, label, icon) {
            this.selected = value;
            this.currentLabel = label;
            this.currentIcon = icon;
            this.open = false;
            this.term = '';
        },

        pick(value) {
            const opt = this.options.find(o => o.value === value);
            if (opt) this.choose(opt.value, opt.label, opt.icon);
        },

        get filtered() {
            const t = this.term.trim().toLowerCase();
            if (!t) return this.options;
            return this.options.filter(o =>
                o.label.toLowerCase().includes(t) || o.group.toLowerCase().includes(t)
            );
        },

        isGroupVisible(groupLabel) {
            return this.filtered.some(o => o.group === groupLabel);
        },

        close() {
            this.open = false;
            this.term = '';
        }
    }" @click.outside="close()" @keydown.escape.window="close()">

    <input type="hidden" name="{{ $name }}" value="{{ $value }}"
           @if($required && $value === '') required @endif>

    <button type="button"
            @click="open = !open"
            :aria-expanded="open ? 'true' : 'false'"
            class="w-full flex items-center justify-between gap-2 text-left {{ $iconClass }} @if($hasError) border-rose-400 focus:border-rose-400 focus:ring-rose-400 @else focus:border-emerald-500 focus:ring-emerald-500 @endif">
        <span class="flex items-center gap-2 min-w-0">
            <i x-show="currentIcon" :class="currentIcon" class="text-[11px] text-emerald-600 shrink-0"></i>
            <i x-show="! currentIcon" class="fa-solid fa-circle-notch text-[10px] text-slate-300 shrink-0"></i>
            <span class="truncate" :class="currentLabel ? 'text-slate-800' : 'text-slate-400'"
                  x-text="currentLabel || @js($placeholder)"></span>
        </span>
        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 shrink-0 transition-transform"
           :class="open ? 'rotate-180' : ''"></i>
    </button>

    <div x-show="open" x-cloak x-transition.opacity.duration.150ms
         class="absolute z-50 mt-1 w-full bg-white rounded-xl border border-slate-200 shadow-xl overflow-hidden">
        <div class="p-2 border-b border-slate-100 bg-slate-50/60">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-2 text-[10px] text-slate-400"></i>
                <input type="text" placeholder="Filtrar..." autocomplete="off" x-model="term"
                       class="w-full rounded-lg border border-slate-200 bg-white ps-7 pe-2 py-1.5 text-[11px] font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500">
            </div>
        </div>

        <div class="max-h-64 overflow-y-auto py-1">
            @foreach($groups as $group)
                <div x-show="isGroupVisible(@js($group['label']))">
                    <div class="px-3 py-1.5 text-[10px] font-black uppercase tracking-wider text-slate-400 bg-slate-50/80 flex items-center gap-1.5 sticky top-0 z-10">
                        <i class="{{ $group['icon'] ?? 'fa-solid fa-layer-group' }} text-[10px] text-emerald-600"></i>
                        {{ $group['label'] }}
                    </div>
                    @foreach($group['options'] as $option)
                        <button type="button"
                                @click="pick(@js((string) $option['value']))"
                                class="w-full flex items-center gap-2 px-3 py-1.5 text-left text-xs font-semibold transition"
                                :class="selected === @js((string) $option['value'])
                                    ? 'bg-emerald-50 text-emerald-800'
                                    : 'text-slate-700 hover:bg-slate-50'">
                            <i class="{{ $option['icon'] ?? 'fa-solid fa-circle' }} text-[10px] text-slate-400 shrink-0"></i>
                            <span class="truncate">{{ $option['label'] }}</span>
                            <i x-show="selected === @js((string) $option['value'])"
                               class="fa-solid fa-check text-[10px] text-emerald-600 ms-auto shrink-0"></i>
                        </button>
                    @endforeach
                </div>
            @endforeach

            <div x-show="filtered.length === 0" class="px-3 py-6 text-center">
                <i class="fa-solid fa-magnifying-glass text-slate-300 text-lg"></i>
                <p class="mt-1 text-[11px] font-semibold text-slate-500">Nenhuma opção encontrada.</p>
            </div>
        </div>
    </div>

    @error($errorKey)
        <p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>
    @enderror
</div>
