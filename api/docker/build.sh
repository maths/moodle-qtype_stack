#!/bin/sh

set -e

#Directory of the script
DIR=$(dirname "$(readlink -f "$0")");

CONTEXT=$DIR/../../

docker build --pull --target production -t registry.dynexite.rwth-aachen.de/stack/stack -f $DIR/Dockerfile $CONTEXT
