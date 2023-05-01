include application/.env
sail := application/vendor/bin/sail

$(eval export $(shell sed -ne 's/ *#.*$$//; /./ s/=.*$$// p' application/.env))

.PHONY: rm
rm:
	$(sail) down -v

.PHONY: docker-setup
docker-setup:
	$(sail) up -d # get services running
	sleep 20

.PHONY: backend-install
backend-install:
	$(sail) composer i

.PHONY: backend-setup
backend-setup:
	make backend-install
	$(sail) artisan key:generate

.PHONY: migrate
migrate:
	$(sail) artisan migrate --seed

.PHONY: frontend-clean
frontend-clean:
	rm -rf node_modules 2>/dev/null || true
	rm package-lock.json 2>/dev/null || true
	rm yarn.lock 2>/dev/null || true
	$(sail) yarn cache clean

.PHONY: frontend-install
frontend-install:
	make frontend-clean
	$(sail) yarn install

.PHONY: dev
dev:
	make docker-setup
	make backend-setup
	make frontend-install

.PHONY: up
up:
	$(sail) up -d

.PHONY: watch
watch:
	$(sail) up -d && $(sail) npx vite

.PHONY: build
build:
	$(sail) npx vite build

.PHONY: down
down:
	$(sail) down

.PHONY: sh
sh:
	$(sail) shell $(filter-out $@,$(MAKECMDGOALS))

.PHONY: artisan
artisan:
	$(sail) artisan $(filter-out $@,$(MAKECMDGOALS))

.PHONY: test-backend
test-backend:
	$(sail) php ./vendor/bin/pest