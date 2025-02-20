<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Widget content --}}
        <div class="flex items-center gap-x-3">
            <x-filament::avatar src="{{ url('storage/images/logo-svg.svg') }}" alt="JalaramKhaman" size="w-30 h-11"
                :circular="false" />

            <div class="flex-1">

                @if (Auth::user()->user_type == 'superuser')
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Users : {{ session('user_count') }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Valid Up to : {{ session('license_expiry') }}
                    </p>
                @endif
            </div>

            <form action="{{ filament()->getLogoutUrl() }}" method="post" class="my-auto">
                @csrf
                <x-filament::button color="gray" icon="heroicon-o-globe-alt" href="https://indianinfotech.org"
                    tag="a" target="_blank" labeled-from="sm">
                    Website
                </x-filament::button>

            </form>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
