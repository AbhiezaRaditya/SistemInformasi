<?php

namespace App\Filament\Resources\ActivityHistories\Pages;

use App\Filament\Resources\ActivityHistories\ActivityHistoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListActivityHistories extends ListRecords
{
    protected static string $resource = ActivityHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            
        ];
    }
}
