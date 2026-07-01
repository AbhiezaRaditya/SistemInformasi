<?php

namespace App\Filament\Resources\Activities\Pages;

use App\Filament\Resources\Activities\ActivityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Filament\Schemas\Components\Tabs\Tab as TabsTab;
use Illuminate\Database\Eloquent\Builder;

class ListActivities extends ListRecords
{
    protected static string $resource = ActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Buat Aktivitas Kemahasiswaan'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => TabsTab::make('Semua'),

            'pending' => TabsTab::make('Menunggu Persetujuan')
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending')),

            'reject' => TabsTab::make('Ditolak')
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'reject')),

            'realisasi' => TabsTab::make('Dalam Realisasi')
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'dalam_realisasi')),

            'complated' => TabsTab::make('Selesai Realisasi')
                ->icon('heroicon-o-check-badge')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'completed')),

        ];
    }
}