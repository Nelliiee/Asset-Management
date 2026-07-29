<div class="card">
    <div class="table-header">
        <h1>Assets</h1>
        <a class="btn btn-primary" href="{{ url('/admin/assets') }}">Add Asset</a>
    </div>

    @if (session()->has('message'))
        <p class="message">{{ session('message') }}</p>
    @endif

    @if ($assets->isEmpty())
        <p>No assets found.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Asset Code</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($assets as $asset)
                    <tr>
                        <td>{{ $asset->asset_code }}</td>
                        <td>{{ $asset->name }}</td>
                        <td><span class="badge">{{ ucfirst($asset->status) }}</span></td>
                        <td>{{ $asset->category?->name ?? 'Uncategorized' }}</td>
                        <td>{{ $asset->brand?->name ?? 'No brand' }}</td>
                        <td>
                            <div class="actions">
                                <button class="btn btn-secondary" wire:click="updateAsset({{ $asset->id }})">Update</button>
                                <button class="btn btn-danger" wire:click="deleteAsset({{ $asset->id }})">Delete</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
