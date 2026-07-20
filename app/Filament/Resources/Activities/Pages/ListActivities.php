<?php

namespace App\Filament\Resources\Activities\Pages;

use App\Filament\Resources\Activities\ActivityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab; // Namespace yang benar untuk Filament v3
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


            'pending' => Tab::make('Menunggu Persetujuan')
                ->label('Menunggu Persetujuan')
                ->icon('heroicon-o-clock')
                ->badge(
                    static::getResource()::getEloquentQuery()
                        ->where('status', 'pending')
                        ->count()
                )
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending')),

            'revisi' => Tab::make('Revisi')
                ->label('Revisi')
                ->icon('heroicon-o-pencil')
                ->badge(
                    static::getResource()::getEloquentQuery()
                        ->where('status', 'revisi')
                        ->count()
                )
                 ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'revisi')),


            'reject' => Tab::make('Ditolak')
                ->label('Ditolak')
                ->icon('heroicon-o-x-circle')
                ->badge(
                    static::getResource()::getEloquentQuery()
                        ->where('status', 'reject')
                        ->count()
                )
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'reject')),

            'dalam_realisasi' => Tab::make('Dalam Realisasi')
                ->label('Dalam Realisasi')
                ->icon('heroicon-o-arrow-path')
                ->badge(
                    static::getResource()::getEloquentQuery()
                        ->where('status', 'dalam_realisasi')
                        ->count()
                )
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'dalam_realisasi')),
        ];
    }
}