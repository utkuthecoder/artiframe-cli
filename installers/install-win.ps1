Write-Host "======================================"
Write-Host " ArtiFrame Ecosystem Windows Installer"
Write-Host "======================================"

if (!(Get-Command winget -ErrorAction SilentlyContinue)) {
    Write-Host "[-] Winget package manager not found!" -ForegroundColor Red
    Write-Host "    Please install PHP and Node.js manually, then run 'npm install -g @artilingo/artiframe-cli'"
    Exit
}

Write-Host "[+] Installing PHP..." -ForegroundColor Cyan
winget install -e --id PHP.PHP --accept-source-agreements --accept-package-agreements

Write-Host "[+] Installing Node.js..." -ForegroundColor Cyan
winget install -e --id OpenJS.NodeJS --accept-source-agreements --accept-package-agreements

Write-Host "[+] Refreshing Environment Variables..." -ForegroundColor Cyan
$env:Path = [System.Environment]::GetEnvironmentVariable("Path","Machine") + ";" + [System.Environment]::GetEnvironmentVariable("Path","User")

Write-Host "[+] Installing ArtiFrame CLI globally..." -ForegroundColor Cyan
npm install -g @artilingo/artiframe-cli

Write-Host "======================================"
Write-Host "✅ Installation Complete!" -ForegroundColor Green
Write-Host "   A shortcut has been added to your Desktop."
Write-Host "   You can double-click 'ArtiFrame DevOps' to launch the studio!"
Write-Host "======================================"
