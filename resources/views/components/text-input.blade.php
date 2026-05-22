@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-slate-200 focus:border-teal-500 focus:ring-teal-500 rounded-lg shadow-sm w-full text-slate-900 placeholder:text-slate-400']) !!}>
