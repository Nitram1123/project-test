DOCKER_COMP   = docker compose
SYMFONY_CLI   = symfony
PHP           = $(SYMFONY_CLI) php
COMPOSER      = $(SYMFONY_CLI) composer
SYMFONY       = $(PHP) bin/console
.DEFAULT_GOAL = help
.PHONY        = help build up start down logs sh composer vendor sf cc sf_start sf_stop init t-pu cbf t-ps t-pcs

## —— 🎵 🐳 Symfony Makefile 🐳 🎵 —————————————————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach

start: up sf_start ## Start the containers and run symfony server

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
vendor: composer

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
sf_start: ## Start the symfony server
	$(SYMFONY_CLI) server:start --no-tls -d

init: ## Generate the SSL keys
	$(PHP) bin/console lexik:jwt:generate-keypair --overwrite

sf_stop: ## Start the symfony server
	$(SYMFONY_CLI) server:stop

sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: c=c:c ## Clear the cache
cc: sf

t-pcs: ## Test phpcs
	$(PHP) ./vendor/bin/phpcs

cbf: ## Using the PHP Code Beautifier and Fixer
	$(PHP) ./vendor/bin/phpcbf

t-ps: ## Test phpstan
	$(PHP) -d memory_limit=-1 ./vendor/bin/phpstan analyse -c phpstan.neon

t-pu: ## Test phpunit
	make sf c="doctrine:database:drop --force"
	make sf c="doctrine:database:create"
	make sf c="doctrine:schema:update --force"
	make sf c="app:member:new Bob bob --admin"
	make sf c="app:member:new Alice alice"
	$(PHP) ./bin/phpunit -c phpunit.xml.dist