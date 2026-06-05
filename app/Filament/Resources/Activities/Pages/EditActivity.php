<?php

namespace App\Filament\Resources\Activities\Pages;

use App\Filament\Resources\Activities\ActivityResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;

class EditActivity extends EditRecord
{
    protected static string $resource = ActivityResource::class;

    public string $submitStatus = 'draft';

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('draft')
                ->label('Save as Draft')
                ->color('gray')
                ->action(function () {
                    $this->submitStatus = 'draft';
                    $this->save(); 
                })
                ->visible(fn () => 
                    !Auth::user()->hasRole(['kaprodi', 'super_admin']) && 
                    in_array($this->record->status, ['draft', 'revisi'])
                ),

            Action::make('send')
                ->label('Send (Kirim ke Kaprodi)')
                ->color('primary')
                ->action(function () {
                    $this->submitStatus = 'pending';
                    $this->save(); 
                })
                ->visible(fn () => 
                    !Auth::user()->hasRole(['kaprodi', 'super_admin']) && 
                    in_array($this->record->status, ['draft', 'revisi'])
                ),

            Action::make('save')
                ->label('Simpan Keputusan')
                ->color('primary')
                ->action(fn () => $this->save())
                ->visible(fn () => Auth::user()->hasRole(['kaprodi', 'super_admin'])),

            $this->getCancelFormAction(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
 
        if (!Auth::user()->hasRole(['kaprodi', 'super_admin'])) {
            if (in_array($this->record->status, ['draft', 'revisi'])) {
                $data['status'] = $this->submitStatus;
            }
        }

        return $data;
    }
}