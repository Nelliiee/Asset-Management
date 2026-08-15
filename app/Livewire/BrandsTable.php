<?php

namespace App\Livewire;

use App\Models\Brand;
use Livewire\Component;

class BrandsTable extends Component
{
    public function deleteBrand(int $brandId): void
    {
        Brand::findOrFail($brandId)->delete();

        session()->flash('message', 'Brand deleted successfully.');
    }

    public function updateBrand(int $brandId): void
    {
        $this->redirect('/admin/brands');
    }

    public function render()
    {
        return view('livewire.brands-table', [
            'brands' => Brand::latest()->get(),
        ]);
    }
}
