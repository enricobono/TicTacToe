# Executables (local)
DOCKER_COMP = docker compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php

# Executables
PHP      = $(PHP_CONT) php
PHP_UNIT = $(PHP) bin/phpunit

# Misc
.DEFAULT_GOAL = help
.PHONY        : help build up start down logs test

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach

start: build up ## Build and start the containers

stop: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

bash: ## Connect to the FrankenPHP container via bash so up and down arrows go to previous commands
	@$(PHP_CONT) bash


## Quality
test:
	@$(PHP_UNIT) $(c)

tu:
	@$(PHP_UNIT) $(c) --testdox

quality:
	$(PHP) vendor/bin/php-cs-fixer fix
	$(PHP) vendor/bin/php-cs-fixer check
	$(PHP) vendor/bin/phpstan analyze
