<?php

namespace App\Filament\Resources\Classrooms\Schemas;

use App\Models\Building;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ClassroomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('building_id')
                    ->relationship('building', 'name')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, ?int $state) {
                        if ($state) {
                            $building = Building::find($state);
                            if ($building) {
                                $set('code', $building->code);
                            }
                        }
                    }),
                TextInput::make('room_number')
                    ->required(),
                TextInput::make('code')
                    ->required(),
                TextInput::make('floor')
                    ->required()
                    ->numeric(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
