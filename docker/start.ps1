$ErrorActionPreference = 'Stop'
Set-Location (Split-Path -Parent $PSScriptRoot)
docker compose --profile testing up -d
if ($LASTEXITCODE -ne 0) { throw 'Não foi possível iniciar os containers.' }
Write-Host 'Aplicação: http://localhost:8000 | Ambiente Cypress: http://localhost:8001'
