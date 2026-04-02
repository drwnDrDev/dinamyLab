<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center bg-secondary dark:bg-slate-700/60 px-5 py-2.5 rounded-xl font-semibold text-xs text-titles dark:text-slate-300 uppercase tracking-wider shadow-sm border border-borders dark:border-slate-600/50 hover:bg-background dark:hover:bg-slate-600/60 hover:text-text dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-titles dark:focus:ring-slate-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900 disabled:opacity-25 transition-all ease-in-out duration-200']) }}>
    {{ $slot }}
</button>
