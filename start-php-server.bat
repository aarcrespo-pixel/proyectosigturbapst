@echo off
cd /d "%~dp0"
if exist ".env" (
	for /f "usebackq eol=# tokens=1,* delims==" %%A in (".env") do if not "%%A"=="" set "%%A=%%B"
)
"C:\xampp\php\php.exe" -S localhost:8000 -t .
