#!/bin/sh

set -e

#Directory of the script
DIR=$(dirname "$(readlink -f "$0")");

CONTEXT=$DIR/../../../

docker build --pull --target production -t registry.dynexite.rwth-aachen.de/dynexite/stack/stack-php -f $DIR/Dockerfile $CONTEXT
