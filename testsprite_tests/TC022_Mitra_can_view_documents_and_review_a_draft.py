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
        await page.fill('input[name="email"]', "mitra@industri.co.id")
        await page.fill('input[name="password"]', "password")
        await page.click('button[type="submit"]')
        await page.wait_for_load_state("networkidle")
        await expect(page).to_have_url(re.compile("mitra"))

        await page.goto("http://localhost:8000/mitra/dokumen")
        await page.wait_for_load_state("networkidle")

        detail_link = page.locator('a[href*="/mitra/dokumen/"]').first
        if await detail_link.count() > 0:
            await detail_link.click()
            await page.wait_for_load_state("networkidle")
            review_textarea = page.locator('textarea[name="catatan_review"]')
            if await review_textarea.count() > 0:
                await review_textarea.fill("Catatan Review Mitra: Dokumen disetujui.")
                await page.click('button:has-text("Kirim Catatan Review")')
                await page.wait_for_load_state("networkidle")

        await asyncio.sleep(2)
    finally:
        if context: await context.close()
        if browser: await browser.close()
        if pw: await pw.stop()

asyncio.run(run_test())
