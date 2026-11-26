@props(['name', 'label', 'options', 'value' => null, 'placeholder' => null])
@php $key = str_replace(['[', ']'], ['.', ''], $name); @endphp
<div {{ $attributes->only('class') }}>
    <label for="{{ $name }}" class="block text-sm font-medium text-slate-700">{{ $label }}</label>
    <select id="{{ $name }}" name="{{ $name }}" {{ $attributes->except('class') }}
            class="mt-1 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500 @error($key) border-red-400 @enderror">
        @if ($placeholder)<option value="">{{ $placeholder }}</option>@endif
        @foreach ($options as $nilai => $teks)
            <option value="{{ $nilai }}" @selected((string) old($key, $value) === (string) $nilai)>{{ $teks }}</option>
        @endforeach
    </select>
    @error($key)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>