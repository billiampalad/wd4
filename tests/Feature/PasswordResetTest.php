<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('password_reset_tokens');
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

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function test_forgot_password_page_can_be_opened(): void
    {
        $this->get(route('password.request'))
            ->assertOk()
            ->assertSee('Lupa Kata Sandi');
    }

    public function test_reset_link_is_sent_to_a_registered_email(): void
    {
        Notification::fake();
        $user = $this->createUser();

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_unknown_email_receives_the_same_generic_response(): void
    {
        Notification::fake();

        $this->post(route('password.email'), ['email' => 'tidak-ada@example.com'])
            ->assertSessionHas(
                'status',
                'Jika email tersebut terdaftar, tautan pemulihan kata sandi telah dikirim.'
            );

        Notification::assertNothingSent();
    }

    public function test_password_can_be_reset_with_a_valid_token(): void
    {
        $user = $this->createUser();
        $token = Password::broker()->createToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'PasswordBaru123',
            'password_confirmation' => 'PasswordBaru123',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('PasswordBaru123', $user->fresh()->password));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_password_cannot_be_reset_with_an_invalid_token(): void
    {
        $user = $this->createUser();

        $this->from(route('password.reset', ['token' => 'token-salah', 'email' => $user->email]))
            ->post(route('password.update'), [
                'token' => 'token-salah',
                'email' => $user->email,
                'password' => 'PasswordBaru123',
                'password_confirmation' => 'PasswordBaru123',
            ])
            ->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('password-lama', $user->fresh()->password));
    }

    private function createUser(): User
    {
        $role = Role::create(['role_name' => 'unit_kerja']);

        return User::create([
            'nik' => '1987654321',
            'name' => 'Pengguna Uji',
            'email' => 'pengguna@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password-lama'),
            'role_id' => $role->id,
        ]);
    }
}
