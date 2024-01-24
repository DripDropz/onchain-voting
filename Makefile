include application/.env
sail := application/vendor/bin/sail

$(eval export $(shell sed -ne 's/ *#.*$$//; /./ s/=.*$$// p' application/.env))

.PHONY: init
init:
	docker run --rm --interactive --tty \
          --volume ${PWD}/application:/app \
          composer install --ignore-platform-reqs
	make up
	sleep 20
	make -j2 backend-install frontend-install lucid-install
	$(sail) artisan key:generate
	make migrate
	make seed

.PHONY: backend-install
backend-install:
	$(sail) composer i

.PHONY: frontend-install
frontend-install:
	make frontend-clean
	make lucid-install
	$(sail) yarn install

.PHONY: lucid-install
lucid-install:
	docker-compose run chainvote.lucid yarn install

.PHONY: up
up:
	$(sail) up -d

.PHONY: seed
seed:
	$(sail) artisan db:seed

.PHONY: migrate
migrate:
	$(sail) artisan migrate

.PHONY: watch
watch:
	$(sail) up -d && $(sail) npx vite

.PHONY: vite
vite:
	$(sail) npx vite

.PHONY: build
build:
	$(sail) npx vite build

.PHONY: sh
sh:
	$(sail) shell $(filter-out $@,$(MAKECMDGOALS))

.PHONY: artisan
artisan:
	$(sail) artisan $(filter-out $@,$(MAKECMDGOALS))

.PHONY: test-backend
test-backend:
	$(sail) php ./vendor/bin/pest

.PHONY: wasm
wasm:
	cp ./application/node_modules/lucid-cardano/esm/src/core/libs/cardano_message_signing/cardano_message_signing_bg.wasm application/node_modules/.vite/deps/cardano_message_signing_bg.wasm
	cp ./application/node_modules/lucid-cardano/esm/src/core/libs/cardano_multiplatform_lib/cardano_multiplatform_lib_bg.wasm application/node_modules/.vite/deps/cardano_multiplatform_lib_bg.wasm

.PHONY: down
down:
	$(sail) down


.PHONY: frontend-clean
frontend-clean:
	rm -rf application/node_modules 2>/dev/null || true
	rm package-lock.json 2>/dev/null || true
	rm yarn.lock 2>/dev/null || true
	$(sail) yarn cache clean

.PHONY: lucid-clean
lucid-clean:
	rm -rf lucid/node_modules 2>/dev/null || true
	rm lucid/package-lock.json 2>/dev/null || true
	rm lucid/yarn.lock 2>/dev/null || true

.PHONY: rm
rm:
	$(sail) down -v


.PHONY: logs
logs:
	docker logs --follow chainvote.test

.PHONY:deps
deps:
	cp -v application/node_modules/lucid-cardano/esm/src/core/libs/cardano_message_signing/cardano_message_signing_bg.wasm \
	application/node_modules/lucid-cardano/esm/src/core/libs/cardano_multiplatform_lib/cardano_multiplatform_lib_bg.wasm \
	application/node_modules/.vite/deps

.PHONY:lucid-standalone-up
lucid-standalone-up:
	$(MAKE) lucid-standalone-down
	docker compose -f lucid/docker-compose-standalone.yml build && \
	docker compose -f lucid/docker-compose-standalone.yml up -d

.PHONY:lucid-standalone-down
lucid-standalone-down:
	docker compose -f lucid/docker-compose-standalone.yml down --remove-orphans

.PHONY:lucid-standalone-status
lucid-standalone-status:
	docker compose -f lucid/docker-compose-standalone.yml ps
