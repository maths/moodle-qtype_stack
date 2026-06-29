/** @jest-environment jsdom */

const fs = require('fs');
const path = require('path');

let mutationObservers = [];

function loadMobileStack(context) {
    const modulePath = path.resolve(__dirname, '../../mobile/stack.js');
    const source = fs.readFileSync(modulePath, 'utf8');
    const wrapped = new Function(`${source}\nreturn result;`);
    return wrapped.call(context);
}

function buildQuestionHtml() {
    return `
        <div id="q1" class="formulation dfexplicitvaildate">
            <div class="prompt">Prompt <em>text</em></div>
            <div class="content">
                <a class="questiontestslink" href="#">dashboard</a>
                <div class="validationerror">validation warning</div>
                <div class="im-controls">
                    <input class="submit" id="pfx-submit" type="submit" value="Check" />
                </div>
                <div id="frame-target">loading iframe</div>
                <div id="frame-target-2">loading iframe 2</div>
                <div id="content-target">placeholder</div>

                <input id="input_ans" type="text" name="pfxans" value=" x+1 " style="width: 10em;" data-stackinitialvalue="seed" />
                <div id="pfxans_val"></div>

                <textarea id="input_txt" name="pfxtxt">line1</textarea>
                <div id="pfxtxt_val"></div>

                <div class="answer" id="checkbox-answer">
                    <div class="option option-a">
                        <label for="pfxchk_1">Choice A</label>
                        <input id="pfxchk_1" type="checkbox" name="pfxchk_1" value="A" checked="checked" />
                    </div>
                    <div class="option option-b">
                        <label for="pfxchk_2">Choice B</label>
                        <input id="pfxchk_2" type="checkbox" name="pfxchk_2" value="B" />
                    </div>
                </div>
                <div id="pfxchk_val"></div>

                <div class="answer" id="radio-answer">
                    <div class="option option-r1">
                        <label for="rad_1">Left</label>
                        <input id="rad_1" type="radio" name="pfxrad" value="left" checked="checked" />
                    </div>
                    <div class="option option-r2">
                        <label for="rad_2">Right</label>
                        <input id="rad_2" type="radio" name="pfxrad" value="right" />
                    </div>
                </div>
                <div id="pfxrad_val"></div>

                <select id="select_1" name="pfxsel">
                    <option value="">None</option>
                    <option value="A" selected="selected">A</option>
                </select>
                <div id="pfxsel_val"></div>

                <div id="pfxmat_container">
                    <input type="text" name="pfxmat_sub_0_0" value=" 1 " />
                    <input type="text" name="pfxmat_sub_0_1" value="2 " />
                    <input type="text" name="pfxmat_sub_1_0" value=" 3" />
                    <input type="text" name="pfxmat_sub_1_1" value=" 4 " />
                </div>
                <div id="pfxmat_val"></div>
            </div>
            <input name="pfxstep_lang" value="fr" />
        </div>
    `;
}

function mountRenderedQuestion(question) {
    document.body.innerHTML = `
        <div id="${question.divId}" class="formulation dfexplicitvaildate">
            <div class="content">${question.text}</div>
            <input name="pfxstep_lang" value="fr" />
        </div>
    `;

    document.querySelectorAll('ion-select').forEach((element) => {
        element.value = element.getAttribute('value') || '';
    });
    document.querySelectorAll('ion-radio-group').forEach((element) => {
        element.value = element.getAttribute('value') || '';
    });
    document.querySelectorAll('ion-checkbox').forEach((element) => {
        element.value = element.getAttribute('value') || '';
        element.checked = element.getAttribute('checked') === 'true';
    });
}

function buildContext(overrides = {}) {
    const read = overrides.read || jest.fn().mockResolvedValue({
        status: 'valid',
        input: 'x+1',
        message: '<span>OK</span>',
    });

    return {
        onAbort: jest.fn(),
        CoreQuestionHelperProvider: {
            showComponentError: jest.fn(() => 'component-error'),
            replaceCorrectnessClasses: jest.fn(),
            replaceFeedbackClasses: jest.fn(),
            treatCorrectnessIcons: jest.fn(),
        },
        CoreDomUtilsProvider: {
            convertToElement: jest.fn(() => document.createElement('div')),
        },
        CoreSitesProvider: {
            currentSite: {siteUrl: 'https://example.test'},
            getCurrentSite: jest.fn(() => ({read})),
        },
        CoreFilterDelegate: {
            getEnabledFilters: jest.fn(() => ['mathjax']),
            handleHtml: jest.fn(),
        },
        ...overrides,
    };
}

async function flushMicrotasks() {
    await Promise.resolve();
    await Promise.resolve();
}

function triggerMutationObservers() {
    mutationObservers.forEach((observer) => {
        if (!observer.disconnected) {
            observer.callback();
        }
    });
}

async function setupMessageHarness(frameIds = ['iframe-1']) {
    const scriptsCode = frameIds.map((frameId, index) =>
        `stackjsvle.create_iframe("${frameId}","<!doctype html><html><body>Frame</body></html>","frame-target${index === 0 ? '' : '-2'}","Author frame",true,false);});;`
    ).join('\n');

    const context = buildContext({
        question: {
            html: buildQuestionHtml(),
            scriptsCode,
        },
    });
    const mobileStack = loadMobileStack(context);

    mobileStack.componentInit.call(context);
    mountRenderedQuestion(context.question);
    jest.runAllTimers();
    await flushMicrotasks();

    const postMessageByFrame = {};
    frameIds.forEach((frameId) => {
        const iframe = document.getElementById(frameId);
        const postMessage = jest.fn();
        Object.defineProperty(iframe, 'contentWindow', {
            value: {postMessage},
            configurable: true,
        });
        postMessageByFrame[frameId] = postMessage;
    });

    const sendMessage = (message) => {
        window.dispatchEvent(new MessageEvent('message', {
            data: JSON.stringify(message),
        }));
    };

    const latestResponse = (frameId) => {
        const calls = postMessageByFrame[frameId].mock.calls;
        return JSON.parse(calls[calls.length - 1][0]);
    };

    return {context, postMessageByFrame, sendMessage, latestResponse};
}

describe('mobile/stack.js', () => {
    beforeEach(() => {
        jest.useFakeTimers();
        jest.restoreAllMocks();
        mutationObservers = [];

        global.CustomEvents = {notifyFilterContentUpdated: jest.fn()};
        global.MathJax = {};
        if (!global.CSS) {
            global.CSS = {};
        }
        if (!global.CSS.escape) {
            global.CSS.escape = (value) => String(value).replace(/(["\\#.;:?+*~'!^$\[\]()=>|/@])/g, '\\$1');
        }

        global.MutationObserver = class {
            constructor(callback) {
                this.callback = callback;
                this.disconnected = false;
                this.disconnect = jest.fn(() => {
                    this.disconnected = true;
                });
                mutationObservers.push(this);
            }

            observe() {
                this.callback();
            }
        };

        document.body.innerHTML = '';
    });

    afterEach(() => {
        jest.useRealTimers();
    });

    test('shows component error when question data is missing', () => {
        const context = buildContext();
        const mobileStack = loadMobileStack(context);

        const ret = mobileStack.componentInit.call(context);

        expect(ret).toBe('component-error');
        expect(context.CoreQuestionHelperProvider.showComponentError).toHaveBeenCalledWith(context.onAbort);
    });

    test('componentInit transforms content and extracts prompt/text', () => {
        const context = buildContext({
            question: {
                html: buildQuestionHtml(),
                scriptsCode: '',
            },
        });
        const mobileStack = loadMobileStack(context);

        const ret = mobileStack.componentInit.call(context);

        expect(ret).toBe(true);
        expect(context.question.divId).toBe('q1');
        expect(context.question.prompt).toContain('Prompt');
        expect(context.question.text).toContain('ion-checkbox');
        expect(context.question.text).toContain('ion-radio-group');
        expect(context.question.text).toContain('ion-select');
        expect(context.question.text).not.toContain('questiontestslink');
        expect(context.question.text).toContain('validationerror');
        expect(context.question.text).toContain('hidden="true"');
        expect(context.question.text).toContain('style="width: 13em;"');
    });

    test('rewrites MathJax iframe config to common HTML on mobile', async() => {
        const baseRef = 'https://example.test/question/type/stack/corsscripts/cors.php?name=';
        const iframeHtml = '<!doctype html><html><head>'
            + '<link href="' + baseRef + 'sortable.min.css" rel="stylesheet">'
            + '<script src="https://cdn.jsdelivr.net/npm/mathjax@2.7.9/MathJax.js?config=TeX-AMS-MML_HTMLorMML&delayStartupUntil=configured"></script>'
            + '</head><body><div class="MathJax">Frame</div></body></html>';
        const context = buildContext({
            question: {
                html: buildQuestionHtml(),
                scriptsCode: 'stackjsvle.create_iframe("iframe-1","' + iframeHtml.replace(/"/g, '\\"')
                    + '","frame-target","Author frame",true,false);});;',
            },
        });
        const mobileStack = loadMobileStack(context);

        mobileStack.componentInit.call(context);
        mountRenderedQuestion(context.question);
        jest.runAllTimers();
        await flushMicrotasks();

        const iframe = document.getElementById('iframe-1');
        expect(iframe.srcdoc).toContain(baseRef + 'styles.css');
        expect(iframe.srcdoc).toContain('MathJax.js?config=TeX-MML-AM_CHTML&delayStartupUntil=configured');
        expect(iframe.srcdoc).not.toContain('MathJax.js?config=TeX-AMS-MML_HTMLorMML&delayStartupUntil=configured');
    });

    test('waits for iframe targets before initialising iframes', async() => {
        const context = buildContext({
            question: {
                html: buildQuestionHtml(),
                scriptsCode: 'stackjsvle.create_iframe("iframe-1","<!doctype html><html><body>Frame</body></html>",'
                    + '"frame-target","Author frame",true,false);});;',
            },
        });
        const mobileStack = loadMobileStack(context);

        mobileStack.componentInit.call(context);
        document.body.innerHTML = `
            <div id="${context.question.divId}" class="formulation dfexplicitvaildate">
                <div class="content"></div>
                <input name="pfxstep_lang" value="fr" />
            </div>
        `;
        jest.runAllTimers();
        await flushMicrotasks();

        expect(document.getElementById('iframe-1')).toBeNull();
        expect(mutationObservers[0].disconnect).not.toHaveBeenCalled();

        mountRenderedQuestion(context.question);
        triggerMutationObservers();
        await flushMicrotasks();

        expect(document.getElementById('iframe-1')).not.toBeNull();
        expect(mutationObservers[0].disconnect).toHaveBeenCalledTimes(1);
    });

    test('initialises instant validation from scriptsCode and hides submit button', async() => {
        const read = jest.fn().mockResolvedValue({
            status: 'valid',
            input: 'x+2',
            message: '<span>Looks good</span>',
        });
        const context = buildContext({
            read,
            question: {
                html: buildQuestionHtml(),
                scriptsCode: 'amd.initInputs("q1","pfx","qa-1",["ans"]);',
            },
        });
        const mobileStack = loadMobileStack(context);

        mobileStack.componentInit.call(context);
        mountRenderedQuestion(context.question);
        jest.runAllTimers();

        const input = document.querySelector('#q1 [name="pfxans"]');
        const validationDiv = document.getElementById('pfxans_val');
        const submit = document.getElementById('pfx-submit');

        const seenValidationEvents = [];
        input.addEventListener('stack-validation', (event) => {
            seenValidationEvents.push(event.detail);
        });

        input.value = ' x+2 ';
        input.dispatchEvent(new Event('input', {bubbles: true}));

        expect(validationDiv.classList.contains('waiting')).toBe(true);
        expect(submit.disabled).toBe(true);

        jest.runAllTimers();
        await flushMicrotasks();

        expect(read).toHaveBeenCalledWith('qtype_stack_validate_input', {
            qaid: 'qa-1',
            name: 'ans',
            input: 'x+2',
            lang: 'fr',
        });
        expect(validationDiv.innerHTML).toContain('Looks good');
        expect(validationDiv.classList.contains('error')).toBe(false);
        expect(submit.hidden).toBe(true);
        expect(context.CoreFilterDelegate.handleHtml).toHaveBeenCalledWith(validationDiv, ['mathjax']);
        expect(seenValidationEvents).toEqual([
            {inputname: 'ans', completed: false, valid: null},
            {inputname: 'ans', completed: true, valid: true},
        ]);
    });

    test('failed validation request sets error state and emits invalid event', async() => {
        const read = jest.fn().mockRejectedValue({message: '<strong>Server error</strong>'});
        const context = buildContext({
            read,
            question: {
                html: buildQuestionHtml(),
                scriptsCode: 'amd.initInputs("q1","pfx","qa-2",["ans"]);',
            },
        });
        const mobileStack = loadMobileStack(context);

        mobileStack.componentInit.call(context);
        mountRenderedQuestion(context.question);
        jest.runAllTimers();

        const input = document.querySelector('#q1 [name="pfxans"]');
        const validationDiv = document.getElementById('pfxans_val');
        const seenValidationEvents = [];
        input.addEventListener('stack-validation', (event) => {
            seenValidationEvents.push(event.detail);
        });

        input.value = 'bad';
        input.dispatchEvent(new Event('input', {bubbles: true}));

        jest.runAllTimers();
        await flushMicrotasks();

        expect(validationDiv.classList.contains('error')).toBe(true);
        expect(validationDiv.innerHTML).toContain('Server error');
        expect(seenValidationEvents).toEqual([
            {inputname: 'ans', completed: false, valid: null},
            {inputname: 'ans', completed: true, valid: false},
        ]);
    });

    test('strips script tags from successful validation message', async() => {
        window.__mobileStackScriptRan = false;

        const read = jest.fn().mockResolvedValue({
            status: 'valid',
            input: 'x+2',
            message: '<span>safe</span><script>window.__mobileStackScriptRan = true;</script><em>content</em>',
        });
        const context = buildContext({
            read,
            question: {
                html: buildQuestionHtml(),
                scriptsCode: 'amd.initInputs("q1","pfx","qa-3",["ans"]);',
            },
        });
        const mobileStack = loadMobileStack(context);

        mobileStack.componentInit.call(context);
        mountRenderedQuestion(context.question);
        jest.runAllTimers();

        const input = document.querySelector('#q1 [name="pfxans"]');
        const validationDiv = document.getElementById('pfxans_val');
        input.value = 'x+2';
        input.dispatchEvent(new Event('input', {bubbles: true}));

        jest.runAllTimers();
        await flushMicrotasks();

        expect(validationDiv.innerHTML).toBe('<span>safe</span><em>content</em>');
        expect(window.__mobileStackScriptRan).toBe(false);
    });

    test('componentInit covers all input handlers through initInputs', async() => {
        const captured = [];
        const read = jest.fn().mockImplementation((method, args) => {
            captured.push(args);
            return Promise.resolve({
                status: 'valid',
                input: args.input,
                message: '<span>ok</span>',
            });
        });
        const context = buildContext({
            read,
            question: {
                html: buildQuestionHtml(),
                scriptsCode: 'amd.initInputs("q1","pfx","qa-all",["ans","txt","sel","rad","chk","mat"]);',
            },
        });
        const mobileStack = loadMobileStack(context);

        mobileStack.componentInit.call(context);
        mountRenderedQuestion(context.question);
        jest.runAllTimers();

        const textInput = document.querySelector('#q1 [name="pfxans"]');
        textInput.value = ' x+y ';
        textInput.dispatchEvent(new Event('input', {bubbles: true}));
        jest.runAllTimers();
        await flushMicrotasks();

        const textarea = document.querySelector('#q1 [name="pfxtxt"]');
        textarea.value = ' c\n d ';
        textarea.dispatchEvent(new Event('input', {bubbles: true}));
        jest.runAllTimers();
        await flushMicrotasks();

        const select = document.querySelector('#q1 ion-select[name="pfxsel"]');
        select.value = '';
        select.dispatchEvent(new Event('ionChange', {bubbles: true}));
        jest.runAllTimers();
        await flushMicrotasks();

        const radioGroup = document.querySelector('#q1 ion-radio-group[name="pfxrad"]');
        radioGroup.value = 'right';
        radioGroup.dispatchEvent(new Event('ionChange', {bubbles: true}));
        jest.runAllTimers();
        await flushMicrotasks();

        const checkbox1 = document.querySelector('#q1 ion-checkbox[name="pfxchk_1"]');
        const checkbox2 = document.querySelector('#q1 ion-checkbox[name="pfxchk_2"]');
        checkbox1.checked = true;
        checkbox2.checked = true;
        checkbox1.dispatchEvent(new Event('click', {bubbles: true}));
        jest.runAllTimers();
        await flushMicrotasks();

        const matrixContainer = document.getElementById('pfxmat_container');
        const matrixCell = document.querySelector('#q1 input[name="pfxmat_sub_1_1"]');
        matrixCell.value = ' 5 ';
        matrixContainer.dispatchEvent(new Event('input', {bubbles: true}));
        jest.runAllTimers();
        await flushMicrotasks();

        expect(read).toHaveBeenCalledTimes(6);
        expect(captured).toEqual([
            {qaid: 'qa-all', name: 'ans', input: 'x+y', lang: 'fr'},
            {qaid: 'qa-all', name: 'txt', input: 'c<br>d', lang: 'fr'},
            {qaid: 'qa-all', name: 'sel', input: '', lang: 'fr'},
            {qaid: 'qa-all', name: 'rad', input: 'right', lang: 'fr'},
            {qaid: 'qa-all', name: 'chk', input: 'A,B', lang: 'fr'},
            {qaid: 'qa-all', name: 'mat', input: '[["1","2"],["3","5"]]', lang: 'fr'},
        ]);
    });

    test('message handler responds to ping and sanitises change-content updates', async() => {
        const {postMessageByFrame, sendMessage} = await setupMessageHarness(['iframe-1']);
        const postMessage = postMessageByFrame['iframe-1'];

        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'ping',
        });

        expect(postMessage).toHaveBeenCalledTimes(1);
        const pingResponse = JSON.parse(postMessage.mock.calls[0][0]);
        expect(pingResponse.type).toBe('ping');

        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'change-content',
            target: 'content-target',
            content: '<p onclick="evil()">Hello</p><a href="javascript:alert(1)">x</a><script>alert(1)</script>',
        });

        const target = document.getElementById('content-target');
        expect(target.innerHTML).toContain('<p>Hello</p>');
        expect(target.innerHTML).toContain('<a>x</a>');
        expect(target.innerHTML).not.toContain('<script');
        expect(target.innerHTML).not.toContain('onclick=');
        expect(target.innerHTML).not.toContain('javascript:');
        expect(global.CustomEvents.notifyFilterContentUpdated).toHaveBeenCalledWith(target);
    });

    test('register-input-listener and track-validation-state return messages for valid inputs', async() => {
        const {postMessageByFrame, sendMessage, latestResponse} = await setupMessageHarness(['iframe-1']);

        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'register-input-listener',
            name: 'ans',
        });
        const initial = latestResponse('iframe-1');
        expect(initial.type).toBe('initial-input');
        expect(initial.name).toBe('ans');
        expect(initial.value).toBe(' x+1 ');

        postMessageByFrame['iframe-1'].mockClear();
        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'track-validation-state',
            name: 'ans',
        });
        const input = document.querySelector('#q1 [name="pfxans"]');
        input.dispatchEvent(new CustomEvent('stack-validation', {
            detail: {valid: true, completed: false},
        }));

        const validation = latestResponse('iframe-1');
        expect(validation.type).toBe('validation-state');
        expect(validation.name).toBe('ans');
        expect(validation.valid).toBe(true);
        expect(validation.completed).toBe(false);
    });

    test('changed-input updates values and notifies other iframe listeners', async() => {
        const {postMessageByFrame, sendMessage, latestResponse} = await setupMessageHarness(['iframe-1', 'iframe-2']);

        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'register-input-listener',
            name: 'ans',
        });
        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-2',
            type: 'register-input-listener',
            name: 'ans',
        });
        postMessageByFrame['iframe-1'].mockClear();
        postMessageByFrame['iframe-2'].mockClear();

        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'changed-input',
            name: 'ans',
            value: 'from-frame',
        });

        const input = document.querySelector('#q1 [name="pfxans"]');
        expect(input.value).toBe('from-frame');
        const responseToOther = latestResponse('iframe-2');
        expect(responseToOther.type).toBe('changed-input');
        expect(responseToOther.name).toBe('ans');
        expect(responseToOther.value).toBe('from-frame');
    });

    test('clear-input and submit button commands operate through message API', async() => {
        const {postMessageByFrame, sendMessage, latestResponse} = await setupMessageHarness(['iframe-1']);

        const input = document.querySelector('#q1 [name="pfxans"]');
        const seenEvents = [];
        input.addEventListener('change', () => seenEvents.push('change'));
        input.addEventListener('input', () => seenEvents.push('input'));
        input.value = 'to-clear';
        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'clear-input',
            name: 'ans',
        });
        expect(input.value).toBe('');
        expect(seenEvents).toContain('change');
        expect(seenEvents).toContain('input');

        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'query-submit-button',
        });
        const submitInfo = latestResponse('iframe-1');
        expect(submitInfo.type).toBe('submit-button-info');
        expect(submitInfo.value).toBe('Check');

        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'enable-submit-button',
            enabled: false,
        });
        expect(document.getElementById('pfx-submit').disabled).toBe(true);

        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'enable-submit-button',
            enabled: true,
        });
        expect(document.getElementById('pfx-submit').disabled).toBe(false);

        sendMessage({
            version: 'STACK-JS:1.5.0',
            src: 'iframe-1',
            type: 'relabel-submit-button',
            name: 'Validate now',
        });
        expect(document.getElementById('pfx-submit').value).toBe('Validate now');

    });
});
