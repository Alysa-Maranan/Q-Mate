@echo off
setlocal enabledelayedexpansion

REM Read the file
set "file=resources\views\inventory.blade.php"

REM Use PowerShell to fix it
powershell.exe -NoProfile -ExecutionPolicy Bypass -Command "^
  [string]$content = [IO.File]::ReadAllText('%file%', [Text.Encoding]::UTF8); ^
  $content = $content.Replace([char]0x20b1 + [char]0x20b1, '??'); ^
  $content = $content.Replace([char]0x20b1 + ' ', '? '); ^
  $content = $content.Replace([char]0x20b1 + ':', '?:'); ^
  $content = $content.Replace([char]0x20b1 + '.', '?.'); ^
  [IO.File]::WriteAllText('%file%', $content, [Text.Encoding]::UTF8); ^
  Write-Host 'Fixed all operators'
"

endlocal
