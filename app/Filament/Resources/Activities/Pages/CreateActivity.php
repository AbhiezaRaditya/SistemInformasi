<?php

namespace App\Filament\Resources\Activities\Pages;

use App\Filament\Resources\Activities\ActivityResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\Action;

class CreateActivity extends CreateRecord
{
    protected static string $resource = ActivityResource::class;

    protected static bool $canCreateAnother = false;

    public string $submitStatus = 'draft';

    protected function getFormActions(): array
    {
        return [
            Action::make('draft')
                ->label('Save as Draft')
                ->color('gray')
                ->action(function () {
                    $this->submitStatus = 'draft';
                    $this->create();
                }),

            Action::make('send')
                ->label('Send (Kirim ke Kaprodi)')
                ->color('primary')
                ->action(function () {
                    $this->submitStatus = 'pending';
                    $this->create();
                }),

            $this->getCancelFormAction(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        $data['unit_id'] = Auth::user()->unit_id;
        $data['status'] = $this->submitStatus; 

        return $data;
    }
}