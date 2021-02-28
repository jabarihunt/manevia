#!/bin/bash

# RUN COMPOSER IF VENDOR DIRECTORY DOESN'T EXIST
if [[ ! -d app/vendor ]] && [[ -x "$(which composer)" ]]; then
  echo -e "\nMANEVIA: Running Composer...\n"
  # shellcheck disable=SC2164
  cd app
  composer install
  cd ../
else
  echo -e "\nMANEVIA: Vendor directory exists"
fi

# CREATE .env FILE IF IT DOESN'T EXIST
if [[ ! -f .env ]]; then
  echo -e "\nMANEVIA: Creating .env file..."
  cp .env.example .env
else
  echo -e "MANEVIA: .env file exists"
fi

# BUILD & RUN DOCKER
if [[ -d app/vendor ]] && [[ -f .env ]]; then
  echo -e "MANEVIA: Starting Docker...\n"

  # CREATE BUILD & RUN STRINGS
  dockerBuildString="docker build ."
  dockerRunString="docker run -d"

  while IFS= read -r line || [ -n "$line" ]; do

    firstChar=${line:0:1}

    if ! [[ $firstChar = "#" ]]  && [[ $line = *"="* ]]; then

      # GET FIELD AND VALUE
      field="$(cut -d'=' -f1 <<< "$line")"
      value="$(cut -d'=' -f2 <<< "$line")"
      value=$(eval "echo $value")

      # CREATE DOCKER RUN VALUES & STRING
      if [[ $field = "DOCKER_TAG_VERSION" ]]; then
        dockerBuildString="$dockerBuildString -t $value"
      elif [[ $field = "DOCKER_TAG_LATEST" ]]; then
        dockerBuildString="$dockerBuildString -t $value"
      elif [[ $field = "DOCKER_APP" ]]; then
        DOCKER_APP=$value
      elif [[ $field = "DOCKER_PORT" ]]; then
        dockerRunString="$dockerRunString -p 8080:\"$value\""
        dockerRunString="$dockerRunString -e PORT=\"$value\""
      else
        dockerRunString="$dockerRunString -e $field=\"$value\""
      fi

    fi

  done < .env

  dockerRunString="$dockerRunString $DOCKER_APP"

  # BUILD & RUN DOCKER
  eval "$dockerBuildString"
  eval "$dockerRunString"

else
  echo -e "MANEVIA: Missing app/vendor directory or .env file"
fi