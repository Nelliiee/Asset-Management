<div class="card">
    <div class="table-header">
        <h1>Brands</h1>
        <a class="btn btn-primary" href="{{ url('/admin/brands') }}">Add Brand</a>
    </div>

    @if (session()->has('message'))
        <p class="message">{{ session('message') }}</p>
    @endif

    @if ($brands->isEmpty())
        <p>No brands found.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Brand Code</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($brands as $brand)
                    <tr>
                        <td>{{ $brand->slug }}</td>
                        <td>{{ $brand->name }}</td>
                        <td>{{ $brand->description }}</td>
                        <td>
                            <div class="actions">
                                <button class="btn btn-secondary" wire:click="updateBrand({{ $brand->id }})">Update</button>
                                <button class="btn btn-danger" wire:click="deleteBrand({{ $brand->id }})">Delete</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>