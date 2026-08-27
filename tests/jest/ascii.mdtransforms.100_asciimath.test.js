import asciimath from '../../corsscripts/ascii/markdownittransforms/100_asciimath.js';

describe('asciimath transform', () => {
    beforeEach(() => {
        global.window = {
            AMparseMath: jest.fn((content) => content)
        };
    });

    afterEach(() => {
        delete global.window;
    });

        test('leaves latex lines unchanged', () => {
        const lines = ['\\[\\begin{align*}', 'x', '\\end{align*}\\]'];
        expect(asciimath(lines, 'asciimath_block')).toEqual([
            '\\[\\begin{align*}',
            'x',
            '\\end{align*}\\]'
        ]);
    });

    test('non-trivial example', () => {
        const lines = ['oo'];
        expect(asciimath(lines, 'asciimath_block')).toEqual([
            'oo',
        ]);
    });
});