<div class="card">
    <div class="table-header">
        <h1>Categories</h1>
        <a class="btn btn-primary" href="{{ url('/admin/categories') }}">Add Category</a>
    </div>

    @if (session()->has('message'))
        <p class="message">{{ session('message') }}</p>
    @endif

    @if ($categories->isEmpty())
        <p>No categories found.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td>
                            <div class="actions">
                                <button class="btn btn-secondary" wire:click="updateCategory({{ $category->id }})">Update</button>
                                <button class="btn btn-danger" wire:click="deleteCategory({{ $category->id }})">Delete</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>