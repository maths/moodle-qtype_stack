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
        DISABLE_CHANGES[msg.name] = false;

        // 3. Resolve the promise so that things can move forward.
        INPUT_PROMISES[msg.name](element.id);

        // 4. Remove the promise from our logic so that the timeout 
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

    case 'ping':
        clearInterval(pinger);
        do_connect(true);
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
     */
    request_access_to_input: function(inputname, inputevents) {
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
                version: 'STACK-JS:1.0.0',
                type: 'register-input-listener',
                name: inputname,
                src: FRAME_ID
            };
            if (inputevents === true) {
                msg['track-input'] = true;
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
     */
    switch_content: function(elementid, newcontent) {
        const msg = {
            version: 'STACK-JS:1.0.0',
            type: 'change-content',
            target: elementid,
            content: newcontent,
            src: FRAME_ID
        };
        CONNECTED.then(() => {window.parent.postMessage(JSON.stringify(msg), '*');});
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
};

export default stack_js;