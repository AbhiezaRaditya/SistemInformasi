<?php

namespace App\Filament\Resources\Activities\Schemas;

use App\Models\Activity;
use Composer\XdebugHandler\Status;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Get;

class ActivityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->maxLength(255)
                    ->required(),
                FileUpload::make('attachment')
                    ->label('Attachment')
                    ->disk('public')
                    ->directory('attachment')
                    ->maxSize(2048)
                    ->visibility('public')
                    ->visibility('public')
                    ->multiple()
                    ->reorderable()
                    ->appendFiles(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                DatePicker::make('tanggal_berlangsung')
                    ->label('Tanggal Berlangsung'),
                DatePicker::make('tanggal_berakhir')
                    ->label('Tanggal Berakhir'),
                Select::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),


                Select::make('status')
                    ->options([
                        'revisi' => 'Revisi',
                        'accept' => 'Accept',
                        'reject' => 'Tolak'
                    ])
                    ->default('draft')
                    ->live()
                    ->visible(fn() => Auth::user()->hasRole(['kaprodi', 'super_admin'])),

                Textarea::make('catatan_revisi')
                    ->label('Catatan Revisi / Penolakan dari Kaprodi')
                    ->required(fn($get) => in_array($get('status'), ['revisi', 'reject']))
                    ->visible(fn($get) => in_array($get('status'), ['revisi', 'reject']))
                    ->disabled(fn() => !Auth::user()->hasRole(['kaprodi', 'super_admin']))
                    ->columnSpanFull(),
            ]);
    }
}
