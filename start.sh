#!/bin/bash

source ".env"
docker build . -t ${DOCKER_TAG_VERSION} -t ${DOCKER_TAG_LATEST}
docker run -d \
-p 8080:${DOCKER_PORT} \
	-e PORT=${DOCKER_PORT} \
  -e ENVIRONMENT="${ENVIRONMENT}" \
  -e DEFAULT_API_VERSION="${DEFAULT_API_VERSION}" \
  -e DEFAULT_AUTH_REQUIRED="${DEFAULT_AUTH_REQUIRED}" \
	-e DATABASE_HOST="${DATABASE_HOST}" \
	-e DATABASE_NAME="${DATABASE_NAME}" \
	-e DATABASE_USER="${DATABASE_USER}" \
	-e DATABASE_PASSWORD="${DATABASE_PASSWORD}" \
	-e DATABASE_SOCKET=${DATABASE_SOCKET} \
	-e SESSION_ENABLED=${SESSION_ENABLED} \
	-e SESSION_SAVE_HANDLER="${SESSION_SAVE_HANDLER}" \
	-e SESSION_SAVE_PATH="${SESSION_SAVE_PATH}" \
	${DOCKER_APP}