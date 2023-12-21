# STACK-Dynexite-API
This repository contains a version of the moodle plugin STACK, which has been extended by a standalone REST-API for integration into external systems, designed for the specific needs of the Dynexite examination system.

## Deployment

### Docker
The STACK API has been designed to be deployed using Docker. Pre-made images are publicly available via a gitlab registry under the identifier `registry.git.rwth-aachen.de/medien-public/moodle-stack`. The used Dockerfile is available [here](docker/Dockerfile).

> :warning: **NOTE**: The pre-built images rely on a recent version of the apache2 webserver, which requires a linux kernel version of 3.17 or newer on the Docker host.

The image requires maxima to be available via http. The URL can be configured via the environment variable `MAXIMA_URL` and defaults to `http://maxima:8080/maxima`. An example docker-compose file deploying both stack and maxima in the goemaxima variant is provided below:

```
version: "3.9"
services:
  maxima:
    image: mathinstitut/goemaxima:2023010400-latest
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
    image: registry.git.rwth-aachen.de/medien-public/moodle-stack
    restart: unless-stopped
    ports:
      - '3080:80'
```

### Manual

The application can also be installed manually, although this variant has only undergone limited testing. Prerequisites are a working installation of PHP 8 and [composer](https://getcomposer.org/):

- Copy the content of this repository to your target server. Only the `./api/public` directory should be publicly accessible. 
- Install the required dependencies by performing `composer install` inside the `./api/` directory. 
- Adopt the content of the `./api/config.php` file to your needs.
- Access the api via the `index.php` file.

## Usage instructions
The STACK service implemented in this repository provides a stateless REST-API with three distinct routes, which all expect and produce `application/json` requests/responses:

- POST /render: Render a stack question
- POST /grade: Grade user input for a question
- POST /validate: Validate a users input

### Render route
The `POST /render` route is used to render a given question. Is expects a json document in the post body, which must contain the following fields:

- `questionDefinition`: The Moodle-XML-Export of a single STACK question.
- `seed`: Seed to choose a question variant. Must be contained in the list of deployed variants. If  
  no seed is provided, the first deployed variant is used.
- `renderInputs`: Boolean. Response will include HTML renders of the inputs if true.

The response is again a json document, with the following fields:

- a string field `questionrender`, containing the rendered question text
- a string field `questionsamplesolutiontext`, containing the rendered general feedback of the question
- a string map `questionassets`, containing the assets used in the question, see [Plots/Assets](#Plots/Assets)
- a map field `questioninputs` mapping an input name to its configuration
- an int field `questionseed` indicating the seed used for this response
- an int array `questionvariants` containing all variant seeds of the question

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
The `POST /grade` route is used to score a given input for a question. The route expects a json document in the post body, which must contain the following fields:

- `questionDefinition`: The Moodle-XML-Export of a single STACK question.
- `answers`: A map from string to string, containing the answers.
- `allowBlanks`: Boolean. If true, grading will be performed even if a valid answer has not been provided for all inputs. (Defaults to false.)

For input rendered as single fields, one entry inside the `answers` map, with the input name as key is expected. More complex input types use multiple entries, with the input name as a prefix, e.g. matrix inputs.

The grading route returns the following fields:

- a boolean field `isgradable`, indicating if the question could be graded. Possibly false e.g. because of missing inputs
- a float field `score` containing the obtained score
- a string field `specificfeedback` containing the rendered specific feedback text
- a map from the PRT names to strings `prts`, containing the rendered PRT feedback
- a string map `gradingassets`, containing a list of assets used in the grading response, see [Plots/Assets](#Plots/Assets) 

Grading of a question is only performed if a valid answer has been provided for all inputs, which in most cases implies a nonempty answer. This decision was made to enforce a manual grading, in case the student entered invalid or incomplete answers in the context of Dynexite. (This behaviour can be overridden with `allowBlanks`.)

### Validate route
The `POST /validate` is used to get validation feedback for a single input of a question. The route expects a json document in the post body containing the following fields:

- `questionDefinition`: The Moodle-XML-Export of a single STACK question.
- `inputName`: The name of the input to be validated.
- `answers`. A map from string to string, containing the answers.

The validation route returns a single string field `Validation` with the corresponding rendered output.

### Rendered CASText format
The API returns rendered CASText as parts of its responses in multiple places. The CASText is output as a single string in an intermediate format, which cannot be directly fed to browsers for display, and requires further processing. Applications using the API have to handle the following cases:

- **Latex**: The rendered CASText can contain Latex code, which must be rendered before being displayed to the user, e.g. by MathJax. Latex blocks are always enclosed by either `\[ <latex> \]` for display mode latex, or `\( <latex> \)` for inline mode.
- **Substitution Tokens**: The rendered CASText can contain substitution tokens, indicating where inputs, input validations or PRT feedback should be inserted. These tokens have the format `[[type:name]]`, where type can be either `feedback`, `input` or `validation`, and name corresponds to the input or PRT name. It is up to the embedding application to replace these tokens with the appropriate content, depending on the context. 
- **Images**: The rendered CASText can contain image tags, which have to be processed as described below: [Plots/Assets](#Plots/Assets)


### Plots/Assets
Any plots generated by stack during rendering or grading, as well as static images embedded inside the question are output as image tags inside the rendered CASText, with a generated filename specified as the src attribute. This ensures that the outputs of the render and grading routes are completely deterministic, which is a desirable property, e.g. to detect duplicate question variants. The API response furthermore includes a mapping from the generated name to a randomized filename, which can be used to retrieve the image, inside the `questionassets` or `gradingassets` respectively. The images can be downloaded under the url `/plots/<filename>.<type>`. It is up to the embedding application to download these images, and replace the generated names with a usable url for viewing the question.

### Multi language content
The API currently supports outputting german and english localization, both for internal messages and as part of multi-language questions. To control which language is selected the `Accept-Language` HTTP header is parsed. If not present, the default language is english.

### Errors
If an error occurs during processing of a request, a response with a single json field `message` and an appropriate http response code is returned. The provided message is intended for user display.

### Limitations
- Questions requiring custom javascript are not supported. This includes questions using JSXGraph.
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
To ease the development process, the Dockerfile contained in the repository contains multiple stages for development, profiling and production deployment. To start developing using a docker container, simply start the docker-compose stack defined in the file [docker-compose.dev.yml](/api/docker/docker-compose.dev.yml). The required development image will automatically be build. After the stack started, you will be able to access the service via http://localhost:3080. Any performed code changes will be visible live. The development build also contains the xdebug extension, which is configured to connect to `host.docker.internal` as a debugger, which will resolve to the locale machines ip address when using docker desktop. Please note that the performance of the development setup will be significantly worse than in production.

### High level overview
When a function of the API is invoked, the contained question definition in the moodle xml format is first converted to an instance of `qtype_stack_question`, by the `StackQuestionLoader` class. After the question has been parsed, it is initialized with the provided seed via a call to `initialise_question_from_seed`. If any runtime errors occur an exception is thrown and will be returned to the user.

After that, for the render route, all desired outputs are extracted from the questions via their accessor functions, and undergo a post-processing process, in which any contained multi-language tags are substituted, and urls to images are replaced according to the desired output format. Any statically included assets (pluginfiles) are extracted and are treated equally to generated plots.

For the validation route, the `get_input_state` function is called for the requested input, and its output is passed to the `render_validation` function of the input.

For the grading route, the controller iterates over the PRTs of the questions, and calls the `get_prt_result` method for each of them, with the answers provided in the request as parameter. If the evaluation returned an error, or not all necessary inputs are contained in the request, according to the `has_necessary_prt_inputs` function, a response with `isgradable` set to false is returned. Otherwise, the scores of the PRTs are aggregated, and the generated feedback undergoes the same post-processing process as described for the render route.


### Moodle Emulation
To allow the stack-moodle-plugin to work standalone, some classes and functions which are normally part of moodle itself have been emulated. All source-code written for this purpose is contained in the `emulation` directory. The central entrypoint to load the emulation layer is the file `MoodleEmulation.php`, which is loaded via `require_once` at the beginning of each controller class. The following individual pieces have been emulated:

- The files questionlib.php and weblib.php have been created as stubs.
- Some constants defined inside of moodle have been copied.
- The `html_writer` class, and some other outputting functions.
- Some functions related to localization.
- The plugin settings
- The moodle_exception class

### Modifications of existing STACK code
The implementation of the standalone api required some modifications to existing STACK code, which could cause issues with future upstream patches. All performed modifications are documented in this section.

#### Input types
To allow the API to return appropriate data describing input configuration, the abstract `stack_input` class has been extended with the following methods:

- `getApiSolution($tavalue)`: Returns the model answer of the input in the same format in which it would be input by the user
- `getApiSolutionRender($tadisplay)`: Returns a rendered version of the model answer of this input.
- `renderApiData($tavalue)`: Returns an array of configuration options which should be exposed via the API.

The `getApiSolution` and `getApiSolutionRender` functions have sensible default implementations, which are only overwritten for more complex input types. The `renderApiData` function on the other hand is abstract, and needs to be implemented by each concrete input type individually.

#### Escalated visibilities
To be accessible directly, the following property/method visibilities have been promoted to public:

- The `search` property inside the `stack_multilang` class.
- The `has_necessary_prt_inputs` function of the `qtype_stack_question` class.

#### Minor changes
- Some new language keys have been added.
- Some imports inside the `question.php` and `mathsoutputfilterbase.class.php` files have been wrapped inside an if statement, to only be performed in non api contexts.
- A new `get_ta_render_for_input` function has been added to the `qtype_stack_question` class.
- The compile function of the JSXGraph Block has been modified to always throw an exception.
- A new  `mathsoutputapi.class.php` file has been added.
