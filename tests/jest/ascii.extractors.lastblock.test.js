import lastblock from '../../corsscripts/ascii/extractors/lastblock.js';

describe('lastblock extractor', () => {

    // ── Block-mode tests ──────────────────────────────────────────────────────

    describe('with blocks', () => {
        test('returns raw of a single code_inline block', () => {
            const blocks = [{ type: 'code_inline', raw: 'x^2' }];
            expect(lastblock('', blocks)).toBe('x^2');
        });

        test('returns raw of a single asciimath_block', () => {
            const blocks = [{ type: 'asciimath_block', raw: 'x + 1\ny = 2' }];
            expect(lastblock('', blocks)).toBe('x + 1\ny = 2');
        });

        test('returns raw of the last code_inline when multiple blocks exist', () => {
            const blocks = [
                { type: 'code_inline', raw: 'first' },
                { type: 'code_inline', raw: 'last' }
            ];
            expect(lastblock('', blocks)).toBe('last');
        });

        test('returns raw of the last asciimath_block when it is the last relevant block', () => {
            const blocks = [
                { type: 'code_inline', raw: 'first' },
                { type: 'asciimath_block', raw: 'second block' }
            ];
            expect(lastblock('', blocks)).toBe('second block');
        });

        test('scans bottom-up: last code_inline after an asciimath_block wins', () => {
            const blocks = [
                { type: 'asciimath_block', raw: 'math block' },
                { type: 'code_inline', raw: 'inline after' }
            ];
            expect(lastblock('', blocks)).toBe('inline after');
        });

        test('ignores blocks that are not code_inline or asciimath_block', () => {
            const blocks = [
                { type: 'paragraph', raw: 'ignored' },
                { type: 'heading', raw: 'also ignored' },
            ];
            expect(lastblock('', blocks)).toBe('ERROR');
        });

        test('mixes eligible and non-eligible blocks, returns last eligible', () => {
            const blocks = [
                { type: 'code_inline', raw: 'inline' },
                { type: 'paragraph', raw: 'para' }
            ];
            expect(lastblock('', blocks)).toBe('inline');
        });
    });

    // ── Raw-fallback tests ────────────────────────────────────────────────────

    describe('without blocks (raw fallback)', () => {
        test('returns last non-empty line of raw', () => {
            expect(lastblock('line one\nline two', null)).toBe('line two');
        });

        test('skips trailing empty lines in raw', () => {
            expect(lastblock('line one\nline two\n\n', null)).toBe('line two');
        });

        test('returns the only non-empty line', () => {
            expect(lastblock('\n\n hello world \n', null)).toBe(' hello world ');
        });

        test('returns line as-is (untrimmed) from raw', () => {
            expect(lastblock('  trimmed  ', null)).toBe('  trimmed  ');
        });

        test('returns ERROR when raw is all empty lines', () => {
            expect(lastblock('\n\n\n', null)).toBe('ERROR');
        });

        test('returns ERROR for empty raw string', () => {
            expect(lastblock('', null)).toBe('ERROR');
        });

        test('handles windows-style line endings in raw', () => {
            expect(lastblock('first\r\nsecond', null)).toBe('second');
        });

        test('falls back to raw when blocks is an empty array', () => {
            expect(lastblock('fallback line', [])).toBe('fallback line');
        });
    });
});
