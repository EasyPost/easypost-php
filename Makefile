## help - Display help about make targets for this Makefile
help:
	@cat Makefile | grep '^## ' --color=never | cut -c4- | sed -e "`printf 's/ - /\t- /;'`" | column -s "`printf '\t'`" -t

## clean - Cleans the project
clean:
	rm -rf vendor clover.xml clover.html bin .phpunit.cache

## coverage - Runs the test suite and generates a coverage report
coverage:
	composer coverage

## docs - Generate documentation for the library
docs:
	curl -LJs https://github.com/phpDocumentor/phpDocumentor/releases/latest/download/phpDocumentor.phar -o phpDocumentor.phar
	php phpDocumentor.phar -d lib -t docs

## fix - Fix linting errors
fix:
	composer fix

# TODO: Change branch to master once example repo is updated
## install-style - Download style guide
install-style:
	curl -LJs https://raw.githubusercontent.com/EasyPost/examples/style_guides/phpcs.xml -o phpcs.xml
	sh adjust_php_style_guide.sh

## install - Install dependencies
install: | install-style
	git submodule init
	git submodule update
	composer install --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist

## lint - Lint the project
lint:
	composer lint

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
update:
	git submodule init
	git submodule update --remote
	composer update

.PHONY: help clean docs fix install install-style lint release scan test update
