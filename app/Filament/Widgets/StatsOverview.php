<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    protected ?string $heading = 'Jumlah Pengguna';

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected int | array | null $columns = [
        'default' => 2,
        'sm' => 2,
        'md' => 4,
        'lg' => 4,
    ];

    public static function canView(): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        return $user->getAllPermissions()->contains(function ($p) {
            $name = strtolower($p->name);
            return str_contains($name, 'stats') 
                && str_contains($name, 'overview') 
                && !str_contains($name, 'kaprodi');
        });
    }

    protected function getStats(): array
    {
        // Ambil semua role dari database secara dinamis
        $roles = Role::all();
        $stats = [];

        // Pilihan warna opsional untuk role tertentu, sisanya otomatis 'gray'
        $colorMapping = [
            'super_admin' => 'danger',
            'kaprodi'     => 'warning',
            'Himpunan'    => 'success',
            'staff'       => 'info',
        ];

        foreach ($roles as $role) {
            $roleName = $role->name;
            // Merapikan nama role untuk judul (contoh: "super_admin" menjadi "Super Admin")
            $displayName = ucwords(str_replace(['_', '-'], ' ', $roleName));
            
            // Menghitung jumlah user yang memiliki role tersebut
            $userCount = User::role($roleName)->count();

            // Menentukan warna kartu (jika tidak ada di mapping, pakai 'gray')
            $color = $colorMapping[$roleName] ?? 'gray';

            $stats[] = Stat::make($displayName, $userCount)
                ->description('Total ' . $displayName)
                ->color($color);
        }

        return $stats;
    }
}