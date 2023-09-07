.PONY: install

install: vendor node_modules

vendor: composer.lock
	composer install 
	## change the date of vendor dir to avoid regerate it each time with make
	touch -d "now" vendor

	
node_modules: package-lock.json
	npm i install
	## change the date of vendor dir to avoid regerate it each time with make
	touch -d "now" node_modules

test: vendor
	vendor/bin/phpunit tests

phpstan: vendor
	vendor/bin/phpstan analyse
	
baseline: vendor
	vendor/bin/phpstan analyse --generate-baseline
