<x-filament-widgets::widget class="fi-account-widget">
    <x-filament::section>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-x-4">
                <x-filament::avatar
                    :src="filament()->getUserAvatarUrl(auth()->user())"
                    class="h-14 w-14 ring-2 ring-blue-100"
                />

                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                        Selamat Datang,
                    </p>

                    <h1 class="text-xl font-bold tracking-tight text-gray-950 dark:text-white">
                        {{ filament()->getUserName(auth()->user()) }}
                    </h1>

                    @php
                        $user = auth()->user();
                        $roles = method_exists($user, 'getRoleNames') ? $user->getRoleNames() : collect();

                        $colors = [
                            'super_admin' => 'bg-red-50 text-red-600 ring-red-200',
                            'admin'       => 'bg-red-50 text-red-600 ring-red-200',
                            'himpunan'    => 'bg-amber-50 text-amber-600 ring-amber-200',
                            'kaprodi'     => 'bg-blue-50 text-blue-600 ring-blue-200',
                            'mahasiswa'   => 'bg-green-50 text-green-600 ring-green-200',
                        ];
                    @endphp

                    @if ($roles->isNotEmpty())
                        <div class="mt-1.5 flex flex-wrap gap-1.5">
                            @foreach ($roles as $role)
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $colors[strtolower($role)] ?? 'bg-gray-50 text-gray-600 ring-gray-200' }}">
                                    {{ \Illuminate\Support\Str::headline($role) }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <form action="{{ filament()->getLogoutUrl() }}" method="post">
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