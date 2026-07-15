<x-filament-widgets::widget class="fi-account-widget" wire:poll.15s>
    <x-filament::section>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                    {{ now()->locale('id')->translatedFormat('l, d F Y') }}
                </p>

                <h1 class="mt-0.5 text-xl font-bold tracking-tight text-gray-950 dark:text-white">
                    Selamat datang kembali, {{ filament()->getUserName(auth()->user()) }}
                </h1>

                <div class="mt-2 flex flex-wrap items-center gap-x-6 gap-y-2">
                    <p class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                        <span class="font-medium text-gray-700 dark:text-gray-300">Login saat ini :</span>
                        <x-filament::badge color="success">
                            {{ optional(auth()->user()->last_login_at)?->translatedFormat('d M Y, H:i') ?? '-' }}
                        </x-filament::badge>
                    </p>

                    <p class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                        <span class="font-medium text-gray-700 dark:text-gray-300">Logout terakhir:</span>
                        <x-filament::badge color="danger">
                            {{ optional(auth()->user()->last_logout_at)?->translatedFormat('d M Y, H:i') ?? 'Belum pernah logout' }}
                        </x-filament::badge>
                    </p>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>