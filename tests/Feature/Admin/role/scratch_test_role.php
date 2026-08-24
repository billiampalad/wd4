<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Admin\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

DB::beginTransaction();
try {
    echo "1. Testing Create Role\n";
    $controller = new RoleController();
    $request = Request::create('/admin/roles', 'POST', [
        'role_name' => 'test_role_123',
    ]);
    $request->setLaravelSession($app['session']->driver());
    $controller->store($request);
    
    echo "SUCCESS: Role created\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
} finally {
    DB::rollBack();
}
