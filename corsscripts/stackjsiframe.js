/**
 * This is a library for accessing the features the VLE side exposes through
 * message passing for all STACK implementations on all VLEs.
 * 
 * This is intended to be loaded into the IFRAME and needs the following things:
 * 
 *  1. The VLE side needs to know about this IFRAME, this happens through a shared
 *     secret that this IFRAME stores in a global JS variable FRAME_ID.
 *  2. The FRAME_ID has been registerd on the VLE side.
 *
 * When receiving messages this libary will only react to those targetting
 * its FRAME_ID. Other logic may handle other messages using any means necessary.
 * 
 * @module     qtype_stack/stackjsiframe
 * @copyright  2023 Aalto University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

"use strict";
/* Flags to disable certain things. */
let DISABLE_CHANGES = {};

/* Map of the promise resolves for inputs to be registered.
 * Basically, the set of inputs that wait registration to complete.
 */
let INPUT_PROMISES = {};

/* Map of the promises for currently executing `get_content` actions.
 * Basically from the element-id to the resolve function.
 */
let FETCH_PROMISES = {};

/* Map of external button listeners. By id.
 * For use with `stack_js.register_external_button_listener`
 */
let BUTTON_CALLBACKS = {};

/* A promise that will resolve when we first hear from the VLE side.
 * It is important to not send anything before we are absolutely certain that
 * the other end is ready. Although the way this has been built should
 * be safe...
 */
let do_connect = null;
let is_connected = false;
const CONNECTED = new Promise((resolve, reject) => {do_connect = resolve});
CONNECTED.then((b) => {is_connected = true});
/* Start the polling for connection. */
const pinger = setInterval(() => {
    if (!is_connected) {
        const msg = {
            version: 'STACK-JS:1.0.0',
            type: 'ping',
            src: FRAME_ID
        };
        window.parent.postMessage(JSON.stringify(msg), '*');
    }
}, 10);

/* A promise for checking if we have a submit button.
 */
let _receive_submit_button = null;
let SUBMIT_BUTTON = null;

window.addEventListener("message", (e) => {
    // NOTE! We do not check the source or origin of the message in
    // the normal way. All actions that can bypass our filters to trigger
    // something are largely irrelevant and all traffic will be kept
    // "safe" as anyone could be listening.

    // All messages we receive are strings, anything else is for someone
    // else and will be ignored.
    if (!(typeof e.data === 'string' || e.data instanceof String)) {
        return;
    }

    // That string is a JSON encoded dictionary.
    let msg = null;
    try {
        msg = JSON.parse(e.data);
    } catch (e) {
        // Only JSON objects that are parseable will work.
        return;
    }

    // All messages we handle contain a version field with a particular
    // value, for now we leave the possibility open for that value to have
    // an actual version number suffix...
    if (!(('version' in msg) && msg.version.startsWith('STACK-JS'))) {
        return;
    }

    // All messages we handle must have a tgt and a type,
    // and that target must be this ones FRAME_ID.
    if (!(('tgt' in msg) && ('type' in msg) && (msg.tgt === FRAME_ID))) {
        return;
    }

    switch (msg.type) {
    case 'initial-input':
        // 1. Get the input we have prepared.
        const element = document.getElementById(msg.name);

        // 2. Set its value. But don't trigger changes.
        DISABLE_CHANGES[msg.name] = true;
        element.value = msg.value;

        // 3. Sync read-only.
        if (msg['input-readonly']) {
            element.setAttribute('readonly', 'readonly');
        }

        // 4. Copy over the data attributes.
        if (msg['input-dataset']) {
            Object.keys(msg['input-dataset']).forEach(function(key) {
                element.dataset[key] = msg['input-dataset'][key];
            });
        }
        DISABLE_CHANGES[msg.name] = false;

        // 5. Resolve the promise so that things can move forward.
        INPUT_PROMISES[msg.name](element.id);

        // 6. Remove the promise from our logic so that the timeout 
        // logic does not trigger.
        delete INPUT_PROMISES[msg.name];
        
        break;
    case 'changed-input':
        // 1. Find the input.
        const input = document.getElementById(msg.name);

        // 2. Set its value. But don't trigger changes.
        DISABLE_CHANGES[msg.name] = true;
        input.value = msg.value;
        const c = new Event('change');
        input.dispatchEvent(c);
        DISABLE_CHANGES[msg.name] = false;

        break;
    case 'button-click':
        if (msg.name in BUTTON_CALLBACKS) {
            BUTTON_CALLBACKS[msg.name].forEach((callbackfunction) => {
                callbackfunction(msg.name);
            });
        }
        break;
    case 'xfer-content':
        if (msg.target in FETCH_PROMISES) {
            FETCH_PROMISES[msg.target](msg.content);
            // Next request will create a new fetch.
            delete FETCH_PROMISES[msg.target];
        }
        break;
    case 'ping':
        clearInterval(pinger);
        do_connect(true);
        break;
    case 'submit-button-info':
        _receive_submit_button(msg.value);
        break;
    case 'error':
    default:
        let errmesg = 'Unknown message-type "' + msg.type + '"';
        if (msg.type === 'error') {
            errmesg = msg.msg;
        }

        // 1. Create the message.
        const p = document.createElement('p');
        p.appendChild(document.createTextNode(errmesg));

        // 2. Do we already have an error-div?
        const div = document.getElementById('error');
        if (div) {
            div.appendChild(p);
        } else {
            // If not
            const div = document.createElement('div');
            div.id = 'error';
            div.style.color = 'red';
            const h1 = document.createElement('h1');
            h1.appendChild(document.createTextNode('Error'));
            div.appendChild(h1);
            div.appendChild(p);
            
            // We simply throw everything away and replace with the message.
            document.body.replaceChildren(div);
        }
        break;
    }

});


export const stack_js = {
    /**
     * Requests for a given input to be cloned from the VLE side.
     * Returns a promise that will either resolve as an identifier of the element
     * or timeout if the input name is not something that can be fetched.
     * 
     * You must not call this twice for the same input.
     * 
     * You may declare that you want to also react to input events.
     * This might not be that efficient but matches the old JSXGraph binding.
     * 
     * From 4.4.7 readonly/disabled inputs are cloned as readonly, at this point
     * we do not automatically disable accessing or editing them but you can base your
     * own logic on the input having that attribute. `.hasAttribute('readonly')`.
     * 
     * Note that this does not work with buttons (except radio-buttons), if you need
     * to react to button presses happening at the VLE side use
     * `register_external_button_listener`.
     * 
     * From STACK-JS: 1.3.0 you can define a boolean third parameter to control whether
     * you want to only search for inputs within the same question as this iframe (true)
     * or allow fallback to searching from all the questions on the page if not present
     * in this question (false). By default this is false as that was the original
     * behaviour.
     * 
     * From STACK-JS: 1.4.0 onwards the data attributes of the input on the VLE side
     * are copied during registration. There is no mechanism for synchronising them you
     * will simply get the initial state of them. You might find the type of the input
     * or the separator settings useful.
     * `ìnput.dataset["stackInputType"]`, `input.dataset["stackInputListSeparator"]`
     */
    request_access_to_input: function(inputname, inputevents, limittoquestion) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.id = inputname;
        DISABLE_CHANGES[inputname] = false;

        document.body.appendChild(input);

        input.addEventListener('change', (e) => {
            if (!DISABLE_CHANGES[inputname]) {
                // Just send a message.
                const msg = {
                    version: 'STACK-JS:1.0.0',
                    type: 'changed-input',
                    name: inputname,
                    value: input.value,
                    src: FRAME_ID
                };
                CONNECTED.then(() => {window.parent.postMessage(JSON.stringify(msg), '*');});
            }
        });

        // Send the connection request.
        CONNECTED.then((whatever) => {
            const msg ={
                version: 'STACK-JS:1.3.0',
                type: 'register-input-listener',
                name: inputname,
                'limit-to-question': false,
                src: FRAME_ID
            };
            if (inputevents === true) {
                msg['track-input'] = true;
            }
            if (limittoquestion !== undefined) {
                msg['limit-to-question'] = limittoquestion;
            }

            window.parent.postMessage(JSON.stringify(msg), '*');
        });

        // So our promise passes that resolve onto a dict
        // from which it will be resolved if we get 
        // the correct message after resolving it will be
        // removed from that dict, if not removed then when 
        // this times out we will reject this promise.
        return new Promise((resolve, reject) => {
            INPUT_PROMISES[inputname] = resolve;
            setTimeout(() => {
                if (inputname in INPUT_PROMISES) {
                    reject('No response to input registration of "' + inputname + '" in 5s.');
                }
            }, 5000);
        });
    },

    /**
     * Attaches a click event listener to a button on the VLE side.
     * 
     * There is no way to press the button from inside of the sandbox,
     * and the normal restrictions related to placement on the VLE side
     * do apply.
     * 
     * The callback function will be given exactly one argument and that
     * is the buttonid that triggered the callback.
     * 
     * Note that we do not currently actually enforce that the target is
     * a button we only attach a click listener to it. However, you should
     * not rely on this working with anything else.
     */
    register_external_button_listener: function(buttonid, callbackfunction) {
        if (!(buttonid in BUTTON_CALLBACKS)) {
            BUTTON_CALLBACKS[buttonid] = [];
        }
        BUTTON_CALLBACKS[buttonid].push(callbackfunction);

        const msg = {
            version: 'STACK-JS:1.2.0',
            type: 'register-button-listener',
            target: buttonid,
            src: FRAME_ID
        };
        CONNECTED.then(() => {window.parent.postMessage(JSON.stringify(msg), '*');});
    },

    /**
     * Asks for an element on the VLE side to be shown or hidden.
     * Will not tell whether this succeeds, if it fails due to no
     * such element found an error will eventtualy get displayed.
     */
    toggle_visibility: function(elementid, show) {
        const msg = {
            version: 'STACK-JS:1.0.0',
            type: 'toggle-visibility',
            target: elementid,
            set: show ? 'show' : 'hide',
            src: FRAME_ID
        };
        CONNECTED.then(() => {window.parent.postMessage(JSON.stringify(msg), '*');});
    },

    /**
     * Asks for an element on the VLE side to be given new content.
     * Will not tell whether this succeeds, if it fails due to no
     * such element found an error will eventtualy get displayed.
     * 
     * Obviously, won't allow scripts to be passed onto the VLE side.
     * 
     * Note did not work in the original release. Requires 4737dc5 to work.
     */
    switch_content: function(elementid, newcontent) {
        const msg = {
            version: 'STACK-JS:1.0.1',
            type: 'change-content',
            target: elementid,
            content: newcontent,
            src: FRAME_ID
        };
        CONNECTED.then(() => {window.parent.postMessage(JSON.stringify(msg), '*');});
    },

    /**
     * Asks for the contents of an element on the VLE side. Resolves to `null`
     * if no element found or if the element is outside the safe area. If found
     * returns the `innerHTML` of the element.
     * 
     * Do not merge this with `switch_content` to build a data transfer route,
     * use inputs as then you can track events to react to changes. There is one
     * case where inputs may be overly complicated and that is when PRTs return
     * logic to set input values, in that case that logic may execute in
     * arbitrary order in relation to the logic that might need those values and
     * reading a "static" value directly from the document may be better, if it
     * just a single read action during initialisation and dynamic binding are
     * irrelevant.
     * 
     * This is meant for transferring results from PRTs or shared static data.
     * 
     * Added in STACK-JS 1.1.0.
     */
    get_content: function(elementid) {
        if (elementid in FETCH_PROMISES) {
            // If we are already fetching that.
            return FETCH_PROMISES[elementid];
        }

        const msg = {
            version: 'STACK-JS:1.1.0',
            type: 'get-content',
            target: elementid,
            src: FRAME_ID
        };
        CONNECTED.then(() => {window.parent.postMessage(JSON.stringify(msg), '*');});

        return new Promise((resolve, reject) => {
            FETCH_PROMISES[elementid] = resolve;
            setTimeout(() => {
                if (elementid in FETCH_PROMISES) {
                    reject('No response to content fetch of "' + elementid + '" in 5s.');
                }
            }, 5000);
        });
    },


    /**
     * Asks for the containing IFRAME to be resized.
     * 
     * The arguments are strings that will be plugged into
     * `.style.width` and `.style.height`.
     */
    resize_containing_frame: function(width, height) {
        const msg = {
            version: 'STACK-JS:1.0.0',
            type: 'resize-frame',
            width: width,
            height: height,
            src: FRAME_ID
        };
        CONNECTED.then(() => {window.parent.postMessage(JSON.stringify(msg), '*');});
    },

    /** 
     * Displays an error message on the question page.
     * 
     * @param {*} errmesg 
     */
    display_error(errmesg) {
        // 1. Create the message.
        const p = document.createElement('p');
        p.appendChild(document.createTextNode(errmesg));

        // 2. Do we already have an error-div?
        const div = document.getElementById('error');
        if (div) {
            div.appendChild(p);
        } else {
            // If not
            const div = document.createElement('div');
            div.id = 'error';
            div.style.color = 'red';
            const h1 = document.createElement('h1');
            h1.appendChild(document.createTextNode('Error'));
            div.appendChild(h1);
            div.appendChild(p);
            
            // We simply throw everything away and replace with the message.
            document.body.replaceChildren(div);
        }
    },

    /**
     * Answers to the question whether the question containing this sandbox IFRAME
     * has a submit button (type="submit" and name/id="...submit"). The existence
     * of such a button depends on the active question behaviour.
     * 
     * If you are planning to use the other functions targetting that button do
     * first confirm that the button exists, otherwise those will generate annoying
     * errors.
     * 
     * Returns a promise that will resolve to null if no such button exists or to
     * a string value telling the value of that button as it was during the first
     * call to this function.
     * 
     * Note that we will consider buttons with the "hidden"-attribute as non existent.
     * For example "Deferred feedback" has such a button.
     */
    has_submit_button: function() {
        if (SUBMIT_BUTTON !== null) {
            return SUBMIT_BUTTON;
        }
        SUBMIT_BUTTON = new Promise((resolve, reject) => {_receive_submit_button = resolve});
        const msg = {
            version: 'STACK-JS:1.3.0',
            type: 'query-submit-button',
            src: FRAME_ID
        };
        CONNECTED.then(() => {window.parent.postMessage(JSON.stringify(msg), '*');});
        return SUBMIT_BUTTON;
    },

    /**
     * Disables/enables the question specific submit-button. 
     */
    enable_submit_button: function(enabled) {
        const msg = {
            version: 'STACK-JS:1.3.0',
            type: 'enable-submit-button',
            src: FRAME_ID,
            enabled: enabled
        };
        CONNECTED.then(() => {window.parent.postMessage(JSON.stringify(msg), '*');});
    },

    /**
     * Changes the value of the question specific submit-button. 
     */
    relabel_submit_button: function(name) {
        const msg = {
            version: 'STACK-JS:1.3.0',
            type: 'relabel-submit-button',
            src: FRAME_ID,
            name: name
        };
        CONNECTED.then(() => {window.parent.postMessage(JSON.stringify(msg), '*');});
    },

    /**
     * Clears an input. MCQ or otherwise. Unselects, unchecks or sets to ''.
     * Do note that this does not require you to register the input in advance.
     * 
     * Note that we do not yet support matrices.
     */
    clear_input: function(name) {
        const msg = {
            version: 'STACK-JS:1.3.0',
            type: 'clear-input',
            src: FRAME_ID,
            name: name
        };
        CONNECTED.then(() => {window.parent.postMessage(JSON.stringify(msg), '*');});
    },

    /**
     * Gets STACK specific input metadata for an input that has been registered and
     * has completed the registration process. Basically, if you are not using this
     * with `input-ref-X`-attributes you will need to deal with asynchronity and wait
     * for that registration to complete.
     *
     * This intentionally leaves out all other data-attributes and shortens the keys.
     * If you wish to look at the others you can always get the input element and
     * check the `dataset`.
     * 
     * Only those keys that get values from the PHP side will be present, so no 
     * decimal separators will be defined for `boolean`-fields.
     */
    get_input_metadata(name) {
        const input = document.getElementById(name);

        let data = {};
        for (let key in input.dataset) {
            if (key.startsWith('stack')) {
                data[key.substring(5)] = input.dataset[key];
            }
        }

        return data;
    }
};

export default stack_js;