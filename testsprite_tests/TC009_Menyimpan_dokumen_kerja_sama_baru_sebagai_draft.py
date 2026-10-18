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
        
        # -> Buka halaman login melalui tombol “Login Sistem”.
        # Login Sistem link
        elem = page.get_by_role("link", name="Login Sistem")
        await elem.click(timeout=10000)
        
        # -> Enter the provided account credentials and submit the “Masuk Sekarang” login form.
        # Masukkan alamat email Anda email field
        elem = page.get_by_role("textbox", name="Masukkan alamat email Anda")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("jurusan@polnado.ac.id")
        
        # -> Enter the provided account credentials and submit the “Masuk Sekarang” login form.
        # Masukkan kata sandi password field
        elem = page.get_by_role("textbox", name="Masukkan kata sandi")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("password")
        
        # -> Enter the provided account credentials and submit the “Masuk Sekarang” login form.
        # Masuk Sekarang button
        elem = page.get_by_role("button", name="Masuk Sekarang")
        await elem.click(timeout=10000)
        
        # -> Open the cooperation document creation form using “Tambah Kerjasama” and inspect its fields and upload requirements.
        # Tambah Kerjasama Buat dokumen MoU, MoA, atau IA... link
        elem = page.get_by_role("link", name=" Tambah Kerjasama Buat")
        await elem.click(timeout=10000)
        
        # -> Inspect the remaining cooperation form fields, especially the PDF upload control and draft-save options.
        await page.mouse.wheel(0, 300)
        
        await asyncio.sleep(5)

    finally:
        if context:
            await context.close()
        if browser:
            await browser.close()
        if pw:
            await pw.stop()

asyncio.run(run_test())
    