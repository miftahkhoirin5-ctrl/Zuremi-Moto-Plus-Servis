@echo off
title Menjalankan Aplikasi Bengkel MotoTrust...
cd /d "%~dp0"
echo ======================================================
echo   Membuka Aplikasi Bengkel Resmi MotoTrust
echo ======================================================
echo.
echo 1. Menjalankan server PHP lokal...
start "" /b "C:\xampp\php\php.exe" -S 127.0.0.1:8000
timeout /t 2 /nobreak >nul

echo 2. Membuka browser ke http://localhost:8000 ...
start "" "http://127.0.0.1:8000"

echo.
echo ======================================================
echo   Server BERJALAN! Jangan tutup jendela hitam ini
echo   selama Anda menggunakan aplikasi.
echo   Tekan Ctrl+C untuk menghentikan server.
echo ======================================================
echo.
pause

