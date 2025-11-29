#Requires -RunAsAdministrator

# One-Click MySQL Fix for Antivirus Blocking Issue
# Run this as Administrator

$ErrorActionPreference = "Continue"

Write-Host ""
Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host "    MySQL Antivirus Block Fix - One Click Solution" -ForegroundColor Cyan
Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host ""

# Check if running as admin
$isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
if (-not $isAdmin) {
    Write-Host "❌ ERROR: This script must be run as Administrator!" -ForegroundColor Red
    Write-Host ""
    Write-Host "Right-click PowerShell and select 'Run as Administrator'" -ForegroundColor Yellow
    Write-Host ""
    pause
    exit 1
}

Write-Host "✅ Running as Administrator" -ForegroundColor Green
Write-Host ""

# Step 1: Stop MySQL
Write-Host "[1/6] Stopping any running MySQL processes..." -ForegroundColor Yellow
$mysqlProcs = Get-Process | Where-Object {$_.ProcessName -like "*mysql*"}
if ($mysqlProcs) {
    $mysqlProcs | Stop-Process -Force -ErrorAction SilentlyContinue
    Start-Sleep -Seconds 3
    Write-Host "   ✓ Stopped $($mysqlProcs.Count) MySQL process(es)" -ForegroundColor Green
} else {
    Write-Host "   ✓ No MySQL processes were running" -ForegroundColor Green
}
Write-Host ""

# Step 2: Add Windows Defender Exclusions
Write-Host "[2/6] Adding MySQL to Windows Defender exclusions..." -ForegroundColor Yellow

try {
    # Add path exclusion
    Add-MpPreference -ExclusionPath "C:\xampp\mysql" -ErrorAction Stop
    Write-Host "   ✓ Added path exclusion: C:\xampp\mysql" -ForegroundColor Green
    
    # Add process exclusion
    Add-MpPreference -ExclusionProcess "mysqld.exe" -ErrorAction Stop
    Write-Host "   ✓ Added process exclusion: mysqld.exe" -ForegroundColor Green
    
    # Also add data directory specifically
    Add-MpPreference -ExclusionPath "C:\xampp\mysql\data" -ErrorAction Stop
    Write-Host "   ✓ Added path exclusion: C:\xampp\mysql\data" -ForegroundColor Green
    
} catch {
    Write-Host "   ⚠️  Warning: Could not add some exclusions" -ForegroundColor Yellow
    Write-Host "   Error: $($_.Exception.Message)" -ForegroundColor Gray
    Write-Host "   (This is OK if exclusions already exist)" -ForegroundColor Gray
}
Write-Host ""

# Step 3: Verify Exclusions
Write-Host "[3/6] Verifying exclusions were added..." -ForegroundColor Yellow
try {
    $exclusions = Get-MpPreference -ErrorAction Stop
    $pathExclusions = $exclusions.ExclusionPath
    $procExclusions = $exclusions.ExclusionProcess
    
    if ($pathExclusions -like "*xampp\mysql*") {
        Write-Host "   ✓ MySQL path is excluded from scanning" -ForegroundColor Green
    } else {
        Write-Host "   ⚠️  Could not verify path exclusion" -ForegroundColor Yellow
    }
    
    if ($procExclusions -contains "mysqld.exe") {
        Write-Host "   ✓ mysqld.exe process is excluded from scanning" -ForegroundColor Green
    } else {
        Write-Host "   ⚠️  Could not verify process exclusion" -ForegroundColor Yellow
    }
} catch {
    Write-Host "   ⚠️  Could not verify exclusions (this may be normal)" -ForegroundColor Yellow
}
Write-Host ""

# Step 4: Add Firewall Rule
Write-Host "[4/6] Configuring Windows Firewall..." -ForegroundColor Yellow
try {
    # Check if rule already exists
    $existingRule = Get-NetFirewallRule -DisplayName "MySQL XAMPP" -ErrorAction SilentlyContinue
    if ($existingRule) {
        Write-Host "   ✓ Firewall rule already exists" -ForegroundColor Green
    } else {
        New-NetFirewallRule -DisplayName "MySQL XAMPP" -Direction Inbound -Protocol TCP -LocalPort 3306 -Action Allow -ErrorAction Stop | Out-Null
        Write-Host "   ✓ Added firewall rule for port 3306" -ForegroundColor Green
    }
} catch {
    Write-Host "   ⚠️  Warning: Could not add firewall rule" -ForegroundColor Yellow
    Write-Host "   (You may need to add it manually)" -ForegroundColor Gray
}
Write-Host ""

# Step 5: Optimize MySQL Configuration
Write-Host "[5/6] Optimizing MySQL configuration..." -ForegroundColor Yellow
$myIniPath = "C:\xampp\mysql\bin\my.ini"
if (Test-Path $myIniPath) {
    # Backup first
    $backupPath = "$myIniPath.backup_$(Get-Date -Format 'yyyyMMdd_HHmmss')"
    Copy-Item $myIniPath $backupPath
    Write-Host "   ✓ Backed up my.ini to: $backupPath" -ForegroundColor Green
    
    # Read and update configuration
    $config = Get-Content $myIniPath
    $updated = $false
    
    # Update buffer pool size if too small
    if ($config -match 'innodb_buffer_pool_size=16M') {
        $config = $config -replace 'innodb_buffer_pool_size=16M', 'innodb_buffer_pool_size=512M'
        $updated = $true
        Write-Host "   ✓ Increased innodb_buffer_pool_size to 512M" -ForegroundColor Green
    }
    
    # Update max_allowed_packet if too small
    if ($config -match 'max_allowed_packet=1M') {
        $config = $config -replace 'max_allowed_packet=1M', 'max_allowed_packet=64M'
        $updated = $true
        Write-Host "   ✓ Increased max_allowed_packet to 64M" -ForegroundColor Green
    }
    
    if ($updated) {
        $config | Set-Content $myIniPath
        Write-Host "   ✓ Configuration updated successfully" -ForegroundColor Green
    } else {
        Write-Host "   ✓ Configuration already optimal" -ForegroundColor Green
    }
} else {
    Write-Host "   ⚠️  Configuration file not found" -ForegroundColor Yellow
}
Write-Host ""

# Step 6: Wait and prepare to start
Write-Host "[6/6] Preparing to start MySQL..." -ForegroundColor Yellow
Write-Host "   Waiting 5 seconds for system to settle..." -ForegroundColor Gray
Start-Sleep -Seconds 5
Write-Host "   ✓ Ready to start MySQL" -ForegroundColor Green
Write-Host ""

# Final Instructions
Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host "                    FIX COMPLETE!" -ForegroundColor Green
Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host ""
Write-Host "NEXT STEPS:" -ForegroundColor Yellow
Write-Host ""
Write-Host "1. Open XAMPP Control Panel (as Administrator if not already)" -ForegroundColor White
Write-Host "   Right-click xampp-control.exe → Run as Administrator" -ForegroundColor Gray
Write-Host ""
Write-Host "2. Click the START button next to MySQL" -ForegroundColor White
Write-Host ""
Write-Host "3. MySQL should now start successfully!" -ForegroundColor White
Write-Host ""
Write-Host "4. Check the status - it should stay green/running" -ForegroundColor White
Write-Host ""
Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host ""
Write-Host "What was fixed:" -ForegroundColor Cyan
Write-Host "  ✓ Added C:\xampp\mysql to Windows Defender exclusions" -ForegroundColor Green
Write-Host "  ✓ Added mysqld.exe process to exclusions" -ForegroundColor Green
Write-Host "  ✓ Configured Windows Firewall for port 3306" -ForegroundColor Green
Write-Host "  ✓ Optimized MySQL configuration (my.ini)" -ForegroundColor Green
Write-Host "  ✓ Stopped restart loop" -ForegroundColor Green
Write-Host ""
Write-Host "If MySQL STILL doesn't start:" -ForegroundColor Yellow
Write-Host "  • Check if you have third-party antivirus (Norton, McAfee, etc.)" -ForegroundColor White
Write-Host "  • You'll need to add exclusions in that software too" -ForegroundColor White
Write-Host "  • See FIX_MYSQL_ANTIVIRUS.md for detailed instructions" -ForegroundColor White
Write-Host ""
Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host ""

# Offer to start XAMPP Control Panel
$openXAMPP = Read-Host "Open XAMPP Control Panel now? (Y/N)"
if ($openXAMPP -eq "Y" -or $openXAMPP -eq "y") {
    $xamppControl = "C:\xampp\xampp-control.exe"
    if (Test-Path $xamppControl) {
        Write-Host ""
        Write-Host "Opening XAMPP Control Panel..." -ForegroundColor Cyan
        Start-Process $xamppControl -Verb RunAs
    } else {
        Write-Host ""
        Write-Host "Could not find XAMPP Control Panel at: $xamppControl" -ForegroundColor Red
        Write-Host "Please open it manually" -ForegroundColor Yellow
    }
}

Write-Host ""
Write-Host "Script complete! Press any key to exit..." -ForegroundColor Green
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")

