.PHONY: help
help:
	@echo ""
	@echo "Onchain Voting - Available Commands"
	@echo ""
	@echo "  make init          - Run setup wizard (TUI)"
	@echo "  make up            - Start Docker services"
	@echo "  make down          - Stop Docker services"
	@echo "  make clean         - Remove all containers, volumes, and .env files"
	@echo "  make build         - Build frontend assets"
	@echo "  make watch         - Start Vite dev server"
	@echo "  make logs          - View app logs"
	@echo "  make logs-worker   - View worker logs"
	@echo "  make logs-lucid    - View lucid API logs"
	@echo "  make sh            - Shell into app container"
	@echo "  make artisan       - Run artisan commands (e.g., make artisan migrate)"
	@echo "  make test-backend  - Run backend tests"
	@echo ""

.PHONY: init
init:
	@echo "Creating environment files from templates..."
	@cp -n application/.env.example application/.env 2>/dev/null || true
	@cp -n serverless-lucid/.env.example serverless-lucid/.env 2>/dev/null || true
	@echo ""
	@bash $(CURDIR)/scripts/setup.sh

.PHONY: backend-install
backend-install:
	@docker exec chainvote-app bash -c "cd /var/www/html && composer install --ignore-platform-reqs --no-interaction"

.PHONY: frontend-install
frontend-install:
	@make frontend-clean
	@docker exec chainvote-app bash -c "cd /var/www/html && yarn install"

.PHONY: restart
restart:
	@make down
	@make up

.PHONY: up
up:
	@cp -n application/.env.example application/.env 2>/dev/null || true
	@cp -n serverless-lucid/.env.example serverless-lucid/.env 2>/dev/null || true
	@docker compose up -d --build
	@echo ""
	@echo "Services are now running:"
	@echo "  - Main App:        http://localhost:8080"
	@echo "  - Admin Dashboard: http://localhost:8080/admin/dashboard"
	@echo "  - Vite Dev Server: http://localhost:5173"
	@echo "  - Lucid API:       http://localhost:3000"
	@echo "  - MinIO Console:   http://localhost:9001"
	@echo "  - MinIO (S3):     http://localhost:9000"
	@echo ""
	@echo "To stop services:  make down"
	@echo "To view logs:      make logs"

.PHONY: down
down:
	@docker compose down

.PHONY: status
status:
	@docker compose ps

.PHONY: setup-db
setup-db:
	@docker exec chainvote-app bash -c "cd /var/www/html && php artisan migrate:fresh --seed --force"

.PHONY: seed
seed:
	@docker exec chainvote-app bash -c "cd /var/www/html && php artisan db:seed --force"

.PHONY: migrate
migrate:
	@docker exec chainvote-app bash -c "cd /var/www/html && php artisan migrate --force"

.PHONY: watch
watch:
	@docker compose up -d && docker exec chainvote-app bash -c "cd /var/www/html && yarn dev"

.PHONY: vite
vite:
	@docker exec chainvote-app bash -c "cd /var/www/html && yarn dev"

.PHONY: build
build:
	@docker exec chainvote-app bash -c "rm -f /var/www/html/public/hot" 2>/dev/null || true
	@docker exec chainvote-app bash -c "cd /var/www/html && yarn install && yarn build"
	@docker exec chainvote-app bash -c "rm -f /var/www/html/public/hot" 2>/dev/null || true

.PHONY: sh
sh:
	@docker exec -it chainvote-app bash

.PHONY: artisan
artisan:
	@docker exec chainvote-app bash -c "cd /var/www/html && php artisan $(filter-out $@,$(MAKECMDGOALS))"

.PHONY: test-backend
test-backend:
	@docker exec chainvote-app bash -c "cd /var/www/html && php ./vendor/bin/pest"

.PHONY: wasm
wasm:
	@mkdir -p application/node_modules/.vite/deps 2>/dev/null || true
	@cp -v application/node_modules/lucid-cardano/esm/src/core/libs/cardano_message_signing/cardano_message_signing_bg.wasm \
		application/node_modules/.vite/deps/ 2>/dev/null || true
	@cp -v application/node_modules/lucid-cardano/esm/src/core/libs/cardano_multiplatform_lib/cardano_multiplatform_lib_bg.wasm \
		application/node_modules/.vite/deps/ 2>/dev/null || true

.PHONY: frontend-clean
frontend-clean:
	@docker exec chainvote-app bash -c "rm -rf /var/www/html/node_modules" 2>/dev/null || true

.PHONY: rm
rm:
	@docker compose down -v

.PHONY: logs
logs:
	@docker logs --follow chainvote-app

.PHONY: logs-worker
logs-worker:
	@docker logs --follow chainvote-worker

.PHONY: logs-lucid
logs-lucid:
	@docker logs --follow chainvote-lucid

.PHONY: clean
clean:
	@echo "Stopping and removing all containers and volumes..."
	@docker compose down -v --remove-orphans 2>/dev/null || true
	@echo "Removing environment files..."
	@rm -f application/.env serverless-lucid/.env
	@echo "Removing node_modules..."
	@rm -rf application/node_modules
	@echo "Clean complete!"

.PHONY: serverless-deploy
serverless-deploy:
	@cd serverless-lucid && \
	npm install && \
	serverless deploy
