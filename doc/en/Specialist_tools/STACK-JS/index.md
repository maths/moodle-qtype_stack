# STACK-JS

This page documents the STACK-JS system, used to connect question inputs and outputted content to custom browser side JavaScript logic, typically hosted inside `[[jsxgraph]]` or `[[javascript]]`-blocks. Basically, if you need to add dynamic behaviour and cannot find a ready made [CASText block](../../Authoring/Question_blocks/Dynamic_blocks.md) for that, you might be able to do it with JavaScript.

However, if you use raw JavaScript you may tie yourself down to the behaviour of your current VLE (e.g. Moodle) version and the scripting may break during the next update of that VLE, or during some security crackdown. Currently, STACK allows both raw JavaScript (i.e. the use of `<script>`-tags) and STACK-JS managed JavaScript executing inside limited sandboxes, the latter provides a limited interface that aims to map logical restricted actions to corresponding features on whatever VLE is currently in use. We **strongly** recommend the use of the latter option, and may at any point in the future actively limit the ability to use the former.

For technical reasoning on why we do not recommend the use of raw JavaScript to [our short description of the security issues](../../Developer/STACK-JS.md#the-general-security-reason), related to unknown JavaScript being executed outside secure contexts.


## Basic interface features

The STACK-JS interface is asynchronic, it communicates whatever you request from it to the VLE side and waits for a response before it can provide any answers. Thus any actions that expect a response will typically provide a [Promise](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Promise) as the result. To get the result you will need to provide logic that executes once said promise resolves. For an example, the following bit of logic asks for the contents of a named element on the VLE side:

```
[[javascript]]
// Ask STACK-JS to fetch the content of a named element.
let promise = stack_js.get_content('[[quid id="someelement"/]]');

// When those contents are ready do something.
promise.then((content) => {
  if (content === null) {
  	// No such element was found.
  } else {
    stack_js.toggle_visibility('[[quid id="otherelement"/]]', content.length % 2 == 0);
  }
});
[[/javascript]]
```
Here the `stack_js.get_content()`-function promises to return the `innerHTML` of that element or `null` if such element can not be found, but it does not give that value now as some parts of logic might still be starting up. On the other hand `stack_js.toggle_visibility()` returns no promises as it has no sensible thing to return, however even it is asynchronous, the visibility will not be toggled before the connection from this sandbox to the VLE becomes ready.

## Basic restrictions

The STACK-JS system limits what you can you access on the VLE side as well as what you can send there, the basic rules are as follows:

 1. You can only access things inside questions, not necessarily limited to the question that contains your logic, but you can limit your request to it. Basically, you cannot touch any of the VLE controls or contents that might be present on the page but are not part of the question display area.
 2. When sending content to the VLE side, it either goes into an input without filtering or it is being used to replace the `innerHTML` of a named element in which case that content will be filtered for scripts, event-handlers and remote references. Basically, you cannot move logic from the sandbox to the VLE side.

Do not ask for exceptions to those rules, otherwise the we are happy to make sensible extensions to the interface to selectively access and interact with the questions from sandboxed logic.


## Function reference

### Functions for inputs

With the exception of `stack_js.register_external_button_listener()` these all target inputs by their name in the STACK question, the exception case targets by element id. Most of these functions also work with non STACK inputs, even manually created ones as long as they have an id-attribute ending with underscore and the name to be searched (`..._name`). Construction of so called fake-inputs with such names is not uncommon.

#### `stack_js.request_access_to_input(name, inputevents, limittoquestion)` 

This is the primary function to use when one wants to keep track of the value of a given input on the VLE side. This function constructs an input element on the sandbox side and ensures that its value mirrors the value of the VLE side, with certain constraints:
 
 1. The value updates from the VLE side on `change`-events happening on the VLE side or if the `inputevents` argument is `true` also on `input`-events. Note that every time the value updates the sandbox side mirror input element will dispatch a `change`-event that one can track for.
 2. The value of the sandbox side element is sent to the VLE side only when a `change`-event is triggered on the sandbox side, so writing to the input is not enough one needs to dispatch an event. `input.dispatchEvent(new Event('change'))` is enough.

The function returns a promise that will resolve to the id of the sandbox side input element once the value has been synced with the VLE side. If one uses the `[[jsxgraph]]` or `[[javascript]]` block style attributes to provide references to inputs, the whole code of those blocks only starts executing once the promises for those inputs have resolved.

The second and third arguments of this function are optional. By default the second one is false and updates are only received on `change`-events, if one wants to react immediately as the student is typing set this to true. By default the third one is `false`, i.e., the search for an input with the given `name` is not limited to the question containing this sandbox, if this question does not have an input of that name then the first matching input on the page will be returned, thus allowing interaction between questions, set this to `true` if you want to ensure that only the containing question can be the source of the input. Should no input match the name an error will become very visible (assuming execution in non hidden sandbox).

Note that matrix inputs are not supported and behaviour for more complicated inputs may be interesting. For example, MCQ inputs are mapped to a basic text-input and checkboxes may prove to be difficult to deal with.

#### `stack_js.register_external_button_listener(id, callback)`

Should one construct a button on the question side and want to react to it being pressed this function allows attaching a callback-function to that button by the id of that button. Note that pressing that button after this type of a  callback has been connected to it will not trigger form submissions as those will be disabled. Likewise, the return value of the callback does not affect the execution of any other callbacks there might be nor the form.

The callback function will receive one argument and that is the id of the button.


#### `stack_js.clear_input(name)`

This function will simply clear whatever value or selection a named input has. You do not need to use `stack_js.request_access_to_input()` before calling this.

Note that for now does not work with matrix-inputs.


#### `stack_js.get_input_metadata(name)`

Etracts metadata about a given input that has previously been connected to the sandbox, either by `stack_js.request_access_to_input()` or by certain convenience attributes. This metadata describes the type of an input this is on the VLE side as well as details related to syntax. In particular, this can be used to identify which decimal separator is in use.


#### `stack_js.register_validation_state_listener(name, callback, limittoquestion)`

Registers a callback function to an input that is assumed to have AJAX/instant validation connected to it. When the state of that validation changes, i.e., when the student starts writing or the validation result comes back the callback function will be called.

The callback-function will receive three arguments:

 1. The first will tell if validation is currently in progress (`false`) or if it has completed (`true`).
 2. The second will tell the validation result, if the validation has not yet been completed will give `null` otherwise a boolean.
 3. The third will be the name of the input.

Do note that the third argument is the same as in `stack_js.request_access_to_input()` and the third argument of the callback is just the name used in the registration, thus you cannot know whether the input is in this question based on that name.

This function errors out if no input matching the search conditions is found, it does not know whether that input actually has any validation connected to it and will not error if no events can ever be generated.

Finally, remember that this will only trigger the callback on state change. If you use this to decide when to read something from the validation message you should probably also check if there is something to read on page load.

There is an example of this in the STACK library and a description of said example [here](Validation_state_listener.md).

### Functions for content

Content focused functions always target elements by id, in general they do not care what sort of elements they are as long as the access restrictions are respected. When defining ids for elements it is highly recommended to use the [`[[quid]]`](../../Authoring/Question_blocks/Static_blocks.md#quid-question-unique-identifier-block)-block to generate identifiers that are unique.

#### `stack_js.toggle_visibility(elementid, show)`

Changes the styling of the target element, if `show` is `true` will set `display:block` for `false` `display:none`. For this reason this capability is primarily meant for block elements. Should no element of matching id be found an error will be made visible (assuming execution in non hidden sandbox).

#### `stack_js.switch_content(elementid, newcontent)`

Replaces the `innerHTML` of the target element with the given string of content. The content will be filtered for scripts and other things previously dicussed. Should no element of matching id be found an error will be made visible (assuming execution in non hidden sandbox).

#### `stack_js.get_content(elementid)`

Returns a promise that resolves to the `innerHTML` of the target element, or to `null` if no such element can be found. This function is particularly handy when using the same large dataset in many places or when communicating using notes present in PRT-feedback or input-validation messages.


### Functions for the sandbox iframe

#### `stack_js.resize_containing_frame(width, height)`

Allows adjusting the external dimensions of a visible sandbox iframe after its initialisation. Suitable for plots that might automatically adapt their own size to the content. Give the dimensions as strings with units.

#### `stack_js.display_error(errmesg)`

Will render a visible error in sandboxes that are visible and also outputs the error to the console. This may be useful when doing debugging.


### Functions for a possible submit button

These are fairly specific functions meant for those that want to affect the submit button. For example, disable it before some conditions match. Note that the submit button is only present when using suitable question behaviours.

Note that there is no function to trigger that button.

#### `stack_js.has_submit_button()`

Will return a promise that will resolve to a boolean signaling whether we even have a submit button.

#### `stack_js.enable_submit_button(enable)`

Allows disabling the submit button, do remember to re-enable it so that the student can use it.

#### `stack_js.relabel_submit_button(label)`

Allows changing the text of the submit button.

