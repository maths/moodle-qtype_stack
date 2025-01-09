# STACK-API

This folder contains a standalone REST-API for integration of STACK into external systems.  This API was originally designed for the specific needs of the Dynexite examination system.

## Deployment

### Docker

The STACK API has been designed to be deployed using Docker. Pre-made images are publicly available via a gitlab registry under the identifier `https://hub.docker.com/u/stackmaths`. The used Dockerfile is available [here](https://github.com/maths/moodle-qtype_stack/blob/master/api/docker/).

E.g. see `https://hub.docker.com/r/mathinstitut/goemaxima` for images.

> :warning: **NOTE**: The pre-built images rely on a recent version of the apache2 webserver, which requires a linux kernel version of 3.17 or newer on the Docker host.

The image requires maxima to be available via http. The URL can be configured via the environment variable `MAXIMA_URL` and defaults to `http://maxima:8080/maxima`. An example docker-compose file deploying both stack and maxima in the goemaxima variant is provided below:

```
version: "4.0"
services:
  maxima:
    image: mathinstitut/goemaxima:2024072400-latest
    tmpfs:
      - "/tmp"
    restart: unless-stopped
    cap_add:
      - SETGID
      - SETUID
    cap_drop:
      - ALL
    environment:
      GOEMAXIMA_QUEUE_LEN: 32
    read_only: true
  stack:
    image: stackmaths/stackapi:2024072400-latest
    restart: unless-stopped
    ports:
      - '3080:80'
```

### Manual

The application can also be installed manually, although this variant has only undergone limited testing. Prerequisites are a working installation of PHP 8 and [composer](https://getcomposer.org/):

- Copy the content of this repository to your target server. Only the `./api/public` directory should be publicly accessible. 
- Install the required dependencies by performing `composer install` inside the `./api/` directory. 
- Copy `./api/config_sample.txt_` into a file `./api/config.php` and adapt to your needs.
- Access the api via the `api/public/index.php` file.

## Usage instructions

The STACK service implemented in this repository provides a stateless REST-API with five distinct routes, which all expect and produce `application/json` requests/responses:

- POST /render: Render a STACK question
- POST /grade: Grade user input for a question
- POST /validate: Validate a user's input
- POST /download: Serves a file for questions that have download links
- POST /test: Run a questions tests against all deployed variants

### Render route

The `POST /render` route is used to render a given question. It expects a JSON document in the post body, which must contain the following fields:

- `questionDefinition`: The Moodle-XML-Export of a single STACK question.
- `seed`: Seed to choose a question variant. Must be contained in the list of deployed variants. If  
  no seed is provided, the first deployed variant is used.
- `renderInputs`: String. Response will include HTML renders of the inputs if value other than ''. The input divs will have the value added as a prefix to their name attribute.
- `readOnly`: boolean. Determines whether rendered inputs are read only.

The response is again a JSON document, with the following fields:

- a string field `questionrender`, containing the rendered question text
- a string field `questionsamplesolutiontext`, containing the rendered general feedback of the question
- a string map `questionassets`, containing the assets used in the question, see [Plots/Assets](#Plots/Assets)
- a map field `questioninputs` mapping an input name to its configuration
- an int field `questionseed` indicating the seed used for this response
- an int array `questionvariants` containing all variant seeds of the question
- an array of arrays `iframes` of arguments to create iframes to hold JS panels e.g. JSXGraph, GeoGebra

The input configuration consists of the following fields:

- `validationtype`: A number indicating the configured validation type of the input. Possible values are 0 (hidden), 1 (with variable list), 2 (without variable list) and 3 (compact)
- `samplesolution`: A map from strings to strings, containing the model answer of the input, in its input form. Input types which are rendered as only one input field, contain only an empty string as key, which is mapped to the model answer. More complex input types contain multiple entries, corresponding to different sub inputs, e.g. for matrix entries, or checkboxes. 
- `samplesolutionrender`: The rendered model answer, as latex code.
- `configuration`: A map of configuration options. See below.

#### Input Configuration Keys

The following keys can be contained inside the input configuration options. The availability depends on the type of the input. Please consult the STACK documentation to check which options are supported by which types, if availability is not explicitly specified below:   

- `type`: Indicates the type of the input, e.g. `algebraic`. Present for all inputs. Possible values are: `algebraic`, `boolean`, `checkbox`, `dropdown`, `equiv`, `matrix`, `notes`, `numerical`, `radio`, `singlechar`, `string`, `textarea`, `units` and `varmatrix`.
- `boxWidth`: Specifies the desired box size of the input.
- `align` If the input is supposed to be `left` or `right` aligned.
- `syntaxHint`: The configured Syntax hint of the input.
- `syntaxHintType`: If the Syntax hint should be displayed as a placeholder, or as initial value. Supported for the types `algebraic, numerical, string, units`
- `options`: Key-Value Object containing the options for choice like input types. Supportet for the types `checkbox, dropdown, radio`
- `matrixbrackets`: The desired matrix bracket style. One of `matrixroundbrackets`, `matrixsquarebrackets`, `matrixbarbrackets`, `matrixnobrackets`. Supported for the types `matrix` and  `varmatrix`
- `width`: Width of the input matrix. Supported for the `matrix` type.
- `height` Height of the input matrix. Supported for the `matrix` type.


### Grade route

The `POST /grade` route is used to score a given input for a question. The route expects a JSON document in the post body, which must contain the following fields:

- `questionDefinition`: The Moodle-XML-Export of a single STACK question.
- `seed`: Seed to choose a question variant. Must be contained in the list of deployed variants. If  
  no seed is provided, the first deployed variant is used.
- `answers`: A map from string to string, containing the answers.

For input rendered as single fields, one entry inside the `answers` map, with the input name as key is expected. More complex input types use multiple entries, with the input name as a prefix, e.g. matrix inputs.

The grading route returns the following fields:

- a boolean field `isgradable`, indicating if the question could be graded. Possibly false e.g. because of missing inputs
- a float field `score` containing the obtained score. (Also contained in scores but kept here for backward compatibility.)
- a map from the PRT names to floats `scores`, containing the marks for each part. `score['total']` contains the total score for the question.
- a map from the PRT names to floats `scoreweights`, containing the weighting for each part. `scoreweights['total']` contains the default total mark for the question. The mark for a question part is its `score[prt] * scoreweights[prt] * scoreweights['total']`.
- a string field `specificfeedback` containing the rendered specific feedback text
- a map from the PRT names to strings `prts`, containing the rendered PRT feedback
- a string map `gradingassets`, containing a list of assets used in the grading response, see [Plots/Assets](#Plots/Assets)
- a string field `responsesummary` containing a summary of response. (See [Reporting](../doc/en/Authoring/../STACK_question_admin/Reporting.md).)
- an array of arrays `iframes` of arguments to create iframes to hold JS panels e.g. JSXGraph, GeoGebra

### Validate route

The `POST /validate` route is used to get validation feedback for a single input of a question. The route expects a JSON document in the post body containing the following fields:

- `questionDefinition`: The Moodle-XML-Export of a single STACK question.
- `inputName`: The name of the input to be validated.
- `answers`. A map from string to string, containing the answers.

The validation route returns a string field `Validation` with the corresponding rendered output and an array of arrays `iframes` of arguments to create iframes to hold JS panels e.g. JSXGraph, GeoGebra.

### Download route

The `POST /download` route is used to download files created by questions.

- `questionDefinition`: The Moodle-XML-Export of a single STACK question.
- `seed`: Seed to choose a question variant. Must be contained in the list of deployed variants. If  
  no seed is provided, the first deployed variant is used.
- `filename` - as specified in the question definition and included in the question render.
- `fileid` - as specified by the question render.

The requested file is returned.

### Test route

The `POST /test` route is used to run a question's test cases.

- `questionDefinition`: The Moodle-XML-Export of a single STACK question.

The grading route returns the following fields:

- string: `name`: The name of the question.
- string: `messages`: Question level error messages.
- boolean: `isupgradeerror`: Are there any issues related to questions being non-compliant with a STACK upgrade?
- boolean: `isgeneralfeedback`: Does the question have general feedback?
- boolean: `isdeployedseeds`: Does the question have deployed seeds?
- boolean: `israndomvariants`: Does the question use random variants?
- boolean: `istests`: Does the question have tests?
- object: `results`: The test results for each each seed, keyed by seed. If the question does not have random variants, there will be a single entry keyed `noseed`.

In the `results` object, each seed will key an object:
- int|null: `passes`: Number of tests passed.
- int|null: `fails`: Number of tests failed.
- string: `messages`: Seed level error message. Includes summaries from the test, runtime errors and general feedback errors.
- object: `outcomes`: Gives detailed breakdown of test result. Entries keyed by `<testcase>`. 

In the outcomes object, each test will key an object:
- boolean: `passed`: Did the test pass?
- string: `reason`: Reason for failure. A test empty message or the part of the output (e.g. score) which doesn't match the expected result.
- object: `inputs`: Keyed by input name. Details of the inputs and their values.
- object: `outcomes`: Keyed by PRT name. Details of the outcomes and expected outcomes for each PRT.

Example result object:
```
"563119235": {
    "passes": 1,
    "fails": 0,
    "messages": "",
    "outcomes": {
        "1": {
            "passed": true,
            "reason": "",
            "inputs": {
                "ans1": {
                    "inputexpression": "ans1",
                    "inputentered": "1.6486",
                    "inputmodified": "1.6486",
                    "inputdisplayed": "\\[ 1.6486 \\]",
                    "inputstatus": "Score",
                    "errors": ""
                }
            },
            "outcomes": {
                "prt1": {
                    "outcome": true,
                    "score": 1,
                    "penalty": 0,
                    "answernote": "prt1-1-T",
                    "expectedscore": 1,
                    "expectedpenalty": 0,
                    "expectedanswernote": "prt1-1-T",
                    "feedback": "",
                    "reason": ""
                }
            }
        }
    }
}
  ```

If a question has no tests, a default test will be run to check if the model answers return a score of 1.

### Rendered CASText format

The API returns rendered CASText as parts of its responses in multiple places. The CASText is output as a single string in an intermediate format, which cannot be directly fed to browsers for display, and requires further processing. Applications using the API have to handle the following cases:

- **Latex**: The rendered CASText can contain Latex code, which must be rendered before being displayed to the user, e.g. by MathJax. Latex blocks are always enclosed by either `\[ <latex> \]` for display mode latex, or `\( <latex> \)` for inline mode.
- **Substitution Tokens**: The rendered CASText can contain substitution tokens, indicating where inputs, input validations or PRT feedback should be inserted. These tokens have the format `[[type:name]]`, where type can be either `feedback`, `input` or `validation`, and name corresponds to the input or PRT name. It is up to the embedding application to replace these tokens with the appropriate content, depending on the context. 
- **Images**: The rendered CASText can contain image tags, which have to be processed as described below: [Plots/Assets](#Plots/Assets)


### Plots/Assets

Any plots generated by stack during rendering or grading, as well as static images embedded inside the question are output as image tags inside the rendered CASText, with a generated filename specified as the src attribute. This ensures that the outputs of the render and grading routes are completely deterministic, which is a desirable property, e.g. to detect duplicate question variants. The API response furthermore includes a mapping from the generated name to a randomized filename, which can be used to retrieve the image, inside the `questionassets` or `gradingassets` respectively. The images can be downloaded under the url `/plots/<filename>.<type>`. It is up to the embedding application to download these images, and replace the generated names with a usable url for viewing the question.

### Multi language content

The API currently supports outputting German and English localization, both for internal messages and as part of multi-language questions. To control which language is selected the `Accept-Language` HTTP header is parsed. If not present, the default language is English.  Note, in order to add additional languages, you will need to include the Moodle language pack directly inside the appropriate `/lang/??` folder.

### Errors

If an error occurs during processing of a request, a response with a single JSON field `message` and an appropriate http response code is returned. The provided message is intended for user display.

### Limitations

- If a question uses randomization, it has to contain deployed variants.
- Grading is only done if all inputs are present and valid (which implies non-empty in most cases).

## Implementation Details

The implementation of the STACK-Service is based on the source code of the STACK-Moodle-Plugin. One of the design goals is to minimize the required maintenance effort for upgrading to new STACK releases in the future. To reach this goal, all new code resides in the [/api]() folder, and modifications to already existing files have been kept to a minimum as far as possible.

The implementation of the API is based on the [Slim](https://www.slimframework.com/) micro framework, which is used for routing, error handling and similar boilerplate tasks. The framework is initialized inside the `./api/public/index.php` file, where middlewares and route controllers are registered. All added classes reside under the `api` namespace and can be autoloaded.

Inside the api directory the code is further split in multiple directories:

- `controller`: Contains the controllers classes for the different routes.
- `docker`: Contains files related to the containerization of the service.
- `dtos`: Contains DTO classes defining the response formats of the routes.
- `emulation`: Contains files related to the emulation of moodle functionality.
- `public`: Contains directly web accessible files.
- `util`: Contains different utility classes.
- `vendor`: Contains composer dependencies.

### Dependencies

Code dependencies of the api implementation are managed using composer. At runtime the service itself is stateless and only depends on an instance of the maxima CAS, which is expected to be reachable via http, under an url provided via the `MAXIMA_URL` environment variable, or `http://maxima:8080/maxima` by default.

### Docker based development setup

To ease the development process, the Dockerfile contained in the repository contains multiple stages for development, profiling and production deployment. To start developing using a docker container, start the docker-compose stack defined in the file [docker-compose.dev.yml](/api/docker/docker-compose.dev.yml). E.g. 

    docker compose -f docker-compose.dev.yml build
    docker compose -f docker-compose.dev.yml up

The required development image will automatically be built. After the stack started, you will be able to access the service via http://localhost:3080. Any performed changes in the PHP code will be visible live. Note, that Maxima is provided by a geomaxima docker image, and this image will _not_ reflect local changes.  The development build also contains the xdebug extension, which is configured to connect to `host.docker.internal` as a debugger, which will resolve to the locale machines ip address when using docker desktop. Please note that the performance of the development setup will be significantly worse than in production.

### Docker production setup

A production image of the API can be built using `docker-compose.stack.prod.yml`. This then needs renamed, tagged and pushed to a suitable repository on Docker hub.

    docker compose -f docker-compose.stack.prod.yml build
    docker tag docker-stack your-repo/imagename:tag
    docker push your-repo/imagename:tag

At this point a user should just need a working Docker setup and an up-to-date `docker-compose.yml` file that points to the goemaxima and stack-api images:

    docker compose -f docker-compose.yml up

This will download the goemaxima and stack-api images and run the containers in an enclosing container. Obviously, config will be the same as for whoever built the stack-api Docker image.

Version numbers will need to match the latest STACK release in `docker-compose.dev.yml`, `docker-compose.yml`, `config_sample.txt` and `config.php`. **(Remember: config.php needs updated locally before building.)**

Pre-built API images are available on Docker Hub at `stackmaths/stackapi`.

### High level overview

When a function of the API is invoked, the contained question definition in the moodle xml format is first converted to an instance of `qtype_stack_question`, by the `StackQuestionLoader` class. After the question has been parsed, it is initialized with the provided seed via a call to `initialise_question_from_seed`. If any runtime errors occur an exception is thrown and will be returned to the user.

After that, for the render route, all desired outputs are extracted from the questions via their access functions, and undergo a post-processing process, in which any contained multi-language tags are substituted, and urls to images are replaced according to the desired output format. Any statically included assets (pluginfiles) are extracted and are treated equally to generated plots.

For the validation route, the `get_input_state` function is called for the requested input, and its output is passed to the `render_validation` function of the input.

For the grading route, the controller iterates over the PRTs of the questions, and calls the `get_prt_result` method for each of them, with the answers provided in the request as parameter. If the evaluation returned an error, or not all necessary inputs are contained in the request, according to the `has_necessary_prt_inputs` function, a response with `isgradable` set to false is returned. Otherwise, the scores of the PRTs are aggregated, and the generated feedback undergoes the same post-processing process as described for the render route.

### Moodle Emulation

To allow the stack-moodle-plugin to work standalone, some classes and functions which are normally part of moodle itself have been emulated. All source-code written for this purpose is contained in the `emulation` directory. The central entry point to load the emulation layer is the file `MoodleEmulation.php`, which is loaded via `require_once` on the index page. The following individual pieces have been emulated:

- The files questionlib.php and weblib.php have been created as stubs.
- Some constants defined inside of moodle have been copied.
- The `html_writer` class, and some other outputting functions.
- Some functions related to localization.
- The plugin settings
- The moodle_exception class

### Basic frontend

A basic frontend is provided at `http://localhost:3080/stack.php`. This should allow you to load the STACK sample questions and try them out. This requires API specific versions of `cors.php` and `stackjsvle.js` (to access files and create iframes) which are in the public folder.

`http://localhost:3080/bulktest.php` provides a front end for selecting a folder of STACK question files and running their included tests, similar to the STACK bulk test functionality in Moodle.

### Modifications of existing STACK code

The implementation of the standalone api required some modifications to existing STACK code, which could cause issues with future upstream patches. All performed modifications are documented in this section.

#### Input types

To allow the API to return appropriate data describing input configuration, the abstract `stack_input` class has been extended with the following methods:

- `get_api_solution($tavalue)`: Returns the model answer of the input in the same format in which it would be input by the user
- `get_api_solution_render($tadisplay)`: Returns a rendered version of the model answer of this input.
- `render_api_data($tavalue)`: Returns an array of configuration options which should be exposed via the API.

The `get_api_solution` and `get_api_solution_render` functions have sensible default implementations, which are only overwritten for more complex input types. The `render_api_data` function on the other hand is abstract, and needs to be implemented by each concrete input type individually.

#### Escalated visibility

To be accessible directly, the following property/method visibility have been promoted to public:

- The `search` property inside the `stack_multilang` class.
- The `has_necessary_prt_inputs` function of the `qtype_stack_question` class.

#### Minor changes in STACK 4.6.0

- Some new language keys have been added.
- Some imports inside the `question.php` and `mathsoutputfilterbase.class.php` files have been wrapped inside an if statement, to only be performed in non-API contexts.
- A new `get_ta_render_for_input` function has been added to the `qtype_stack_question` class.
- A new `pluginfiles` property has been added to the `qtype_stack_question` class.
- `iframe.block.php` handles plot URLs and iframe creation conditional on context (i.e API vs not API).
- `textdownload.block.php` sets the document link href conditionally on context.
- A new `mathsoutputapi.class.php` file has been added.
