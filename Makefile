#-----------------------------------------------------------
# Docker
#-----------------------------------------------------------

# Wake up docker containers
up:
	docker-compose up -d

# Shut down docker containers
down:
	docker-compose down

# Show a status of each container
status:
	docker-compose ps

# Status alias
s: status

# Show logs of each container
logs:
	docker-compose logs

# Restart all containers
restart: down up

# Restart the node container
restart-node:
	docker-compose restart node

# Restart the node container alias
rn: restart-node

# Build and up docker containers
build:
	docker-compose up -d --build

# Build and up docker containers
rebuild: down build

# Shut down and remove all volumes
remove-volumes:
	docker-compose down --volumes

# Remove all existing networks (usefull if network already exists with the same attributes)
prune-networks:
	docker network prune

# Run terminal of the php container
php:
	docker-compose exec php-cli bash

# Run terminal of the node container
node:
	docker-compose exec node-cli /bin/sh

#-----------------------------------------------------------
# Logs
#-----------------------------------------------------------

# Clear file-based logs
logs-clear:
	sudo rm docker/nginx/logs/*.log
	sudo rm docker/supervisor/logs/*.log
	sudo rm api/storage/logs/*.log


#-----------------------------------------------------------
# Database
#-----------------------------------------------------------

# Run database migrations
db-migrate:
	docker-compose exec php-cli php artisan migrate

# Migrate alias
migrate: db-migrate

# Run migrations rollback
db-rollback:
	docker-compose exec php-cli php artisan rollback

# Rollback alias
rollback: db-rollback

# Run seeders
db-seed:
	docker-compose exec php-cli php artisan db:seed

# Fresh all migrations
db-fresh:
	docker-compose exec php-cli php artisan migrate:fresh

# Dump database into file
db-dump:
	docker-compose exec postgres pg_dump -U app -d app > docker/postgres/dumps/dump.sql


#-----------------------------------------------------------
# Redis
#-----------------------------------------------------------

redis:
	docker-compose exec redis redis-cli

redis-flush:
	docker-compose exec redis redis-cli FLUSHALL

redis-install:
	docker-compose exec php-cli composer require predis/predis


#-----------------------------------------------------------
# Queue
#-----------------------------------------------------------

# Restart queue process
queue-restart:
	docker-compose exec php-cli php artisan queue:restart


#-----------------------------------------------------------
# Testing
#-----------------------------------------------------------

# Run phpunit tests
test:
	docker-compose exec php-cli vendor/bin/phpunit --order-by=defects --stop-on-defect

# Run all tests ignoring failures.
test-all:
	docker-compose exec php-cli vendor/bin/phpunit --order-by=defects

# Run phpunit tests with coverage
coverage:
	docker-compose exec php-cli vendor/bin/phpunit --coverage-html tests/report

# Run phpunit tests
dusk:
	docker-compose exec php-cli php artisan dusk

# Generate metrics
metrics:
	docker-compose exec php-cli vendor/bin/phpmetrics --report-html=api/tests/metrics api/app


#-----------------------------------------------------------
# Dependencies
#-----------------------------------------------------------

composer-install:
	docker-compose exec php-cli composer install

composer-update:
	docker-compose exec php-cli composer install

yarn-update:
	docker-compose exec node-cli yarn update

dependencies-update: composer-update yarn-update

yarn-outdated:
	docker-compose exec yarn outdated

composer-outdated:
	docker-compose exec yarn outdated

deps-outdated: yarn-update composer-outdated


#-----------------------------------------------------------
# Tinker
#-----------------------------------------------------------

# Run tinker
tinker:
	docker-compose exec php-cli php artisan tinker


#-----------------------------------------------------------
# Installation
#-----------------------------------------------------------

# Copy the Laravel API environment file
env-api:
	cp .env.api api/.env

# Copy the NuxtJS environment file
env-client:
	cp .env.client client/.env

# Add permissions for Laravel cache and storage folders
permissions:
	sudo chmod -R 777 api/bootstrap/cache
	sudo chmod -R 777 api/storage

# Permissions alias
perm: permissions

# Generate a Laravel app key
key:
	docker-compose exec php-cli php artisan key:generate --ansi

# PHP composer autoload comand
autoload:
	docker-compose exec php-cli composer dump-autoload

# Install the app
install: build env-api env-client composer-install key permissions migrate rn


#-----------------------------------------------------------
# Git commands
#-----------------------------------------------------------

# Git commit undo
gc-undo:
	git reset --soft HEAD~1

# Git commit 'WIP'
gc-wip:
	git add .
	git commit -m "WIP"
