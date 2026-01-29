@echo off
REM Run from project root: scripts\commit-and-push.cmd [commit message]
REM Or from Cursor: open Terminal (Ctrl+`) then: scripts\commit-and-push.cmd "Your message"
setlocal
cd /d "%~dp0.."
if "%~1"=="" (
  set "msg=Update"
) else (
  set "msg=%~1"
)
git add -A
git status
git commit -m "%msg%"
if %ERRORLEVEL% equ 0 git push origin master
endlocal
