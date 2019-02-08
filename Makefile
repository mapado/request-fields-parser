.PONY: install

install: vendor

vendor: composer.lock
	composer install

test: vendor
	vendor/bin/atoum -d tests
