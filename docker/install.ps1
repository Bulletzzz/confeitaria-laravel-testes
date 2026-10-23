$ErrorActionPreference = 'Stop'
Set-Location (Split-Path -Parent $PSScriptRoot)

function Invoke-Docker {
    & docker @args
    if ($LASTEXITCODE -ne 0) { throw "Docker falhou: $args" }
}

if (!(Test-Path -LiteralPath '.env')) { Copy-Item -LiteralPath '.env.example' -Destination '.env' }
Invoke-Docker info --format '{{.ServerVersion}}'
Invoke-Docker compose --profile testing build
Invoke-Docker compose up -d --wait db
Invoke-Docker compose run --rm app composer install --no-interaction --prefer-dist
if ((Get-Content -LiteralPath '.env' -Raw) -match '(?m)^APP_KEY=\s*$') {
    Invoke-Docker compose run --rm app php artisan key:generate --force
}
Invoke-Docker compose run --rm app php artisan migrate --seed --force
Invoke-Docker compose exec -T db mysql -uroot -proot -e "CREATE DATABASE IF NOT EXISTS confeitaria_e2e; GRANT ALL PRIVILEGES ON confeitaria_e2e.* TO 'confeitaria'@'%';"
Invoke-Docker compose --profile testing up -d
Invoke-Docker compose exec -T app-e2e php artisan migrate --seed --force
Write-Host 'Aplicação: http://localhost:8000 | Ambiente Cypress: http://localhost:8001'
