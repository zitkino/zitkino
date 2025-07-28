#! /bin/bash
# Script handles database

COMMAND=$1

function help() {
	echo "clean - cleans cache"
	echo "entities - generates entities from database to classes"
	echo "update - updates database schema from entities"
	echo "validate - validates schema"
}

# Cleans cache
function clean() {
	php bin/console doctrine:cache:clear-metadata
}

# Generates entities from database to classes
function entities() {
	php bin/console doctrine:mapping:convert --namespace="App\Models\\" --force --from-database annotation ".temp"
}

# Exports structure and data from database
function export() {
	mysqldump --comments --create-options --host db --no-data -p zitkino > "db/zitkino.sql"
	mysqldump --comments --host db --no-create-info -p zitkino > "db/zitkino-data.sql"
}

# Updates database schema from entities
function update() {
	php bin/console doctrine:schema:update --dump-sql

	echo -e "\nDo you want to update schema?"
	select structure in "Yes" "No"; do
		case $structure in
		"Yes")
			php bin/console doctrine:schema:update --force
			break
			;;
		"No")
			echo "Nothing was updated!"
			break
			;;
		*)
			echo "Nothing was updated!"
			break
			;;
		esac
	done
}

# Validates schema
function validate() {
	php bin/console doctrine:schema:validate
}

if [ "${COMMAND}" == "" ]; then
	help
fi

# run command
$COMMAND
