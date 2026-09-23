$ErrorActionPreference = "Continue"
Set-Location "D:\Laravel Herd\laporan-fiqri"

$job = Start-Job -ScriptBlock {
    Set-Location "D:\Laravel Herd\laporan-fiqri"
    & "C:\Users\ASUS\.config\herd\bin\php84\php.exe" artisan serve --port=8129
}

Start-Sleep -Seconds 6

$node = "C:\Program Files\nodejs\node.exe"
$browser = "C:\Users\ASUS\.codegpt\skills\browser-automation\browser.mjs"
& $node $browser "http://127.0.0.1:8129/login" --script "D:\Laravel Herd\laporan-fiqri\qa-flow.mjs" --timeout 40000

Stop-Job $job -ErrorAction SilentlyContinue
Remove-Job $job -Force -ErrorAction SilentlyContinue
