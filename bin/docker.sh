#! /bin/bash

COMMAND=$1

function help() {
    echo -e "usage:
    \t bash docker.sh COMMAND"

    echo -e "commands:
    \t clean - cleans state of Docker for running container
    \t down - downs container (stops and removes it)
    \t list - lists containers
    \t logs - lists logs
    \t port [NUMBER] - stops containers on port from parameter NUMBER
    \t prune - prunes containers (removes unused containers)
    \t restart - downs and starts container
    \t ssh - logins to container
    \t start - builds and startups container
    \t stop - stops container"
}

function clean() {
    # stops ports needed for container
    local PORTS=(":80->80/" ":443->443/" ":3306->3306/")
    for PORT in "${PORTS[@]}"; do
        port "${PORT}"
    done
}

function down() {
    docker-compose down
}

function list() {
    docker-compose ps
}

function logs() {
    docker-compose logs
}

function port() {
    docker ps
    echo -e "\n"

    echo "Looking for a container on port: ${1///}"
    ID=$(docker container ls --format="{{.ID}}\t{{.Ports}}" | grep ${1///} | awk '{print $1}')

    # if ID is not empty
    if [ ! -z "${ID}" ]; then
        echo "Found container ID: ${ID} - stopping it"
        docker container stop ${ID}
    else
        echo "Not found container on port: ${1///}"
    fi
}

function prune() {
    docker system prune -a
}

function restart() {
    down
    start
}

function ssh() {
    local USER="root"

    if [[ "${1}" != "" ]]; then
        USER=$1
    fi

    docker exec -u ${USER} -it zitkino_www bash
}

function start() {
    clean
    echo -e "\n"
    docker-compose up -d --build
    echo -e "\n"
    list
}

function stop() {
    docker-compose stop
}

if [[ "${COMMAND}" == "" ]]; then
    help
fi

# run command
$COMMAND
