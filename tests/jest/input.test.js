/** @jest-environment jsdom */

const path = require('path');
const {loadAmdModule} = require('./loadAmdModule');

function loadInputModule(ajaxMock, eventsMock) {
    const modulePath = path.resolve(__dirname, '../../amd/src/input.js');
    return loadAmdModule(modulePath, {
        'core/ajax': ajaxMock,
        'core_filters/events': eventsMock,
    });
}

describe('amd/src/input.js', () => {
    beforeEach(() => {
        jest.useFakeTimers();
        jest.restoreAllMocks();
    });

    afterEach(() => {
        jest.useRealTimers();
    });

    test('does not validate when input returns to already validated value', () => {
        document.body.innerHTML = `
            <div id="question-1" class="dfexplicitvaildate">
                <div class="im-controls">
                    <input class="submit" id="pfx-submit" type="submit" value="Check" />
                </div>
                <input id="in1" name="pfxans" value=" 2x " />
                <div id="pfxans_val"></div>
                <input name="pfxstep_lang" value="fr"/>
                <input name="pfxstep_lang" value="de"/>
            </div>
        `;

        let callCount = 1;

        const ajaxMock = {
            call: jest.fn((requests) => {
                const request = requests[0];
                expect(request.methodname).toBe('qtype_stack_validate_input');
                if (callCount === 1) {
                    expect(request.args).toEqual({
                        qaid: 'qa-1',
                        name: 'ans',
                        input: 'x+1',
                        lang: 'fr',
                    });
                    setTimeout(() => {
                        expect(validationDiv.classList.contains('loading')).toBe(true);
                        expect(validationDiv.classList.contains('waiting')).toBe(false);
                        request.done({
                            status: 'valid',
                            input: 'x+1',
                            message: '<span>Looks good</span>',
                        });
                    });
                }
                if (callCount === 2) {
                    expect(request.args).toEqual({
                        qaid: 'qa-1',
                        name: 'ans',
                        input: '',
                        lang: 'fr',
                    });
                    setTimeout(() => {
                        request.done({
                            status: '',
                            input: '',
                            message: '',
                        });
                    });
                }
                callCount++;
            }),
        };
        const eventsMock = {notifyFilterContentUpdated: jest.fn()};
        const module = loadInputModule(ajaxMock, eventsMock);

        module.initInputs('question-1', 'pfx', 'qa-1', ['ans']);

        const input = document.getElementById('in1');
        const submit = document.getElementById('pfx-submit');
        const validationDiv = document.getElementById('pfxans_val');
        input.dispatchEvent(new Event('input', {bubbles: true}));

        expect(submit.disabled).toBe(true);
        jest.runAllTimers();

        input.value = 'x+1';
        input.dispatchEvent(new Event('input', {bubbles: true}));
        jest.runAllTimers();

        expect(ajaxMock.call).toHaveBeenCalledTimes(1);
        expect(validationDiv.innerHTML).toContain('Looks good');
        expect(validationDiv.classList.contains('error')).toBe(false);
        expect(eventsMock.notifyFilterContentUpdated).toHaveBeenCalledWith(validationDiv);
        expect(submit.disabled).toBe(false);

        input.dispatchEvent(new Event('input', {bubbles: true}));
        expect(submit.disabled).toBe(true);

        jest.runAllTimers();
        expect(ajaxMock.call).toHaveBeenCalledTimes(1);
        expect(submit.disabled).toBe(false);
        expect(submit.hidden).toBe(true);

        input.value = '';
        input.dispatchEvent(new Event('input', {bubbles: true}));
        jest.runAllTimers();
        expect(ajaxMock.call).toHaveBeenCalledTimes(2);
        expect(validationDiv.classList.contains('error')).toBe(false);
        expect(validationDiv.classList.contains('empty')).toBe(true);
        expect(validationDiv.classList.contains('loading')).toBe(false);
        expect(validationDiv.classList.contains('waiting')).toBe(false);
    });

    test('validates changed text input and dispatches completion events', () => {
        document.body.innerHTML = `
            <div id="question-2" class="dfexplicitvaildate">
                <div class="im-controls">
                    <input class="submit" id="pfx-submit" type="submit" value="Check" />
                </div>
                <input id="in2" name="pfxans" value="x" />
                <div id="pfxans_val"></div>
                <input name="pfxstep_lang" value=""/>
            </div>
        `;

        const ajaxMock = {
            call: jest.fn((requests) => {
                const request = requests[0];
                expect(request.methodname).toBe('qtype_stack_validate_input');
                expect(request.args).toEqual({
                    qaid: 'qa-2',
                    name: 'ans',
                    input: 'x+1',
                    lang: null,
                });
                setTimeout(() => {
                    request.done({
                        status: 'valid',
                        input: 'x+1',
                        message: '<span>Looks good</span>',
                    });
                });
            }),
        };
        const eventsMock = {notifyFilterContentUpdated: jest.fn()};
        const module = loadInputModule(ajaxMock, eventsMock);

        module.initInputs('question-2', 'pfx', 'qa-2', ['ans']);

        const input = document.getElementById('in2');
        const validationDiv = document.getElementById('pfxans_val');
        const seenValidationEvents = [];

        input.addEventListener('stack-validation', (event) => {
            seenValidationEvents.push(event.detail);
        });

        input.value = 'x+1';
        input.dispatchEvent(new Event('input', {bubbles: true}));
        jest.runAllTimers();

        expect(ajaxMock.call).toHaveBeenCalledTimes(1);
        expect(validationDiv.innerHTML).toContain('Looks good');
        expect(validationDiv.classList.contains('error')).toBe(false);
        expect(eventsMock.notifyFilterContentUpdated).toHaveBeenCalledWith(validationDiv);
        expect(seenValidationEvents).toEqual([
            {inputname: 'ans', completed: false, valid: null},
            {inputname: 'ans', completed: true, valid: true},
        ]);
        expect(validationDiv.classList.contains('empty')).toBe(false);
        expect(validationDiv.classList.contains('loading')).toBe(false);
        expect(validationDiv.classList.contains('waiting')).toBe(false);
    });

    test('textarea values are normalised and invalid responses show error state', () => {
        document.body.innerHTML = `
            <div id="question-3">
                <textarea id="ta1" name="prefixta">  a\n b  </textarea>
                <div id="prefixta_val"></div>
            </div>
        `;

        const ajaxMock = {
            call: jest.fn((requests) => {
                const request = requests[0];
                // Line ending converted to HTML break.
                expect(request.args.input).toBe('c<br>d');
                setTimeout(() => {
                    request.done({
                        status: 'invalid',
                        message: '<strong>Invalid input</strong>',
                    });
                }, 0);
            }),
        };
        const eventsMock = {notifyFilterContentUpdated: jest.fn()};
        const module = loadInputModule(ajaxMock, eventsMock);

        module.initInputs('question-3', 'prefix', 'qa-3', ['ta']);

        const textarea = document.getElementById('ta1');
        const seenValidationEvents = [];
        textarea.addEventListener('stack-validation', (event) => {
            seenValidationEvents.push(event.detail);
        });
        const validationDiv = document.getElementById('prefixta_val');
        textarea.value = ' c\n d ';
        expect(validationDiv.classList.contains('error')).toBe(false);
        expect(validationDiv.classList.contains('empty')).toBe(false);
        expect(validationDiv.classList.contains('loading')).toBe(false);
        expect(validationDiv.classList.contains('waiting')).toBe(false);
        textarea.dispatchEvent(new Event('input', {bubbles: true}));
        expect(validationDiv.classList.contains('error')).toBe(false);
        expect(validationDiv.classList.contains('empty')).toBe(false);
        expect(validationDiv.classList.contains('loading')).toBe(false);
        expect(validationDiv.classList.contains('waiting')).toBe(true);
        jest.runAllTimers();

        expect(validationDiv.classList.contains('error')).toBe(true);
        expect(validationDiv.classList.contains('empty')).toBe(false);
        expect(validationDiv.classList.contains('loading')).toBe(false);
        expect(validationDiv.classList.contains('waiting')).toBe(false);
        expect(validationDiv.innerHTML).toContain('Invalid input');
        expect(eventsMock.notifyFilterContentUpdated).toHaveBeenCalledWith(validationDiv);
        expect(seenValidationEvents).toEqual([
            {inputname: 'ta', completed: false, valid: null},
            {inputname: 'ta', completed: true, valid: false},
        ]);
    });

    test('strips script tags from validation message output', () => {
        document.body.innerHTML = `
            <div id="question-4">
                <input id="in4" name="pfxans" value="x" />
                <div id="pfxans_val"></div>
            </div>
        `;

        window.__stackExtractScriptsRan = false;

        const ajaxMock = {
            call: jest.fn((requests) => {
                const request = requests[0];
                setTimeout(() => {
                    request.done({
                        status: 'valid',
                        input: 'x+1',
                        message: '<span>safe</span><script>window.__stackExtractScriptsRan = true;</script><em>content</em>',
                    });
                });
            }),
        };
        const eventsMock = {notifyFilterContentUpdated: jest.fn()};
        const module = loadInputModule(ajaxMock, eventsMock);

        module.initInputs('question-4', 'pfx', 'qa-4', ['ans']);

        const input = document.getElementById('in4');
        const validationDiv = document.getElementById('pfxans_val');

        input.value = 'x+1';
        input.dispatchEvent(new Event('input', {bubbles: true}));
        jest.runAllTimers();

        expect(validationDiv.innerHTML).toBe('<span>safe</span><em>content</em>');
        expect(window.__stackExtractScriptsRan).toBe(false);
        expect(eventsMock.notifyFilterContentUpdated).toHaveBeenCalledWith(validationDiv);
    });

    test('strips multiple script tags from validation message output', () => {
        document.body.innerHTML = `
            <div id="question-5">
                <input id="in5" name="pfxans" value="x" />
                <div id="pfxans_val"></div>
            </div>
        `;

        const ajaxMock = {
            call: jest.fn((requests) => {
                const request = requests[0];
                setTimeout(() => {
                    request.done({
                        status: 'valid',
                        input: 'x+2',
                        message: 'A<script>first()</script>B<script type="text/javascript">second()</script>C',
                    });
                });
            }),
        };
        const eventsMock = {notifyFilterContentUpdated: jest.fn()};
        const module = loadInputModule(ajaxMock, eventsMock);

        module.initInputs('question-5', 'pfx', 'qa-5', ['ans']);

        const input = document.getElementById('in5');
        const validationDiv = document.getElementById('pfxans_val');

        input.value = 'x+2';
        input.dispatchEvent(new Event('input', {bubbles: true}));
        jest.runAllTimers();

        expect(validationDiv.innerHTML).toBe('ABC');
    });

    test('getValue returns expected payload for all supported input types', () => {
        document.body.innerHTML = `
            <div id="question-all-types">
                <input id="simple1" name="pfxsimple" value="  start  " />
                <div id="pfxsimple_val"></div>

                <textarea id="ta-all" name="pfxta">  a\n b  </textarea>
                <div id="pfxta_val"></div>

                <div class="answer" id="rad-answer">
                    <input id="rad-1" type="radio" name="pfxrad" value="left" checked />
                    <input id="rad-2" type="radio" name="pfxrad" value="right" />
                </div>
                <div id="pfxrad_val"></div>

                <div class="answer" id="chk-answer">
                    <input id="chk-1" type="checkbox" name="pfxchk_1" value="A" checked />
                    <input id="chk-2" type="checkbox" name="pfxchk_2" value="B" />
                    <input id="chk-3" type="checkbox" name="pfxchk_2" value="C" />
                </div>
                <div id="pfxchk_val"></div>

                <select id="sel-1" name="pfxsel">
                    <option value="first" selected>First</option>
                    <option value="second">Second</option>
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
        `;

        const capturedInputs = {};
        const ajaxMock = {
            call: jest.fn((requests) => {
                const request = requests[0];
                capturedInputs[request.args.name] = request.args.input;
                setTimeout(() => {
                    request.done({
                        status: 'valid',
                        input: request.args.input,
                        message: '<span>ok</span>',
                    });
                }, 0);
            }),
        };
        const eventsMock = {notifyFilterContentUpdated: jest.fn()};
        const module = loadInputModule(ajaxMock, eventsMock);

        module.initInputs('question-all-types', 'pfx', 'qa-all', ['simple', 'ta', 'rad', 'chk', 'sel', 'mat']);

        const simple = document.getElementById('simple1');
        simple.value = '  x+y  ';
        simple.dispatchEvent(new Event('input', {bubbles: true}));

        const textarea = document.getElementById('ta-all');
        textarea.value = '  c\n d  ';
        textarea.dispatchEvent(new Event('input', {bubbles: true}));

        const radioRight = document.getElementById('rad-2');
        radioRight.checked = true;
        radioRight.dispatchEvent(new Event('input', {bubbles: true}));

        const checkboxC = document.getElementById('chk-3');
        checkboxC.checked = true;
        checkboxC.dispatchEvent(new Event('input', {bubbles: true}));

        const select = document.getElementById('sel-1');
        select.value = 'second';
        select.dispatchEvent(new Event('input', {bubbles: true}));

        const matrixCell = document.querySelector('input[name="pfxmat_sub_1_1"]');
        matrixCell.value = ' 5 ';
        matrixCell.dispatchEvent(new Event('input', {bubbles: true}));

        jest.runAllTimers();

        expect(ajaxMock.call).toHaveBeenCalledTimes(6);
        expect(capturedInputs).toEqual({
            simple: 'x+y',
            ta: 'c<br>d',
            rad: 'right',
            chk: 'A,C',
            sel: 'second',
            mat: '[["1","2"],["3","5"]]',
        });
    });
});
