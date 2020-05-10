#! /bin/bash
# Script runs tests

COMMAND=$1

function help() {
    echo "all - runs all tests"
    echo "phpstan - runs PHPStan"
    echo "doctrine - validates entities"
    echo "tester - runs Nette Tester tests"
}

# Runs PHPStan
function phpstan() {
    echo -e "PHPStan"
    vendor/bin/phpstan analyse app tests -c phpstan.neon
}

# Validates entities
function doctrine() {
    echo -e "Doctrine entities validation"
    php "index.php" orm:validate-schema
}

# Runs Nette Tester tests
function tester() {
    # Default shell script for running tests from `tests` folder
    echo -e "Nette Tester"
    vendor/bin/tester -C -s tests tests

    # PHP version of script for running tests
    #php ../vendor/nette/tester/src/tester.php -C -s tests test
}

function all() {
    phpstan
    doctrine
    tester
}

function ci() {
	local results=0
    phpstan || results=$((results+$?))
    tester || results=$((results+$?))
    exit ${results}
}

if [[ "${COMMAND}" == "" ]]; then
    help
fi

# run command
$COMMAND
