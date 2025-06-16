# STACK API

STACK also has an API to provide STACK questions as a web service.

The API provides a basic, stateless, interface to STACK questions.  This is an advanced feature.  STACK is shipped with a very basic front-end for evaluation and testing, but the use of the API will require existing questions in Moodle XML, a "quiz system" to manage access to individual questions, and a database to store attempts at questions.

## Minimal example

The STACK API has been designed to be deployed using Docker. 

1. Images are available from [https://hub.docker.com/u/stackmaths](https://hub.docker.com/u/stackmaths)
2. The [Dockerfile is available here](https://github.com/maths/moodle-qtype_stack/blob/master/api/docker/docker-compose.yml).

To start the docker container try `docker compose -f docker-compose.yml up`

To use the API look at `http://localhost:3080/stack.php` (or perhaps `http://172.18.0.2/stack.php`)

To stop the docker container try `docker compose -f docker-compose.yml down`

The API also provides a bulk test option `http://localhost:3080/bulktest.php`

## Building the image locally

The code, and further documentation, for the API is in the `api/README.md` directory of the distribution.