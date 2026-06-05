$php = "$env:LOCALAPPDATA\Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe"

if (-not (Test-Path $php)) {
    Write-Error "PHP est introuvable. Fermez puis rouvrez le terminal, ou reinstallez PHP avec winget."
    exit 1
}

& $php -S localhost:8000 -t public
