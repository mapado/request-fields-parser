.PONY: install

install: vendor

vendor: composer.lock
	composer install

test: vendor
	atoum -d tests
