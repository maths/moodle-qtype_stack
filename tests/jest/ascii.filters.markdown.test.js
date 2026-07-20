import markdown from '../../corsscripts/ascii/filters/markdown.js';

describe('markdown filter', () => {
    beforeEach(() => {
        global.window = {
            AMparseMath: jest.fn((content) => content)
        };
    });

    afterEach(() => {
        delete global.window;
    });

    test('renders simple markdown to HTML', () => {
        const html = markdown('**bold**', null, {});
        expect(html).toContain('<strong>bold</strong>');
    });

    test('applies transforms from op.transforms', () => {
        // aligneq and boldfilter are in the transformLib, but we just check no error is thrown.
        const html = markdown('`f(x) = x^2`', null, { transforms: 'aligneq,boldfilter' });
        expect(typeof html).toBe('string');
    });

    test('populates blockCollector if provided', () => {
        const collector = { blocks: [] };
        markdown('`x`\n`y`\nhdkfhds', collector, {});
        expect(Array.isArray(collector.blocks)).toBe(true);
        expect(collector.blocks.length).toBe(2);
    });

    test('handles empty transforms', () => {
        const html = markdown('plain', null, { transforms: '' });
        expect(typeof html).toBe('string');
    });

    test('updates shared state.transforms and state.collector per call', () => {
        jest.resetModules();

        let capturedState = null;
        jest.doMock('../../corsscripts/ascii/filters/markdownitrules.js', () => ({
            __esModule: true,
            default: jest.fn((mdit, options) => {
                capturedState = options.state;
            })
        }));

        const markdownModule = require('../../corsscripts/ascii/filters/markdown.js');
        const isolatedMarkdown = markdownModule.default || markdownModule;

        const collector = { blocks: [] };
        isolatedMarkdown('plain', collector, { transforms: 'aligneq, boldfilter' });

        expect(capturedState).not.toBeNull();
        expect(capturedState.transforms).toEqual(['aligneq', 'boldfilter']);
        expect(capturedState.collector).toBe(collector);
    });

    test('resets shared state to empty transforms and null collector', () => {
        jest.resetModules();

        let capturedState = null;
        jest.doMock('../../corsscripts/ascii/filters/markdownitrules.js', () => ({
            __esModule: true,
            default: jest.fn((mdit, options) => {
                capturedState = options.state;
            })
        }));

        const markdownModule = require('../../corsscripts/ascii/filters/markdown.js');
        const isolatedMarkdown = markdownModule.default || markdownModule;

        const collector = { blocks: [] };

        isolatedMarkdown('plain', collector, { transforms: 'aligneq' });
        expect(capturedState).not.toBeNull();
        expect(capturedState.transforms).toEqual(['aligneq']);
        expect(capturedState.collector).toBe(collector);

        isolatedMarkdown('plain', null, { transforms: '' });
        expect(capturedState).not.toBeNull();
        expect(capturedState.transforms).toEqual([]);
        expect(capturedState.collector).toBeNull();
    });
});
