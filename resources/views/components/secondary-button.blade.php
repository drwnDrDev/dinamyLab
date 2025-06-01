<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center bg-secondary px-4 py-2 rounded-md font-semibold text-xs text-text uppercase tracking-widest shadow-sm hover:bg-background hover:text-text hover:border hover:border-borders
    focus:outline-none focus:ring-1 focus:ring-titles focus:ring-offset-2 disabled:opacity-25 transition ease-in-out 
    duration-150']) }}>
    {{ $slot }}
</button>
