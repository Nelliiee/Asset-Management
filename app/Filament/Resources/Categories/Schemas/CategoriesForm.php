<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Filament\Resources\Categories\Pages\CreateCategories;
use App\Filament\Resources\Categories\Pages\EditCategories;
use App\Filament\Resources\Categories\Pages\ListCategories;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CategoriesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'create' => CreateCategories::route('/create'),
            'edit' => EditCategories::route('/{record}/edit'),
            'index' => ListCategories::route('/'),
        ];
    }
}
