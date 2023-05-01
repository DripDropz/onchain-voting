include application/.env
sail := application/vendor/bin/sail

$(eval export $(shell sed -ne 's/ *#.*$$//; /./ s/=.*$$// p' application/.env))

.PHONY: init
init:
	docker run --rm --interactive --tty \
          --volume ${PWD}/application:/app \
          composer install --ignore-platform-reqs
	cp application/.env.example application/.env
	make up
	sleep 20
	make backend-install
	make frontend-install
	$(sail) artisan key:generate
	make migrate

.PHONY: backend-install
backend-install:
	$(sail) composer i

.PHONY: frontend-install
frontend-install:
	make frontend-clean
	$(sail) yarn install

.PHONY: up
up:
	$(sail) up -d

.PHONY: migrate
migrate:
	$(sail) artisan migrate --seed

.PHONY: watch
watch:
	$(sail) up -d && $(sail) npx vite

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


.PHONY: down
down:
	$(sail) down


.PHONY: frontend-clean
frontend-clean:
	rm -rf node_modules 2>/dev/null || true
	rm package-lock.json 2>/dev/null || true
	rm yarn.lock 2>/dev/null || true
	$(sail) yarn cache clean

.PHONY: rm
rm:
	$(sail) down -v


