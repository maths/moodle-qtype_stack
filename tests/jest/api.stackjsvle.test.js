/** @jest-environment jsdom */

const fs = require('fs');
const path = require('path');

function loadApiStackJsVleModule() {
    const modulePath = path.resolve(__dirname, '../../api/public/stackjsvle.js');
    const source = fs.readFileSync(modulePath, 'utf8');
    const wrapped = new Function(`${source}\nreturn {create_iframe, register_iframe};`);
    return wrapped();
}

describe('api/public/stackjsvle.js', () => {
    let stackjsvle;
    let postMessageByFrame;
    let messageListener;

    function installPostMessageSpy(iframeId) {
        const iframe = document.getElementById(iframeId);
        const postMessage = jest.fn();
        Object.defineProperty(iframe, 'contentWindow', {
            value: {postMessage},
            configurable: true,
        });
        postMessageByFrame[iframeId] = postMessage;
        return postMessage;
    }

    function sendMessage(message) {
        window.dispatchEvent(new MessageEvent('message', {
            data: JSON.stringify(message),
        }));
    }

    function sendRawMessage(data) {
        window.dispatchEvent(new MessageEvent('message', {data}));
    }

    function latestResponse(iframeId) {
        const calls = postMessageByFrame[iframeId].mock.calls;
        return JSON.parse(calls[calls.length - 1][0]);
    }

    function expectLatestResponse(iframeId, expectedCalls = 1) {
        const calls = postMessageByFrame[iframeId].mock.calls;
        expect(calls).toHaveLength(expectedCalls);
        return JSON.parse(calls[calls.length - 1][0]);
    }

    beforeEach(() => {
        const addEventListenerSpy = jest.spyOn(window, 'addEventListener');
        stackjsvle = loadApiStackJsVleModule();
        messageListener = addEventListenerSpy.mock.calls.find(([eventName]) => eventName === 'message')?.[1];
        addEventListenerSpy.mockRestore();

        postMessageByFrame = {};
        if (!global.CSS) {
            global.CSS = {};
        }
        if (!global.CSS.escape) {
            global.CSS.escape = (value) => String(value).replace(/(["\\#.;:?+*~'!^$\[\]()=>|/@])/g, '\\$1');
        }

        document.body.innerHTML = `
            <div class="formulation" id="qform">
                <div class="im-controls">
                    <input id="behaviour-submit" type="submit" value="Check" />
                </div>
                <div id="frame-target">loading</div>
                <div id="frame-target-2">loading2</div>
                <div id="content-target">placeholder</div>
                <button id="action-btn" type="button">Act</button>

                <input id="input_mainmeta" name="q1:1_ans" value=" 1+ x " data-stackinitialvalue="seed" />
                <input id="input_mainchange" name="q1:1_ans2" value="first" />
                <input id="input_maininput" name="q1:1_ans3" value="typed-start" />
                <input id="input_mainchanged" name="q1:1_ans4" value="before-change" />
                <input id="input_valid" name="q1:1_valid" value="v" />

                <textarea id="input_txt" name="q1:1_txt">line1</textarea>
                <select id="input_sel" name="q1:1_sel">
                    <option value="">None</option>
                    <option value="A" selected>A</option>
                </select>

                <div class="answer" id="radio-answer">
                    <input id="input_rad_1" type="radio" name="q1:1_rad" value="R1" checked />
                    <input id="input_rad_2" type="radio" name="q1:1_rad" value="R2" />
                </div>

                <div class="answer" id="check-answer">
                    <input id="input_chk_1" type="checkbox" name="q1:1_chk_1" value="C1" checked />
                    <input id="input_chk_2" type="checkbox" name="q1:1_chk_2" value="C2" />
                </div>
            </div>
            <div class="formulation" id="other-question">
                <input id="input_otheronly" name="q2:1_otheronly" value="other-question-value" />
            </div>
            <div class="outcome" id="feedback">
                <div id="feedback-target">feedback</div>
                <input id="input_outcome" name="q1:1_out" value="from-feedback" />
            </div>
        `;

        stackjsvle.create_iframe(
            'iframe-1',
            '<!doctype html><html><body>Frame</body></html>',
            'frame-target',
            'Author frame',
            true,
            false
        );
        stackjsvle.create_iframe(
            'iframe-2',
            '<!doctype html><html><body>Frame2</body></html>',
            'frame-target-2',
            'Author frame 2',
            true,
            false
        );
        installPostMessageSpy('iframe-1');
        installPostMessageSpy('iframe-2');
    });

    afterEach(() => {
        if (messageListener) {
            window.removeEventListener('message', messageListener);
            messageListener = null;
        }
    });

    test('create_iframe replaces target children and applies sandbox restrictions', () => {
        const target = document.getElementById('frame-target');
        expect(target.children).toHaveLength(1);

        const iframe = target.children[0];
        expect(iframe.tagName).toBe('IFRAME');
        expect(iframe.id).toBe('iframe-1');
        expect(iframe.sandbox.toString()).toContain('allow-scripts');
        expect(iframe.sandbox.toString()).toContain('allow-downloads');
        expect(iframe.sandbox.toString()).not.toContain('allow-same-origin');
        expect(iframe.title).toBe('Author frame');
    });

    test('register_iframe attaches existing iframe for message handling', () => {
        const manualTarget = document.createElement('div');
        manualTarget.id = 'manual-target';
        const manualFrame = document.createElement('iframe');
        manualFrame.id = 'iframe-manual';
        manualTarget.appendChild(manualFrame);
        document.getElementById('qform').appendChild(manualTarget);

        const postMessage = jest.fn();
        Object.defineProperty(manualFrame, 'contentWindow', {
            value: {postMessage},
            configurable: true,
        });

        stackjsvle.register_iframe('iframe-manual');

        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-manual',
            type: 'ping',
        });

        expect(postMessage).toHaveBeenCalledTimes(1);
        const response = JSON.parse(postMessage.mock.calls[0][0]);
        expect(response.type).toBe('ping');
        expect(response.tgt).toBe('iframe-manual');
    });

    test('change-content sanitises script/style/event attributes before insertion', () => {
        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'change-content',
            target: 'content-target',
            content: '<p onclick="evil()">Hello</p><a href="javascript:alert(1)">link</a><style>p{color:red;}</style><script>alert(1)</script>',
        });

        const target = document.getElementById('content-target');
        expect(target.innerHTML).toContain('<p>Hello</p>');
        expect(target.innerHTML).toContain('<a>link</a>');
        expect(target.innerHTML).not.toContain('<script');
        expect(target.innerHTML).not.toContain('<style');
        expect(target.innerHTML).not.toContain('onclick=');
        expect(target.innerHTML).not.toContain('javascript:');
    });

    test('register-input-listener returns initial value metadata for text input', () => {
        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'register-input-listener',
            name: 'mainmeta',
        });

        const response = expectLatestResponse('iframe-1');
        expect(response.type).toBe('initial-input');
        expect(response.name).toBe('mainmeta');
        expect(response.tgt).toBe('iframe-1');
        expect(response.value).toBe(' 1+ x ');
        expect(response['input-type']).toBe('text');
        expect(response['input-readonly']).toBe(false);
        expect(response['input-dataset']).toEqual({stackinitialvalue: 'seed'});
    });

    test('register-input-listener fails to connect', () => {
        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'register-input-listener',
            name: 'fake',
        });

        const response = expectLatestResponse('iframe-1');
        expect(response.type).toBe('error');
        expect(response.tgt).toBe('iframe-1');
        expect(response.msg).toBe('Failed to connect to input: "fake"');
    });

    test('register-input-listener tracks change events and notifies all listeners', () => {
        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'register-input-listener',
            name: 'mainchange',
        });
        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-2',
            type: 'register-input-listener',
            name: 'mainchange',
        });

        const input = document.getElementById('input_mainchange');
        input.value = 'new-value';
        input.dispatchEvent(new Event('change'));

        const response1 = latestResponse('iframe-1');
        const response2 = latestResponse('iframe-2');
        expect(response1.type).toBe('changed-input');
        expect(response1.name).toBe('mainchange');
        expect(response1.value).toBe('new-value');
        expect(response2.type).toBe('changed-input');
        expect(response2.value).toBe('new-value');
    });

    test('register-input-listener with track-input sends updates on input event', () => {
        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'register-input-listener',
            name: 'maininput',
            'track-input': true,
        });

        postMessageByFrame['iframe-1'].mockClear();
        const input = document.getElementById('input_maininput');
        input.value = 'typed';
        input.dispatchEvent(new Event('input'));

        const response = expectLatestResponse('iframe-1');
        expect(response.type).toBe('changed-input');
        expect(response.name).toBe('maininput');
        expect(response.value).toBe('typed');
    });

    test('track-validation-state forwards stack-validation events', () => {
        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'track-validation-state',
            name: 'valid',
        });

        const input = document.getElementById('input_valid');
        input.dispatchEvent(new CustomEvent('stack-validation', {
            detail: {valid: true, completed: false},
        }));

        const response = expectLatestResponse('iframe-1');
        expect(response.type).toBe('validation-state');
        expect(response.name).toBe('valid');
        expect(response.valid).toBe(true);
        expect(response.completed).toBe(false);
    });

    test('track-validation-state forwards radio-group validation events', () => {
        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'track-validation-state',
            name: 'rad',
        });

        const radioAnswer = document.getElementById('radio-answer');
        radioAnswer.dispatchEvent(new CustomEvent('stack-validation', {
            bubbles: true,
            detail: {valid: false, completed: true},
        }));

        const response = expectLatestResponse('iframe-1');
        expect(response.type).toBe('validation-state');
        expect(response.name).toBe('rad');
        expect(response.valid).toBe(false);
        expect(response.completed).toBe(true);
    });

    test('changed-input updates element value and notifies other iframe listeners', () => {
        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'register-input-listener',
            name: 'mainchanged',
        });
        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-2',
            type: 'register-input-listener',
            name: 'mainchanged',
        });
        postMessageByFrame['iframe-1'].mockClear();
        postMessageByFrame['iframe-2'].mockClear();

        const input = document.getElementById('input_mainchanged');
        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'changed-input',
            name: 'mainchanged',
            value: 'from-frame',
        });

        expect(input.value).toBe('from-frame');
        const responseToOther = latestResponse('iframe-2');
        expect(responseToOther.type).toBe('changed-input');
        expect(responseToOther.name).toBe('mainchanged');
        expect(responseToOther.value).toBe('from-frame');
    });

    test('clear-input empties select, textarea, radio and checkbox groups', () => {
        const select = document.getElementById('input_sel');
        select.value = 'A';
        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'clear-input',
            name: 'sel',
        });
        expect(select.value).toBe('');

        const textarea = document.getElementById('input_txt');
        textarea.value = 'some text';
        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'clear-input',
            name: 'txt',
        });
        expect(textarea.value).toBe('');

        const radio1 = document.getElementById('input_rad_1');
        const radio2 = document.getElementById('input_rad_2');
        radio1.value = '';
        radio2.checked = true;
        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'clear-input',
            name: 'rad',
        });
        expect(radio1.checked).toBe(true);
        expect(radio2.checked).toBe(false);

        const check1 = document.getElementById('input_chk_1');
        const check2 = document.getElementById('input_chk_2');
        check1.checked = true;
        check2.checked = true;
        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'clear-input',
            name: 'chk',
        });
        expect(check1.checked).toBe(false);
        expect(check2.checked).toBe(false);
    });

    test('toggle-visibility and get-content support question and outcome regions', () => {
        const target = document.getElementById('content-target');
        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'toggle-visibility',
            target: 'content-target',
            set: 'hide',
        });
        expect(target.style.display).toBe('none');

        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'toggle-visibility',
            target: 'content-target',
            set: 'show',
        });
        expect(target.style.display).toBe('block');

        const feedback = document.getElementById('feedback-target');
        feedback.innerHTML = '<strong>feedback</strong>';
        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'get-content',
            target: 'feedback-target',
        });
        const response = expectLatestResponse('iframe-1');
        expect(response.type).toBe('xfer-content');
        expect(response.target).toBe('feedback-target');
        expect(response.content).toBe('<strong>feedback</strong>');
    });

    test('resize-frame updates wrapper and iframe dimensions', () => {
        const wrapper = document.getElementById('frame-target');
        const iframe = document.getElementById('iframe-1');

        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'resize-frame',
            width: '420px',
            height: '240px',
        });

        expect(wrapper.style.width).toBe('420px');
        expect(wrapper.style.height).toBe('240px');
        expect(iframe.style.width).toBe('100%');
        expect(iframe.style.height).toBe('100%');
    });

    test('ping and submit button handlers respond as expected', () => {
        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'ping',
        });
        expect(expectLatestResponse('iframe-1').type).toBe('ping');

        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'query-submit-button',
        });
        const submitInfo = expectLatestResponse('iframe-1', 2);
        expect(submitInfo.type).toBe('submit-button-info');
        expect(submitInfo.value).toBe('Check');

        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'enable-submit-button',
            enabled: false,
        });
        expect(document.getElementById('behaviour-submit').disabled).toBe(true);

        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'enable-submit-button',
            enabled: true,
        });
        expect(document.getElementById('behaviour-submit').disabled).toBe(false);

        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'relabel-submit-button',
            name: 'Validate now',
        });
        expect(document.getElementById('behaviour-submit').value).toBe('Validate now');
    });

    test('register-button-listener sends button-click event and prevents default', () => {
        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'register-button-listener',
            target: 'action-btn',
        });

        const button = document.getElementById('action-btn');
        const event = new MouseEvent('click', {cancelable: true});
        button.dispatchEvent(event);

        const response = expectLatestResponse('iframe-1');
        expect(response.type).toBe('button-click');
        expect(response.name).toBe('action-btn');
        expect(event.defaultPrevented).toBe(true);
    });

    test('returns errors for unknown message type and missing targets', () => {
        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'unknown-type',
        });
        const unknown = expectLatestResponse('iframe-1');
        expect(unknown.type).toBe('error');
        expect(unknown.msg).toContain('Unknown message-type');

        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'change-content',
            target: 'does-not-exist',
            content: '<p>x</p>',
        });
        const missingTarget = expectLatestResponse('iframe-1', 2);
        expect(missingTarget.type).toBe('error');
        expect(missingTarget.msg).toContain('Failed to find element');
    });

    test('register-input-listener obeys limit-to-question search scope', () => {
        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'register-input-listener',
            name: 'otheronly',
            'limit-to-question': true,
        });
        const limitedResponse = expectLatestResponse('iframe-1');
        expect(limitedResponse.type).toBe('error');
        expect(limitedResponse.msg).toBe('Failed to connect to input: "otheronly"');

        postMessageByFrame['iframe-1'].mockClear();
        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'register-input-listener',
            name: 'otheronly',
        });
        const globalResponse = expectLatestResponse('iframe-1');
        expect(globalResponse.type).toBe('initial-input');
        expect(globalResponse.name).toBe('otheronly');
        expect(globalResponse.value).toBe('other-question-value');
    });

    test('ignores malformed, non-STACK, and unknown-source messages', () => {
        sendRawMessage({type: 'message-object'});
        sendRawMessage('{not-json');
        sendMessage({
            version: 'NOT-STACK:1.0.0',
            src: 'iframe-1',
            type: 'ping',
        });
        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-does-not-exist',
            type: 'ping',
        });

        expect(postMessageByFrame['iframe-1']).not.toHaveBeenCalled();
        expect(postMessageByFrame['iframe-2']).not.toHaveBeenCalled();
    });
});
