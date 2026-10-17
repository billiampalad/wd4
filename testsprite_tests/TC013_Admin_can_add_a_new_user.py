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
        
        # -> Open the sign-in page using the visible “Login Sistem” link.
        # Login Sistem link
        elem = page.get_by_role("link", name="Login Sistem")
        await elem.click(timeout=10000)
        
        # -> Enter the provided admin email and password in the sign-in form and submit it.
        # Masukkan alamat email Anda email field
        elem = page.get_by_role("textbox", name="Masukkan alamat email Anda")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("admin@polnado.ac.id")
        
        # -> Enter the provided admin email and password in the sign-in form and submit it.
        # Masukkan kata sandi password field
        elem = page.get_by_role("textbox", name="Masukkan kata sandi")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("password")
        
        # -> Enter the provided admin email and password in the sign-in form and submit it.
        # Masuk Sekarang button
        elem = page.get_by_role("button", name="Masuk Sekarang")
        await elem.click(timeout=10000)
        
        # -> Open the specified administrator sign-in page at /admin/login.
        await page.goto("http://127.0.0.1:8000/admin/login")
        try:
            await page.wait_for_load_state("domcontentloaded", timeout=5000)
        except Exception:
            pass
        
        # -> Enter the supplied administrator email and password, then submit the administrator sign-in form.
        # Masukkan Email Administrator email field
        elem = page.get_by_role("textbox", name="Email Administrator")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("admin@polnado.ac.id")
        
        # -> Enter the supplied administrator email and password, then submit the administrator sign-in form.
        # Masukkan kata sandi password field
        elem = page.get_by_role("textbox", name="Kata Sandi")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("password")
        
        # -> Enter the supplied administrator email and password, then submit the administrator sign-in form.
        # Masuk ke Sistem button
        elem = page.get_by_role("button", name="Masuk ke Sistem")
        await elem.click(timeout=10000)
        
        # -> Open the “Pengguna” user-management page and inspect its user list and creation controls.
        # Pengguna link
        elem = page.get_by_role("link", name="Pengguna", exact=True)
        await elem.click(timeout=10000)
        
        # -> Open the “Tambah Pengguna” form to inspect the fields and available roles.
        # Tambah Pengguna link
        elem = page.get_by_role("link", name="+ Tambah Pengguna")
        await elem.click(timeout=10000)
        
        # -> Inspect the available roles in the “Role” selector before completing the new-user form.
        # -- Pilih Role -- button
        elem = page.get_by_role("button", name="-- Pilih Role -- ")
        await elem.click(timeout=10000)
        
        # -> Select the visible “Admin” role and inspect the profile fields that appear.
        # Admin button
        elem = page.get_by_role("button", name="Admin")
        await elem.click(timeout=10000)
        
        # -> Fill the required identity, password, and Jabatan fields with distinct test-user details, then save the new user.
        # Masukkan nama lengkap text field
        elem = page.get_by_role("textbox", name=" Nama Lengkap *")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("Test User Admin QA")
        
        # -> Fill the required identity, password, and Jabatan fields with distinct test-user details, then save the new user.
        # Nomor Induk Kependudukan text field
        elem = page.get_by_role("textbox", name=" NIK/NIP *")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("9876543210987654")
        
        # -> Fill the required identity, password, and Jabatan fields with distinct test-user details, then save the new user.
        # nama@institusi.ac.id email field
        elem = page.get_by_role("textbox", name=" Email *")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("testuseradminqa@example.com")
        
        # -> Fill the required identity, password, and Jabatan fields with distinct test-user details, then save the new user.
        # Min. 8 karakter password field
        elem = page.get_by_role("textbox", name=" Password *")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("Password123!")
        
        # -> Fill the required identity, password, and Jabatan fields with distinct test-user details, then save the new user.
        # Contoh: Direktur text field
        elem = page.get_by_role("textbox", name=" Jabatan")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("Koordinator QA")
        
        # -> Save the completed new-user form and verify the new user appears in the user list.
        # Simpan Pengguna button
        elem = page.get_by_role("button", name=" Simpan Pengguna")
        await elem.click(timeout=10000)
        
        # --> Assertions to verify final state
        
        # --> The newly created user appears in the user list with the submitted email address.
        # Assert-outcome: not verified — the export-time check could not be evaluated
        # Assert: The user list includes the newly created account with the submitted email address.
        await expect(page.locator("tbody").nth(0)).to_contain_text("testuseradminqa@example.com", timeout=15000), "The user list includes the newly created account with the submitted email address."
        await asyncio.sleep(5)

    finally:
        if context:
            await context.close()
        if browser:
            await browser.close()
        if pw:
            await pw.stop()

asyncio.run(run_test())
    