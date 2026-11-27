<?php

namespace App\Filament\Resources\Assets\Schemas;

use App\Models\Brand;
use App\Models\Category;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

class AssetGroupForm
{
    /**
     * @return array<int, Grid|Section>
     */
    public static function components(): array
    {
        $components = [
            'monitor' => 'Monitor',
            'system_unit' => 'System Unit',
            'keyboard' => 'Keyboard',
            'mouse' => 'Mouse',
            'ups' => 'UPS',
        ];

        return [
            Grid::make(2)
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Select::make('classroom_id')
                        ->relationship('classroom', 'room_number')
                        ->searchable()
                        ->preload()
                        ->required(),
                ]),
            ...array_map(
                fn (string $label, string $key): Section => Section::make($label)
                    ->schema(self::componentFields($key)),
                array_values($components),
                array_keys($components),
            ),
        ];
    }

    /**
     * @return array<int, TextInput|Select|DateTimePicker>
     */
    private static function componentFields(string $key): array
    {
        return [
            Select::make("components.{$key}.category_id")
                ->label('Category')
                ->options(fn (): array => Category::query()->orderBy('name')->pluck('name', 'id')->all())
                ->searchable()
                ->required(),
            Select::make("components.{$key}.brand_id")
                ->label('Brand')
                ->options(fn (): array => Brand::query()->orderBy('name')->pluck('name', 'id')->all())
                ->searchable()
                ->required(),
            TextInput::make("components.{$key}.serial_number")
                ->label('Serial Number')
                ->required()
                ->maxLength(255),
            TextInput::make("components.{$key}.terminal_number")
                ->label('Terminal Number')
                ->maxLength(255),
            DateTimePicker::make("components.{$key}.expiry_date")
                ->label('Expiry Date'),
        ];
    }
}
