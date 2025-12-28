# Script to remove all references to admin.reports.personal-task-report route
# This route was removed in December 2025 as part of task system removal

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Removing personal-task-report references" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Find all files containing the route reference
Write-Host "Searching for files containing 'personal-task-report'..." -ForegroundColor Yellow
$files = Get-ChildItem -Path . -Recurse -File -Include *.php,*.blade.php -Exclude *.backup | 
    Select-String -Pattern "personal-task-report" -List | 
    Select-Object -ExpandProperty Path | 
    Sort-Object -Unique

if ($files.Count -eq 0) {
    Write-Host "No files found containing 'personal-task-report'" -ForegroundColor Green
    exit 0
}

Write-Host "Found $($files.Count) file(s):" -ForegroundColor Yellow
foreach ($file in $files) {
    $relativePath = $file.Replace((Get-Location).Path + "\", "")
    Write-Host "  - $relativePath" -ForegroundColor Gray
}

Write-Host ""
Write-Host "Processing files..." -ForegroundColor Cyan
Write-Host ""

$totalModified = 0

foreach ($file in $files) {
    $relativePath = $file.Replace((Get-Location).Path + "\", "")
    Write-Host "Processing: $relativePath" -ForegroundColor Yellow
    
    # Read file content
    $content = Get-Content -Path $file
    $originalLineCount = $content.Count
    $newContent = @()
    $fileModified = $false
    
    # Process each line
    for ($i = 0; $i -lt $content.Count; $i++) {
        $line = $content[$i]
        
        # Check if line contains the route reference
        if ($line -match "personal-task-report") {
            # Skip if it's already a commented route definition (routes/web.php)
            if ($line -match "^\s*//\s*Route::.*personal-task-report") {
                Write-Host "  Line $($i + 1): Already commented (keeping)" -ForegroundColor Gray
                $newContent += $line
            }
            # Skip if it's in a PHP comment block (ReportController.php)
            elseif ($line -match "^\s*/\*" -or $line -match "^\s*\*/" -or $line -match "^\s*//") {
                Write-Host "  Line $($i + 1): In comment block (keeping)" -ForegroundColor Gray
                $newContent += $line
            }
            # Remove the line if it contains the route in HTML comment or compiled view
            else {
                $preview = $line.Trim()
                if ($preview.Length -gt 60) {
                    $preview = $preview.Substring(0, 60) + "..."
                }
                Write-Host "  Line $($i + 1): REMOVING - $preview" -ForegroundColor Red
                $fileModified = $true
                # Don't add this line to newContent
            }
        }
        else {
            $newContent += $line
        }
    }
    
    # Save the file if modified
    if ($fileModified) {
        try {
            # Create backup
            $backupPath = "$file.backup"
            Copy-Item -Path $file -Destination $backupPath -Force | Out-Null
            $backupName = Split-Path -Leaf $backupPath
            Write-Host "  Backup created: $backupName" -ForegroundColor Gray
            
            # Save modified content
            $newContent | Set-Content -Path $file
            $linesRemoved = $originalLineCount - $newContent.Count
            Write-Host "  File updated (removed $linesRemoved line(s))" -ForegroundColor Green
            $totalModified++
        }
        catch {
            Write-Host "  Error: $_" -ForegroundColor Red
        }
    }
    else {
        Write-Host "  No changes needed" -ForegroundColor Gray
    }
    
    Write-Host ""
}

# Clear Laravel caches
Write-Host "Clearing Laravel caches..." -ForegroundColor Cyan

try {
    php artisan view:clear 2>&1 | Out-Null
    Write-Host "  View cache cleared" -ForegroundColor Green
}
catch {
    Write-Host "  Could not clear view cache" -ForegroundColor Yellow
}

try {
    php artisan route:clear 2>&1 | Out-Null
    Write-Host "  Route cache cleared" -ForegroundColor Green
}
catch {
    Write-Host "  Could not clear route cache" -ForegroundColor Yellow
}

try {
    php artisan config:clear 2>&1 | Out-Null
    Write-Host "  Config cache cleared" -ForegroundColor Green
}
catch {
    Write-Host "  Could not clear config cache" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Summary:" -ForegroundColor Cyan
Write-Host "  Files processed: $($files.Count)" -ForegroundColor White
Write-Host "  Files modified: $totalModified" -ForegroundColor White
Write-Host "  Backup files created with .backup extension" -ForegroundColor White
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Done! Please verify the changes and delete .backup files if everything looks good." -ForegroundColor Green
