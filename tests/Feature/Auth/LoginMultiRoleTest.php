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
     * Uji autentikasi dan redirect untuk seluruh 8 role aktor sistem WD4.
     */
    public function test_all_8_roles_can_login_and_redirect_to_correct_dashboard(): void
    {
        $roleTestCases = [
            [
                'role' => 'admin',
                'nik' => '120604',
                'expected_redirect' => '/admin',
                'name' => 'Akun Admin',
            ],
            [
                'role' => 'pimpinan',
                'nik' => '012460',
                'expected_redirect' => '/pimpinan',
                'name' => 'Akun Pimpinan',
            ],
            [
                'role' => 'humas',
                'nik' => '123456',
                'expected_redirect' => '/unit',
                'name' => 'Akun Humas / Unit Kerja',
            ],
            [
                'role' => 'jurusan',
                'nik' => '222222',
                'expected_redirect' => '/jurusan',
                'name' => 'Akun Jurusan',
            ],
            [
                'role' => 'upa',
                'nik' => '121206',
                'expected_redirect' => '/upa',
                'name' => 'Akun UPA',
            ],
            [
                'role' => 'pusat',
                'nik' => '3333333',
                'expected_redirect' => '/pusat',
                'name' => 'Akun Pusat',
            ],
            [
                'role' => 'prodi',
                'nik' => '4444444',
                'expected_redirect' => '/prodi',
                'name' => 'Akun Prodi',
            ],
            [
                'role' => 'mitra',
                'nik' => '5555555',
                'expected_redirect' => '/mitra',
                'name' => 'Akun Mitra',
            ],
        ];

        foreach ($roleTestCases as $case) {
            // Pastikan user ada di DB
            $user = User::where('nik', $case['nik'])->first();
            $this->assertNotNull($user, "User dengan NIK {$case['nik']} untuk role {$case['role']} harus ada di database.");

            // Lakukan request login
            $response = $this->post('/login', [
                'nik' => $case['nik'],
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
            ['nik' => '120604', 'url' => '/admin/dashboard'],
            ['nik' => '012460', 'url' => '/pimpinan'],
            ['nik' => '123456', 'url' => '/unit'],
            ['nik' => '222222', 'url' => '/jurusan'],
            ['nik' => '121206', 'url' => '/upa'],
            ['nik' => '3333333', 'url' => '/pusat'],
            ['nik' => '4444444', 'url' => '/prodi'],
            ['nik' => '5555555', 'url' => '/mitra'],
        ];

        foreach ($dashboards as $d) {
            $user = User::where('nik', $d['nik'])->firstOrFail();

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
        $jurusanUser = User::where('nik', '222222')->firstOrFail();
        $response = $this->actingAs($jurusanUser)->get('/pimpinan');
        $response->assertRedirect('/jurusan');
        $response->assertSessionHas('error', 'Anda tidak memiliki akses ke halaman tersebut.');

        // 2. User Humas mencoba buka Dashboard Admin -> Harus dilarang & redirect ke dashboard unit
        $humasUser = User::where('nik', '123456')->firstOrFail();
        $response = $this->actingAs($humasUser)->get('/admin/dashboard');
        $response->assertRedirect('/unit');
        $response->assertSessionHas('error', 'Anda tidak memiliki akses ke halaman tersebut.');

        // 3. User UPA mencoba buka Dashboard Jurusan -> Harus dilarang & redirect ke dashboard upa
        $upaUser = User::where('nik', '121206')->firstOrFail();
        $response = $this->actingAs($upaUser)->get('/jurusan');
        $response->assertRedirect('/upa');
        $response->assertSessionHas('error', 'Anda tidak memiliki akses ke halaman tersebut.');

        // 4. User Mitra mencoba buka Dashboard Pusat -> Harus dilarang & redirect ke dashboard mitra
        $mitraUser = User::where('nik', '5555555')->firstOrFail();
        $response = $this->actingAs($mitraUser)->get('/pusat');
        $response->assertRedirect('/mitra');
        $response->assertSessionHas('error', 'Anda tidak memiliki akses ke halaman tersebut.');

        // 5. User Prodi mencoba buka Dashboard Pimpinan -> Harus dilarang & redirect ke dashboard prodi
        $prodiUser = User::where('nik', '4444444')->firstOrFail();
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
            'nik' => '120604',
            'password' => 'password-yang-salah-123',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('error');
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
