<?php

namespace App\Filament\Resources\Assets\Pages;

use App\Filament\Resources\Assets\AssetResource;
use App\Filament\Resources\Assets\Schemas\AssetGroupForm;
use App\Models\Asset;
use App\Models\AssetGroup;
use App\Models\Classroom;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\DB;

class ManageAssets extends ManageRecords
{
    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->after(function (Asset $record): void {
                    if ($record->status !== 'maintenance') {
                        return;
                    }

                    $record->reports()->create([
                        'reporter_id' => Filament::auth()->id(),
                        'description' => "Asset {$record->asset_code} ({$record->name}) requires maintenance.",
                        'status' => 'pending',
                    ]);
                }),
            CreateAction::make('createAssetGroup')
                ->label('Create Asset Group')
                ->model(AssetGroup::class)
                ->schema(AssetGroupForm::components())
                ->using(function (array $data): AssetGroup {
                    return DB::transaction(function () use ($data): AssetGroup {
                        $classroom = Classroom::query()->findOrFail($data['classroom_id']);
                        $assetGroup = AssetGroup::query()->create([
                            'name' => $data['name'],
                            'classroom_id' => $classroom->id,
                        ]);

                        foreach ([
                            'monitor' => 'Monitor',
                            'system_unit' => 'System Unit',
                            'keyboard' => 'Keyboard',
                            'mouse' => 'Mouse',
                            'ups' => 'UPS',
                        ] as $key => $name) {
                            $component = $data['components'][$key];

                            $assetGroup->assets()->create([
                                'category_id' => $component['category_id'],
                                'brand_id' => $component['brand_id'],
                                'classroom_id' => $classroom->id,
                                'building_id' => $classroom->building_id,
                                'name' => $name,
                                'serial_number' => $component['serial_number'],
                                'terminal_number' => $component['terminal_number'] ?? null,
                                'expiry_date' => $component['expiry_date'] ?? null,
                                'status' => 'available',
                            ]);
                        }

                        return $assetGroup;
                    });
                }),
        ];
    }
}
