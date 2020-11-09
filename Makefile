#!/usr/bin/env bash

DOCKER 		   = docker
DOCKER_COMPOSE = docker-compose
PHP            = $(DOCKER_COMPOSE) exec -T php
SYMFONY        = $(PHP) bin/console --env=$(APP_ENV)
COMPOSER       = $(PHP) composer
QA             = $(DOCKER) run --rm -v `pwd`:/project mykiwi/phaudit:7.3

##
## Project Installation and Configuration
## ---------------------------------------
## 

.DEFAULT_GOAL := help

help: ## Output this help screen
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
.PHONY: help

##
## Docker
## -------
##
build: ## Build docker containers
	$(DOCKER_COMPOSE) build --pull

up: docker-compose.override.yaml ## Run docker containers
	$(DOCKER_COMPOSE) up -d

down: ## Stop and remove docker containers
	$(DOCKER_COMPOSE) down --remove-orphans

restart: ## Restart docker containers
	$(DOCKER_COMPOSE) restart

start: build up ## Building and running containers

ps: ## List the services
	$(DOCKER_COMPOSE) ps

.PHONY: build up down start ps

##
## Composer
## ----------------
##
composer.lock: composer.json
	$(COMPOSER) update --lock --no-scripts --no-interaction

vendor: composer.lock ## Install the php dependencies
	$(COMPOSER) install --prefer-dist --no-interaction --no-progress

composer-validate: composer.json ## Checks that the composer.json and composer.lock files are valid
	$(COMPOSER) validate --no-check-all

.PHONY: vendor composer-validate

##
## Database
## --------
##
database-setup: .env.local vendor ## Reset the database and load fixtures
	@$(PHP) php -r 'echo "Wait database...\n"; set_time_limit(30); require __DIR__."/config/bootstrap.php"; $$u = parse_url($$_ENV["DATABASE_URL"]); for(;;) { if(@fsockopen($$u["host"].":".($$u["port"] ?? 3306))) { break; }}'
	-$(SYMFONY) doctrine:database:drop --if-exists --force
	-$(SYMFONY) doctrine:database:create --if-not-exists
	$(SYMFONY) doctrine:migrations:migrate --no-interaction --allow-no-migration
	$(SYMFONY) doctrine:fixtures:load --no-interaction

database-validate-schema: .env.local vendor ## Validate the doctrine ORM mapping
	$(SYMFONY) doctrine:schema:validate --skip-sync --no-interaction

.PHONY: database-setup database-validate-schema ## Alias for creating database, migrating and loading fixtures

##
## Tests
## -----
##
test: tu tf ## Run unit and functional tests

tu: vendor .env.test ## Run unit tests
	$(PHP) bin/phpunit --exclude-group functional

tf: vendor .env.test ## Run functional tests
	$(PHP) bin/phpunit --group functional

.PHONY: test tu tf

.env.local: .env
	@if [ -f .env.local ]; \
	then\
		echo '\033[1;41m/!\ The .env file has changed. Please check your .env.local file (this message will not be displayed again).\033[0m' >&2;\
		touch .env.local;\
		exit 1;\
	else\
		echo cp .env .env.local;\
		cp .env .env.local;\
	fi

docker-compose.override.yaml: docker-compose.override.yaml.dist
	@if [ -f docker-compose.override.yaml ]; \
	then\
		echo '\033[1;41m/!\ The docker-compose.override.yaml.dist file has changed. Please check your docker-compose.override.yml file (this message will not be displayed again).\033[0m';\
		touch docker-compose.override.yaml;\
		exit 1;\
	else\
		echo cp docker-compose.override.yaml.dist docker-compose.override.yaml;\
		cp docker-compose.override.yaml.dist docker-compose.override.yaml;\
	fi

##
## Quality assurance
## -----------------
##
lint: ly ## Lints yaml files

ly: vendor
	$(SYMFONY) lint:yaml config

security: vendor ## Check security of your dependencies (https://security.sensiolabs.org/)
	$(SYMFONY) security:check

phpmd: ## PHP Mess Detector (https://phpmd.org)
	$(QA) phpmd src text .phpmd.xml

php_codesnifer: ## PHP_CodeSnifer (https://github.com/squizlabs/PHP_CodeSniffer)
	$(QA) phpcs -v --colors --standard=PSR12 src --ignore="*/migrations/*,Kernel.php" --extensions=php

phpcpd: ## PHP Copy/Paste Detector (https://github.com/sebastianbergmann/phpcpd)
	$(QA) phpcpd src

quality: phpmd php_codesnifer phpcpd ## Full quality assurance code

.PHONY: lint ly phpmd php_codesnifer phpcpd quality

##
## Utils
## -----
##

clear-cache: ## Clear symfony cache
	$(SYMFONY) cache:clear
.PHONY: clear-cache

symfony: ## Display available symfony commands
	$(SYMFONY)
.PHONY: symfony

##
## Clean / install
## ---------------
##

permissions:
	$(PHP) chmod -R 777 var/ public/

clean: ## Reset the project
	rm -rf var/ vendor/
.PHONY: clean

install: start clean vendor database-setup permissions clear-cache tu ## Full installation
.PHONY: install
