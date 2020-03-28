DOCKER_COMPOSE_DEV := docker-compose -f deploy/docker-compose.yaml

docker-up: .env
	$(DOCKER_COMPOSE_DEV) up

docker-down:
	$(DOCKER_COMPOSE_DEV) down --remove-orphans

docker-ssh:
	$(DOCKER_COMPOSE_DEV) exec bank-http-server sh

docker-build:
	$(DOCKER_COMPOSE_DEV) build

docker-test:
	$(DOCKER_COMPOSE_DEV) run --rm bank-http-server make test

.env:
	cp .env.dist .env

.PHONY: test
test:
	composer run test
