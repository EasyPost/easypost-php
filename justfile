# Cleans the project
clean:
    rm -rf vendor clover.xml clover.html bin .phpunit.cache

# Run linting on the PHP files
codesniffer:
    composer lint

# Fix lint errors on PHP files
codesniffer-fix:
    composer fix

# Runs the test suite and generates a coverage report
coverage:
    composer coverage

# Generate documentation for the library
docs:
    curl -LJs https://github.com/phpDocumentor/phpDocumentor/releases/download/v3.3.1/phpDocumentor.phar -o phpDocumentor.phar
    php phpDocumentor.phar -d lib -t docs

# Initialize the examples submodule
init-examples-submodule:
    git submodule init
    git submodule update

# Install dependencies
install: init-examples-submodule
    composer install --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist

# Lint the project
lint: codesniffer phpstan scan

# Fix linting errors
lint-fix: codesniffer-fix

# Scan for static analysis errors
phpstan:
    composer phpstan

# Cuts a release for the project on GitHub (requires GitHub CLI)
# tag = The associated tag title of the release
# target = Target branch or full commit SHA
release tag target:
    gh release create {{tag}} --target {{target}}

# Runs security analysis on the project
scan:
    composer scan

# Test the project
test:
    composer test

# Update dependencies
update: update-examples-submodule
    composer update

# Update the examples submodule
update-examples-submodule:
    git submodule init
    git submodule update --remote
