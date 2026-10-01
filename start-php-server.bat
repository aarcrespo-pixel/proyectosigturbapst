@echo off
cd /d "%~dp0"
<<<<<<< HEAD
if exist ".env" (
	for /f "usebackq eol=# tokens=1,* delims==" %%A in (".env") do if not "%%A"=="" set "%%A=%%B"
)
=======
>>>>>>> 00361ec4d7278fe96ed7b2843a726b6c83d0105b
"C:\xampp\php\php.exe" -S localhost:8000 -t .
