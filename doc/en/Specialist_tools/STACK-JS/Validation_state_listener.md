# Validation state listeners and extra validation messages

This page documents the STACK library example *Doc-Examples > Specialist-Tools > STACK-JS > Validation state listener*. Said example demonstrates a combination of advanced features used in a way that is not very efficient. It is an intentionally built cautionary example that mostly works.

## Features of the example

The example question uses a JSXGraph to plot a list of (x,y) coordinates as a curve, i.e, it plots a function. However, the thign that it does differently is that it generates those coordinates inside custom validation feedback so that the plotting can happen in response to instant validation. A validation state listener is used to detect when new feedback arrives and then trigger an update of the graph.

### Bespoke validation

Inputs can have custom validation tests and messages. These so called [bespoke validators](../../CAS/Validator.md) act on the received input and can generate output visible to the student. In this example the question contains both a validator and a feedback generator, the former tries to detect if the input is a function of x and nothing more so that plotting is even possible, the latter then evaluates the input in 200 points within a predetermined range and outputs a hidden div containing those points encoded as JSON.

Both the validator and the feedback generator are defined in question variables and are attached to the input though the inputs extra options. Note, that the generated feedback uses a named `<div>` and that name has been made question usage level unique using the [`[[quid]]`](../../Authoring/Question_blocks/Static_blocks.md#quid-question-unique-identifier-block)-block, which is the recommend thing to do when referencing named elements in logic.

The primary problem of this question is related to the feedback generator, 200 points takes room in the response message and may slow down the response too much to be considered instant. Furthermore, no validator of sensible execution time exists to check if said function can even be evaluated in those points.

### STACK-JS in JSXGraph

In this question we use a [JSXGraph](../JSXGraph/index.md) to do the plotting and in this case we do not connect that JSXGraph to the input, thus we do not use any of the input references or binding-functions one migth normally use. Instead, we use a validation state listener (`stack_js.register_validation_state_listener`) and react to various states of the validation differently:

 1. When the validation starts we mimic normal validation displays by greying out the plotted curve if present.
 2. When the validation completes and states that the input is invalid, or validation fails, we throw the whole curve out.
 3. When the validation completes successfully and the input is valid, we do the actual plotting.

Durign the plotting we ask `stack_js.get_content` to fetch the contents of the named `<div>` present in the validation output. The content is the parsed as JSON and used to update the curve should it exists or to create a new curve. Any possible greyout is also reset. As the bounding-box was also evaluated on the server side (would be sensible to evaluate on the JSXGraph side) we set that as well.


## General notes

The methods used here are all valid to use but remember not to overload the validation phase with extraneous tasks, every extra thing there will make it less instant. 

Note that traditionally, this very same thing has been done through PRT-feedback with either plots in that feedback or with named divs in it, which have been loaded into question-text level plots on page load and no listeners have been needed as page load only happens once and PRT-feedback only updates during page load.