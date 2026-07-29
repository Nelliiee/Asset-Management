<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Attributes\On;
use Livewire\Component;

class CategoriesTable extends Component
{
    public function deleteCategory(int $categoryId): void
    {
        Category::findOrFail($categoryId)->delete();

        session()->flash('message', 'Category deleted successfully.');
    }

    public function updateCategory(int $categoryId): void
    {
        $this->redirect('/admin/categories');
    }

    public function render()
    {
        return view('livewire.categories-table', [
            'categories' => Category::latest()->get(),
        ]);
    }
}
