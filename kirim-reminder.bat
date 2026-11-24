@echo off
REM =====================================================
REM SIMONIKA - Reminder Email Scheduler
REM Jalankan via Windows Task Scheduler setiap hari jam 08:00
REM =====================================================

set PHP_PATH=C:\xampp\php\php.exe
set ARTISAN_PATH=C:\xampp\htdocs\monitoring-kinerja\artisan
set LOG_PATH=C:\xampp\htdocs\monitoring-kinerja\storage\logs\reminder-deadline.log

echo [%DATE% %TIME%] Menjalankan reminder deadline SIMONIKA... >> %LOG_PATH%
%PHP_PATH% %ARTISAN_PATH% simonika:kirim-reminder >> %LOG_PATH% 2>&1
echo [%DATE% %TIME%] Selesai. >> %LOG_PATH%
