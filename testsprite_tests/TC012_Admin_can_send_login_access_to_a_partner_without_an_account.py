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
        
        # -> Click the 'Login Sistem' link in the header to open the login page.
        # Login Sistem link
        elem = page.get_by_role("link", name="Login Sistem")
        await elem.click(timeout=10000)
        
        # -> Fill the 'Email' field with admin@polnado.ac.id, fill the 'Kata Sandi' field with password, and click the 'Masuk Sekarang' button to submit the login form.
        # Masukkan alamat email Anda email field
        elem = page.get_by_role("textbox", name="Masukkan alamat email Anda")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("admin@polnado.ac.id")
        
        # -> Fill the 'Email' field with admin@polnado.ac.id, fill the 'Kata Sandi' field with password, and click the 'Masuk Sekarang' button to submit the login form.
        # Masukkan kata sandi password field
        elem = page.get_by_role("textbox", name="Masukkan kata sandi")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("password")
        
        # -> Fill the 'Email' field with admin@polnado.ac.id, fill the 'Kata Sandi' field with password, and click the 'Masuk Sekarang' button to submit the login form.
        # Masuk Sekarang button
        elem = page.get_by_role("button", name="Masuk Sekarang")
        await elem.click(timeout=10000)
        
        # -> Open the 'Mitra' (Partners) admin page to view the partners list and controls
        await page.goto("http://localhost:8000/admin/mitra")
        try:
            await page.wait_for_load_state("domcontentloaded", timeout=5000)
        except Exception:
            pass
        
        # -> Open the 'Semua Status Akun' dropdown in the 'Filter Data Mitra' panel to reveal account-status filter options.
        # Semua Status Akun
        elem = page.locator(".ad-trigger-content").first
        await elem.click(timeout=10000)
        
        # -> Select the 'Belum Punya Akun' option from the 'Status Akun Login' dropdown to filter partners without an account.
        # Belum Punya Akun
        elem = page.locator("#mitraFilter_form").get_by_text("Belum Punya Akun")
        await elem.click(timeout=10000)
        
        # -> Open the partner's actions menu for the first listed partner and click the 'Kirim Akses' (Send login access) option.
        # Open the partner's actions menu for the first listed partner and click the 'Kirim Akses' (Send login access) option.
        elem = page.get_by_role("row", name="1 PT Mitra Contoh  Indonesia").locator("i").nth(1)
        await elem.click(timeout=10000)
        
        # -> Click the 'Kirim Akses' (Send login access) button for the first partner row (PT Mitra Contoh) to open the access modal.
        # Kirim Akses Login button
        elem = page.get_by_role("row", name="1 PT Mitra Contoh  Indonesia").get_by_role("button").first
        await elem.click(timeout=10000)
        
        # -> Fill the 'Alamat Email Mitra' field with 'contoh@mitra.com' and click the 'Kirim Akses' button to send access.
        # contoh@mitra.com email field
        elem = page.get_by_role("textbox", name="Alamat Email Mitra")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("contoh@mitra.com")
        
        # -> Fill the 'Alamat Email Mitra' field with 'contoh@mitra.com' and click the 'Kirim Akses' button to send access.
        # Kirim Akses button
        elem = page.get_by_role("button", name=" Kirim Akses")
        await elem.click(timeout=10000)
        
        # --> Assertions to verify final state
        
        # --> A success confirmation message should have appeared after sending access.
        # Assert-outcome: failed
        # Assert: Expected to see a success confirmation message 'Berhasil' after sending access.
        await expect(page.locator("tbody").nth(0)).to_contain_text("Berhasil", timeout=15000), "Expected to see a success confirmation message 'Berhasil' after sending access."
        
        # --> The partner row should have been updated to indicate access was sent.
        # Assert-outcome: failed
        # Assert: Expected the partner's Status Akun cell to show 'Terdaftar' indicating access was sent.
        await expect(page.locator("xpath=/html/body/div[1]/main/div[2]/div[2]/table/tbody/tr[1]/td[7]").nth(0)).to_have_text("Terdaftar", timeout=15000), "Expected the partner's Status Akun cell to show 'Terdaftar' indicating access was sent."
        await asyncio.sleep(5)

    finally:
        if context:
            await context.close()
        if browser:
            await browser.close()
        if pw:
            await pw.stop()

asyncio.run(run_test())
    