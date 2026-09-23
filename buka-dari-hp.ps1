# =====================================================================
#  Buka Laporan Fiqri dari HP (tanpa hosting, cukup WiFi yang sama)
#
#  Cara pakai: klik kanan file ini -> "Run with PowerShell"
#  Lalu buka link yang muncul di browser HP.
# =====================================================================

$ErrorActionPreference = 'Stop'
Set-Location -Path $PSScriptRoot

Write-Host ''
Write-Host '=== Laporan Fiqri - Mode Jaringan Lokal ===' -ForegroundColor Cyan
Write-Host ''

# --- 1. Cari PHP dari Herd ---
$php = "$env:USERPROFILE\.config\herd\bin\php84\php.exe"
if (-not (Test-Path $php)) {
    Write-Host 'PHP tidak ditemukan di:' -ForegroundColor Red
    Write-Host "  $php" -ForegroundColor Red
    Write-Host ''
    Write-Host 'Pastikan Laravel Herd sudah terpasang, atau ubah $php di file ini.'
    Read-Host 'Tekan Enter untuk menutup'
    exit 1
}

# --- 2. Cari IP WiFi / LAN ---
$ip = (Get-NetIPAddress -AddressFamily IPv4 |
    Where-Object {
        $_.IPAddress -notlike '127.*' -and
        $_.IPAddress -notlike '169.254.*' -and
        $_.PrefixOrigin -ne 'WellKnown'
    } |
    Sort-Object -Property @{Expression = { $_.InterfaceAlias -like '*Wi*' }; Descending = $true } |
    Select-Object -First 1).IPAddress

if (-not $ip) {
    Write-Host 'Tidak menemukan IP jaringan lokal.' -ForegroundColor Red
    Write-Host 'Pastikan WiFi / LAN sedang terhubung.'
    Read-Host 'Tekan Enter untuk menutup'
    exit 1
}

# --- 3. Samakan APP_URL dengan IP ini supaya asset & link benar ---
$envFile = Join-Path $PSScriptRoot '.env'
if (Test-Path $envFile) {
    $isi = [System.IO.File]::ReadAllText($envFile)
    $isiBaru = $isi -replace '(?m)^APP_URL=.*$', "APP_URL=http://${ip}:8000"
    if ($isiBaru -ne $isi) {
        # Simpan tanpa BOM supaya .env tetap terbaca Laravel.
        [System.IO.File]::WriteAllText($envFile, $isiBaru, (New-Object System.Text.UTF8Encoding($false)))
        Write-Host "APP_URL diset ke http://${ip}:8000" -ForegroundColor Green
    }
}

# --- 4. Pastikan port 8000 boleh diakses dari jaringan ---
$ruleNama = 'Laporan Fiqri (port 8000)'
$ruleAda = Get-NetFirewallRule -DisplayName $ruleNama -ErrorAction SilentlyContinue
if (-not $ruleAda) {
    try {
        New-NetFirewallRule -DisplayName $ruleNama -Direction Inbound -Protocol TCP `
            -LocalPort 8000 -Action Allow -Profile Private -ErrorAction Stop | Out-Null
        Write-Host 'Aturan firewall untuk port 8000 ditambahkan.' -ForegroundColor Green
    } catch {
        Write-Host 'Catatan: gagal menambah aturan firewall (perlu izin admin).' -ForegroundColor Yellow
        Write-Host 'Kalau HP tidak bisa membuka, jalankan file ini sebagai Administrator.' -ForegroundColor Yellow
    }
} else {
    Write-Host 'Aturan firewall port 8000 sudah ada.' -ForegroundColor Green
}

# --- 5. Bersihkan cache config lalu jalankan server ---
& $php artisan config:clear 2>&1 | Out-Null

Write-Host ''
Write-Host '=====================================================' -ForegroundColor Cyan
Write-Host '  SERVER SUDAH JALAN' -ForegroundColor Green
Write-Host '=====================================================' -ForegroundColor Cyan
Write-Host ''
Write-Host '  Buka di HP (harus satu WiFi dengan laptop):' -ForegroundColor White
Write-Host ''
Write-Host "      http://${ip}:8000" -ForegroundColor Yellow
Write-Host ''
Write-Host '  Buka di laptop ini:' -ForegroundColor White
Write-Host '      http://localhost:8000' -ForegroundColor Yellow
Write-Host ''
Write-Host '  Akun:' -ForegroundColor White
Write-Host '      tinter@warnatanjungjaya.com   / password  (bisa isi laporan)'
Write-Host '      manajer@warnatanjungjaya.com  / password  (hanya lihat)'
Write-Host ''
Write-Host '  Tekan Ctrl + C untuk mematikan server.' -ForegroundColor DarkGray
Write-Host ''

& $php artisan serve --host=0.0.0.0 --port=8000
