include .env.example
serviceList=php nginx mysql redis
sshContainer=php
mysqlContainer=mysql

build: ## Builds docker-compose
	@cp .env.example .env
	@make set-app-slug
	@make set-user-group
	@make generate-db-password
	cd .docker && docker-compose build --no-cache $(sshContainer)

set-app-slug: ## Converts APP_NAME to DOCKER_APP_SLUG
	@APP_NAME=$$(grep '^APP_NAME=' .env | cut -d '=' -f2-); \
	SLUG=$$(echo $$APP_NAME | tr '[:upper:]' '[:lower:]' | sed 's/ /-/g'); \
	sed -i.bak -e "s/^DOCKER_APP_SLUG=.*/DOCKER_APP_SLUG=$$SLUG/" .env && rm -f .env.bak

set-user-group: ## Set user and group IDs in .env
	@sed -i.bak -e "s/^DOCKER_USER_ID=.*/DOCKER_USER_ID=$(shell id -u)/" .env && rm -f .env.bak
	@sed -i.bak -e "s/^DOCKER_GROUP_ID=.*/DOCKER_GROUP_ID=$(shell id -g)/" .env && rm -f .env.bak

generate-db-password: ## Generate and set a random DB_PASSWORD in .env
	@PASS=$$(cat /dev/urandom | tr -dc 'A-Za-z0-9' | head -c 10); \
	sed -i.bak -e "s/^DB_PASSWORD=.*/DB_PASSWORD=$$PASS/" .env && rm -f .env.bak

install: ## First installation
	rm -rf .docker/mysql/volumes/* && \
	make restart && \
	docker-compose exec $(sshContainer) bash -c "\
		composer install && \
		composer dump-autoload && \
		php artisan migrate:fresh && \
		php artisan db:seed && \
		php artisan key:generate" && \
	make restart

kill: ## Stops all docker containers
	docker stop $(shell docker ps -aq)

start: ## Starts docker-compose
	docker-compose up -d $(serviceList) && docker-compose exec $(sshContainer) bash -c "php artisan queue:work --daemon &"

stop: ## Stops docker-compose
	docker-compose down

restart: ## Stops docker-compose and starts docker-compose
	make stop && make start

ssh: ## SSH to docker-compose
	docker-compose exec $(sshContainer) bash

db-create: ## Create MySQL database
	DB_PASSWORD=$$(grep '^DB_PASSWORD=' .env | cut -d '=' -f2); \
	DB_USERNAME=$$(grep '^DB_USERNAME=' .env | cut -d '=' -f2); \
	DB_DATABASE=$$(grep '^DB_DATABASE=' .env | cut -d '=' -f2); \
	docker-compose exec $(mysqlContainer) bash -c "mysql -h db -u$$DB_USERNAME -p'$$DB_PASSWORD' -e 'CREATE DATABASE IF NOT EXISTS $$DB_DATABASE;'"

prune: ## Clear build cache
	sudo docker system prune -af
