<?php

use App\Models\Group;
use App\Services\GroupExportService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/debug-group-report/{group}', function (Group $group) {
    // Load relationships to avoid N+1 queries
    $group->load(['transactions.type', 'transactions.account', 'transactions.entity']);

    $service = new GroupExportService;

    // Use reflection to access the private method for testing
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('calculateGroupStatistics');
    $method->setAccessible(true);
    $stats = $method->invoke($service, $group);

    // Prepare data for template (same as in the service)
    $data = [
        'group' => $group,
        'stats' => $stats,
        'transactions' => $group->transactions()->with(['type', 'account', 'entity'])->orderBy('date', 'desc')->get(),
        'generated_at' => now(),
    ];

    return view('exports.group-report', $data);
})->name('debug.group.report');
