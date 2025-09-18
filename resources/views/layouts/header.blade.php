<header x-data="{ open: false }" class="fixed top-0 left-0 border-b z-10 bg-background border-borders col-span-2 w-full print:hidden">
    <!-- Primary Navigation Menu -->
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <div class="flex gap-2 items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">

                           @if (session('sede'))
                           <figure>
                                 <img class="h-10 w-auto fill-current text-gray-800" src="{{ asset('storage/logos/'.session('sede')->logo) }}" alt="{{ session('sede')->nombre }}">
                           </figure>

                           @else
                            <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                           @endif()

                    </a>
                </div>
                <div>
                    <span class="text-2xl font-bold text-text">{{ session('sede')->nombre }}</span>
                </div>
            </div>

            <!-- Mnesajes de session -->
            <div class="w-1/3 flex items-center justify-end">
                <form class="py-3 w-full" action="{{ route('search') }}" method="post">
                    @csrf
                    <label class="flex flex-col min-w-40 h-12 w-full">
                        <div class="flex w-full flex-1 items-stretch rounded-xl h-full">
                            <div
                                class="text-titles flex border-none bg-secondary items-center justify-center pl-4 rounded-l-xl border-r-0"
                                data-icon="MagnifyingGlass"
                                data-size="24px"
                                data-weight="regular">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                                    <path
                                        d="M229.66,218.34l-50.07-50.06a88.11,88.11,0,1,0-11.31,11.31l50.06,50.07a8,8,0,0,0,11.32-11.32ZM40,112a72,72,0,1,1,72,72A72.08,72.08,0,0,1,40,112Z"></path>
                                </svg>
                            </div>
                            <input
                                placeholder="{{__('Search')}}"
                                name="search"
                                type="text"
                                id="search"
                                class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-text focus:outline-0 focus:ring-0 border-none bg-secondary focus:border-none h-full placeholder:text-titles px-4 rounded-l-none border-l-0 pl-2 text-base font-normal leading-normal" />
                        </div>
                    </label>
                </form>

            </div>


            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center">
                <div    id="dropdownComponent"
                        class="relative z-10"
                        data-nombre="{{ Auth::user()->name }}"
                        data-profile-url="{{ route('profile.edit') }}">
                </div>
            </div>


        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->empleado->cargo }}</div>


                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</header>
