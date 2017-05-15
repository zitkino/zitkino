# Zitkino

[![Join the chat at https://gitter.im/zitkino/zitkino](https://badges.gitter.im/zitkino/zitkino.svg)](https://gitter.im/zitkino/zitkino?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

Here is the code for the web [https://www.zitkino.cz](https://www.zitkino.cz).

## Installation
* Install this via [Composer](https://getcomposer.org): `composer create-project zitkino/zitkino:dev-master`
* Delete Doctrine tests in folder `libs\doctrine\cache` and `libs\doctrine\inflector`
* Rename in folder `app` file `default.neon` to `config.neon`
* Setup your database connection in file `app/database.ini` ([example of this file](https://github.com/hermajan/lib/blob/master/src/database/database.ini))
* Create tables in your database with SQL expressions in folder `db`
