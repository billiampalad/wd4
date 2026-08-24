<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Admin\UserController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

DB::beginTransaction();

try {
    $controller = new UserController();
    $jurusanRole = Role::where('role_name', 'jurusan')->first();

    echo "1. Testing Store Method...\n";
    $request = Request::create('/admin/users', 'POST', [
        'name' => 'QA Test User',
        'nik' => 'QA123456789',
        'email' => 'qa.test@wd4.com',
        'password' => 'password123',
        'role_id' => $jurusanRole->id,
        'jabatan' => 'QA Tester',
        'jurusan_id' => 1
    ]);
    // Mock the session for validation to work
    $request->setLaravelSession($app['session']->driver());
    
    // Validate request
    $controller->store($request);

    $user = User::where('email', 'qa.test@wd4.com')->first();
    if ($user) {
        echo "SUCCESS: User created successfully. ID: {$user->id}\n";
    } else {
        echo "ERROR: User not created.\n";
    }

    echo "\n2. Testing Update Method...\n";
    $updateRequest = Request::create('/admin/users/' . $user->id, 'PUT', [
        'name' => 'QA Test User Updated',
        'nik' => 'QA123456789',
        'email' => 'qa.test2@wd4.com', // changed email
        'password' => '', // blank password means no change
        'role_id' => $jurusanRole->id,
        'jabatan' => 'Senior QA Tester',
        'jurusan_id' => 1
    ]);
    $updateRequest->setLaravelSession($app['session']->driver());

    $controller->update($updateRequest, $user->id);

    $user->refresh();
    if ($user->email === 'qa.test2@wd4.com' && $user->name === 'QA Test User Updated') {
        echo "SUCCESS: User updated successfully.\n";
    } else {
        echo "ERROR: User not updated properly.\n";
    }

    echo "\n3. Testing Delete Method...\n";
    $controller->destroy($user->id);

    $deletedUser = User::find($user->id);
    if (!$deletedUser) {
        echo "SUCCESS: User deleted successfully.\n";
    } else {
        echo "ERROR: User not deleted.\n";
    }
    
} catch (\Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
} finally {
    DB::rollBack();
    echo "\nTest finished.\n";
}
