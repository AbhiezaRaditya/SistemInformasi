@php
    $user = auth()->user();
    $roles = method_exists($user, 'getRoleNames') ? $user->getRoleNames() : collect();

    $roleBadges = $roles->map(fn ($role) => [
        'label' => \Illuminate\Support\Str::headline($role),
        'color' => match (strtolower($role)) {
            'super_admin', 'admin' => 'danger',
            'himpunan' => 'warning',
            'kaprodi' => 'info',
            'mahasiswa' => 'success',
            default => 'gray',
        },
    ])->values();

    // DIUBAH: dari $user->studyProgram (relasi tunggal) jadi $user->studyPrograms
    // (relasi many-to-many) — ambil semua codename, bukan cuma satu.
    $studyProgramLabels = $user->studyPrograms
        ? $user->studyPrograms->pluck('codename')->filter()->values()
        : collect();

    // DIUBAH: dari $user->unit (relasi tunggal) jadi $user->units (many-to-many),
    // logic singkatan tetap dipertahankan tapi dijalankan per-item.
    $unitLabels = $user->units
        ? $user->units->pluck('codename')->filter()->map(function ($unitName) {
            return strlen($unitName) <= 5
                ? strtoupper($unitName)
                : collect(explode(' ', $unitName))->map(fn ($w) => str($w)->substr(0, 1)->upper())->implode('');
        })->values()
        : collect();
@endphp

<x-filament::dropdown placement="bottom-end" teleport>
    <x-slot name="trigger">
        <button type="button" class="custom-user-menu-trigger">
            <x-filament::avatar :src="filament()->getUserAvatarUrl($user)" class="h-9 w-9" />
        </button>
    </x-slot>

    <x-filament::dropdown.list class="custom-user-menu-list custom-user-menu-list-wide">
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

            @if ($roleBadges->isNotEmpty())
                <div class="custom-user-menu-badges">
                    @foreach ($roleBadges as $badge)
                        <x-filament::badge :color="$badge['color']" size="sm" class="custom-badge-pill">
                            {{ $badge['label'] }}
                        </x-filament::badge>
                    @endforeach
                </div>
            @endif

            {{-- Info tambahan (Program Studi / Unit) dikelompokkan dalam satu blok
                 supaya tidak ada garis pemisah nyasar di antara label dan badge-nya --}}
            @if ($studyProgramLabels->isNotEmpty() || $unitLabels->isNotEmpty())
                <div class="custom-user-menu-extra">
                    {{-- Baris "Program Studi: ..." — hanya muncul kalau user punya minimal 1 --}}
                    @if ($studyProgramLabels->isNotEmpty())
                        <div class="custom-user-menu-info-row">
                            <span class="custom-user-menu-info-label">Program Studi</span>
                            <div class="custom-user-menu-badges">
                                @foreach ($studyProgramLabels as $label)
                                    <x-filament::badge color="success" size="sm" class="custom-badge-pill">
                                        {{ $label }}
                                    </x-filament::badge>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Baris "Unit: ..." — hanya muncul kalau user punya minimal 1 --}}
                    @if ($unitLabels->isNotEmpty())
                        <div class="custom-user-menu-info-row">
                            <span class="custom-user-menu-info-label">Unit</span>
                            <div class="custom-user-menu-badges">
                                @foreach ($unitLabels as $label)
                                    <x-filament::badge color="info" size="sm" class="custom-badge-pill">
                                        {{ $label }}
                                    </x-filament::badge>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </li>
    </x-filament::dropdown.list>
</x-filament::dropdown>

<style>
    /* Dropdown dibuat lebih lebar */
    .custom-user-menu-list-wide {
        min-width: 260px;
        max-width: 360px;
    }

    /* Blok pembungkus Program Studi + Unit, dipisah dari badge Role di atasnya
       dengan garis tipis SEKALI SAJA (bukan per-baris) */
    .custom-user-menu-extra {
        display: flex;
        flex-direction: column;
        gap: 0.625rem;
        margin-top: 0.625rem;
        padding-top: 0.625rem;
        border-top: 1px solid rgb(229 231 235);
    }

    .custom-user-menu-info-row {
        display: flex;
        flex-direction: column;
        gap: 0.375rem;
    }

    .custom-user-menu-info-label {
        font-size: 0.6875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: rgb(107 114 128);
    }

    .custom-user-menu-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 0.375rem;
    }
</style>