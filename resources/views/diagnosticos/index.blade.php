<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-evenly items-center mb-4">
            <h1 class="text-text text-2xl font-bold leading-tight tracking-[-0.015em] px-4 pb-3 pt-5">
                {{ __('CIE-10 Codes') }}
            </h1>

        </div>
    </x-slot>
    <x-canva>
        <div id="cie10-root">
            <!-- React se montará aquí -->
        </div>
    </x-canva>

    @vite(['resources/js/cie10.jsx'])
</x-app-layout>
