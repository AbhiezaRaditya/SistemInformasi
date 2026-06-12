<?php

namespace App\Filament\Resources\StudyPrograms;

use App\Filament\Resources\StudyPrograms\Pages\CreateStudyProgram;
use App\Filament\Resources\StudyPrograms\Pages\EditStudyProgram;
use App\Filament\Resources\StudyPrograms\Pages\ListStudyPrograms;
use App\Filament\Resources\StudyPrograms\Pages\ViewStudyProgram;
use App\Filament\Resources\StudyPrograms\Schemas\StudyProgramForm;
use App\Filament\Resources\StudyPrograms\Schemas\StudyProgramInfolist;
use App\Filament\Resources\StudyPrograms\Tables\StudyProgramsTable;
use App\Models\StudyProgram;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudyProgramResource extends Resource
{
    protected static ?string $model = StudyProgram::class;

    protected static ?string $navigationLabel = 'Program Studi';
    protected static ?string $modelLabel = 'Program Studi';
    protected static ?string $pluralModelLabel = 'Program Studi';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return StudyProgramForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StudyProgramInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudyProgramsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudyPrograms::route('/'),
            'create' => CreateStudyProgram::route('/create'),
            'view' => ViewStudyProgram::route('/{record}'),
            'edit' => EditStudyProgram::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}