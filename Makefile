DC    = docker compose
APP   = $(DC) exec -u sail app
FRONT = $(DC) exec frontend

.PHONY: setup up down migrate fresh test lint mutate api-types shell

setup: ## primeira vez: dependências, .env, chave, banco e seeders
	$(DC) up -d --build pgsql mailpit app
	$(DC) exec app chown sail:sail /var/www/html/vendor
	$(APP) composer install --no-interaction
	$(APP) sh -c '[ -f .env ] || cp .env.example .env'
	$(APP) php artisan key:generate --no-interaction
	$(APP) php artisan migrate:fresh --seed --no-interaction
	$(DC) restart app
	$(DC) run --rm -u root frontend chown node:node /app/node_modules
	$(DC) run --rm frontend npm ci
	$(DC) up -d

up:
	$(DC) up -d

down:
	$(DC) down

migrate:
	$(APP) php artisan migrate

fresh: ## recria o banco de dev com os seeders
	$(APP) php artisan migrate:fresh --seed

test:
	$(APP) vendor/bin/pest

lint:
	$(APP) vendor/bin/pint --test
	$(APP) vendor/bin/phpstan analyse --no-progress --memory-limit=1G
	$(FRONT) npm run lint
	$(FRONT) npm run typecheck

BASE ?= origin/main

mutate: ## antes do PR: cobertura de linhas de 100% e mutação nas classes alteradas em relação à BASE (padrão origin/main), nota mínima 90%
	$(APP) vendor/bin/pest --parallel --coverage --min=100
	@classes="$$(scripts/changed-php-classes.sh $(BASE))"; \
	if [ -z "$$classes" ]; then echo "Nenhuma classe de backend/app alterada em relação a $(BASE)."; \
	else $(APP) vendor/bin/pest --mutate --class="$$classes" --covered-only --parallel --min=90; fi

api-types: ## exporta o OpenAPI e regenera os tipos do front
	$(DC) exec -u sail -e APP_URL=http://localhost app php artisan scramble:export --path=storage/app/openapi.json
	cp backend/storage/app/openapi.json frontend/openapi.json
	$(FRONT) npm run api-types

shell:
	$(APP) bash
