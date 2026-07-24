<?php

use App\Models\Asset;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    $assets = Asset::with(['category', 'brand'])
        ->latest()
        ->take(5)
        ->get();

    return view('home', [
        'assets' => $assets,
        'totalAssets' => Asset::count(),
        'assignedAssets' => Asset::where('status', 'assigned')->count(),
        'maintenanceAssets' => Asset::where('status', 'maintenance')->count(),
        'availableAssets' => Asset::where('status', 'available')->count(),
    ]);
});

Route::get('/assets', function () {
    $assets = Asset::with(['category', 'brand'])
        ->latest()
        ->get();

    return view('assets', [
        'assets' => $assets,
    ]);
});
