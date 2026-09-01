/** @jest-environment jsdom */

const fs = require('fs');
const path = require('path');

function loadPrivateDemoModule() {
    const modulePath = path.resolve(__dirname, '../../api/private-demo/public/private-demo.js');
    const source = fs.readFileSync(modulePath, 'utf8');
    const readyHandlers = [];
    const wrapped = new Function('readyHandlers', `
        const $ = () => ({ready: (handler) => readyHandlers.push(handler)});
        ${source}
        return {
            readyHandlers,
            renderQuestionList,
            goToPage,
            setQuestions(value) {
                questions = value;
            },
            getQuestions() {
                return questions;
            },
            getPage() {
                return page;
            },
        };
    `);
    return wrapped(readyHandlers);
}

function setupDom() {
    document.body.innerHTML = `
        <input id="question-search" />
        <select id="question-list"></select>
        <div id="question-count"></div>
        <iframe id="question-frame"></iframe>
        <div id="errors"></div>
    `;
}

function question(overrides) {
    return {
        questionId: 'q_default',
        name: 'Default question',
        filename: 'default.xml',
        category: 'Default',
        ...overrides,
    };
}

async function flushPromises() {
    for (let i = 0; i < 5; i++) {
        await Promise.resolve();
    }
}

describe('api/private-demo/public/private-demo.js', () => {
    let privateDemo;

    beforeEach(() => {
        jest.restoreAllMocks();
        setupDom();
        privateDemo = loadPrivateDemoModule();
    });

    test('renderQuestionList filters by name and filename case-insensitively', () => {
        privateDemo.setQuestions([
            question({questionId: 'q_alpha', name: 'Algebra warmup', filename: 'algebra.xml', category: 'A'}),
            question({questionId: 'q_beta', name: 'Calculus', filename: 'derivative-demo.xml', category: 'B'}),
            question({questionId: 'q_gamma', name: 'Geometry', filename: 'triangles.xml', category: 'C'}),
        ]);
        document.getElementById('question-search').value = 'DERIVATIVE';

        privateDemo.renderQuestionList();

        const options = [...document.querySelectorAll('#question-list option')];
        expect(options).toHaveLength(1);
        expect(options[0].value).toBe('1');
        expect(options[0].text).toBe('Calculus - derivative-demo.xml');
        expect(options[0].title).toBe('B');
        expect(document.getElementById('question-count').innerText).toBe('1 matching questions');
    });

    test('renderQuestionList limits long catalogues to the visible maximum', () => {
        privateDemo.setQuestions(Array.from({length: 101}, (_, index) => question({
            questionId: `q_${index}`,
            name: `Question ${index}`,
            filename: `question-${index}.xml`,
        })));

        privateDemo.renderQuestionList();

        expect(document.querySelectorAll('#question-list option')).toHaveLength(100);
        expect(document.getElementById('question-count').innerText)
            .toBe('Showing 100 of 101 matching questions');
    });

    test('goToPage selects the list value and URL-encodes the question id', () => {
        privateDemo.setQuestions([
            question({questionId: 'q_alpha'}),
            question({questionId: 'q value/with spaces'}),
        ]);
        privateDemo.renderQuestionList();

        privateDemo.goToPage(1);

        expect(privateDemo.getPage()).toBe(1);
        expect(document.getElementById('question-list').value).toBe('1');
        expect(document.getElementById('question-frame').getAttribute('src'))
            .toBe('/embed?questionId=q%20value%2Fwith%20spaces');
    });

    test('ready handler fetches catalogue, wires controls, renders, and opens the first question', async () => {
        const catalogue = [
            question({questionId: 'q_first', name: 'First', filename: 'first.xml'}),
            question({questionId: 'q_second', name: 'Second', filename: 'second.xml'}),
        ];
        global.fetch = jest.fn(() => Promise.resolve({
            json: () => Promise.resolve(catalogue),
        }));

        privateDemo.readyHandlers[0]();
        await flushPromises();

        expect(global.fetch).toHaveBeenCalledWith('demo/questions');
        expect(privateDemo.getQuestions()).toEqual(catalogue);
        expect(document.querySelectorAll('#question-list option')).toHaveLength(2);
        expect(document.getElementById('question-frame').getAttribute('src'))
            .toBe('/embed?questionId=q_first');

        document.getElementById('question-list').value = '1';
        document.getElementById('question-list').dispatchEvent(new Event('change'));
        expect(document.getElementById('question-frame').getAttribute('src'))
            .toBe('/embed?questionId=q_second');
    });

    test('ready handler displays a loading error when the catalogue request fails', async () => {
        global.fetch = jest.fn(() => Promise.reject(new Error('network')));

        privateDemo.readyHandlers[0]();
        await flushPromises();

        expect(document.getElementById('errors').innerText)
            .toBe('There was an error loading the question list.');
    });
});
