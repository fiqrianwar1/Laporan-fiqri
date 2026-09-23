$ErrorActionPreference = "Continue"
$php = "C:\Users\ASUS\.config\herd\bin\php84\php.exe"
Set-Location "D:\Laravel Herd\laporan-fiqri"

$job = Start-Job -ScriptBlock {
    Set-Location "D:\Laravel Herd\laporan-fiqri"
    & "C:\Users\ASUS\.config\herd\bin\php84\php.exe" artisan serve --port=8128
}

Start-Sleep -Seconds 6

$result = @()
foreach ($path in @("/login", "/dashboard")) {
    try {
        $r = Invoke-WebRequest -Uri "http://127.0.0.1:8128$path" -UseBasicParsing -TimeoutSec 25 -MaximumRedirection 0 -ErrorAction Stop
        $result += "GET $path -> STATUS $($r.StatusCode), LEN $($r.Content.Length)"
    } catch {
        $resp = $_.Exception.Response
        if ($resp) {
            $code = [int]$resp.StatusCode
            $result += "GET $path -> STATUS $code (redirect/denied, expected utk /dashboard tanpa login)"
        } else {
            $result += "GET $path -> ERR $($_.Exception.Message)"
        }
    }
}

$result | ForEach-Object { Write-Output $_ }

Stop-Job $job -ErrorAction SilentlyContinue
Remove-Job $job -Force -ErrorAction SilentlyContinue
