#! /bin/bash

COMMAND=$1

function help() {
	echo "setup - setup system settings"
    echo "start - build and up container"
    echo "stop - down container"
    echo "logs - list logs"
    echo "list - list containers"
    echo "restart - down and start container"
    echo "ssh - login to container"
}

function start() {
	export DOCKER_HOST='tcp://127.0.0.1:2375'
    docker-compose up -d --build
}

function stop() {
    docker-compose down
}

function clean() {
	docker system prune -a
}

function logs() {
	export DOCKER_HOST='tcp://127.0.0.1:2375'
    sudo -E docker-compose logs
}

function list() {

    docker-compose ps
}

function restart() {
    stop
    start
}

function ssh() {
    local USER="root"

    if [ "${1}" != "" ]; then
        USER=$1
    fi

    ../docker exec -u ${USER} -it ${PWD##*/} bash
}

if [ "${COMMAND}" == "" ]; then
    help
fi

# run command
$COMMAND
