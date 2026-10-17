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
        
        # -> Open the 'Login Sistem' page (navigate to the login page).
        await page.goto("http://localhost:8000/login")
        try:
            await page.wait_for_load_state("domcontentloaded", timeout=5000)
        except Exception:
            pass
        
        # -> Fill 'admin@polnado.ac.id' into the Email field and 'password' into the Kata Sandi field, then click the 'Masuk Sekarang' button to submit the login form.
        # Masukkan alamat email Anda email field
        elem = page.get_by_role("textbox", name="Masukkan alamat email Anda")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("admin@polnado.ac.id")
        
        # -> Fill 'admin@polnado.ac.id' into the Email field and 'password' into the Kata Sandi field, then click the 'Masuk Sekarang' button to submit the login form.
        # Masukkan kata sandi password field
        elem = page.get_by_role("textbox", name="Masukkan kata sandi")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("password")
        
        # -> Fill 'admin@polnado.ac.id' into the Email field and 'password' into the Kata Sandi field, then click the 'Masuk Sekarang' button to submit the login form.
        # Masuk Sekarang button
        elem = page.get_by_role("button", name="Masuk Sekarang")
        await elem.click(timeout=10000)
        
        # -> Open the 'Mitra Kerjasama' (Partners) admin page to manage partners.
        await page.goto("http://localhost:8000/admin/mitra")
        try:
            await page.wait_for_load_state("domcontentloaded", timeout=5000)
        except Exception:
            pass
        
        # -> Open the 'Status Akun Login' dropdown in the Filter Data Mitra panel so the options (including the one for partners without an account) become visible.
        # Open the 'Status Akun Login' dropdown in the Filter Data Mitra panel so the options (including the one for partners without an account) become visible.
        elem = page.locator(".ad-trigger > i").first
        await elem.click(timeout=10000)
        
        # -> Select the 'Belum Punya Akun' option in the 'Status Akun Login' dropdown to filter partners without an account.
        # Belum Punya Akun
        elem = page.locator("#mitraFilter_form div").filter(has_text="Belum Punya Akun").nth(4)
        await elem.click(timeout=10000)
        
        # -> Open a partner's actions menu and click the 'Kirim Akses' (Send access) action to initiate sending login access.
        # Belum Punya Akun
        elem = page.locator("td:nth-child(7)").first
        await elem.click(timeout=10000)
        
        # -> Click the 'Kirim Akses Login' button for the first partner row (PT Mitra Test Jurusan) to open the send-access modal or trigger the send action.
        # Kirim Akses Login button
        elem = page.get_by_role("row", name="1 PT Mitra Test Jurusan  -").get_by_role("button").first
        await elem.click(timeout=10000)
        
        # -> Fill 'contoh@mitra.com' into the 'Alamat Email Mitra' field and click the 'Kirim Akses' button in the 'Kirim Akses Login' modal.
        # contoh@mitra.com email field
        elem = page.get_by_role("textbox", name="Alamat Email Mitra")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("contoh@mitra.com")
        
        # -> Fill 'contoh@mitra.com' into the 'Alamat Email Mitra' field and click the 'Kirim Akses' button in the 'Kirim Akses Login' modal.
        # Kirim Akses button
        elem = page.get_by_role("button", name=" Kirim Akses")
        await elem.click(timeout=10000)
        
        # -> Scroll down the partners list to reveal the partner action buttons and any send-access confirmation message, then click the 'Kirim Akses' button if visible.
        await page.mouse.wheel(0, 300)
        
        # -> Click the 'PT Mitra Test Jurusan' row to open its actions and the 'Kirim Akses Login' modal.
        # PT Mitra Test Jurusan
        elem = page.get_by_role("cell", name="PT Mitra Test Jurusan")
        await elem.click(timeout=10000)
        
        # -> Click the 'PT Mitra Test Jurusan' row to open its actions and the 'Kirim Akses' modal, then check for the 'Kirim Akses' modal or any success confirmation text.
        # PT Mitra Test Jurusan
        elem = page.get_by_role("cell", name="PT Mitra Test Jurusan")
        await elem.click(timeout=10000)
        
        # -> Open the action menu for the partner row 'PT Mitra Test Jurusan' and bring up the 'Kirim Akses' (Send Akses) modal so the send-access action can be retried and verified.
        # 2
        elem = page.get_by_role("cell", name="2").locator("span")
        await elem.click(timeout=10000)
        
        # -> Click the 'PT Mitra Contoh' row to open its actions and bring up the 'Kirim Akses' (Send Akses) modal so the send-access flow can be retried and verified.
        # PT Mitra Contoh
        elem = page.get_by_role("cell", name="PT Mitra Contoh")
        await elem.click(timeout=10000)
        
        # -> Open the partner action menu by clicking the action icon for 'PT Mitra Contoh' so the 'Kirim Akses' option or modal can be revealed.
        # 1
        elem = page.get_by_role("cell", name="1", exact=True).locator("span")
        await elem.click(timeout=10000)
        
        # --> Assertions to verify final state
        
        # --> The partner row for PT Mitra Test Jurusan is marked as 'Terdaftar' in the Status Akun column.
        # Assert-outcome: passed
        # Assert: Partner status cell equals 'Terdaftar'.
        await expect(page.locator("xpath=/html/body/div[1]/main/div[2]/div[2]/table/tbody/tr[2]/td[7]").nth(0)).to_have_text("Terdaftar", timeout=15000), "Partner status cell equals 'Terdaftar'."
        await asyncio.sleep(5)

    finally:
        if context:
            await context.close()
        if browser:
            await browser.close()
        if pw:
            await pw.stop()

asyncio.run(run_test())
    