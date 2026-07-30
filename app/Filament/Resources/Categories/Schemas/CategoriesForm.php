<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Filament\Resources\Categories\Pages\CreateCategories;
use App\Filament\Resources\Categories\Pages\EditCategories;
use App\Filament\Resources\Categories\Pages\ListCategories;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;

class CategoriesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('slug')
                    ->required()
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
