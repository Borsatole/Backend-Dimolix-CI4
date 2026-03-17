@echo off
:loop
cls
echo Rodando jobs...
cd /d "C:\Users\HomeUser\Desktop\DEVELOPER\Dimolix\Backend"
php spark cron:rotinas
echo Aguardando 60 segundos...
timeout /t 60 /nobreak >nul
goto loop
