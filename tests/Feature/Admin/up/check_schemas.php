<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "Jurusans:\n";
print_r(Schema::getColumnListing('jurusans'));

echo "\nProdis:\n";
print_r(Schema::getColumnListing('prodis'));

echo "\nUpas:\n";
print_r(Schema::getColumnListing('upas'));

echo "\nPusats:\n";
print_r(Schema::getColumnListing('pusats'));

echo "\nUnit Kerjas (Humas):\n";
print_r(Schema::getColumnListing('unit_kerjas'));

$tables = ['jurusans', 'prodis', 'upas', 'pusats', 'unit_kerjas'];
foreach($tables as $t) {
    echo "\n$t CREATE TABLE:\n";
    echo \DB::select("SHOW CREATE TABLE $t")[0]->{'Create Table'} . "\n";
}
