## General
.DEFAULT_GOAL := help
help: ## Show the help
	    @grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

# Constants
DOCKER_COMPOSE = docker-compose

# Environments
ENV_PHP = $(DOCKER_COMPOSE) exec -u symfony web
ENV_ROOT_PHP = $(DOCKER_COMPOSE) exec web
ENV_ENCORE = $(DOCKER_COMPOSE) exec -u symfony encore

# Tools
COMPOSER = $(DOCKER_COMPOSE) run -u symfony web composer
YARN = $(DOCKER_COMPOSE) run -u symfony encore yarn

## Docker managment
.PHONY: build
build: docker-compose.yml docker/apache/Dockerfile ## Build docker images
	    make build-database
	    make build-encore
	    make build-web-dev

.PHONY: build-database
build-database: docker-compose.yml docker/database/Dockerfile ## Build docker database image
	    $(DOCKER_COMPOSE) build --build-arg UID=$(shell id -u) --build-arg GID=$(shell id -g) database

.PHONY: build-encore
build-encore: docker-compose.yml docker/node/Dockerfile ## Build docker encore image
	    $(DOCKER_COMPOSE) build --build-arg UID=$(shell id -u) --build-arg GID=$(shell id -g) encore

.PHONY: build-web-base
build-web-base: docker-compose.yml docker/apache/Dockerfile ## Build base docker web image
	    docker build -t so-vue:base -f docker/apache/Dockerfile docker/apache

.PHONY: build-web-dev
build-web-dev: docker-compose.yml docker/apache/Dockerfile.dev ## Build dev docker web image
	    make build-web-base
	    $(DOCKER_COMPOSE) build --build-arg UID=$(shell id -u) --build-arg GID=$(shell id -g) web

.PHONY: build-web-prod
build-web-prod: docker/apache/Dockerfile.prod ## Build production docker web image
	    make build-base-web
	    docker build -t so-vue:prod -f docker/apache/Dockerfile.prod .

.PHONY: clean
clean: docker-compose.yml ## Clean the PHP and JS libraries
	    $(ENV_ROOT_ENV) rm -rf ./node_modules ./vendor

.PHONY: create
create: docker-compose.yml ## Build docker images, run the containers, install PHP libraries, create database,
	    make build
	    make pinstall
	    make einstall
	    $(DOCKER_COMPOSE) up -d --remove-orphans --force-recreate
	    make create-db
	    make create-schema

.PHONY: down
down: docker-compose.yml ## Kill the containers
	    $(DOCKER_COMPOSE) down

.PHONY: recreate
recreate: docker-compose.yml ## Restart the containers and install PHP libraries
	    make pinstall
	    make einstall
	    $(DOCKER_COMPOSE) up -d --build --remove-orphans --force-recreate
	    make cache-clear

.PHONY: stop
stop: docker-compose.yml ## Stop the containers
	    $(DOCKER_COMPOSE) stop

.PHONY: up
up: docker-compose.yml ## Start the containers
	    $(DOCKER_COMPOSE) up -d

## PHP commands
.PHONY: pinstall
pinstall: symfony/composer.json ## Install PHP libaries
	    $(COMPOSER) install

.PHONY: pupdate
pupdate: symfony/composer.json ## Update PHP libraries
	    $(COMPOSER) update

.PHONY: prequire
prequire: symfony/composer.json ## Require a new PHP libary
	    $(COMPOSER) require $(PACKAGE)

.PHONY: prequire-dev
prequire-dev: symfony/composer.json ## Require a new PHP libary for dev
	    $(COMPOSER) require --dev $(PACKAGE)

.PHONY: premove
premove: symfony/composer.json ## Remove a PHP library
	    $(COMPOSER) remove $(PACKAGE)

.PHONY: psh
psh: docker-compose.yml ## Jump into the PHP container
	    $(ENV_PHP) /bin/sh

.PHONY: prsh
prsh: docker-compose.yml ## Jump into the PHP container as root
	    $(ENV_ROOT_PHP) /bin/sh

## Encore commands
.PHONY: einstall
einstall: symfony/package.json ## Install JS/CSS libraries
	    $(YARN)

.PHONY: eupdate
eupdate: symfony/package.json ## Update JS/CSS libraries
	    $(YARN) upgrade

.PHONY: esh
esh: symfony/package.json ## Jump into encore container
	    $(ENV_ENCORE) sh

.PHONY: elog
elog: symfony/package.json ## Show the logs of the encore container
	    $(DOCKER_COMPOSE) logs -f encore

.PHONY: erestart
erestart: symfony/package.json ## Restart the encore container
	    $(DOCKER_COMPOSE) restart encore

## Symfony commands
.PHONY: cache-clear
cache-clear: symfony/var/cache/ ## Purge Symfony cache
	    $(ENV_PHP) rm -rf ./var/cache/*

router: symfony/config/routes/ ## Print router
	    $(ENV_PHP) php bin/console debug:router

## Tools commands
.PHONY: dump
dump: symfony/vendor/bin/var-dump-check ## Check if there is no dump in the code base
	    $(ENV_PHP) php vendor/bin/var-dump-check --no-colors --symfony --exclude bin --exclude public --exclude var --exclude vendor .

.PHONY: phpcs
phpcs: symfony/vendor/bin/php-cs-fixer symfony/.php_cs.dist ## Launch php-cs-fixer
	    $(ENV_PHP) php vendor/bin/php-cs-fixer fix --config=.php_cs.dist

.PHONY: php-cs-dry-run
php-cs-dry-run: symfony/vendor/bin/php-cs-fixer ## Launch php-cs-fixer but no modifications are made
	    make phpcs --dry-run

.PHONY: phpstan
phpstan: symfony/vendor/bin/phpstan symfony/.phpstan.neon ## Launch phpstan verification
	    $(ENV_PHP) php vendor/bin/phpstan analyse -c .phpstan.neon

.PHONY: phpunit
phpunit: symfony/bin/phpunit ## Lauch phpunit test
	    $(ENV_PHP) php bin/phpunit

## Doctrine commands
.PHONY: create-database
create-db: symfony/bin/console ## Create database if not exists
	    $(ENV_PHP) php bin/console doctrine:database:create --if-not-exists --no-interaction

.PHONY: create-schema
create-schema: symfony/bin/console ## Create schema
	    $(ENV_PHP) php bin/console doctrine:schema:create --no-interaction

.PHONY: drop-database
drop-db: symfony/bin/console ## Drop database if exists
	    $(ENV_PHP) php bin/console doctrine:database:drop --if-exists --force --no-interaction

.PHONY: drop-schema
drop-schema: symfony/bin/console ## Drop schema
	    $(ENV_PHP) php bin/console doctrine:schema:drop --force --no-interaction

.PHONY: load-fixtures
load-fixtures: symfony/bin/console symfony/src/DataFixtures ## Load fixtures
	    $(ENV_PHP) php bin/console doctrine:fixtures:load --no-interaction

.PHONY: migrate
migrate: symfony/bin/console symfony/src/Migrations ## Execute migrations
	    $(ENV_PHP) php bin/console doctrine:migration:migrate
