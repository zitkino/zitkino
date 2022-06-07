.DEFAULT_GOAL := help
.PHONY: help

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

install: ## Installs and setups project
	composer install
	npm install

.PHONY: tests
tests: ## Runs all tests for project
	composer run fixer
	composer run phpstan
	composer run tester

entities: ## Generates entities from database to classes
	php "index.php" orm:convert-mapping --namespace="App\Models\\" --force --from-database annotation ".temp"

database.update:
	bash bin/database.sh update

validate: ## Validates project
	composer validate --no-interaction --verbose --with-dependencies
	php "index.php" orm:validate-schema

clean: ## Cleans cache
	php "index.php" orm:clear-cache:metadata
	php "index.php" nette:cache:purge
