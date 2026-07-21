import lastblock from '../../corsscripts/ascii/extractors/lastblock.js';
import { setExtractorStrings } from '../../corsscripts/ascii/extractors/extractorresult.js';

describe('lastblock extractor', () => {
    beforeEach(() => {
        setExtractorStrings({
            asciistringextractorlastblocknotfound: 'No AsciiMath expression or block was found to extract.'
        });
    });

    // ── Block-mode tests ──────────────────────────────────────────────────────

    describe('with blocks', () => {
        test('returns raw of a single code_inline block', () => {
            const blocks = [{ type: 'code_inline', raw: 'x^2' }];
            expect(lastblock('', blocks)).toEqual({ result: 'x^2' });
        });

        test('returns raw of a single asciimath_block', () => {
            const blocks = [{ type: 'asciimath_block', raw: 'x + 1\ny = 2' }];
            expect(lastblock('', blocks)).toEqual({ result: 'x + 1\ny = 2' });
        });

        test('returns raw of the last code_inline when multiple blocks exist', () => {
            const blocks = [
                { type: 'code_inline', raw: 'first' },
                { type: 'code_inline', raw: 'last' }
            ];
            expect(lastblock('', blocks)).toEqual({ result: 'last' });
        });

        test('returns raw of the last asciimath_block when it is the last relevant block', () => {
            const blocks = [
                { type: 'code_inline', raw: 'first' },
                { type: 'asciimath_block', raw: 'second block' }
            ];
            expect(lastblock('', blocks)).toEqual({ result: 'second block' });
        });

        test('scans bottom-up: last code_inline after an asciimath_block wins', () => {
            const blocks = [
                { type: 'asciimath_block', raw: 'math block' },
                { type: 'code_inline', raw: 'inline after' }
            ];
            expect(lastblock('', blocks)).toEqual({ result: 'inline after' });
        });

        test('ignores blocks that are not code_inline or asciimath_block', () => {
            const blocks = [
                { type: 'paragraph', raw: 'ignored' },
                { type: 'heading', raw: 'also ignored' },
            ];
            expect(lastblock('', blocks)).toEqual({
                error: 'No AsciiMath expression or block was found to extract.'
            });
        });

        test('mixes eligible and non-eligible blocks, returns last eligible', () => {
            const blocks = [
                { type: 'code_inline', raw: 'inline' },
                { type: 'paragraph', raw: 'para' }
            ];
            expect(lastblock('', blocks)).toEqual({ result: 'inline' });
        });
    });

    // ── Raw-fallback tests ────────────────────────────────────────────────────

    describe('without blocks (raw fallback)', () => {
        test('returns last non-empty line of raw', () => {
            expect(lastblock('line one\nline two', null)).toEqual({ result: 'line two' });
        });

        test('skips trailing empty lines in raw', () => {
            expect(lastblock('line one\nline two\n\n', null)).toEqual({ result: 'line two' });
        });

        test('returns the only non-empty line', () => {
            expect(lastblock('\n\n hello world \n', null)).toEqual({ result: ' hello world ' });
        });

        test('returns line as-is (untrimmed) from raw', () => {
            expect(lastblock('  trimmed  ', null)).toEqual({ result: '  trimmed  ' });
        });

        test('returns translated error when raw is all empty lines', () => {
            expect(lastblock('\n\n\n', null)).toEqual({
                error: 'No AsciiMath expression or block was found to extract.'
            });
        });

        test('returns translated error for empty raw string', () => {
            expect(lastblock('', null)).toEqual({
                error: 'No AsciiMath expression or block was found to extract.'
            });
        });

        test('handles windows-style line endings in raw', () => {
            expect(lastblock('first\r\nsecond', null)).toEqual({ result: 'second' });
        });

        test('falls back to raw when blocks is an empty array', () => {
            expect(lastblock('fallback line', [])).toEqual({ result: 'fallback line' });
        });
    });
});
