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
        
        # -> Click the 'Ajukan kerja sama' button to open the public cooperation submission page.
        # Ajukan kerja sama link
        elem = page.get_by_role("link", name="Ajukan kerja sama")
        await elem.click(timeout=10000)
        
        # -> Fill the 'Nama Lembaga / Instansi / Perusahaan', 'Nomor HP/WA', and 'Alamat Kantor' fields, then click the 'Selanjutnya' button.
        # Contoh: PT Inovasi Sulawesi Utara text field
        elem = page.get_by_role("textbox", name="Nama Lembaga / Instansi /")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("PT Inovasi Sulawesi Utara")
        
        # -> Fill the 'Nama Lembaga / Instansi / Perusahaan', 'Nomor HP/WA', and 'Alamat Kantor' fields, then click the 'Selanjutnya' button.
        # Contoh: 081234567896 text field
        elem = page.get_by_role("textbox", name="Nomor HP/WA *")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("081234567896")
        
        # -> Fill the 'Nama Lembaga / Instansi / Perusahaan', 'Nomor HP/WA', and 'Alamat Kantor' fields, then click the 'Selanjutnya' button.
        # Tulis alamat lengkap, termasuk kota dan provinsi text area
        elem = page.get_by_role("textbox", name="Alamat Kantor *")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("Jalan Contoh No.1, Kota Contoh, Provinsi Contoh")
        
        # -> Fill the 'Nama Lembaga / Instansi / Perusahaan', 'Nomor HP/WA', and 'Alamat Kantor' fields, then click the 'Selanjutnya' button.
        # Selanjutnya button
        elem = page.get_by_role("button", name="Selanjutnya ")
        await elem.click(timeout=10000)
        
        # -> Open the 'Pilih klasifikasi' dropdown (label: 'Pilih klasifikasi') so a classification can be selected.
        # Pilih klasifikasi button
        elem = page.get_by_role("button", name="Pilih klasifikasi ")
        await elem.click(timeout=10000)
        
        # -> Pilih opsi 'Dunia Usaha' dari dropdown 'Pilih klasifikasi'.
        # Dunia Usaha button
        elem = page.get_by_role("listbox").get_by_role("option", name="Dunia Usaha")
        await elem.click(timeout=10000)
        
        # -> Click the 'Selanjutnya' button to proceed to the 'Kontak' step.
        # Selanjutnya button
        elem = page.get_by_role("button", name="Selanjutnya ")
        await elem.click(timeout=10000)
        
        # -> Fill the contact form fields ('Nama Penandatangan', 'Jabatan Penandatangan', 'Nama Penanggung Jawab', 'Jabatan Penanggung Jawab', 'Email') and click the 'Selanjutnya' button to proceed to the Rencana step.
        # Nama lengkap penandatangan text field
        elem = page.get_by_role("textbox", name="Nama Penandatangan *")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("Budi Santoso")
        
        # -> Fill the contact form fields ('Nama Penandatangan', 'Jabatan Penandatangan', 'Nama Penanggung Jawab', 'Jabatan Penanggung Jawab', 'Email') and click the 'Selanjutnya' button to proceed to the Rencana step.
        # Contoh: Direktur / Kepala Bagian text field
        elem = page.get_by_role("textbox", name="Jabatan Penandatangan *")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("Direktur")
        
        # -> Fill the contact form fields ('Nama Penandatangan', 'Jabatan Penandatangan', 'Nama Penanggung Jawab', 'Jabatan Penanggung Jawab', 'Email') and click the 'Selanjutnya' button to proceed to the Rencana step.
        # Nama lengkap penanggung jawab text field
        elem = page.get_by_role("textbox", name="Nama Penanggung Jawab")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("Siti Aminah")
        
        # -> Fill the contact form fields ('Nama Penandatangan', 'Jabatan Penandatangan', 'Nama Penanggung Jawab', 'Jabatan Penanggung Jawab', 'Email') and click the 'Selanjutnya' button to proceed to the Rencana step.
        # Contoh: Koordinator Kerja Sama text field
        elem = page.get_by_role("textbox", name="Jabatan Penanggung Jawab")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("Koordinator Kerja Sama")
        
        # -> Fill the contact form fields ('Nama Penandatangan', 'Jabatan Penandatangan', 'Nama Penanggung Jawab', 'Jabatan Penanggung Jawab', 'Email') and click the 'Selanjutnya' button to proceed to the Rencana step.
        # email.pic@perusahaan.com email field
        elem = page.get_by_role("textbox", name="Email *")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("contact@inovasisulut.co.id")
        
        # -> Click the 'Selanjutnya' button to proceed from the 'Kontak' step to the 'Rencana' step.
        # Selanjutnya button
        elem = page.get_by_role("button", name="Selanjutnya ")
        await elem.click(timeout=10000)
        
        # -> Fill 'Judul Rencana Kerja Sama' and 'Tujuan Kerja Sama', then click the 'Pilih ruang lingkup kegiatan' button to open scope options.
        # Contoh: Program Magang Bersertifikat dan... text field
        elem = page.get_by_role("textbox", name="Judul Rencana Kerja Sama *")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("Program Magang Bersertifikat dan Rekrutmen Lulusan")
        
        # -> Fill 'Judul Rencana Kerja Sama' and 'Tujuan Kerja Sama', then click the 'Pilih ruang lingkup kegiatan' button to open scope options.
        # Jelaskan tujuan utama, manfaat, dan hasil yang... text area
        elem = page.get_by_role("textbox", name="Tujuan Kerja Sama *")
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("Menyelenggarakan program magang bersertifikat untuk mahasiswa dan membuka jalur rekrutmen bagi lulusan yang berpotensi.")
        
        # -> Fill 'Judul Rencana Kerja Sama' and 'Tujuan Kerja Sama', then click the 'Pilih ruang lingkup kegiatan' button to open scope options.
        # Pilih ruang lingkup kegiatan button
        elem = page.get_by_role("button", name="Pilih ruang lingkup kegiatan ")
        await elem.click(timeout=10000)
        
        # -> Select the 'Magang/Praktik Kerja-Kampus Merdeka' option from the 'Pilih ruang lingkup kegiatan' list.
        # Magang/Praktik Kerja-Kampus Merdeka button
        elem = page.get_by_role("listbox").get_by_role("option", name="Magang/Praktik Kerja-Kampus")
        await elem.click(timeout=10000)
        
        # -> Click the 'Selanjutnya' button to advance to the review (Tinjau) step where the file upload and submit controls should be visible.
        # Selanjutnya button
        elem = page.get_by_role("button", name="Selanjutnya ")
        await elem.click(timeout=10000)
        
        # -> Click the 'Selanjutnya' button to advance to the 'Kirim' (submit) step so the file upload and submit controls become visible.
        # Selanjutnya button
        elem = page.get_by_role("button", name="Selanjutnya ")
        await elem.click(timeout=10000)
        
        # -> Click the visible 'Kirim Data' button to attempt submission and observe any validation or confirmation message.
        # Kirim Data button
        elem = page.get_by_role("button", name=" Kirim Data")
        await elem.click(timeout=10000)
        
        # -> Check the declaration checkbox labeled 'Saya menyatakan bahwa seluruh data pengajuan...' and then click the 'Kirim Data' button to attempt submission.
        # declaration_agree checkbox
        elem = page.get_by_role("checkbox", name="Saya menyatakan bahwa seluruh")
        await elem.click(timeout=10000)
        
        # -> Check the declaration checkbox labeled 'Saya menyatakan bahwa seluruh data pengajuan...' and then click the 'Kirim Data' button to attempt submission.
        # Kirim Data button
        elem = page.get_by_role("button", name=" Kirim Data")
        await elem.click(timeout=10000)
        
        # --> Assertions to verify final state
        
        # --> Submission confirmation notification ('Pengajuan Berhasil Dikirim!' with code "PGM-20261016-0001") is visible.
        await page.locator("xpath=/html/body/div[2]/div/div[1]").nth(0).scroll_into_view_if_needed()
        # Assert-outcome: passed
        # Assert: Submission confirmation notification is visible on the page.
        await expect(page.locator("xpath=/html/body/div[2]/div/div[1]").nth(0)).to_be_visible(timeout=15000), "Submission confirmation notification is visible on the page."
        await asyncio.sleep(5)

    finally:
        if context:
            await context.close()
        if browser:
            await browser.close()
        if pw:
            await pw.stop()

asyncio.run(run_test())
    