import lastexpr from '../../corsscripts/ascii/extractors/lastexpr.js';

describe('lastexpr extractor', () => {

    // ── Block-mode tests ──────────────────────────────────────────────────────

    describe('with blocks', () => {
        test('returns trimmed raw of a single code_inline block', () => {
            const blocks = [{ type: 'code_inline', raw: 'x^2' }];
            expect(lastexpr('', blocks)).toBe('x^2');
        });

        test('trims whitespace from code_inline raw', () => {
            const blocks = [{ type: 'code_inline', raw: '  x^2  ' }];
            expect(lastexpr('', blocks)).toBe('x^2');
        });

        test('returns last non-empty line of an asciimath_block', () => {
            const blocks = [{ type: 'asciimath_block', raw: 'first line\nsecond line' }];
            expect(lastexpr('', blocks)).toBe('second line');
        });

        test('skips empty trailing lines in an asciimath_block', () => {
            const blocks = [{ type: 'asciimath_block', raw: 'only line\n\n' }];
            expect(lastexpr('', blocks)).toBe('only line');
        });

        test('scans asciimath_block lines bottom-up to find last non-empty line', () => {
            const blocks = [{
                type: 'asciimath_block',
                raw: 'line1\nline2\nline3\n   '
            }];
            expect(lastexpr('', blocks)).toBe('line3');
        });

        test('returns last code_inline over an earlier asciimath_block', () => {
            const blocks = [
                { type: 'asciimath_block', raw: 'math line' },
                { type: 'code_inline', raw: 'inline last' }
            ];
            expect(lastexpr('', blocks)).toBe('inline last');
        });

        test('falls back to asciimath_block when last block is not eligible', () => {
            const blocks = [
                { type: 'asciimath_block', raw: 'math content' },
                { type: 'paragraph', raw: 'not eligible' }
            ];
            expect(lastexpr('', blocks)).toBe('math content');
        });

        test('scans bottom-up: last code_inline wins when multiple exist', () => {
            const blocks = [
                { type: 'code_inline', raw: 'first' },
                { type: 'code_inline', raw: 'last' }
            ];
            expect(lastexpr('', blocks)).toBe('last');
        });

        test('ignores blocks that are not code_inline or asciimath_block', () => {
            const blocks = [
                { type: 'heading', raw: 'ignored' },
                { type: 'code_inline', raw: 'first' },
                { type: 'calculation', raw: 'also ignored' }
            ];
            expect(lastexpr('', blocks)).toBe('first');
        });

        test('returns ERROR when no eligible block is found', () => {
            const blocks = [{ type: 'paragraph', raw: 'nothing' }];
            expect(lastexpr('', blocks)).toBe('ERROR');
        });

        test('handles windows-style line endings in asciimath_block', () => {
            const blocks = [{ type: 'asciimath_block', raw: 'line one\r\nline two' }];
            expect(lastexpr('', blocks)).toBe('line two');
        });
    });

    // ── Raw-fallback tests ────────────────────────────────────────────────────

    describe('without blocks (raw fallback)', () => {
        test('returns last non-empty line of raw', () => {
            expect(lastexpr('line one\nline two', null)).toBe('line two');
        });

        test('skips trailing empty lines in raw', () => {
            expect(lastexpr('line one\nline two\n  \n', null)).toBe('line two');
        });

        test('trims whitespace from matched raw line', () => {
            expect(lastexpr('  trimmed  ', null)).toBe('trimmed');
        });

        test('returns ERROR when all raw lines are empty', () => {
            expect(lastexpr('\n\n\n', null)).toBe('ERROR');
        });

        test('returns ERROR for empty raw with null blocks', () => {
            expect(lastexpr('', null)).toBe('ERROR');
        });

        test('handles windows-style line endings in raw fallback', () => {
            expect(lastexpr('first\r\nsecond', null)).toBe('second');
        });

        test('falls back to raw when blocks is an empty array', () => {
            expect(lastexpr('fallback', [])).toBe('fallback');
        });
    });
});
