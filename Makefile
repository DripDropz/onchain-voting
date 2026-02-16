.PHONY: help
help:
	@echo "Onchain Voting - Available Commands"
	@echo ""
	@echo "  make init          - Full fresh setup (removes containers, rebuilds, installs, configures)"
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
	@echo "This will set up the complete local development environment."
	@echo ""
	@echo -n "Continue with setup? [y/N]: " && read ans && [ $${ans:-N} = y ] || exit 0
	@echo ""
	@echo "==> Step 1: Removing old containers and volumes..."
	@make clean
	@echo ""
	@echo "==> Step 2: Copying environment files..."
	@cp -n application/.env.example application/.env 2>/dev/null || true
	@cp -n serverless-lucid/.env.example serverless-lucid/.env 2>/dev/null || true
	@echo ""
	@echo "==> Step 3: Starting Docker services..."
	@make up
	@echo ""
	@echo "==> Waiting for services to be ready (30 seconds)..."
	@sleep 30
	@echo ""
	@echo "==> Step 4: Installing composer dependencies (inside container)..."
	@docker exec chainvote-app bash -c "cd /var/www/html && composer install --ignore-platform-reqs --no-interaction" || { echo "Composer install failed"; exit 1; }
	@echo ""
	@echo "==> Step 5: Cardano Network Selection"
	@echo "----------------------------------------------"
	@echo "Available networks:"
	@echo "  1) Preview    (cardano-preview.blockfrost.io)"
	@echo "  2) Preprod   (cardano-preprod.blockfrost.io)"
	@echo "  3) Mainnet   (cardano-mainnet.blockfrost.io)"
	@echo ""
	@echo -n "Select network [1]: " && read network_choice; \
	network_choice=$${network_choice:-1}; \
	case $$network_choice in \
		1) echo "preview" > /tmp/chainvote_network ;; \
		2) echo "preprod" > /tmp/chainvote_network ;; \
		3) echo "mainnet" > /tmp/chainvote_network ;; \
		*) echo "preview" > /tmp/chainvote_network ;; \
	esac
	@network=$$(cat /tmp/chainvote_network); \
	echo "Selected network: $$network"
	@echo ""
	@echo "==> Step 6: Blockfrost API Configuration"
	@echo "----------------------------------------------"
	@network=$$(cat /tmp/chainvote_network); \
	case $$network in \
		preview) \
			echo "Enter your Blockfrost Preview Project ID:"; \
			echo "Get it from: https://blockfrost.io/dashboard/preview"; \
			echo -n "[leave empty for local dev only]: " && read bf_id; \
			if [ -n "$$bf_id" ]; then \
				docker exec chainvote-app bash -c "sed -i 's/BLOCKFROST_PROJECT_ID=.*/BLOCKFROST_PROJECT_ID=$$bf_id/' /var/www/html/.env"; \
				docker exec chainvote-serverless sh -c "sed -i 's/BF_PREVIEW_ID=.*/BF_PREVIEW_ID=$$bf_id/' /code/.env"; \
			fi; \
			docker exec chainvote-app bash -c "sed -i 's|CARDANO_LUCID_NETWORK=.*|CARDANO_LUCID_NETWORK=preview|' /var/www/html/.env"; \
			docker exec chainvote-app bash -c "sed -i 's|blockfrost.io/api/v0|cardano-preview.blockfrost.io/api/v0|' /var/www/html/.env"; \
			docker exec chainvote-app bash -c "sed -i 's/CARDANO_NETWORK=.*/CARDANO_NETWORK=0/' /var/www/html/.env" \
			;; \
		preprod) \
			echo "Enter your Blockfrost Preprod Project ID:"; \
			echo "Get it from: https://blockfrost.io/dashboard/preprod"; \
			echo -n "[leave empty for local dev only]: " && read bf_id; \
			if [ -n "$$bf_id" ]; then \
				docker exec chainvote-app bash -c "sed -i 's/BLOCKFROST_PROJECT_ID=.*/BLOCKFROST_PROJECT_ID=$$bf_id/' /var/www/html/.env"; \
				docker exec chainvote-serverless sh -c "sed -i 's/BF_PREPROD_ID=.*/BF_PREPROD_ID=$$bf_id/' /code/.env"; \
			fi; \
			docker exec chainvote-app bash -c "sed -i 's|CARDANO_LUCID_NETWORK=.*|CARDANO_LUCID_NETWORK=preprod|' /var/www/html/.env"; \
			docker exec chainvote-app bash -c "sed -i 's|blockfrost.io/api/v0|cardano-preprod.blockfrost.io/api/v0|' /var/www/html/.env"; \
			docker exec chainvote-app bash -c "sed -i 's/CARDANO_NETWORK=.*/CARDANO_NETWORK=1/' /var/www/html/.env" \
			;; \
		mainnet) \
			echo "Enter your Blockfrost Mainnet Project ID:"; \
			echo "Get it from: https://blockfrost.io/dashboard/mainnet"; \
			echo -n "[leave empty for local dev only]: " && read bf_id; \
			if [ -n "$$bf_id" ]; then \
				docker exec chainvote-app bash -c "sed -i 's/BLOCKFROST_PROJECT_ID=.*/BLOCKFROST_PROJECT_ID=$$bf_id/' /var/www/html/.env"; \
				docker exec chainvote-serverless sh -c "sed -i 's/BF_MAINNET_ID=.*/BF_MAINNET_ID=$$bf_id/' /code/.env"; \
			fi; \
			docker exec chainvote-app bash -c "sed -i 's|CARDANO_LUCID_NETWORK=.*|CARDANO_LUCID_NETWORK=mainnet|' /var/www/html/.env"; \
			docker exec chainvote-app bash -c "sed -i 's|blockfrost.io/api/v0|cardano-mainnet.blockfrost.io/api/v0|' /var/www/html/.env"; \
			docker exec chainvote-app bash -c "sed -i 's/CARDANO_NETWORK=.*/CARDANO_NETWORK=2/' /var/www/html/.env" \
			;; \
	esac
	@echo ""
	@echo "==> Step 7: App Configuration"
	@echo "----------------------------------------------"
	@echo -n "App URL [http://localhost:8080]: " && read app_url; \
		app_url=$${app_url:-http://localhost:8080}; \
		docker exec chainvote-app bash -c "sed -i 's|APP_URL=.*|APP_URL=$$app_url|' /var/www/html/.env"
	@echo ""
	@echo "==> Step 8: Generating application keys..."
	@docker exec -u sail chainvote-app bash -c "cd /var/www/html && php artisan key:generate --force"
	@docker exec -u sail chainvote-app bash -c "cd /var/www/html && php artisan ciphersweet:generate-key --force"
	@echo ""
	@echo "==> Step 9: Running database migrations..."
	@docker exec -u sail chainvote-app bash -c "cd /var/www/html && php artisan migrate --force"
	@echo ""
	@echo "==> Step 10: Seeding database..."
	@docker exec -u sail chainvote-app bash -c "cd /var/www/html && php artisan db:seed --class=RoleSeeder --force"
	@docker exec -u sail chainvote-app bash -c "cd /var/www/html && php artisan db:seed --class=AdminUserSeeder --force"
	@echo ""
	@echo "==> Step 11: Installing frontend dependencies..."
	@docker exec -u sail chainvote-app bash -c "cd /var/www/html && yarn install"
	@echo ""
	@echo "==> Step 12: Building frontend assets..."
	@docker exec -u sail chainvote-app bash -c "cd /var/www/html && yarn build"
	@echo ""
	@echo "==> Step 13: Fixing storage permissions..."
	@docker exec chainvote-app bash -c "chown -R sail:sail /var/www/html/storage /var/www/html/bootstrap/cache && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache"
	@echo ""
	@echo "==> Step 14: Fixing WASM modules..."
	@make wasm 2>/dev/null || true
	@rm -f /tmp/chainvote_network
	@echo ""
	@echo "=============================================="
	@echo "   Setup Complete!                          "
	@echo "=============================================="
	@echo ""
	@echo "Services are now running:"
	@echo "  - Main App:        http://localhost:8080"
	@echo "  - Admin Dashboard: http://localhost:8080/admin/dashboard"
	@echo "  - Vite Dev Server: http://localhost:5173"
	@echo "  - Lucid API:       http://localhost:3000"
	@echo "  - MinIO Console:   http://localhost:9001"
	@echo "  - MinIO (S3):     http://localhost:9000"
	@echo ""
	@echo "Admin Credentials:"
	@echo "  Username: chainvote"
	@echo "  Password: ouroboros"
	@echo ""
	@echo "To stop services:  make down"
	@echo "To start services: make up"
	@echo "To view logs:      make logs"
	@echo ""

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
	@docker exec chainvote-app bash -c "cd /var/www/html && yarn build"

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
	@echo "Clean complete!"

.PHONY: serverless-deploy
serverless-deploy:
	@cd serverless-lucid && \
	npm install && \
	serverless deploy
