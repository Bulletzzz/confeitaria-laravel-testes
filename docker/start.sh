#!/usr/bin/env sh
set -eu
cd "$(dirname "$0")/.."
docker compose --profile testing up -d
echo "Aplicação disponível em http://localhost:8000"
