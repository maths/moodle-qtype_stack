import aligneq from '../../corsscripts/ascii/markdownittransforms/200_aligneq.js';

describe('aligneq transform', () => {
    test('returns empty array for empty input', () => {
        expect(aligneq([], 'code_inline')).toEqual([]);
    });

    test('ignores code inlint and does not wrap', () => {
        expect(aligneq(['x = y'], 'code_inline')).toEqual([
            'x = y',
        ]);
    });

    test('returns empty align environment for empty input', () => {
        expect(aligneq([], 'asciimath_block')).toEqual([
            '\\begin{align*}',
            '\\end{align*}'
        ]);
    });

    test('wraps a simple relation line into align blocks', () => {
        expect(aligneq(['x = y'], 'asciimath_block')).toEqual([
            '\\begin{align*}',
            '& & x  & = y\\\\',
            '\\end{align*}'
        ]);
    });

    test('places leading implication text in the first column', () => {
        expect(aligneq(['\\Rightarrow x = y'], 'asciimath_block')).toEqual([
            '\\begin{align*}',
            '\\Rightarrow & &  x  & = y\\\\',
            '\\end{align*}'
        ]);
    });

    test('moves \text content into the fourth column when not excluded', () => {
        expect(aligneq(['a = b \\text{then} c'], 'asciimath_block')).toEqual([
            '\\begin{align*}',
            '& & a  & = b  & & \\text{then} c\\\\',
            '\\end{align*}'
        ]);
    });

    test('does not treat excluded \text tokens as a split point', () => {
        expect(aligneq(['a = b \\text{or} c'], 'asciimath_block')).toEqual([
            '\\begin{align*}',
            '& & a  & = b \\text{or} c\\\\',
            '\\end{align*}'
        ]);
    });

    test('adds an ampersand when no relation token is found', () => {
        expect(aligneq(['plain content'], 'asciimath_block')).toEqual([
            '\\begin{align*}',
            '& & plain content &\\\\',
            '\\end{align*}'
        ]);
    });

    test('handles brace-delimited relation tokens only at top level', () => {
        expect(aligneq(['a = {b > c}'], 'asciimath_block')).toEqual([
            '\\begin{align*}',
            '& & a  & = {b > c}\\\\',
            '\\end{align*}'
        ]);
    });

    test('handles relation token at index 0', () => {
        expect(aligneq(['= y'], 'asciimath_block')).toEqual([
            '\\begin{align*}',
            '& & & = y\\\\',
            '\\end{align*}'
        ]);
    });

    test('splits at the first top-level relation token when multiple exist', () => {
        expect(aligneq(['a > b = c'], 'asciimath_block')).toEqual([
            '\\begin{align*}',
            '& & a  & > b = c\\\\',
            '\\end{align*}'
        ]);
    });

    test('ignores excluded text token and splits at a later non-excluded text token', () => {
        expect(aligneq(['a = b \\text{or} c \\text{then} d'], 'asciimath_block')).toEqual([
            '\\begin{align*}',
            '& & a  & = b \\text{or} c  & & \\text{then} d\\\\',
            '\\end{align*}'
        ]);
    });

    test('ignores relation tokens inside \\lbrace...\\rbrace and splits at top-level relation', () => {
        expect(aligneq(['a \\lbrace b = c \\rbrace = d'], 'asciimath_block')).toEqual([
            '\\begin{align*}',
            '& & a \\lbrace b = c \\rbrace  & = d\\\\',
            '\\end{align*}'
        ]);
    });

    test('handles implication token with brace form', () => {
        expect(aligneq(['\\Rightarrow{x = y}'], 'asciimath_block')).toEqual([
            '\\begin{align*}',
            '\\Rightarrow & & {x = y} & \\\\',
            '\\end{align*}'
        ]);
    });

    test('handles complex multiline content in a single transform call', () => {
        expect(aligneq([
            '\\Rightarrow x = y',
            'a = {b > c} \\text{then} d',
            'plain content',
            '\\because p \\subseteq q'
        ], 'asciimath_block')).toEqual([
            '\\begin{align*}',
            '\\Rightarrow & &  x  & = y\\\\',
            '& & a  & = {b > c}  & & \\text{then} d\\\\',
            '& & plain content &\\\\',
            '\\because & &  p  & \\subseteq q\\\\',
            '\\end{align*}'
        ]);
    });

    test('returns lines unchanged when any line contains a skip environment', () => {
        const input = ['a = b', '\\begin{align} x & = y \\end{align}'];
        expect(aligneq(input, 'asciimath_block')).toEqual(input);
    });

    test('returns lines unchanged for starred skip environments', () => {
        const input = ['\\begin{equation*} x = y \\end{equation*}'];
        expect(aligneq(input, 'asciimath_block')).toEqual(input);
    });

    test('skips wrapping for every environment in the nonest list and its starred variant', () => {
        const envs = [
            'align', 'flalign', 'alignat', 'xalignat', 'xxalignat',
            'gather', 'multline', 'equation', 'split', 'subequations'
        ];
        for (const env of envs) {
            const plain = [`\\begin{${env}} a \\end{${env}}`];
            const starred = [`\\begin{${env}*} a \\end{${env}*}`];
            expect(aligneq(plain, 'asciimath_block')).toEqual(plain);
            expect(aligneq(starred, 'asciimath_block')).toEqual(starred);
        }
    });
});