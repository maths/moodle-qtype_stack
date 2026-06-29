import lastblock from '../../corsscripts/ascii/extractors/lastblock.js';

describe('lastblock extractor', () => {

    // ── Block-mode tests ──────────────────────────────────────────────────────

    describe('with blocks', () => {
        test('returns raw of a single asciimath_inline block', () => {
            const blockCollector = { blocks: [{ type: 'asciimath_inline', raw: 'x^2' }] };
            expect(lastblock('', blockCollector)).toBe('x^2');
        });

        test('returns raw of a single asciimath_block', () => {
            const blockCollector = { blocks: [{ type: 'asciimath_block', raw: 'x + 1\ny = 2' }] };
            expect(lastblock('', blockCollector)).toBe('x + 1\ny = 2');
        });

        test('returns raw of the last asciimath_inline when multiple blocks exist', () => {
            const blockCollector = { blocks: [
                { type: 'asciimath_inline', raw: 'first' },
                { type: 'asciimath_inline', raw: 'last' }
            ]};
            expect(lastblock('', blockCollector)).toBe('last');
        });

        test('returns raw of the last asciimath_block when it is the last relevant block', () => {
            const blockCollector = { blocks: [
                { type: 'asciimath_inline', raw: 'first' },
                { type: 'asciimath_block', raw: 'second block' }
            ]};
            expect(lastblock('', blockCollector)).toBe('second block');
        });

        test('scans bottom-up: last asciimath_inline after an asciimath_block wins', () => {
            const blockCollector = { blocks: [
                { type: 'asciimath_block', raw: 'math block' },
                { type: 'asciimath_inline', raw: 'inline after' }
            ]};
            expect(lastblock('', blockCollector)).toBe('inline after');
        });

        test('ignores blocks that are not asciimath_inline or asciimath_block', () => {
            const blockCollector = { blocks: [
                { type: 'paragraph', raw: 'ignored' },
                { type: 'heading', raw: 'also ignored' },
            ]};
            expect(lastblock('', blockCollector)).toBe('ERROR');
        });

        test('mixes eligible and non-eligible blocks, returns last eligible', () => {
            const blockCollector = { blocks: [
                { type: 'asciimath_inline', raw: 'inline' },
                { type: 'paragraph', raw: 'para' }
            ]};
            expect(lastblock('', blockCollector)).toBe('inline');
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
