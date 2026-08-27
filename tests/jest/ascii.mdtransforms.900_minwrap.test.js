import minwrap from '../../corsscripts/ascii/markdownittransforms/900_minwrap.js';

describe('minwrap transform', () => {
    test('returns empty string, rather than empty math environment, for empty input', () => {
        expect(minwrap([], 'code_inline')).toEqual([""]);
    });

    test('ignores code inlint and does not wrap', () => {
        expect(minwrap(['x = y'], 'code_inline')).toEqual([
            '\\(x = y\\)',
        ]);
    });

    test('simple wrap', () => {
        expect(minwrap(['x = y'], 'asciimath_block')).toEqual([
            '\\[' ,
            'x = y' ,
            '\\]',
        ]);
    });

    test('multiple wrap', () => {
        expect(minwrap(['x = y', 'a^2-1'], 'asciimath_block')).toEqual([
            '\\[' ,
            'x = y' ,
            'a^2-1' ,
            '\\]',
        ]);
    });

    test('multiple do not warp align* environments', () => {
        expect(minwrap(['\\begin{align*}', 'x & = y \\\\', 'a^2-1 & = 0 \\\\', '\\uend{align*}'], 'asciimath_block')).toEqual([
            '\\begin{align*}',
            'x & = y \\\\',
            'a^2-1 & = 0 \\\\',
            '\\uend{align*}'
        ]);
    });
});