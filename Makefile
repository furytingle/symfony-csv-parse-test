APP=php-fpm

run-composer-install:
	docker exec -it $(APP) composer install --prefer-dist
