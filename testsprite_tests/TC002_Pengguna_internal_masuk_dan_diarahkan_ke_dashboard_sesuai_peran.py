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
        
        # -> Click the 'Login Sistem' button in the header to open the login page.
        # Login Sistem link
        elem = page.get_by_role("link", name="Login Sistem")
        await elem.click(timeout=10000)
        
        # -> Fill the 'Email' field with jurusan@polnado.ac.id and the 'Kata Sandi' field with password, then click the 'Masuk Sekarang' button to submit the form.
        # Masukkan alamat email Anda email field
        elem = page.get_by_role("textbox", name="Masukkan alamat email Anda")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("jurusan@polnado.ac.id")
        
        # -> Fill the 'Email' field with jurusan@polnado.ac.id and the 'Kata Sandi' field with password, then click the 'Masuk Sekarang' button to submit the form.
        # Masukkan kata sandi password field
        elem = page.get_by_role("textbox", name="Masukkan kata sandi")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("password")
        
        # -> Fill the 'Email' field with jurusan@polnado.ac.id and the 'Kata Sandi' field with password, then click the 'Masuk Sekarang' button to submit the form.
        # Masuk Sekarang button
        elem = page.get_by_role("button", name="Masuk Sekarang")
        await elem.click(timeout=10000)
        
        # --> Assertions to verify final state
        
        # --> User is on the jurusan dashboard and the 'Beranda' dashboard controls are visible.
        # Assert-outcome: passed
        # Assert: URL contains 'jurusan', indicating the dashboard page is loaded.
        await expect(page).to_have_url(re.compile("jurusan"), timeout=15000), "URL contains 'jurusan', indicating the dashboard page is loaded."
        await page.get_by_role("link", name=" Beranda").nth(0).scroll_into_view_if_needed()
        # Assert-outcome: passed
        # Assert: The 'Beranda' sidebar link is visible, indicating the dashboard view is active.
        await expect(page.get_by_role("link", name=" Beranda").nth(0)).to_be_visible(timeout=15000), "The 'Beranda' sidebar link is visible, indicating the dashboard view is active."
        await asyncio.sleep(5)

    finally:
        if context:
            await context.close()
        if browser:
            await browser.close()
        if pw:
            await pw.stop()

asyncio.run(run_test())
    