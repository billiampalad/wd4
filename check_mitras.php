<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach(App\Models\Mitra::with('users')->get() as $m) {
    echo "ID: {$m->id} | Name: {$m->nama_mitra} | Users Count: {$m->users->count()} | Status: {$m->status_akses}\n";
}
