@props([
    'name',
    'label' => null,
    'value' => null,
    'placeholder' => '',
    'rows' => 3,
    'required' => false,
    'error' => null,
])

<div class="space-y-1">
    @if($label)
        <label for="{{ $name }}" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
            {{ $label }}
            @if($required) <span class="text-rose-500">*</span> @endif
        </label>
    @endif
    <textarea name="{{ $name }}" 
              id="{{ $name }}" 
              rows="{{ $rows }}"
              placeholder="{{ $placeholder }}"
              @if($required) required @endif
              {{ $attributes->merge(['class' => 'w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#151E3D] text-slate-900 dark:text-white py-2.5 px-3 text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition']) }}>{{ old($name, $value) }}</textarea>
    @if($error || $errors->has($name))
        <span class="text-[11px] text-rose-500 mt-1 block font-bold">{{ $error ?? $errors->first($name) }}</span>
    @endif
</div>

