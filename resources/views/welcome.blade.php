<x-filament-widgets::widget class="fi-account-widget">
    <x-filament::section>
        <div class="flex items-center gap-x-4">
            {{-- Foto Profil / Avatar User --}}
            <x-filament::avatar
                :src="filament()->getUserAvatarUrl($user)"
                class="h-12 w-12"
            />

            <div class="flex-1">
                {{-- Mengubah tulisan teks 'Welcome' menjadi kustom --}}
                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                    Selamat Datang,
                </p>

                {{-- Menampilkan Nama User yang Sedang Login --}}
                <h1 class="text-xl font-bold tracking-tight text-gray-950 dark:text-white">
                    {{ filament()->getUserName($user) }}
                </h1>
            </div>

            {{-- Tombol Keluar (Sign Out) --}}
            <form
                action="{{ filament()->getLogoutUrl() }}"
                method="post"
                class="my-auto"
            >
                @csrf

                <x-filament::button
                    color="gray"
                    icon="heroicon-m-arrow-left-on-rectangle"
                    icon-alias="widgets::account-widget.logout-button"
                    labeled-from="sm"
                    tag="button"
                    type="submit"
                >
                    Sign out
                </x-filament::button>
            </form>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>