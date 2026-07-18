<?php

namespace App\Filament\Resources\ActivityHistories\Pages;

use App\Filament\Resources\ActivityHistories\ActivityHistoryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditActivityHistory extends EditRecord
{
    protected static string $resource = ActivityHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
