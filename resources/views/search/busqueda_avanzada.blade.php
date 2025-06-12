<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Advanced Search') }}
        </h2>
    </x-slot>
    <x-canva>
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold">Advanced Search</h2>
        </div>

   @if ($error)
   <p class="text-xs text-red-800">{{$error}}</p>
   @endif

        <form action="#" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" placeholder="Enter name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="document_number" :value="__('Document Number')" />
                    <x-text-input id="document_number" name="document_number" type="text" class="mt-1 block w-full" placeholder="Enter document number" />
                    <x-input-error :messages="$errors->get('document_number')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" placeholder="Enter email" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />

                </div>
                <div>
                    <x-input-label for="phone" :value="__('Phone')" />
                    <x-text-input id="phone" name="phone" type="text" class="mt-1 block
                        w-full" placeholder="Enter phone number" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="address" :value="__('Address')" />
                    <x-text-input id="address" name="address" type="text" class="mt-1 block
                        w-full" placeholder="Enter address" />
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                </div>
</div>
            <div class="flex justify-end">
                <x-primary-button type="submit">
                    {{ __('Search') }}
                </x-primary-button>
            </div>
        </form>

        @if(isset($results) && $results->isNotEmpty())
            <div class="mt-6">
                <h3 class="text-xl font-semibold mb-4">Search Results</h3>
                <ul class="space-y-2">
                    @foreach($results as $result)
                        <li class="p-4 bg-white shadow rounded-lg">
                            <p><strong>Name:</strong> {{ $result->name }}</p>
                            <p><strong>Document Number:</strong> {{ $result->document_number }}</p>
                            <p><strong>Email:</strong> {{ $result->email }}</p>
                            <p><strong>Phone:</strong> {{ $result->phone }}</p>
                            <p><strong>Address:</strong> {{ $result->address }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </x-canva>
</x-app-layout>
