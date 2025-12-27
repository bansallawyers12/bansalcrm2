@echo off
REM PostgreSQL Database Creation and Restore Script

set PGHOST=127.0.0.1
set PGPORT=5432
set PGUSER=postgres
set PGPASSWORD=admin123
set PGDATABASE=bansalcrm_pg
set DUMPFILE=C:\Users\5560\Downloads\database_dump_20251227_183930\database_dump_20251227_183930.sql
set POSTGRES_BIN=C:\Program Files\PostgreSQL\18\bin

REM Set PGPASSWORD environment variable for PostgreSQL tools
set PGPASSWORD=%PGPASSWORD%

echo ========================================
echo PostgreSQL Database Restore Script
echo ========================================
echo Host: %PGHOST%
echo Port: %PGPORT%
echo Database: %PGDATABASE%
echo Username: %PGUSER%
echo Dump File: %DUMPFILE%
echo.

REM Check if dump file exists
if not exist "%DUMPFILE%" (
    echo Error: Dump file not found at %DUMPFILE%
    exit /b 1
)

REM Step 1: Drop database if it exists
echo Step 1: Checking if database exists...
"%POSTGRES_BIN%\psql.exe" -h %PGHOST% -p %PGPORT% -U %PGUSER% -d postgres -c "DROP DATABASE IF EXISTS %PGDATABASE%;"
if errorlevel 1 (
    echo Warning: Could not drop existing database. Continuing anyway...
)

REM Step 2: Create the database
echo.
echo Step 2: Creating database '%PGDATABASE%'...
"%POSTGRES_BIN%\createdb.exe" -h %PGHOST% -p %PGPORT% -U %PGUSER% -E UTF8 %PGDATABASE%
if errorlevel 1 (
    echo Error: Failed to create database
    exit /b 1
)
echo Database created successfully
echo.

REM Step 3: Restore the dump
echo Step 3: Restoring database from dump file...
echo This may take a few minutes depending on the database size...
echo.
"%POSTGRES_BIN%\psql.exe" -h %PGHOST% -p %PGPORT% -U %PGUSER% -d %PGDATABASE% -f "%DUMPFILE%"
if errorlevel 1 (
    echo.
    echo Error: Database restore failed
    exit /b 1
) else (
    echo.
    echo ========================================
    echo Database restored successfully!
    echo ========================================
)

echo.
echo Database '%PGDATABASE%' is ready to use!
pause

