.PHONY: up down sh init test fmt

up:
	docker compose up -d --build

down:
	docker compose down

sh:
	docker compose exec app bash

init: up
	# Crear proyecto Laravel dentro de src si no existe
	test -d src || mkdir -p src
	docker compose exec app sh -lc "cd /var/www/html && [ -f artisan ] || composer create-project laravel/laravel:^12.0 ."
	# .env + sqlite
	docker compose exec app sh -lc "cp -n .env.example .env || true"
	docker compose exec app sh -lc "php -r \"file_exists('database') || mkdir('database');\""
	docker compose exec app sh -lc "php -r \"touch('database/database.sqlite');\""
	# Config .env para sqlite
	docker compose exec app sh -lc "php -r \"\
	\$$c=file_get_contents('.env');\
	\$$c=preg_replace('/^DB_CONNECTION=.*/m','DB_CONNECTION=sqlite',\$$c);\
	\$$c=preg_replace('/^DB_HOST=.*/m','',\$$c);\
	\$$c=preg_replace('/^DB_PORT=.*/m','',\$$c);\
	\$$c=preg_replace('/^DB_DATABASE=.*/m','DB_DATABASE=\\/var\\/www\\/html\\/database\\/database.sqlite',\$$c);\
	\$$c=preg_replace('/^DB_USERNAME=.*/m','',\$$c);\
	\$$c=preg_replace('/^DB_PASSWORD=.*/m','',\$$c);\
	file_put_contents('.env',\$$c);\""
	# Key + migraciones base
	docker compose exec app php artisan key:generate
	docker compose exec app php artisan migrate

test:
	docker compose exec app php artisan test

fmt:
	docker compose exec app ./vendor/bin/pint

optimize:
	docker compose exec app php artisan optimize:clear

