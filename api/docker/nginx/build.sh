#!/bin/sh

set -e

#Directory of the script
DIR=$(dirname "$(readlink -f "$0")");

docker build --pull -t registry.dynexite.rwth-aachen.de/dynexite/stack/stack-web $DIR
