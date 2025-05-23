<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 border 
    border-accent rounded-md font-semibold text-xs text-accent uppercase tracking-widest shadow-sm hover:bg-accent hover:text-white 
    focus:outline-none focus:ring-1 focus:ring-accent focus:ring-offset-2 disabled:opacity-25 transition ease-in-out 
    duration-150']) }}>
    {{ $slot }}
</button>
