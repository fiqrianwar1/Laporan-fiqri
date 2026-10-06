// Ambil screenshot tampilan aplikasi untuk dokumentasi README.
// Jalankan: node scripts/ambil-screenshot.mjs
// Syarat: aplikasi sudah jalan (mis. http://laporan-fiqri.test) dan Playwright terpasang.

import { chromium } from 'playwright'
import { mkdirSync } from 'node:fs'

const BASE = process.env.APP_BASE_URL ?? 'http://laporan-fiqri.test'
const OUT = 'docs/screenshots'

const AKUN = {
  tinter: { email: 'tinter@warnatanjungjaya.com', password: 'password' },
  manajer: { email: 'manajer@warnatanjungjaya.com', password: 'password' },
}

mkdirSync(OUT, { recursive: true })

const browser = await chromium.launch()

async function masuk(page, akun) {
  await page.goto(`${BASE}/login`, { waitUntil: 'networkidle' })
  await page.fill('input[name="email"]', akun.email)
  await page.fill('input[name="password"]', akun.password)
  await Promise.all([
    page.waitForURL((u) => !u.pathname.includes('login'), { timeout: 30000 }),
    page.click('button[type="submit"]'),
  ])
}

async function jepret(page, nama, { lebar = 1500, tinggi = 940, penuh = false } = {}) {
  await page.setViewportSize({ width: lebar, height: tinggi })
  await page.waitForTimeout(1200)
  await page.screenshot({ path: `${OUT}/${nama}.png`, fullPage: penuh })
  console.log(`✓ ${OUT}/${nama}.png`)
}

// ---------- Desktop ----------
const ctxTinter = await browser.newContext({ viewport: { width: 1500, height: 940 } })
const tinter = await ctxTinter.newPage()
await masuk(tinter, AKUN.tinter)

await tinter.goto(`${BASE}/dashboard`, { waitUntil: 'networkidle' })
await jepret(tinter, 'dashboard-desktop')

await tinter.goto(`${BASE}/laporan-oplosan`, { waitUntil: 'networkidle' })
await jepret(tinter, 'laporan-oplosan-desktop')

await tinter.goto(`${BASE}/riwayat-order`, { waitUntil: 'networkidle' })
await jepret(tinter, 'riwayat-order-desktop')

await tinter.goto(`${BASE}/laporan-harian`, { waitUntil: 'networkidle' })
await jepret(tinter, 'laporan-harian-desktop', { penuh: true })

// Halaman ke-2 dipakai sebagai bukti paginasi jalan (tombol halaman aktif pindah).
await tinter.goto(`${BASE}/laporan-harian?page=2`, { waitUntil: 'networkidle' })
await jepret(tinter, 'laporan-harian-desktop-hal2', { penuh: true })

// ---------- Mobile ----------
const ctxHp = await browser.newContext({
  viewport: { width: 412, height: 900 },
  deviceScaleFactor: 2,
  isMobile: true,
  hasTouch: true,
})
const hp = await ctxHp.newPage()
await masuk(hp, AKUN.tinter)

await hp.goto(`${BASE}/dashboard`, { waitUntil: 'networkidle' })
await jepret(hp, 'dashboard-hp', { lebar: 412, tinggi: 900 })

await hp.goto(`${BASE}/laporan-oplosan`, { waitUntil: 'networkidle' })
await jepret(hp, 'laporan-oplosan-hp', { lebar: 412, tinggi: 900 })

await hp.goto(`${BASE}/laporan-harian`, { waitUntil: 'networkidle' })
await jepret(hp, 'laporan-harian-hp', { lebar: 412, tinggi: 900 })

// ---------- Mode manajer (hanya lihat) ----------
const ctxManajer = await browser.newContext({ viewport: { width: 1500, height: 940 } })
const manajer = await ctxManajer.newPage()
await masuk(manajer, AKUN.manajer)

await manajer.goto(`${BASE}/manajer`, { waitUntil: 'networkidle' })
await jepret(manajer, 'mode-manajer')

await manajer.goto(`${BASE}/manajer/laporan-harian`, { waitUntil: 'networkidle' })
await jepret(manajer, 'mode-manajer-laporan-harian', { penuh: true })

await browser.close()
console.log('\nSelesai. Semua screenshot tersimpan di', OUT)