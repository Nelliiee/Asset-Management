<?php

namespace App\Livewire;

use App\Models\Asset;
use Livewire\Attributes\On;
use Livewire\Component;

class AssetsTable extends Component
{
    public function deleteAsset(int $assetId): void
    {
        Asset::findOrFail($assetId)->delete();

        session()->flash('message', 'Asset deleted successfully.');
    }

    public function updateAsset(int $assetId): void
    {
        $this->redirect('/admin/assets');
    }

    public function render()
    {
        return view('livewire.assets-table', [
            'assets' => Asset::with(['category', 'brand'])
                ->latest()
                ->get(),
        ]);
    }
}
