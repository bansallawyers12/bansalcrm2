<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$needles = ['celesty', 'khushi', 'ajay', 'arun', 'vipul', 'ankit', 'gurjent', 'krishna'];

foreach ($needles as $n) {
    echo "--- {$n} ---\n";
    $rows = DB::table('staff')
        ->where('status', 1)
        ->where(function ($q) use ($n) {
            $q->whereRaw('LOWER(first_name) LIKE ?', ['%' . $n . '%'])
                ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . $n . '%'])
                ->orWhereRaw('LOWER(email) LIKE ?', ['%' . $n . '%']);
        })
        ->orderBy('id')
        ->get(['id', 'first_name', 'last_name', 'email', 'role']);
    foreach ($rows as $x) {
        echo "{$x->id} | {$x->first_name} {$x->last_name} | {$x->email} | role={$x->role}\n";
    }
    echo "\n";
}
