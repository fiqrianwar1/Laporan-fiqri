export default async function run(page, ui) {
  // Login pakai akun manajer yang sudah ada, lalu cek dashboard + sidebar.
  await page.goto('http://127.0.0.1:8123/login', { waitUntil: 'domcontentloaded' })
  const snap = await ui.snapshot()
  const boxes = [...snap.matchAll(/@(e\d+) textbox/g)].map(m => m[1])
  if (boxes.length < 2) return { error: 'field login tidak ditemukan', snapshot: snap }

  await ui.fill(boxes[0], 'manajer@warnatanjungjaya.com')
  await ui.fill(boxes[1], 'password')
  await page.keyboard.press('Enter')
  await page.waitForTimeout(1500)

  await page.setViewportSize({ width: 1440, height: 1100 })
  const res = await page.goto('http://127.0.0.1:8123/dashboard', { waitUntil: 'domcontentloaded' })
  await page.waitForTimeout(700)
  await page.evaluate(() => document.body.classList.remove('is-collapsed'))
  await page.waitForTimeout(400)

  const isi = await page.evaluate(() => ({
    h1: document.querySelector('h1')?.textContent?.trim(),
    periode: document.querySelector('h1')?.parentElement?.querySelector('p')?.textContent?.replace(/[ ]+/g, ').trim(),
    bulanIni: [...document.querySelectorAll('.card')].find(c => c.querySelector('.card-title')?.textContent.includes('Bulan Ini'))?.querySelector('.card-sub')?.textContent?.trim(),
      bulanIni: [...document.querySelectorAll('.card')].find(c => c.querySelector('.card-title')?.textContent.includes('Bulan Ini'))?.querySelector('.card-sub')?.textContent?.trim(),
      dashboardDiSidebar: [...document.querySelectorAll('#app-sidebar nav a .side-full span:first-child')].map(el => el.textContent.trim()),
      bottomNav: [...document.querySelectorAll('.bottom-nav a')].map(el => el.textContent.trim()),
      paletteAdaDashboard: document.getElementById('palette-list')?.textContent?.includes('Dashboard') ?? false,
      aksiCepatRail: document.querySelectorAll('#app-sidebar nav .side-rail').length,
      errorBanner: document.body.innerText.includes('Whoops') || document.body.innerText.includes('Exception'),
  }))

  await page.screenshot({ path: 'storage/app/qa-open.png' })
  await page.click('[data-sidebar-toggle]')
  await page.waitForTimeout(500)
  await page.screenshot({ path: 'storage/app/qa-collapsed.png' })

  const rail = await page.evaluate(() => ({
    lebar: document.getElementById('app-sidebar').getBoundingClientRect().width,
    labelTersembunyi: [...document.querySelectorAll('#app-sidebar .side-full')].every(el => getComputedStyle(el).display === 'none'),
    railTampil: [...document.querySelectorAll('#app-sidebar .side-rail')].filter(el => getComputedStyle(el).display === 'flex').length,
    paddingKonten: getComputedStyle(document.getElementById('app-content')).paddingLeft,
  }))

  return { status: res?.status(), isi, rail }
}
