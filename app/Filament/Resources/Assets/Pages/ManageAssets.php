<?php

namespace App\Filament\Resources\Assets\Pages;

use App\Filament\Resources\Assets\AssetResource;
use App\Models\Asset;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ManageRecords;

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
        ];
    }
}
