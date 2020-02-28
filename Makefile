up: docker-up
down: docker-down
restart: docker-down docker-up
init: docker-down-clear docker-pull docker-build docker-up api-composer api-lint api-phpstan api-oauth-keys api-migrations api-fixtures api-test frontend-assets-install frontend-assets-build
test: api-test

docker-up:
	docker-compose up --build -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

api-composer:
	docker-compose run --rm api-php-cli composer install

api-migrations:
	docker-compose run --rm api-php-cli php bin/console doctrine:migrations:migrate --no-interaction

api-fixtures:
	docker-compose run --rm api-php-cli php bin/console doctrine:fixtures:load --no-interaction

api-oauth-keys:
	docker-compose run --rm api-php-cli mkdir -p var/oauth
	docker-compose run --rm api-php-cli openssl genrsa -out var/oauth/private.key 2048
	docker-compose run --rm api-php-cli openssl rsa -in var/oauth/private.key -pubout -out var/oauth/public.key
	docker-compose run --rm api-php-cli chgrp -R www-data var/oauth/private.key var/oauth/public.key
	docker-compose run --rm api-php-cli chmod -R ug+rwx var/oauth/private.key var/oauth/public.key
	docker-compose run --rm api-php-cli chmod 660 var/oauth/private.key var/oauth/public.key

api-permissions:
	sudo chown 777 api/var
	sudo chown 777 api/var/cache
	sudo chown 777 api/var/log
	sudo chown 777 storage/public/video

api-test:
	docker-compose run --rm api-php-cli php bin/phpunit

api-test-coverage:
	docker-compose run --rm api-php-cli php bin/phpunit --coverage-clover var/clover.xml --coverage-html var/coverage

api-test-unit:
	docker-compose run --rm api-php-cli php bin/phpunit --testsuite=unit

api-test-unit-coverage:
	docker-compose run --rm api-php-cli php bin/phpunit --testsuite=unit --coverage-clover var/clover.xml --coverage-html var/coverage

api-metrics:
	docker-compose run --rm api-php-cli php ./vendor/bin/phpmetrics --report-html="etc/alitics/metrics/report" ./src

api-lint:
	docker-compose run --rm api-php-cli composer lint
	docker-compose run --rm api-php-cli php ./vendor/bin/phpcs --warning-severity=6

api-phpstan:
	docker-compose run --rm api-php-cli php -d memory_limit=4G vendor/bin/phpstan analyse -l 8 src tests

api-cs-fix:
	docker-compose run --rm api-php-cli composer cs-fix

frontend-assets-install:
	docker-compose run --rm frontend-node npm install
	docker-compose run --rm frontend-node npm rebuild node-sass

frontend-assets-build:
	docker-compose run --rm frontend-node npm run build

frontend-assets-watch:
	docker-compose run --rm frontend-node npm run watch