## help - Display help about make targets for this Makefile
help:
	@cat Makefile | grep '^## ' --color=never | cut -c4- | sed -e "`printf 's/ - /\t- /;'`" | column -s "`printf '\t'`" -t

## clean - Cleans the project
clean:
	rm -rf vendor clover.xml clover.html bin .phpunit.cache

## codesniffer - Run linting on the PHP files
codesniffer:
	composer lint

## codesniffer-fix- Fix lint errors on PHP files
codesniffer-fix:
	composer fix

## coverage - Runs the test suite and generates a coverage report
coverage:
	composer coverage

## docs - Generate documentation for the library
docs:
	curl -LJs https://github.com/phpDocumentor/phpDocumentor/releases/download/v3.3.1/phpDocumentor.phar -o phpDocumentor.phar
	php phpDocumentor.phar -d lib -t docs

## install - Install dependencies
install: | update-examples-submodule
	composer install --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist

## lint - Lint the project
lint: codesniffer scan

## lint-fix - Fix linting errors
lint-fix: codesniffer-fix

## release - Cuts a release for the project on GitHub (requires GitHub CLI)
# tag = The associated tag title of the release
release:
	gh release create ${tag}

## scan - Runs security analysis on the project
scan:
	composer scan

## test - Test the project
test:
	composer test

## update - Update dependencies
update: | update-examples-submodule
	composer update

## update-examples-submodule - Update the examples submodule
update-examples-submodule:
	git submodule init
	git submodule update --remote

.PHONY: help clean codesniffer codesniffer-fix coverage docs install lint lint-fix release scan test update update-examples-submodule
