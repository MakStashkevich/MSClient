@echo off
TITLE MSClient to Minecraft: Bedrock Edition 1.1
cd /d %~dp0

if exist bin\php\php.exe (
	set PHPRC=""
	set PHP_BINARY=bin\php\php.exe
) else (
	set PHP_BINARY=php
)

if exist Runnable.php (
    set CLIENT_FILE=Runnable.php
) else (
    echo "[ERROR] Couldn't find a valid MSClient installation."
    pause
    exit 8
)

if exist bin\mintty.exe (
	start "" bin\mintty.exe -o Columns=88 -o Rows=32 -o AllowBlinking=0 -o FontQuality=3 -o Font="Consolas" -o FontHeight=10 -o CursorType=0 -o CursorBlinks=1 -h error -t "MSClient" -w max %PHP_BINARY% %CLIENT_FILE% --enable-ansi %*
) else (
	%PHP_BINARY% -c bin\php %CLIENT_FILE% %*
)