#!/bin/bash

# RUN COMPOSER IF VENDOR DIRECTORY DOESN'T EXIST
if test ! -d app/vendor && test -x "$(which composer)"; then
  echo -e "\nMANEVIA: Running Composer...\n"
  cd app
  composer install
  cd ../
else
  echo -e "\nMANEVIA: Vendor directory exists"
fi

# CREATE .env FILE IF IT DOESN'T EXIST
if test ! -f .env; then
  echo -e "\nMANEVIA: Creating .env file..."
  cp .env.example .env
else
  echo -e "MANEVIA: .env file exists"
fi

# LOAD ENVIRONMENT FILE AND START DOCKER
if test -d app/vendor && test -f .env; then
  echo -e "MANEVIA: Starting Docker...\n"
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
else
  echo -e "MANEVIA: Missing app/vendor directory or .env file"
fi