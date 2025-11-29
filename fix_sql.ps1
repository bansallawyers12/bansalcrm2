# PowerShell script to fix MySQL row size too large error
# Converts VARCHAR columns to TEXT in the leads table

$inputFile = "bansalc_db229112025.sql"
$outputFile = "bansalc_db229112025_fixed.sql"

Write-Host "Processing SQL file..." -ForegroundColor Green

# Columns to convert from VARCHAR(255) to TEXT
$columnsToConvert = @(
    '`comments_note`',
    '`advertisements_name`',
    '`social_link`',
    '`address`',
    '`nomi_occupation`',
    '`high_quali_aus`',
    '`high_quali_overseas`',
    '`relevant_work_exp_aus`',
    '`relevant_work_exp_over`',
    '`married_partner`',
    '`total_points`',
    '`profile_img`',
    '`tags_label`'
)

$inLeadsTable = $false
$convertedCount = 0
$lineNumber = 0

Write-Host "Reading and processing file..." -ForegroundColor Yellow

Get-Content $inputFile -Encoding UTF8 | ForEach-Object {
    $line = $_
    $lineNumber++
    
    # Check if we're entering the leads table
    if ($line -match 'CREATE TABLE `leads`') {
        $inLeadsTable = $true
        Write-Host "Found leads table at line $lineNumber" -ForegroundColor Cyan
    }
    
    # Process lines within the leads table definition
    if ($inLeadsTable) {
        $originalLine = $line
        
        foreach ($colName in $columnsToConvert) {
            # Match: column_name varchar(255) DEFAULT NULL
            $pattern = "($colName\s+)varchar\(255\)(\s+DEFAULT\s+NULL)?"
            if ($line -match $pattern) {
                $line = $line -replace "($colName\s+)varchar\(255\)", "`$1text"
                $convertedCount++
                Write-Host "  Converted $colName from varchar(255) to text" -ForegroundColor Green
            }
        }
        
        # Check if we've reached the end of the table definition
        if ($line -match 'ENGINE=InnoDB' -or ($line.Trim().EndsWith(');') -and $inLeadsTable)) {
            $inLeadsTable = $false
        }
    }
    
    $line
} | Set-Content $outputFile -Encoding UTF8

Write-Host "`nDone! Fixed SQL file created: $outputFile" -ForegroundColor Green
Write-Host "Converted $convertedCount column definitions" -ForegroundColor Green
Write-Host "`nYou can now import the fixed file using:" -ForegroundColor Yellow
Write-Host "  mysql -u root -p bansalc_db2 < $outputFile" -ForegroundColor Cyan

