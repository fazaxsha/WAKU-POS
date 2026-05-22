<button {{ $attributes->merge(['type' => 'submit', 'class' =>
    'inline-flex items-center justify-center gap-2 w-full px-5 py-3
     bg-slate-900 hover:bg-slate-800 active:bg-slate-950
     text-white text-sm font-semibold rounded-xl
     shadow-sm shadow-slate-900/20
     focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2
     transition-all duration-150 ease-in-out'
]) }}>
    {{ $slot }}
</button>
