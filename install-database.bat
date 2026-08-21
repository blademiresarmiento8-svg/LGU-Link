@echo off
setlocal enabledelayedexpansion

echo ================================================
echo   LGU-Link Database Installer
echo ================================================
echo.

REM Locate the MySQL client shipped with XAMPP. Edit MYSQL_EXE below if
REM your XAMPP is installed somewhere other than C:\xampp.
set "MYSQL_EXE=C:\xampp\mysql\bin\mysql.exe"

if not exist "%MYSQL_EXE%" (
    where mysql >nul 2>nul
    if !errorlevel! equ 0 (
        set "MYSQL_EXE=mysql"
    ) else (
        echo Could not find mysql.exe.
        echo   Checked: C:\xampp\mysql\bin\mysql.exe
        echo   Also checked your PATH.
        echo.
        echo Edit this script and set MYSQL_EXE to the full path of your
        echo XAMPP mysql.exe if it's installed somewhere else.
        echo.
        pause
        exit /b 1
    )
)

set "SQL_FILE=%~dp0lgu_link.sql"

if not exist "%SQL_FILE%" (
    echo Could not find lgu_link.sql next to this script.
    echo   Expected at: %SQL_FILE%
    echo.
    pause
    exit /b 1
)

echo Using MySQL client: %MYSQL_EXE%
echo Using dump file:    %SQL_FILE%
echo.
echo This will DROP the "lgu_link" database if it already exists on this
echo machine, then recreate it from lgu_link.sql (all data in the dump:
echo accounts, Citizen's Charter services, news posts).
echo.
echo Make sure MySQL is running in the XAMPP Control Panel first.
echo.
set /p CONFIRM="Continue? (Y/N): "
if /i not "%CONFIRM%"=="Y" (
    echo Cancelled.
    pause
    exit /b 0
)

echo.
echo Recreating the database...
"%MYSQL_EXE%" -u root -e "DROP DATABASE IF EXISTS lgu_link; CREATE DATABASE lgu_link CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
if errorlevel 1 (
    echo.
    echo Failed to connect to MySQL. Make sure MySQL is running in the
    echo XAMPP Control Panel, then try again.
    echo.
    pause
    exit /b 1
)

echo Importing lgu_link.sql...
"%MYSQL_EXE%" -u root lgu_link < "%SQL_FILE%"
if errorlevel 1 (
    echo.
    echo Import failed. See the error above.
    echo.
    pause
    exit /b 1
)

echo.
echo ================================================
echo   Done. The lgu_link database is ready.
echo ================================================
echo.
echo Default accounts:
echo   Admin: admin@norzagaray.gov.ph / Admin@123
echo   User:  juan.delacruz@example.com / User@123
echo.
echo A few things this script does NOT do -- handle these separately:
echo   1. Run "composer install" to get vendor\ (needed for the chatbot's
echo      Claude API calls).
echo   2. Copy config\anthropic-key.local.php over from your other machine
echo      (or create it from the .example file) if you want the AI
echo      chatbot working here too -- it's gitignored, so it never syncs
echo      on its own.
echo   3. Copy the assets\uploads\news\ folder over from your other
echo      machine if you want existing news photos to actually display --
echo      the database only stores each photo's file path, not the image
echo      itself, and that folder is gitignored too.
echo.
pause
