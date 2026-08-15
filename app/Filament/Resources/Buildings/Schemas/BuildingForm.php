<?php

namespace App\Filament\Resources\Buildings\Schemas;

use App\Filament\Resources\Buildings\Pages\CreateBuildings;
use App\Filament\Resources\Buildings\Pages\EditBuildings;
use App\Filament\Resources\Buildings\Pages\ListBuildings;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;

class BuildingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Building Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('code')
                    ->label('Building Code')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'create' => CreateBuildings::route('/create'),
            'edit' => EditBuildings::route('/{record}/edit'),
            'index' => ListBuildings::route('/'),
        ];
    }   
}
