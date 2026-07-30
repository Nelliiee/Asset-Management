<?php

namespace App\Filament\Resources\Buildings\Schemas;

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
                TextInput::make('slug')
                    ->label('Building Slug')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}
