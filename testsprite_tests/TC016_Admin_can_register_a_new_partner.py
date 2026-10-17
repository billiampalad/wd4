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
        
        # -> Click the 'Login Sistem' link in the page header to open the login page.
        # Login Sistem link
        elem = page.get_by_role("link", name="Login Sistem")
        await elem.click(timeout=10000)
        
        # -> Fill the Email field with 'admin@polnado.ac.id', fill the Kata Sandi field with 'password', then click the 'Masuk Sekarang' button.
        # Masukkan alamat email Anda email field
        elem = page.get_by_role("textbox", name="Masukkan alamat email Anda")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("admin@polnado.ac.id")
        
        # -> Fill the Email field with 'admin@polnado.ac.id', fill the Kata Sandi field with 'password', then click the 'Masuk Sekarang' button.
        # Masukkan kata sandi password field
        elem = page.get_by_role("textbox", name="Masukkan kata sandi")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("password")
        
        # -> Fill the Email field with 'admin@polnado.ac.id', fill the Kata Sandi field with 'password', then click the 'Masuk Sekarang' button.
        # Masuk Sekarang button
        elem = page.get_by_role("button", name="Masuk Sekarang")
        await elem.click(timeout=10000)
        
        # -> Click the 'Mitra Kerjasama' navigation link in the sidebar to open the Mitra management page.
        # Mitra Kerjasama link
        elem = page.get_by_role("link", name=" Mitra Kerjasama")
        await elem.click(timeout=10000)
        
        # -> Click the 'Tambah Mitra' button to open the add-partner form.
        # Tambah Mitra link
        elem = page.get_by_role("link", name="+ Tambah Mitra")
        await elem.click(timeout=10000)
        
        # -> Open the 'Klasifikasi Mitra' dropdown to reveal classification options
        # - Pilih Klasifikasi -
        elem = page.get_by_role("main").locator("form div").filter(has_text="- Pilih Klasifikasi -").nth(4)
        await elem.click(timeout=10000)
        
        # -> Select the 'Dunia Usaha' option from the Klasifikasi Mitra dropdown.
        # Dunia Usaha
        elem = page.get_by_text("Dunia Usaha")
        await elem.click(timeout=10000)
        
        # -> Fill the 'Nama Instansi' field with 'PT Mitra Contoh', fill 'Alamat' with 'Jalan Contoh 1', fill 'Nomor Telepon' with '081234567890', fill 'Website' with 'https://mitra.contoh.example', then click the 'Simpan Mitra' button to save the pa...
        # Masukkan nama instansi/mitra text field
        elem = page.get_by_role("textbox", name="Masukkan nama instansi/mitra")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("PT Mitra Contoh")
        
        # -> Fill the 'Nama Instansi' field with 'PT Mitra Contoh', fill 'Alamat' with 'Jalan Contoh 1', fill 'Nomor Telepon' with '081234567890', fill 'Website' with 'https://mitra.contoh.example', then click the 'Simpan Mitra' button to save the pa...
        # Masukkan alamat lengkap mitra... text area
        elem = page.get_by_role("textbox", name="Masukkan alamat lengkap mitra")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("Jalan Contoh 1")
        
        # -> Fill the 'Nama Instansi' field with 'PT Mitra Contoh', fill 'Alamat' with 'Jalan Contoh 1', fill 'Nomor Telepon' with '081234567890', fill 'Website' with 'https://mitra.contoh.example', then click the 'Simpan Mitra' button to save the pa...
        # Contoh: 021-12345678 text field
        elem = page.get_by_role("textbox", name="Contoh: 021-")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("081234567890")
        
        # -> Fill the 'Nama Instansi' field with 'PT Mitra Contoh', fill 'Alamat' with 'Jalan Contoh 1', fill 'Nomor Telepon' with '081234567890', fill 'Website' with 'https://mitra.contoh.example', then click the 'Simpan Mitra' button to save the pa...
        # https://www.example.com text field
        elem = page.get_by_role("textbox", name="https://www.example.com")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("https://mitra.contoh.example")
        
        # -> Fill the 'Nama Instansi' field with 'PT Mitra Contoh', fill 'Alamat' with 'Jalan Contoh 1', fill 'Nomor Telepon' with '081234567890', fill 'Website' with 'https://mitra.contoh.example', then click the 'Simpan Mitra' button to save the pa...
        # Simpan Mitra button
        elem = page.get_by_role("button", name=" Simpan Mitra")
        await elem.click(timeout=10000)
        
        # -> Click the 'Mitra Kerjasama' link in the sidebar to open the mitra list and verify 'PT Mitra Contoh' appears.
        # Mitra Kerjasama link
        elem = page.get_by_role("link", name=" Mitra Kerjasama")
        await elem.click(timeout=10000)
        
        # --> Assertions to verify final state
        
        # --> The Mitra list shows the newly added partner 'PT Mitra Contoh'.
        # Assert-outcome: passed
        # Assert: Mitra list contains the partner name 'PT Mitra Contoh'.
        await expect(page.locator("xpath=/html/body/div[1]/main/div[2]/div[2]/table/tbody/tr[1]/td[2]").nth(0)).to_have_text("PT Mitra Contoh", timeout=15000), "Mitra list contains the partner name 'PT Mitra Contoh'."
        await asyncio.sleep(5)

    finally:
        if context:
            await context.close()
        if browser:
            await browser.close()
        if pw:
            await pw.stop()

asyncio.run(run_test())
    