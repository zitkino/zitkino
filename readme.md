# Zitkino

[![Packagist](https://img.shields.io/packagist/v/zitkino/zitkino.svg)](https://packagist.org/packages/zitkino/zitkino)

Here is the code for the web [https://www.zitkino.cz](https://www.zitkino.cz).

## Installation
* download and install project via [Composer](https://getcomposer.org):
```bash
composer create-project zitkino/zitkino:dev-master
```
* build [Docker](https://docs.docker.com/get-started/overview/) container with dependencies of project with command in terminal:
```bash
bash bin/docker.sh start
```
* setup your database connection in file [`app/config/env/development.neon`](./app/config/env/development.neon)
* run Docker terminal:
```bash
bash bin/docker.sh ssh
```
* install dependencies:
```bash
make install
```
* create tables in your database in Docker terminal with
```bash
make database.update
```
* project will be available on `http://localhost`

## JavaScripts a Sass (CSS) styles
They are compiled via [Gulp](https://gulpjs.com) commands:

* `gulp` - for compiling and watching changed
* `gulp setup` - for compiling only

## Tests
To test the project, a script is prepared that can be run in Docker terminal via:
```bash
make tests
```

### [Nette Tester](https://tester.nette.org/cs/)
Used for backend testing. Can be run in Docker terminal via:
```bash
composer run tester
```

### [PHPStan](https://github.com/phpstan/phpstan)
It analyzes the code and looks for errors in it. It is configured in [`phpstan.neon`](./app/config/phpstan.neon). Can be run in Docker terminal via:
```bash
composer run phpstan
```
