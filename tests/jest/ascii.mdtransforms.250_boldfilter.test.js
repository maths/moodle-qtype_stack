import boldfilter from '../../corsscripts/ascii/markdownittransforms/250_boldfilter.js';

describe('boldfilter transform', () => {
    test('leaves wrapper lines unchanged', () => {
        const lines = ['\\[\\begin{align*}', 'x', '\\end{align*}\\]'];
        expect(boldfilter(lines)).toEqual([
            '\\[\\begin{align*}',
            '\\boldsymbol{x}',
            '\\end{align*}\\]'
        ]);
    });

    test('wraps trimmed content in boldsymbol', () => {
        const lines = ['\\[\\begin{align*}', '& & x = y & & z', '\\end{align*}\\]'];
        expect(boldfilter(lines)).toEqual([
            '\\[\\begin{align*}',
            '& &\\boldsymbol{x = y}& &\\boldsymbol{z}',
            '\\end{align*}\\]'
        ]);
    });

    test('preserves trailing row breaks outside boldsymbol', () => {
        const lines = ['\\[\\begin{align*}', '& & before & after\\\\', '\\end{align*}\\]'];
        expect(boldfilter(lines)).toEqual([
            '\\[\\begin{align*}',
            '& &\\boldsymbol{before}&\\boldsymbol{after}\\\\',
            '\\end{align*}\\]'
        ]);
    });

    test('keeps displaystyle outside boldsymbol', () => {
        const lines = ['\\[\\begin{align*}', '& & \\displaystyle x + 1', '\\end{align*}\\]'];
        expect(boldfilter(lines)).toEqual([
            '\\[\\begin{align*}',
            '& &\\displaystyle\\boldsymbol{x + 1}',
            '\\end{align*}\\]'
        ]);
    });

    test('leaves blank columns unchanged', () => {
        const lines = ['\\[\\begin{align*}', '& &   & & value', '\\end{align*}\\]'];
        expect(boldfilter(lines)).toEqual([
            '\\[\\begin{align*}',
            '& &   & &\\boldsymbol{value}',
            '\\end{align*}\\]'
        ]);
    });

    test('does not split ampersands inside brace groups', () => {
        const lines = ['\\[\\begin{align*}', '& & x = \\text{A & B}\\\\', '\\end{align*}\\]'];
        expect(boldfilter(lines)).toEqual([
            '\\[\\begin{align*}',
            '& &\\boldsymbol{x = \\text{A & B}}\\\\',
            '\\end{align*}\\]'
        ]);
    });

    test('does not split ampersands inside nested array environments', () => {
        const lines = ['\\[\\begin{align*}', '& & M & = \\begin{array}{cc}a&b\\\\c&d\\end{array}\\\\', '\\end{align*}\\]'];
        expect(boldfilter(lines)).toEqual([
            '\\[\\begin{align*}',
            '& &\\boldsymbol{M}&\\boldsymbol{= \\begin{array}{cc}a&b\\\\c&d\\end{array}}\\\\',
            '\\end{align*}\\]'
        ]);
    });
});
