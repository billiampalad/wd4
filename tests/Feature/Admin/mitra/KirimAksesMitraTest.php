<?php

namespace Tests\Feature\Admin\mitra;

use App\Models\Role;
use App\Models\User;
use App\Models\Mitra;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\MitraAccessLoginMail;

class KirimAksesMitraTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $this->adminUser = User::factory()->create([
            'role_id' => $adminRole->id,
            'email' => 'admin_akses_' . rand(1,999) . '@wd4.com',
        ]);
        
        // Ensure Mitra role exists
        Role::firstOrCreate(['role_name' => 'mitra'], ['name' => 'mitra', 'guard_name' => 'web']);
    }

    public function test_admin_can_send_access_login_to_mitra()
    {
        Mail::fake();

        $mitra = Mitra::create([
            'nama_mitra' => 'Mitra Untuk Akses',
            'negara' => 'Indonesia',
            'status_akses' => 'Pending'
        ]);

        $testEmail = 'mitra_' . rand(100, 999) . '@wd4.com';

        $response = $this->actingAs($this->adminUser)
            ->post(route('mitra.sendAccessLogin', $mitra->id), [
                'email' => $testEmail
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check user created
        $this->assertDatabaseHas('users', [
            'email' => $testEmail,
            'mitra_id' => $mitra->id,
        ]);

        // Check mitra status changed
        $this->assertDatabaseHas('mitras', [
            'id' => $mitra->id,
            'status_akses' => 'Aktif',
        ]);

        // Check email sent
        Mail::assertSent(MitraAccessLoginMail::class, function ($mail) use ($testEmail) {
            return $mail->hasTo($testEmail);
        });
    }

    public function test_admin_cannot_send_access_if_email_duplicate()
    {
        Mail::fake();

        $existingUser = User::factory()->create([
            'email' => 'duplicate@wd4.com'
        ]);

        $mitra = Mitra::create([
            'nama_mitra' => 'Mitra Gagal Akses',
            'negara' => 'Indonesia',
            'status_akses' => 'Pending'
        ]);

        $response = $this->actingAs($this->adminUser)
            ->post(route('mitra.sendAccessLogin', $mitra->id), [
                'email' => 'duplicate@wd4.com'
            ]);

        $response->assertSessionHasErrors('email');

        // Check user NOT created
        $this->assertDatabaseMissing('users', [
            'mitra_id' => $mitra->id,
        ]);
    }

    public function test_admin_cannot_send_access_if_email_empty()
    {
        Mail::fake();

        $mitra = Mitra::create([
            'nama_mitra' => 'Mitra Email Kosong',
            'negara' => 'Indonesia',
            'status_akses' => 'Pending'
        ]);

        $response = $this->actingAs($this->adminUser)
            ->post(route('mitra.sendAccessLogin', $mitra->id), [
                'email' => ''
            ]);

        $response->assertSessionHasErrors('email');
    }
}
