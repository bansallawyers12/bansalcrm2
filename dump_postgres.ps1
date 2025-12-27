# PostgreSQL Database Dump Script
# This script uses pg_dump to export the database in SQL format

param(
    [string]$DbHost = $env:DB_HOST,
    [string]$DbPort = $env:DB_PORT,
    [string]$DbName = $env:DB_DATABASE,
    [string]$DbUser = $env:DB_USERNAME,
    [string]$DbPassword = $env:DB_PASSWORD,
    [string]$OutputFile = "database_dump_$(Get-Date -Format 'yyyyMMdd_HHmmss').sql"
)

# Set defaults if environment variables are not set
if ([string]::IsNullOrEmpty($DbHost)) { $DbHost = "127.0.0.1" }
if ([string]::IsNullOrEmpty($DbPort)) { $DbPort = "5432" }
if ([string]::IsNullOrEmpty($DbName)) { $DbName = "forge" }
if ([string]::IsNullOrEmpty($DbUser)) { $DbUser = "forge" }
if ([string]::IsNullOrEmpty($DbPassword)) { $DbPassword = "" }

# Set PGPASSWORD environment variable for pg_dump
$env:PGPASSWORD = $DbPassword

# Build pg_dump command
$pgDumpCmd = "pg_dump"
$pgDumpArgs = @(
    "-h", $DbHost,
    "-p", $DbPort,
    "-U", $DbUser,
    "-d", $DbName,
    "--format=plain",
    "--no-owner",
    "--no-acl",
    "-f", $OutputFile
)

Write-Host "Dumping PostgreSQL database..." -ForegroundColor Green
Write-Host "Host: $DbHost" -ForegroundColor Cyan
Write-Host "Port: $DbPort" -ForegroundColor Cyan
Write-Host "Database: $DbName" -ForegroundColor Cyan
Write-Host "Username: $DbUser" -ForegroundColor Cyan
Write-Host "Output File: $OutputFile" -ForegroundColor Cyan
Write-Host ""

# Execute pg_dump
try {
    & $pgDumpCmd $pgDumpArgs
    if ($LASTEXITCODE -eq 0) {
        Write-Host "Database dump completed successfully!" -ForegroundColor Green
        Write-Host "Output saved to: $OutputFile" -ForegroundColor Green
        $fileInfo = Get-Item $OutputFile -ErrorAction SilentlyContinue
        if ($fileInfo) {
            Write-Host "File size: $([math]::Round($fileInfo.Length / 1MB, 2)) MB" -ForegroundColor Green
        }
    } else {
        Write-Host "Error: pg_dump failed with exit code $LASTEXITCODE" -ForegroundColor Red
        exit $LASTEXITCODE
    }
} catch {
    Write-Host "Error: Could not execute pg_dump. Make sure PostgreSQL client tools are installed and in your PATH." -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
    exit 1
} finally {
    # Clear password from environment
    $env:PGPASSWORD = $null
}
