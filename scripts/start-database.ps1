$mariadb = "C:\Program Files\MariaDB 12.3\bin\mariadbd.exe"
$config = "C:\Users\Abrah\Ecoride\mariadb-data\my.ini"

if (-not (Test-Path $mariadb)) {
    Write-Error "MariaDB est introuvable. Installez MariaDB.Server avec winget."
    exit 1
}

if (-not (Test-Path $config)) {
    Write-Error "La base locale n'est pas initialisee. Lancez mariadb-install-db avant de demarrer."
    exit 1
}

$existing = Get-NetTCPConnection -LocalPort 3306 -State Listen -ErrorAction SilentlyContinue
if ($existing) {
    Write-Host "MariaDB est deja demarre sur le port 3306."
    exit 0
}

Start-Process -FilePath $mariadb -ArgumentList @("--defaults-file=$config") -WorkingDirectory "C:\Users\Abrah\Ecoride" -WindowStyle Hidden
Start-Sleep -Seconds 4
Write-Host "MariaDB demarre sur le port 3306."
