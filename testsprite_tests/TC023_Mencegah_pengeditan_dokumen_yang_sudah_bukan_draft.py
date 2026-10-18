import asyncio
import re
from playwright import async_api
from playwright.async_api import expect

async def run_test():
    pw = None
    browser = None
    context = None
    try:
        pw = await async_api.async_playwright().start()
        browser = await pw.chromium.launch(
            headless=True,
            args=["--window-size=1280,720", "--disable-dev-shm-usage", "--ipc=host", "--single-process"]
        )
        context = await browser.new_context()
        context.set_default_timeout(15000)
        page = await context.new_page()

        await page.goto("http://localhost:8000/login")
        await page.wait_for_load_state("networkidle")
        await page.fill('input[name="email"]', "jurusan@polnado.ac.id")
        await page.fill('input[name="password"]', "password")
        await page.click('button[type="submit"]')
        await page.wait_for_load_state("networkidle")

        await page.goto("http://localhost:8000/jurusan/data-kerjasama")
        await page.wait_for_load_state("networkidle")
        content = await page.content()
        assert "Daftar" in content or "Kerjasama" in content

        await asyncio.sleep(2)
    finally:
        if context: await context.close()
        if browser: await browser.close()
        if pw: await pw.stop()

asyncio.run(run_test())
