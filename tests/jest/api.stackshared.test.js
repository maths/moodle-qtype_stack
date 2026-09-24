/** @jest-environment jsdom */

const fs = require('fs');
const path = require('path');

function loadApiStackSharedModule() {
    const modulePath = path.resolve(__dirname, '../../api/public/stackshared.js');
    const source = fs.readFileSync(modulePath, 'utf8');
    const wrapped = new Function(`${source}\nreturn {collectAnswer, processNodes, send, validate, answer, createIframes, replaceFeedbackTags, loading, download, diff};`);
    return wrapped();
}

class MockXMLHttpRequest {
    static instances = [];

    constructor() {
        this.headers = {};
        this.readyState = 0;
        this.responseText = '';
        this.method = null;
        this.url = null;
        this.async = true;
        this.body = undefined;
        MockXMLHttpRequest.instances.push(this);
    }

    static reset() {
        MockXMLHttpRequest.instances = [];
    }

    open(method, url, async) {
        this.method = method;
        this.url = url;
        this.async = async;
    }

    setRequestHeader(name, value) {
        this.headers[name] = value;
    }

    send(body) {
        this.body = body;
    }

    respondWith(responseText, extra = {}) {
        Object.assign(this, extra);
        this.responseText = responseText;
        this.readyState = 4;
        if (typeof this.onreadystatechange === 'function') {
            this.onreadystatechange();
        }
    }
}

function installJQueryMock() {
    const chain = {
        prop: jest.fn().mockReturnThis(),
        addClass: jest.fn().mockReturnThis(),
        removeClass: jest.fn().mockReturnThis(),
        show: jest.fn().mockReturnThis(),
        hide: jest.fn().mockReturnThis(),
    };
    global.$ = jest.fn(() => chain);
    return chain;
}

function setupBaseDom() {
    document.body.innerHTML = `
        <div class="main-content">
            <button class="btn-primary">Submit</button>
        </div>
        <div id="stackapi_spinner"></div>
        <a id="stackapi-nav"></a>

        <div id="errors"></div>
        <div id="output"></div>
        <div id="stackapi_validity"></div>
        <div id="specificfeedback"></div>
        <div id="response_summary"></div>
        <div id="stackapi_summary"></div>
        <div id="stackapi_score"></div>
        <div id="stackapi_combinedfeedback"></div>
        <div id="score"></div>
        <div id="stackapi_qtext" style="display:none"></div>
        <div id="stackapi_correct" style="display:none"></div>
        <div id="stackapi_generalfeedback" style="display:none"></div>
        <div id="generalfeedback"></div>
        <div id="stackapi_questionnote" style="display:none"></div>
        <div id="questionnote"></div>
        <div id="formatcorrectresponse"></div>
        <div id="stack-iframe-holder-0"></div>
        <div class="noninfo" id="needs-input" style="display:none"></div>

        <div id="stackapi_difference" style="display:none"></div>
        <pre id="difference"></pre>

        <input id="in-text" name="stackapi_input_alpha" value="x+1" />
        <input id="in-radio-1" type="radio" name="stackapi_input_choice" value="A" />
        <input id="in-radio-2" type="radio" name="stackapi_input_choice" value="B" checked />
        <input id="in-check-1" type="checkbox" name="stackapi_input_check_1" value="yes" checked />
        <input id="in-check-2" type="checkbox" name="stackapi_input_check_2" value="no" />
        <input id="in-val" name="stackapi_input_alpha_val" value="ok" />
        <textarea id="ta" name="stackapi_input_beta">line1</textarea>
        <select id="sel" name="stackapi_input_gamma">
            <option value="">None</option>
            <option value="G" selected>G</option>
        </select>

        <span name="stackapi_val_alpha"></span>
        <div name="stackapi_fb_prt1" class="feedback">old fb 1</div>
        <div name="stackapi_fb_prt2" class="feedback">old fb 2</div>

        <a id="download-link" href="javascript:download('report.txt', 7)">Download</a>
    `;

    const output = document.getElementById('output');
    Object.defineProperty(output, 'innerText', {
        configurable: true,
        writable: true,
        value: 'Rendered question',
    });
}

function allowBrokenIframeHolderSelector() {
    const nativeQuerySelectorAll = document.querySelectorAll.bind(document);
    return jest.spyOn(document, 'querySelectorAll').mockImplementation((selector) => {
        if (selector === '[id^=stack-iframe-holder]:not([id$=old]') {
            return nativeQuerySelectorAll('[id^=stack-iframe-holder]');
        }
        return nativeQuerySelectorAll(selector);
    });
}

describe('api/public/stackshared.js', () => {
    let stackshared;
    let jqueryChain;

    beforeEach(() => {
        jest.restoreAllMocks();
        MockXMLHttpRequest.reset();
        global.XMLHttpRequest = MockXMLHttpRequest;

        global.collectData = jest.fn(() => ({
            questionDefinition: 'QDEF',
            answers: {alpha: 'x+1'},
            seed: 42,
        }));

        global.create_iframe = jest.fn();
        global.MathJax = {typesetPromise: jest.fn()};

        if (!global.CSS) {
            global.CSS = {};
        }
        if (!global.CSS.escape) {
            global.CSS.escape = (value) => String(value).replace(/(["\\#.;:?+*~'!^$\[\]()=>|/@])/g, '\\$1');
        }

        global.URL.createObjectURL = jest.fn(() => 'blob:mocked');

        jqueryChain = installJQueryMock();
        setupBaseDom();
        stackshared = loadApiStackSharedModule();
    });

    test('collectAnswer aggregates text, checked choices, textarea, select, and _val fields', () => {
        const result = stackshared.collectAnswer();

        expect(result).toEqual({
            alpha: 'x+1',
            choice: 'B',
            check_1: 'yes',
            stackapi_input_alpha_val: 'ok',
            beta: 'line1',
            gamma: 'G',
        });
    });

    test('processNodes ignores unchecked radio/checkbox values', () => {
        const unchecked = document.createElement('input');
        unchecked.type = 'checkbox';
        unchecked.name = 'stackapi_input_skip';
        unchecked.value = 'ignored';
        unchecked.checked = false;

        const checked = document.createElement('input');
        checked.type = 'checkbox';
        checked.name = 'stackapi_input_keep';
        checked.value = 'kept';
        checked.checked = true;

        const result = stackshared.processNodes({}, [unchecked, checked]);

        expect(result).toEqual({keep: 'kept'});
    });

    test('replaceFeedbackTags swaps all feedback placeholders with named divs', () => {
        const output = stackshared.replaceFeedbackTags('A [[feedback:prt1]] and [[feedback:prt_2]] B');

        expect(output).toContain("<div name='stackapi_fb_prt1'></div>");
        expect(output).toContain("<div name='stackapi_fb_prt_2'></div>");
        expect(output).not.toContain('[[feedback:');
    });

    test('loading toggles button/nav/spinner state using jQuery hooks', () => {
        stackshared.loading(true);
        expect(global.$).toHaveBeenCalledWith('.main-content .btn-primary');
        expect(global.$).toHaveBeenCalledWith('[id$="stackapi-nav"]');
        expect(global.$).toHaveBeenCalledWith('#stackapi_spinner');
        expect(jqueryChain.prop).toHaveBeenCalledWith('disabled', true);
        expect(jqueryChain.addClass).toHaveBeenCalledWith('link-disabled');
        expect(jqueryChain.show).toHaveBeenCalled();

        stackshared.loading(false);
        expect(jqueryChain.prop).toHaveBeenCalledWith('disabled', false);
        expect(jqueryChain.removeClass).toHaveBeenCalledWith('link-disabled');
        expect(jqueryChain.hide).toHaveBeenCalled();
    });

    test('createIframes injects base href and delegates to create_iframe', () => {
        const frames = [
            ['stack-iframe-0', '<html><head></head><body>One</body></html>', 'holder1', 'title1'],
            ['stack-iframe-1', '<html><head></head><body>Two</body></html>', 'holder2', 'title2'],
        ];

        stackshared.createIframes(frames);

        expect(global.create_iframe).toHaveBeenCalledTimes(2);
        expect(global.create_iframe.mock.calls[0][1]).toContain('<base href="/" />');
        expect(global.create_iframe.mock.calls[1][1]).toContain('<base href="/" />');
    });

    test('send posts to render endpoint and removes answers from payload', () => {
        stackshared.send();

        const xhr = MockXMLHttpRequest.instances[0];
        expect(xhr.method).toBe('POST');
        expect(xhr.url).toBe('/render');
        expect(xhr.headers['Content-Type']).toBe('application/json');
        expect(xhr.headers['Accept-Language']).toBe('en');
        expect(JSON.parse(xhr.body)).toEqual({questionDefinition: 'QDEF', seed: 42});

        xhr.respondWith(JSON.stringify({message: 'Render failed'}));
        expect(document.getElementById('errors').innerText).toBe('Render failed');
    });

    test('send handles successful render response and updates output sections', () => {
        const setTimeoutSpy = jest.spyOn(window, 'setTimeout').mockReturnValue(1234);
        const selectorSpy = allowBrokenIframeHolderSelector();

        stackshared.send();

        const xhr = MockXMLHttpRequest.instances[0];
        xhr.respondWith(JSON.stringify({
            questionrender: '<div>[[input:alpha]][[validation:alpha]][[feedback:prt1]] PLOT_Q</div>',
            questioninputs: {
                alpha: {
                    render: '<input id="rendered-alpha" name="stackapi_input_alpha" />',
                    samplesolutionrender: 'x+1',
                    samplesolution: {
                        alpha: 'line 1\nline 2',
                        alpha_val: 'ignore',
                        scratch: '[[{"used":1}]',
                    },
                    configuration: {options: {}},
                },
            },
            questionassets: {PLOT_Q: 'plot-q.png'},
            questionsamplesolutiontext: 'General [[feedback:prt2]] PLOT_Q',
            questionnote: 'Note PLOT_Q',
            iframes: [['iframe-id', '<html><head></head><body></body></html>', 'holder', 'title']],
        }));

        expect(document.getElementById('errors').innerText).toBe('');
        expect(document.getElementById('output').innerHTML).toContain('name="stackapi_val_alpha"');
        expect(document.getElementById('output').innerHTML).toContain("name=\"stackapi_fb_prt1\"");
        expect(document.getElementById('output').innerHTML).toContain('/plot.php/plot-q.png');
        expect(document.getElementById('stackapi_qtext').style.display).toBe('block');
        expect(document.getElementById('stackapi_correct').style.display).toBe('block');
        expect(document.getElementById('needs-input').style.display).toBe('inline-block');
        expect(document.getElementById('generalfeedback').innerHTML).toContain('name="stackapi_fb_prt2"');
        expect(document.getElementById('stackapi_generalfeedback').style.display).toBe('block');
        expect(document.getElementById('questionnote').innerHTML).toContain('/plot.php/plot-q.png');
        expect(document.getElementById('stackapi_questionnote').style.display).toBe('block');
        expect(document.getElementById('formatcorrectresponse').innerHTML).toContain('\\[{x+1}\\]');
        expect(document.getElementById('formatcorrectresponse').innerHTML).toContain('line 1<br>line 2');
        expect(document.getElementById('stack-iframe-holder-0_old')).not.toBeNull();
        expect(global.create_iframe).toHaveBeenCalledTimes(1);
        expect(global.MathJax.typesetPromise).toHaveBeenCalled();

        const renderedInput = document.getElementById('rendered-alpha');
        renderedInput.oninput({target: renderedInput});
        expect(setTimeoutSpy).toHaveBeenCalled();
        expect(setTimeoutSpy.mock.calls[0][1]).toBe(1000);

        selectorSpy.mockRestore();
        setTimeoutSpy.mockRestore();
    });

    test('send shows generic error when render response is invalid JSON', () => {
        stackshared.send();

        const xhr = MockXMLHttpRequest.instances[0];
        xhr.respondWith('not-json');

        expect(document.getElementById('errors').innerText)
            .toBe('There was an error attempting to display the request. Please try again or reload the page.');
    });

    test('validate sends trimmed input name and reports API message', () => {
        const input = document.getElementById('in-text');
        stackshared.validate(input);

        const xhr = MockXMLHttpRequest.instances[0];
        expect(xhr.url).toBe('/validate');
        expect(JSON.parse(xhr.body)).toEqual({
            questionDefinition: 'QDEF',
            answers: {alpha: 'x+1'},
            seed: 42,
            inputName: 'alpha',
        });

        xhr.respondWith(JSON.stringify({message: 'Invalid input'}));
        expect(document.getElementById('errors').innerText).toBe('Invalid input');
    });

    test('validate applies returned validation HTML and creates iframes', () => {
        const selectorSpy = allowBrokenIframeHolderSelector();
        const target = document.getElementsByName('stackapi_val_alpha')[0];
        target.classList.add('validation');

        stackshared.validate(document.getElementById('in-text'));

        const xhr = MockXMLHttpRequest.instances[0];
        xhr.respondWith(JSON.stringify({
            validation: '<span>OK</span>',
            iframes: [['vframe', '<html><head></head><body></body></html>', 'holder', 'title']],
        }));

        expect(target.innerHTML).toBe('<span>OK</span>');
        expect(target.classList.contains('validation')).toBe(true);
        expect(global.create_iframe).toHaveBeenCalledTimes(1);
        expect(global.MathJax.typesetPromise).toHaveBeenCalled();

        xhr.respondWith(JSON.stringify({validation: '', iframes: []}));
        expect(target.classList.contains('validation')).toBe(false);
        selectorSpy.mockRestore();
    });

    test('validate shows generic error when validation response cannot be parsed', () => {
        stackshared.validate(document.getElementById('in-text'));

        const xhr = MockXMLHttpRequest.instances[0];
        xhr.respondWith('{');

        expect(document.getElementById('errors').innerText)
            .toBe('There was an error attempting to display the request. Please try again or reload the page.');
    });

    test('answer exits before send when no rendered output exists', () => {
        document.getElementById('output').innerText = '';

        stackshared.answer();

        const xhr = MockXMLHttpRequest.instances[0];
        expect(xhr.url).toBe('/grade');
        expect(xhr.body).toBeUndefined();
    });

    test('answer sets validity warning when response is not gradable', () => {
        stackshared.answer();

        const xhr = MockXMLHttpRequest.instances[0];
        expect(JSON.parse(xhr.body)).toEqual({
            questionDefinition: 'QDEF',
            answers: {alpha: 'x+1'},
            seed: 42,
        });

        xhr.respondWith(JSON.stringify({message: '', isgradable: false}));

        expect(document.getElementById('stackapi_validity').innerText).toBe(' Please supply additional valid answers.');
        expect(document.getElementById('errors').innerText).toBe('');
    });

    test('answer renders grading results, specific feedback, and per-prt marks', () => {
        const selectorSpy = allowBrokenIframeHolderSelector();
        document.getElementById('specificfeedback').classList.add('feedback');
        document.getElementById('specificfeedback').innerHTML = 'old specific';
        document.getElementById('response_summary').innerText = 'old summary';
        document.getElementById('stackapi_summary').style.display = 'block';
        document.getElementById('stackapi_score').style.display = 'block';

        stackshared.answer();

        expect(document.getElementById('specificfeedback').innerHTML).toBe('');
        expect(document.getElementById('specificfeedback').classList.contains('feedback')).toBe(false);
        expect(document.getElementById('response_summary').innerText).toBe('');
        expect(document.getElementById('stackapi_summary').style.display).toBe('none');
        expect(document.getElementById('stackapi_score').style.display).toBe('none');
        expect(document.getElementsByName('stackapi_fb_prt1')[0].innerHTML).toBe('');

        const xhr = MockXMLHttpRequest.instances[0];
        xhr.respondWith(JSON.stringify({
            isgradable: true,
            score: 0.5,
            scoreweights: {total: 4, prt1: 0.25},
            responsesummary: 'summary text',
            prts: {
                prt1: 'Good work PLOT_G',
                prt2: '',
            },
            specificfeedback: 'Specific [[feedback:prt1]] PLOT_G',
            gradingassets: {PLOT_G: 'grading.png'},
            scores: {prt1: 0.8},
            iframes: [['gframe', '<html><head></head><body></body></html>', 'holder', 'title']],
        }));

        expect(document.getElementById('errors').innerText).toBe('');
        expect(document.getElementById('score').innerText).toBe('2.00 out of 4');
        expect(document.getElementById('stackapi_score').style.display).toBe('block');
        expect(document.getElementById('response_summary').innerText).toBe('summary text');
        expect(document.getElementById('stackapi_summary').style.display).toBe('block');
        expect(document.getElementById('specificfeedback').innerHTML).toContain('name="stackapi_fb_prt1"');
        expect(document.getElementById('specificfeedback').innerHTML).toContain('/plot.php/grading.png');
        expect(document.getElementById('specificfeedback').classList.contains('feedback')).toBe(true);
        expect(document.getElementsByName('stackapi_fb_prt1')[0].innerHTML).toContain('/plot.php/grading.png');
        expect(document.getElementsByName('stackapi_fb_prt1')[0].innerHTML).toContain('Marks for this submission:');
        expect(document.getElementsByName('stackapi_fb_prt1')[0].innerHTML).toContain('0.80');
        expect(document.getElementsByName('stackapi_fb_prt1')[0].innerHTML).toContain('1.00');
        expect(document.getElementsByName('stackapi_fb_prt2')[0].classList.contains('feedback')).toBe(false);
        expect(global.create_iframe).toHaveBeenCalledTimes(1);
        expect(global.MathJax.typesetPromise).toHaveBeenCalled();
        selectorSpy.mockRestore();
    });

    test('answer displays API message and parse failures', () => {
        stackshared.answer();
        let xhr = MockXMLHttpRequest.instances[0];
        xhr.respondWith(JSON.stringify({message: 'Cannot grade'}));
        expect(document.getElementById('errors').innerText).toBe('Cannot grade');

        stackshared.answer();
        xhr = MockXMLHttpRequest.instances[1];
        xhr.respondWith('not json');
        expect(document.getElementById('errors').innerText)
            .toBe('There was an error attempting to display the request. Please try again or reload the page.');
    });

    test('download requests file, rewires link to blob URL, and triggers click', () => {
        const link = document.getElementById('download-link');
        link.click = jest.fn();
        const nativeQuerySelectorAll = document.querySelectorAll.bind(document);
        const querySelectorAllSpy = jest.spyOn(document, 'querySelectorAll').mockImplementation((selector) => {
            if (selector.startsWith('a[href^=')) {
                return [link];
            }
            return nativeQuerySelectorAll(selector);
        });

        stackshared.download('report.txt', 7);

        const xhr = MockXMLHttpRequest.instances[0];
        expect(xhr.url).toBe('/download');
        expect(JSON.parse(xhr.body)).toEqual({
            questionDefinition: 'QDEF',
            seed: 42,
            filename: 'report.txt',
            fileid: 7,
        });

        xhr.respondWith('binary-content', {filename: 'report.txt', fileid: 7});

        expect(global.URL.createObjectURL).toHaveBeenCalled();
        expect(link.getAttribute('href')).toBe('blob:mocked');
        expect(link.getAttribute('download')).toBe('report.txt');
        expect(link.click).toHaveBeenCalledTimes(1);
        querySelectorAllSpy.mockRestore();
    });

    test('download writes response text to errors when link rewrite fails', () => {
        const querySelectorAllSpy = jest.spyOn(document, 'querySelectorAll').mockImplementation(() => []);

        stackshared.download('report.txt', 7);
        const xhr = MockXMLHttpRequest.instances[0];
        xhr.respondWith('download failed', {filename: 'report.txt', fileid: 7});

        expect(document.getElementById('errors').innerText).toBe('download failed');
        querySelectorAllSpy.mockRestore();
    });

    test('diff posts only questionDefinition and renders returned diff', () => {
        stackshared.diff();

        const xhr = MockXMLHttpRequest.instances[0];
        expect(xhr.url).toBe('/diff');
        expect(JSON.parse(xhr.body)).toEqual({questionDefinition: 'QDEF'});

        xhr.respondWith(JSON.stringify({diff: 'Line 1\nLine 2'}));

        expect(document.getElementById('stackapi_difference').style.display).toBe('block');
        expect(document.getElementById('difference').innerText).toBe('Line 1\nLine 2');
        expect(document.getElementById('errors').innerText).toBe('');
    });

    test('diff handles returned error message and invalid JSON response', () => {
        stackshared.diff();
        let xhr = MockXMLHttpRequest.instances[0];
        xhr.respondWith(JSON.stringify({message: 'No diff'}));
        expect(document.getElementById('errors').innerText).toBe('No diff');

        stackshared.diff();
        xhr = MockXMLHttpRequest.instances[1];
        xhr.respondWith('{');
        expect(document.getElementById('errors').innerText)
            .toBe('There was an error attempting to display the request. Please try again or reload the page.');
    });
});
