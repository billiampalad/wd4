<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LoginLockoutTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_name')->unique();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->unique();
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        $this->travelBack();

        parent::tearDown();
    }

    public function test_user_login_remains_blocked_with_correct_password_during_lockout(): void
    {
        $user = $this->createUser('unit_kerja', '1987654321', 'password-benar');

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            $this->post('/login', [
                'nik' => $user->nik,
                'password' => 'password-salah',
            ]);
        }

        $this->post('/login', [
            'nik' => $user->nik,
            'password' => 'password-benar',
        ])
            ->assertSessionHas('error')
            ->assertSessionHas('lockout_seconds');

        $this->assertGuest();

        $this->travel(61)->seconds();

        $this->post('/login', [
            'nik' => $user->nik,
            'password' => 'password-benar',
        ])->assertRedirect('/unit');

        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_login_remains_blocked_with_correct_password_during_lockout(): void
    {
        $admin = $this->createUser('admin', '120604', 'password-benar');

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            $this->post('/admin/login', [
                'nik' => $admin->nik,
                'password' => 'password-salah',
            ]);
        }

        $this->post('/admin/login', [
            'nik' => $admin->nik,
            'password' => 'password-benar',
        ])
            ->assertSessionHas('error')
            ->assertSessionHas('lockout_seconds');

        $this->assertGuest();

        $this->travel(61)->seconds();

        $this->post('/admin/login', [
            'nik' => $admin->nik,
            'password' => 'password-benar',
        ])->assertRedirect('/admin/dashboard');

        $this->assertAuthenticatedAs($admin);
    }

    private function createUser(string $roleName, string $nik, string $password): User
    {
        $role = Role::create(['role_name' => $roleName]);

        return User::create([
            'nik' => $nik,
            'name' => 'Pengguna Uji',
            'email' => $nik . '@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make($password),
            'role_id' => $role->id,
        ]);
    }
}