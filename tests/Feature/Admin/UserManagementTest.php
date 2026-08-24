<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use App\Models\Profile;
use App\Models\Jurusan;
use App\Models\UnitKerja;
use App\Models\Upa;
use App\Models\Pusat;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class UserManagementTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        // Create an admin user and role
        $this->adminRole = Role::firstOrCreate(['role_name' => 'admin']);
        $this->adminUser = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'email' => 'admin_test@wd4.com',
        ]);
        
        // Other roles
        $this->prodiRole = Role::firstOrCreate(['role_name' => 'prodi']);
        $this->jurusanRole = Role::firstOrCreate(['role_name' => 'jurusan']);
    }

    public function test_admin_can_view_users_list()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('users.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.layout.users');
    }

    public function test_admin_can_view_user_create_form()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('users.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.create');
    }

    public function test_admin_can_create_new_user_with_valid_data()
    {
        $jurusan = Jurusan::factory()->create();

        $userData = [
            'name' => 'New User Test',
            'nik' => '1234567890',
            'email' => 'newuser@wd4.com',
            'password' => 'password123',
            'role_id' => $this->jurusanRole->id,
            'jurusan_id' => $jurusan->id, // If role is jurusan
            'jabatan' => 'Ketua Jurusan',
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('users.store'), $userData);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@wd4.com',
            'nik' => '1234567890',
            'role_id' => $this->jurusanRole->id,
        ]);

        $user = User::where('email', 'newuser@wd4.com')->first();
        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'jurusan_id' => $jurusan->id,
            'jabatan' => 'Ketua Jurusan',
        ]);
    }

    public function test_admin_cannot_create_user_with_duplicate_email()
    {
        // Create an existing user
        $existingUser = User::factory()->create([
            'email' => 'duplicate@wd4.com',
            'nik' => '999999999',
            'role_id' => $this->prodiRole->id,
        ]);

        $userData = [
            'name' => 'Another User',
            'nik' => '888888888',
            'email' => 'duplicate@wd4.com', // Duplicate
            'password' => 'password123',
            'role_id' => $this->prodiRole->id,
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('users.store'), $userData);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_admin_can_view_edit_user_form()
    {
        $user = User::factory()->create([
            'role_id' => $this->prodiRole->id,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('users.edit', $user->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.edit');
    }

    public function test_admin_can_update_user_details()
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@wd4.com',
            'nik' => '111111111',
            'role_id' => $this->prodiRole->id,
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@wd4.com',
            'nik' => '222222222',
            'password' => '', // Empty password means don't update
            'role_id' => $this->prodiRole->id,
            'jabatan' => 'New Jabatan',
        ];

        $response = $this->actingAs($this->adminUser)
            ->put(route('users.update', $user->id), $updateData);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@wd4.com',
            'nik' => '222222222',
        ]);
        
        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'jabatan' => 'New Jabatan',
        ]);
    }

    public function test_admin_can_delete_user()
    {
        $user = User::factory()->create([
            'role_id' => $this->prodiRole->id,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->delete(route('users.destroy', $user->id));

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}
