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
        
        # -> Open the specified admin sign-in page and inspect its visible login form.
        await page.goto("http://127.0.0.1:8000/admin/login")
        try:
            await page.wait_for_load_state("domcontentloaded", timeout=5000)
        except Exception:
            pass
        
        # -> Enter the supplied administrator credentials and submit the 'Masuk ke Sistem' form.
        # Masukkan Email Administrator email field
        elem = page.get_by_role("textbox", name="Email Administrator")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("admin@polnado.ac.id")
        
        # -> Enter the supplied administrator credentials and submit the 'Masuk ke Sistem' form.
        # Masukkan kata sandi password field
        elem = page.get_by_role("textbox", name="Kata Sandi")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("password")
        
        # -> Enter the supplied administrator credentials and submit the 'Masuk ke Sistem' form.
        # Masuk ke Sistem button
        elem = page.get_by_role("button", name="Masuk ke Sistem")
        await elem.click(timeout=10000)
        
        # -> Open the “Jenis Kerjasama” management page from the admin navigation.
        # Jenis Kerjasama link
        elem = page.get_by_role("link", name=" Jenis Kerjasama")
        await elem.click(timeout=10000)
        
        # -> Open the “Tambah Jenis” form to inspect the cooperation-type fields.
        # Tambah Jenis button
        elem = page.get_by_role("button", name="+ Tambah Jenis")
        await elem.click(timeout=10000)
        
        # -> Enter “Kerja Sama Riset” and its supplied description in the add form, then save the new cooperation type.
        # Contoh: Magang, Penelitian, dsb text field
        elem = page.get_by_role("textbox", name=" Nama Jenis Kerjasama *")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("Kerja Sama Riset")
        
        # -> Enter “Kerja Sama Riset” and its supplied description in the add form, then save the new cooperation type.
        # Masukkan keterangan atau deskripsi bentuk... text area
        elem = page.get_by_role("textbox", name=" Keterangan")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("Jenis kerja sama untuk riset kolaboratif")
        
        # -> Enter “Kerja Sama Riset” and its supplied description in the add form, then save the new cooperation type.
        # Simpan Jenis button
        elem = page.get_by_role("button", name=" Simpan Jenis")
        await elem.click(timeout=10000)
        
        # -> Search the cooperation-type list for “Kerja Sama Riset” and verify the new entry appears.
        # Cari jenis kerjasama text field
        elem = page.get_by_role("textbox", name="Cari jenis kerjasama...")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("Kerja Sama Riset")
        
        # --> Assertions to verify final state
        
        # --> The new cooperation type “Kerja Sama Riset” appears in the list.
        # Assert-outcome: not verified — the export-time check could not be evaluated
        # Assert: The new cooperation type appears in the list.
        await expect(page.get_by_role("mark").nth(0)).to_have_text("Kerja Sama Riset", timeout=15000), "The new cooperation type appears in the list."
        await asyncio.sleep(5)

    finally:
        if context:
            await context.close()
        if browser:
            await browser.close()
        if pw:
            await pw.stop()

asyncio.run(run_test())
    