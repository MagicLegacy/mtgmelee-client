.PHONY: install update phpcs phpcbf tests

PHP_FILES := $(shell find src tests -type f -name '*.php')

install:
	composer install

update:
	composer update

composer.lock: composer.json
	composer install

vendor/bin/phpunit: composer.lock

build/reports/cs/MagicLegacy.xml: composer.lock $(PHP_FILES)
	mkdir -p build/reports/cs
	./vendor/bin/phpcs --standard=./config/phpcs/MagicLegacy.xml --cache=./build/cs_magiclegacy.cache -p --report-full --report-checkstyle=./build/reports/cs/MagicLegacy.xml src/ tests/

build/reports/php74/compatibility_check.xml: composer.lock $(PHP_FILES)
	mkdir -p build/reports/php74
	./vendor/bin/phpcs --standard=./config/phpcs/PHP74Compatibility.xml --cache=./build/php74.cache -p --report-full --report-checkstyle=./build/reports/php74/compatibility_check.xml src/ tests/

build/reports/php80/compatibility_check.xml: composer.lock $(PHP_FILES)
	mkdir -p build/reports/php80
	./vendor/bin/phpcs --standard=./config/phpcs/PHP80Compatibility.xml --cache=./build/php80.cache -p --report-full --report-checkstyle=./build/reports/php80/compatibility_check.xml src/ tests/

phpcs: build/reports/cs/MagicLegacy.xml

php74compatibility: build/reports/php74/compatibility_check.xml

php80compatibility: build/reports/php80/compatibility_check.xml

phpcbf: composer.lock
	./vendor/bin/phpcbf --standard=./config/phpcs/MagicLegacy.xml src/ tests/

build/reports/phpunit/unit.xml build/reports/phpunit/unit.cov: vendor/bin/phpunit $(PHP_FILES)
	mkdir -p build/reports/phpunit
	php -dzend_extension=xdebug.so ./vendor/bin/phpunit -c ./phpunit.xml.dist --coverage-clover=./build/reports/phpunit/clover.xml --log-junit=./build/reports/phpunit/unit.xml --coverage-php=./build/reports/phpunit/unit.cov --coverage-html=./build/reports/coverage/ --fail-on-warning

tests: build/reports/phpunit/unit.xml build/reports/phpunit/unit.cov

testdox: vendor/bin/phpunit $(PHP_FILES)
	php -dzend_extension=xdebug.so ./vendor/bin/phpunit -c ./phpunit.xml.dist --fail-on-warning --testdox
