/** @jest-environment jsdom */

const path = require('path');
const {loadAmdModule} = require('./loadAmdModule');

function loadLibraryModule(ajaxMock, eventsMock) {
    const modulePath = path.resolve(__dirname, '../../amd/src/library.js');
    return loadAmdModule(modulePath, {
        'core/ajax': ajaxMock,
        'core_filters/events': eventsMock,
    });
}

function setupLibraryDom() {
    document.body.innerHTML = `
        <div id="dashboard-link-holder">/question/bank/editquestion/question.php?foo=bar</div>
        <div id="quiz-link-holder">/question/bank/quiz.php</div>

        <div class="stack-library-error" hidden></div>
        <div class="stack-library-error-details"></div>
        <div class="loading-display" hidden></div>

        <div class="stack_library_display"></div>
        <div class="stack_library_raw_display"></div>
        <div class="stack_library_variables_display"></div>
        <div class="stack_library_selected_question"></div>
        <div class="stack_library_description_display"></div>

        <div class="stack-library-imported-list"></div>
        <div class="stack-library-import-success" hidden></div>
        <div class="stack-library-import-failure" hidden></div>
        <div class="stack-library-import-success-file"></div>
        <div class="stack-library-import-failure-file"></div>

        <div class="library-secondary-info" hidden></div>
        <div class="stack-library-category-holder"></div>
        <div class="stack-library-course" hidden></div>

        <button class="library-import-link" disabled>Import</button>
        <button class="library-import-link-folder" disabled>Import folder</button>

        <a class="library-file-link" data-filepath="library/topic/question.json">Question</a>
        <a class="library-file-link" data-filepath="library/topic/sheet_quiz.json">Quiz</a>

        <div data-id="stack_library_course_id" data-value="27"></div>

        <select id="id_category">
            <option value="5,anything">Main category (11)</option>
            <option value="9,anything">Nested (3)</option>
        </select>
    `;
}

describe('amd/src/library.js', () => {
    beforeEach(() => {
        jest.useFakeTimers();
        jest.restoreAllMocks();
        setupLibraryDom();
    });

    afterEach(() => {
        jest.useRealTimers();
    });

    test('setup wires listeners and removes category question counts', () => {
        const ajaxMock = {call: jest.fn()};
        const eventsMock = {notifyFilterContentUpdated: jest.fn()};
        const module = loadLibraryModule(ajaxMock, eventsMock);

        module.setup();

        const options = Array.from(document.querySelectorAll('#id_category option')).map((option) => option.text);
        expect(options[0]).toBe('Main category');
        expect(options[1]).toBe('Nested');

        expect(document.querySelector('.loading-display').hasAttribute('hidden')).toBe(true);
        expect(document.querySelector('.library-file-link').hasAttribute('disabled')).toBe(false);
    });

    test('clicking a library file renders the selected question details', () => {
        const createIframeMock = jest.fn();
        global.require = jest.fn((deps, callback) => callback({create_iframe: createIframeMock}));

        const ajaxMock = {
            call: jest.fn((requests) => {
                const request = requests[0];
                expect(request.methodname).toBe('qtype_stack_library_render');
                setTimeout(() => {
                    expect(document.querySelector('.loading-display').hasAttribute('hidden')).toBe(false);
                    request.done({
                        questionrender: '<div class="rendered">Rendered question</div>',
                        iframes: [{
                            iframeid: 'frame1',
                            content: '<html></html>',
                            targetdivid: 'target1',
                            title: 'Frame title',
                            scrolling: true,
                            evil: false,
                        }],
                        questiontext: 'raw question text',
                        questiondescription: '<p>Description</p>',
                        questionvariables: 'a;b;c',
                        questionname: 'Question A',
                    });
                });
            }),
        };

        expect(document.querySelector('.loading-display').hasAttribute('hidden')).toBe(true);
        const eventsMock = {notifyFilterContentUpdated: jest.fn()};
        const module = loadLibraryModule(ajaxMock, eventsMock);

        module.setup();
        const quizFileLink = document.querySelectorAll('.library-file-link')[1];
        quizFileLink.dispatchEvent(new MouseEvent('click', {bubbles: true}));
        jest.runAllTimers();

        expect(document.querySelector('.stack_library_display').innerHTML).toContain('Rendered question');
        expect(document.querySelector('.stack_library_raw_display').innerText).toBe('raw question text');
        expect(document.querySelector('.stack_library_description_display').innerHTML).toContain('Description');
        expect(document.querySelector('.stack_library_variables_display').innerHTML).toContain('a;<br>b;<br>c');
        expect(document.querySelector('.stack_library_selected_question').innerHTML).toContain('sheet_quiz.json');
        expect(document.querySelector('.stack-library-category-holder').hasAttribute('hidden')).toBe(true);
        expect(document.querySelector('.stack-library-course').hasAttribute('hidden')).toBe(false);
        expect(document.querySelector('.loading-display').hasAttribute('hidden')).toBe(true);

        expect(eventsMock.notifyFilterContentUpdated).toHaveBeenCalledWith(document.querySelector('.stack_library_display'));
        expect(createIframeMock).toHaveBeenCalledTimes(1);
    });

    test('clicking import appends success and failure entries', () => {
        const ajaxMock = {
            call: jest.fn((requests) => {
                const request = requests[0];

                if (request.methodname === 'qtype_stack_library_render') {
                    request.done({
                        questionrender: '<div>Rendered</div>',
                        iframes: [],
                        questiontext: 'raw',
                        questiondescription: '<p>desc</p>',
                        questionvariables: 'x;y',
                        questionname: 'Rendered question',
                    });
                    return;
                }

                if (request.methodname === 'qtype_stack_library_import') {
                    request.done([
                        {
                            success: true,
                            questionid: 41,
                            questionname: 'STACK imported',
                            isstack: true,
                            filename: 'library/topic/question.json',
                        },
                        {
                            success: true,
                            questionid: 52,
                            questionname: 'Quiz imported',
                            isstack: false,
                            filename: 'library/topic/sheet_quiz.json',
                        },
                        {
                            success: false,
                            questionid: null,
                            questionname: 'Broken question',
                            isstack: false,
                            filename: 'library/topic/bad.json',
                        },
                    ]);
                }
            }),
        };
        const eventsMock = {notifyFilterContentUpdated: jest.fn()};
        const module = loadLibraryModule(ajaxMock, eventsMock);

        module.setup();
        document.querySelector('.library-file-link').dispatchEvent(new MouseEvent('click', {bubbles: true}));
        document.querySelector('.library-import-link').dispatchEvent(new MouseEvent('click', {bubbles: true}));

        const importListHtml = document.querySelector('.stack-library-imported-list').innerHTML;
        expect(importListHtml).toContain('STACK imported');
        expect(importListHtml).toContain('questionid=41');
        expect(importListHtml).toContain('/question/bank/quiz.php?id=52');

        expect(document.querySelector('.stack-library-import-success').hasAttribute('hidden')).toBe(false);
        expect(document.querySelector('.stack-library-import-failure').hasAttribute('hidden')).toBe(false);
        expect(document.querySelector('.stack-library-import-failure-file').innerHTML).toContain('bad.json');
    });
});
