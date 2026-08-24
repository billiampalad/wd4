<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginMultiRoleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    /**
     * Uji halaman login dapat diakses publik (UC36 - Langkah 1).
     */
    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('login', false);
    }

    /**
     * Uji autentikasi dan redirect untuk seluruh 8 role aktor sistem WD4 menggunakan email.
     */
    public function test_all_8_roles_can_login_with_email_and_redirect_to_correct_dashboard(): void
    {
        $roleTestCases = [
            [
                'role' => 'admin',
                'email' => 'admin@polnado.ac.id',
                'expected_redirect' => '/admin',
                'name' => 'Akun Admin',
            ],
            [
                'role' => 'pimpinan',
                'email' => 'pimpinan@polnado.ac.id',
                'expected_redirect' => '/pimpinan',
                'name' => 'Akun Pimpinan',
            ],
            [
                'role' => 'humas',
                'email' => 'humas@polnado.ac.id',
                'expected_redirect' => '/unit',
                'name' => 'Akun Humas / Unit Kerja',
            ],
            [
                'role' => 'jurusan',
                'email' => 'jurusan@polnado.ac.id',
                'expected_redirect' => '/jurusan',
                'name' => 'Akun Jurusan',
            ],
            [
                'role' => 'upa',
                'email' => 'upa@polnado.ac.id',
                'expected_redirect' => '/upa',
                'name' => 'Akun UPA',
            ],
            [
                'role' => 'pusat',
                'email' => 'pusat@polnado.ac.id',
                'expected_redirect' => '/pusat',
                'name' => 'Akun Pusat',
            ],
            [
                'role' => 'prodi',
                'email' => 'prodi@polnado.ac.id',
                'expected_redirect' => '/prodi',
                'name' => 'Akun Prodi',
            ],
            [
                'role' => 'mitra',
                'email' => 'mitra@industri.co.id',
                'expected_redirect' => '/mitra',
                'name' => 'Akun Mitra',
            ],
        ];

        foreach ($roleTestCases as $case) {
            // Pastikan user ada di DB
            $user = User::where('email', $case['email'])->first();
            $this->assertNotNull($user, "User dengan email {$case['email']} untuk role {$case['role']} harus ada di database.");

            // Lakukan request login dengan email
            $response = $this->post('/login', [
                'email' => $case['email'],
                'password' => 'password',
            ]);

            // Validasi status redirect 302 dan URL target
            $response->assertStatus(302);
            $response->assertRedirect($case['expected_redirect']);
            $response->assertSessionHas('success', 'Berhasil masuk ke sistem.');

            // Validasi status authenticated
            $this->assertAuthenticatedAs($user);

            // Logout untuk iterasi berikutnya
            $this->post('/logout');
            $this->assertGuest();
        }
    }

    /**
     * Uji akses dashboard masing-masing role setelah login.
     */
    public function test_each_role_can_access_their_own_dashboard(): void
    {
        $dashboards = [
            ['email' => 'admin@polnado.ac.id', 'url' => '/admin/dashboard'],
            ['email' => 'pimpinan@polnado.ac.id', 'url' => '/pimpinan'],
            ['email' => 'humas@polnado.ac.id', 'url' => '/unit'],
            ['email' => 'jurusan@polnado.ac.id', 'url' => '/jurusan'],
            ['email' => 'upa@polnado.ac.id', 'url' => '/upa'],
            ['email' => 'pusat@polnado.ac.id', 'url' => '/pusat'],
            ['email' => 'prodi@polnado.ac.id', 'url' => '/prodi'],
            ['email' => 'mitra@industri.co.id', 'url' => '/mitra'],
        ];

        foreach ($dashboards as $d) {
            $user = User::where('email', $d['email'])->firstOrFail();

            $response = $this->actingAs($user)->get($d['url']);
            $response->assertStatus(200);
        }
    }

    /**
     * Uji Role Middleware: Mencegah akses silang antar role (RBAC Protection).
     */
    public function test_role_middleware_prevents_unauthorized_cross_role_access(): void
    {
        // 1. User Jurusan mencoba buka Dashboard Pimpinan -> Harus dilarang & redirect ke dashboard jurusan
        $jurusanUser = User::where('email', 'jurusan@polnado.ac.id')->firstOrFail();
        $response = $this->actingAs($jurusanUser)->get('/pimpinan');
        $response->assertRedirect('/jurusan');
        $response->assertSessionHas('error', 'Anda tidak memiliki akses ke halaman tersebut.');

        // 2. User Humas mencoba buka Dashboard Admin -> Harus dilarang & redirect ke dashboard unit
        $humasUser = User::where('email', 'humas@polnado.ac.id')->firstOrFail();
        $response = $this->actingAs($humasUser)->get('/admin/dashboard');
        $response->assertRedirect('/unit');
        $response->assertSessionHas('error', 'Anda tidak memiliki akses ke halaman tersebut.');

        // 3. User UPA mencoba buka Dashboard Jurusan -> Harus dilarang & redirect ke dashboard upa
        $upaUser = User::where('email', 'upa@polnado.ac.id')->firstOrFail();
        $response = $this->actingAs($upaUser)->get('/jurusan');
        $response->assertRedirect('/upa');
        $response->assertSessionHas('error', 'Anda tidak memiliki akses ke halaman tersebut.');

        // 4. User Mitra mencoba buka Dashboard Pusat -> Harus dilarang & redirect ke dashboard mitra
        $mitraUser = User::where('email', 'mitra@industri.co.id')->firstOrFail();
        $response = $this->actingAs($mitraUser)->get('/pusat');
        $response->assertRedirect('/mitra');
        $response->assertSessionHas('error', 'Anda tidak memiliki akses ke halaman tersebut.');

        // 5. User Prodi mencoba buka Dashboard Pimpinan -> Harus dilarang & redirect ke dashboard prodi
        $prodiUser = User::where('email', 'prodi@polnado.ac.id')->firstOrFail();
        $response = $this->actingAs($prodiUser)->get('/pimpinan');
        $response->assertRedirect('/prodi');
        $response->assertSessionHas('error', 'Anda tidak memiliki akses ke halaman tersebut.');
    }

    /**
     * Uji skenario negatif: Login dengan password salah.
     */
    public function test_login_with_invalid_password_returns_error(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@polnado.ac.id',
            'password' => 'password-yang-salah-123',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    /**
     * Uji skenario negatif: Format email tidak valid.
     */
    public function test_login_with_invalid_email_format_returns_validation_error(): void
    {
        $response = $this->post('/login', [
            'email' => 'bukan-email-valid',
            'password' => 'password',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * Uji proteksi tamu: Pengguna yang belum login diarahkan ke halaman login.
     */
    public function test_guest_is_redirected_to_login_when_accessing_dashboards(): void
    {
        $protectedUrls = [
            '/pimpinan',
            '/jurusan',
            '/unit',
            '/upa',
            '/pusat',
            '/prodi',
            '/mitra',
            '/admin/dashboard',
        ];

        foreach ($protectedUrls as $url) {
            $response = $this->get($url);
            $response->assertRedirect('/login');
        }
    }
}
