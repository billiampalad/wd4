import asyncio
import re
from playwright import async_api
from playwright.async_api import expect

async def run_test():
    pw = None
    browser = None
    context = None

    try:
        # Start a Playwright session in asynchronous mode
        pw = await async_api.async_playwright().start()

        # Launch a Chromium browser in headless mode with custom arguments
        browser = await pw.chromium.launch(
            headless=True,
            args=[
                "--window-size=1280,720",
                "--disable-dev-shm-usage",
                "--ipc=host",
                "--single-process"
            ],
        )

        # Create a new browser context (like an incognito window)
        context = await browser.new_context()
        # Wider default timeout to match the agent's DOM-stability budget;
        # auto-waiting Playwright APIs (expect, locator.wait_for) inherit this.
        context.set_default_timeout(15000)

        # Open a new page in the browser context
        page = await context.new_page()

        # Interact with the page elements to simulate user flow
        # -> navigate
        await page.goto("http://localhost:8000/")
        try:
            await page.wait_for_load_state("domcontentloaded", timeout=5000)
        except Exception:
            pass
        
        # -> Click the 'Login Sistem' link to open the login page.
        # Login Sistem link
        elem = page.get_by_role("link", name="Login Sistem")
        await elem.click(timeout=10000)
        
        # -> Click the 'Masuk Sekarang' button to submit the login form after filling email and password.
        # Masukkan alamat email Anda email field
        elem = page.get_by_role("textbox", name="Masukkan alamat email Anda")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("admin@polnado.ac.id")
        
        # -> Click the 'Masuk Sekarang' button to submit the login form after filling email and password.
        # Masukkan kata sandi password field
        elem = page.get_by_role("textbox", name="Masukkan kata sandi")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("password")
        
        # -> Click the 'Masuk Sekarang' button to submit the login form after filling email and password.
        # Masuk Sekarang button
        elem = page.get_by_role("button", name="Masuk Sekarang")
        await elem.click(timeout=10000)
        
        # -> Open the left sidebar navigation and click 'Master Data' then 'Kelola Pengguna' (User Management) to reach the users page.
        await page.mouse.wheel(0, 300)
        
        # -> Click the 'Master Data' sidebar entry, then open the 'Kelola Pengguna' (Kelola Users) page to reach the Users management view.
        # Master Data
        elem = page.locator("#dataMasterBtn")
        await elem.click(timeout=10000)
        
        # -> Click the 'Master Data' sidebar entry, then open the 'Kelola Pengguna' (Kelola Users) page to reach the Users management view.
        # Kelola Pengguna Tambah, ubah, atau hapus akun... link
        elem = page.get_by_role("link", name=" Kelola Pengguna Tambah,")
        await elem.click(timeout=10000)
        
        # -> Click the 'Tambah Pengguna' (Add User) button to open the Add User form.
        # Tambah Pengguna link
        elem = page.get_by_role("link", name="+ Tambah Pengguna")
        await elem.click(timeout=10000)
        
        # -> Click the '-- Pilih Role --' Role dropdown button to open available role options.
        # -- Pilih Role -- button
        elem = page.get_by_role("button", name="-- Pilih Role -- ")
        await elem.click(timeout=10000)
        
        # -> Select the 'Admin' option from the Role dropdown and wait for the form to update.
        # Admin button
        elem = page.get_by_role("button", name="Admin")
        await elem.click(timeout=10000)
        
        # -> Fill the 'Nama Lengkap', 'NIK/NIP', 'Email', and 'Password' fields with the test user's data, then click the 'Simpan Pengguna' button.
        # Masukkan nama lengkap text field
        elem = page.get_by_role("textbox", name=" Nama Lengkap *")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("Test User Admin")
        
        # -> Fill the 'Nama Lengkap', 'NIK/NIP', 'Email', and 'Password' fields with the test user's data, then click the 'Simpan Pengguna' button.
        # Nomor Induk Kependudukan text field
        elem = page.get_by_role("textbox", name=" NIK/NIP *")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("1234567890123456")
        
        # -> Fill the 'Nama Lengkap', 'NIK/NIP', 'Email', and 'Password' fields with the test user's data, then click the 'Simpan Pengguna' button.
        # nama@institusi.ac.id email field
        elem = page.get_by_role("textbox", name=" Email *")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("testuseradmin@example.com")
        
        # -> Fill the 'Nama Lengkap', 'NIK/NIP', 'Email', and 'Password' fields with the test user's data, then click the 'Simpan Pengguna' button.
        # Min. 8 karakter password field
        elem = page.get_by_role("textbox", name=" Password *")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("Password123!")
        
        # -> Fill the 'Nama Lengkap', 'NIK/NIP', 'Email', and 'Password' fields with the test user's data, then click the 'Simpan Pengguna' button.
        # Simpan Pengguna button
        elem = page.get_by_role("button", name=" Simpan Pengguna")
        await elem.click(timeout=10000)
        
        # --> Assertions to verify final state
        
        # --> The new user appears in the users list with email testuseradmin@example.com.
        # Assert-outcome: passed
        # Assert: Verifies the user's email 'testuseradmin@example.com' is displayed in the users table.
        await expect(page.locator("xpath=/html/body/div[1]/main/div/div[2]/table/tbody/tr[10]/td[4]").nth(0)).to_have_text("testuseradmin@example.com", timeout=15000), "Verifies the user's email 'testuseradmin@example.com' is displayed in the users table."
        await asyncio.sleep(5)

    finally:
        if context:
            await context.close()
        if browser:
            await browser.close()
        if pw:
            await pw.stop()

asyncio.run(run_test())
    