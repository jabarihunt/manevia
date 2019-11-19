#!/bin/bash

docker ps
docker build ../. --build-arg PORT=8080 -t gcr.io/manevia/manevia-app:1.0.0 -t gcr.io/manevia/manevia-app:latest
PORT=8080 && \
	docker run \
	-p 8080:${PORT} \
	-e PORT=${PORT} \
	-e DATABASE_HOST='DATABASE-HOST-OR-IP' \
	-e DATABASE_NAME='manevia_db' \
	-e DATABASE_USER='manevia-db-user' \
	-e DATABASE_PASSWORD='SUPER-SECRET-PASSWORD' \
	-e DATABASE_SOCKET=NULL \
	-e DATABASE_SESSION_STORE_IN_DB=0 \
	-e DATABASE_SESSION_EXPIRES=60 \
	gcr.io/manevia/manevia-app