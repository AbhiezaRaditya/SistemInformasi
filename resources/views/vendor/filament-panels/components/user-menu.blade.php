@php
    $user = auth()->user();
    $roles = method_exists($user, 'getRoleNames') ? $user->getRoleNames() : collect();

    $badges = $roles->map(fn ($role) => [
        'label' => \Illuminate\Support\Str::headline($role),
        'color' => match (strtolower($role)) {
            'super_admin', 'admin' => 'danger',
            'himpunan' => 'warning',
            'kaprodi' => 'info',
            'mahasiswa' => 'success',
            default => 'gray',
        },
    ])->values();

    if ($user->studyProgram ?? null) {
        $badges->push(['label' => $user->studyProgram->codename, 'color' => 'success']);
    }

    if ($user->unit ?? null) {
        $unitName = $user->unit->codename;
        $badges->push([
            'label' => strlen($unitName) <= 5
                ? strtoupper($unitName)
                : collect(explode(' ', $unitName))->map(fn ($w) => str($w)->substr(0, 1)->upper())->implode(''),
            'color' => 'info',
        ]);
    }
@endphp

<x-filament::dropdown placement="bottom-end" teleport>
    <x-slot name="trigger">
        <button type="button" class="custom-user-menu-trigger">
            <x-filament::avatar :src="filament()->getUserAvatarUrl($user)" class="h-9 w-9" />
        </button>
    </x-slot>

    <x-filament::dropdown.list class="custom-user-menu-list">
        <li class="custom-user-menu-header">
            <div class="custom-user-menu-identity">
                <div class="relative flex-shrink-0">
                    <x-filament::avatar :src="filament()->getUserAvatarUrl($user)" class="h-11 w-11" />
                </div>
                <div class="custom-user-menu-name-email">
                    <p class="custom-user-menu-eyebrow">Selamat Datang</p>
                    <h4>{{ filament()->getUserName($user) }}</h4>
                    @if ($user->email ?? null)
                        <p>{{ $user->email }}</p>
                    @endif
                </div>
            </div>

            @if ($badges->isNotEmpty())
                <div class="custom-user-menu-badges">
                    @foreach ($badges as $badge)
                        <x-filament::badge :color="$badge['color']" size="sm" class="custom-badge-pill">
                            {{ $badge['label'] }}
                        </x-filament::badge>
                    @endforeach
                </div>
            @endif
        </li>
    </x-filament::dropdown.list>
</x-filament::dropdown>