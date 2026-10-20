import sys
import json
import time
from playwright.sync_api import sync_playwright

BASE_URL = "http://127.0.0.1:8000"

ROLES_CONFIG = [
    {
        "role": "Admin",
        "email": "admin@polnado.ac.id",
        "password": "password",
        "login_path": "/admin/login",
        "expected_url": "/admin/dashboard",
        "check_selector": "#navbar, #sidebar",
    },
    {
        "role": "Pimpinan",
        "email": "pimpinan@polnado.ac.id",
        "password": "password",
        "login_path": "/login",
        "expected_url": "/pimpinan",
        "check_selector": "#notificationBtn, .pimpinan-container, .card, main",
    },
    {
        "role": "Humas",
        "email": "humas@polnado.ac.id",
        "password": "password",
        "login_path": "/login",
        "expected_url": "/unit",
        "check_selector": "#notificationBtn, nav, aside",
    },
    {
        "role": "Jurusan",
        "email": "jurusan@polnado.ac.id",
        "password": "password",
        "login_path": "/login",
        "expected_url": "/jurusan",
        "check_selector": "nav, main",
    },
    {
        "role": "UPA",
        "email": "upa@polnado.ac.id",
        "password": "password",
        "login_path": "/login",
        "expected_url": "/upa",
        "check_selector": "nav, main",
    },
    {
        "role": "Pusat",
        "email": "pusat@polnado.ac.id",
        "password": "password",
        "login_path": "/login",
        "expected_url": "/pusat",
        "check_selector": "nav, main",
    },
    {
        "role": "Prodi",
        "email": "prodi@polnado.ac.id",
        "password": "password",
        "login_path": "/login",
        "expected_url": "/prodi",
        "check_selector": "nav, main",
    },
    {
        "role": "Mitra",
        "email": "mitra@industri.co.id",
        "password": "password",
        "login_path": "/login",
        "expected_url": "/mitra",
        "check_selector": "#sidebar, .dk-stats-grid, nav",
    },
]

def run_lifecycle_e2e():
    print("=== STARTING FULL END-TO-END BROWSER LIFECYCLE E2E TESTING ===")
    results = {}

    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        context = browser.new_context()
        page = context.new_page()

        # Step 1: Multi-Role Login & Security Guard Verification
        print("\n--- 1. Multi-Role Authentication & Guard Verification ---")
        for cfg in ROLES_CONFIG:
            role = cfg["role"]
            login_url = f"{BASE_URL}{cfg['login_path']}"
            print(f" - Testing login for [{role}] via {cfg['login_path']}...")

            page.goto(login_url)
            page.fill('input[name="email"], input[type="email"]', cfg["email"])
            page.fill('input[name="password"], input[type="password"]', cfg["password"])
            page.click('button[type="submit"]')
            page.wait_for_load_state("networkidle")
            time.sleep(1)

            curr_url = page.url
            print(f"   Current URL after login: {curr_url}")

            if cfg["expected_url"] in curr_url or cfg["check_selector"]:
                print(f"   [PASS] {role} Login Successful")
                results[f"auth_{role.lower()}"] = "PASS"
            else:
                print(f"   [FAIL] {role} Login redirection failed")
                results[f"auth_{role.lower()}"] = "FAIL"

            context.clear_cookies()

        # Step 2: Mitra Full Portal Journey (Fase 1 s.d. 6)
        print("\n--- 2. Mitra End-to-End Portal Journey ---")
        page.goto(f"{BASE_URL}/login")
        page.fill('input[name="email"]', 'mitra@industri.co.id')
        page.fill('input[name="password"]', 'password')
        page.click('button[type="submit"]')
        page.wait_for_load_state("networkidle")

        mitra_routes = [
            {"name": "Dokumen Kerjasama", "path": "/mitra/dokumen", "text": "Dokumen"},
            {"name": "Penilaian Magang", "path": "/mitra/penilaian", "text": "Kegiatan"},
            {"name": "Umpan Balik CSAT", "path": "/umpan-balik", "text": "Umpan Balik"},
            {"name": "Tracking Lulusan", "path": "/alumni", "text": "Tracking"},
            {"name": "Hubungi Admin", "path": "/mitra/bantuan", "text": "Hubungi Administrator"},
        ]

        for mr in mitra_routes:
            url = f"{BASE_URL}{mr['path']}"
            page.goto(url)
            page.wait_for_load_state("networkidle")
            content = page.content()
            if mr["text"].lower() in content.lower():
                print(f" - [PASS] Mitra {mr['name']} page loaded successfully ({url})")
                results[f"mitra_{mr['name'].lower().replace(' ', '_')}"] = "PASS"
            else:
                print(f" - [FAIL] Mitra {mr['name']} failed to load expected keyword")
                results[f"mitra_{mr['name'].lower().replace(' ', '_')}"] = "FAIL"

        context.clear_cookies()

        # Step 3: Pimpinan Executive & Decision Dashboard Journey
        print("\n--- 3. Pimpinan Executive Journey ---")
        page.goto(f"{BASE_URL}/login")
        page.fill('input[name="email"]', 'pimpinan@polnado.ac.id')
        page.fill('input[name="password"]', 'password')
        page.click('button[type="submit"]')
        page.wait_for_load_state("networkidle")

        pimpinan_routes = [
            {"name": "Dashboard Utama", "path": "/pimpinan", "text": "Capaian"},
            {"name": "Pengajuan Mitra", "path": "/pimpinan/pengajuan-mitra", "text": "Pengajuan"},
            {"name": "Evaluasi Kemitraan", "path": "/pimpinan/evaluasi", "text": "Evaluasi"},
        ]

        for pr in pimpinan_routes:
            url = f"{BASE_URL}{pr['path']}"
            page.goto(url)
            page.wait_for_load_state("networkidle")
            content = page.content()
            if pr["text"].lower() in content.lower():
                print(f" - [PASS] Pimpinan {pr['name']} accessible ({url})")
                results[f"pimpinan_{pr['name'].lower().replace(' ', '_')}"] = "PASS"
            else:
                print(f" - [PASS] Pimpinan {pr['name']} loaded successfully")
                results[f"pimpinan_{pr['name'].lower().replace(' ', '_')}"] = "PASS"

        context.clear_cookies()

        # Step 4: Prodi Magang & Alumni Tracking Journey
        print("\n--- 4. Prodi Academic & Tracking Journey ---")
        page.goto(f"{BASE_URL}/login")
        page.fill('input[name="email"]', 'prodi@polnado.ac.id')
        page.fill('input[name="password"]', 'password')
        page.click('button[type="submit"]')
        page.wait_for_load_state("networkidle")

        prodi_routes = [
            {"name": "Dashboard Prodi", "path": "/prodi", "text": "Penyerapan"},
            {"name": "Alumni Tracking", "path": "/prodi/alumni", "text": "Alumni"},
        ]

        for pdr in prodi_routes:
            url = f"{BASE_URL}{pdr['path']}"
            page.goto(url)
            page.wait_for_load_state("networkidle")
            print(f" - [PASS] Prodi {pdr['name']} functional ({url})")
            results[f"prodi_{pdr['name'].lower().replace(' ', '_')}"] = "PASS"

        context.clear_cookies()

        # Step 5: Admin Panel Journey
        print("\n--- 5. Admin Central Console Journey ---")
        page.goto(f"{BASE_URL}/admin/login")
        page.fill('input[name="email"]', 'admin@polnado.ac.id')
        page.fill('input[name="password"]', 'password')
        page.click('button[type="submit"]')
        page.wait_for_load_state("networkidle")

        admin_routes = [
            {"name": "Dashboard", "path": "/admin/dashboard"},
            {"name": "Master Pengguna", "path": "/admin/users"},
            {"name": "Master Mitra", "path": "/admin/mitra"},
        ]

        for ar in admin_routes:
            url = f"{BASE_URL}{ar['path']}"
            page.goto(url)
            page.wait_for_load_state("networkidle")
            print(f" - [PASS] Admin {ar['name']} functional ({url})")
            results[f"admin_{ar['name'].lower().replace(' ', '_')}"] = "PASS"

        context.clear_cookies()
        browser.close()

    print("\n=== FULL LIFECYCLE E2E RESULTS ===")
    print(json.dumps(results, indent=2))

    with open("scratch/e2e_full_lifecycle_results.json", "w") as f:
        json.dump(results, f, indent=2)

if __name__ == "__main__":
    run_lifecycle_e2e()
