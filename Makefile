.PONY: install

install: vendor

vendor: composer.lock
	composer install

test: vendor
	vendor/bin/atoum -d tests

phpstan: vendor
	vendor/bin/phpstan analyse
	
baseline: vendor
	vendor/bin/phpstan analyse --generate-baseline
