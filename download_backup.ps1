# Download Latest Database Backup
# This script creates a database backup and saves it to Downloads folder

$downloadsPath = Join-Path $env:USERPROFILE "Downloads"
$timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$outputFile = Join-Path $downloadsPath "database_dump_$timestamp.sql"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Creating Database Backup" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Output location: $outputFile" -ForegroundColor Yellow
Write-Host ""

# Run the dump script
& .\dump_postgres.ps1 -OutputFile $outputFile

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "========================================" -ForegroundColor Green
    Write-Host "Backup saved successfully!" -ForegroundColor Green
    Write-Host "Location: $outputFile" -ForegroundColor Green
    Write-Host "========================================" -ForegroundColor Green
    
    # Open the Downloads folder
    Start-Process explorer.exe -ArgumentList $downloadsPath
} else {
    Write-Host ""
    Write-Host "Backup failed!" -ForegroundColor Red
    exit 1
}




