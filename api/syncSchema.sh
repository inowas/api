#!/usr/bin/env bash

THIS_DIR=$(cd "$(dirname "$1")"; pwd -P)/$(basename "$1")
cd ${THIS_DIR}
rm -rf schema && wget --recursive -A json https://schema.inowas.com && mv schema.inowas.com schema
