#!/usr/bin/env sh
set -eu
cd "$(dirname "$0")/.."
[ -f .env ] || cp .env.example .env
docker compose --profile testing build
docker compose up -d --wait db
docker compose run --rm app composer install --no-interaction --prefer-dist
if grep -q '^APP_KEY=$' .env; then
    docker compose run --rm app php artisan key:generate --force
fi
docker compose run --rm app php artisan migrate --seed --force
docker compose exec -T db mysql -uroot -proot -e "CREATE DATABASE IF NOT EXISTS confeitaria_e2e; GRANT ALL PRIVILEGES ON confeitaria_e2e.* TO 'confeitaria'@'%';"
docker compose --profile testing up -d
docker compose exec -T app-e2e php artisan migrate --seed --force
echo "Instalação concluída. Execute ./docker/start.sh e acesse http://localhost:8000"
