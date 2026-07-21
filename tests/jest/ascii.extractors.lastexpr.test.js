import lastexpr from '../../corsscripts/ascii/extractors/lastexpr.js';
import { setExtractorStrings } from '../../corsscripts/ascii/extractors/extractorresult.js';

describe('lastexpr extractor', () => {
    beforeEach(() => {
        setExtractorStrings({
            asciistringextractorlastexprnotfound: 'No expression or non-empty line was found to extract.'
        });
    });

    // ── Block-mode tests ──────────────────────────────────────────────────────

    describe('with blocks', () => {
        test('returns trimmed raw of a single code_inline block', () => {
            const blocks = [{ type: 'code_inline', raw: 'x^2' }];
            expect(lastexpr('', blocks)).toEqual({ result: 'x^2' });
        });

        test('trims whitespace from code_inline raw', () => {
            const blocks = [{ type: 'code_inline', raw: '  x^2  ' }];
            expect(lastexpr('', blocks)).toEqual({ result: 'x^2' });
        });

        test('returns last non-empty line of an asciimath_block', () => {
            const blocks = [{ type: 'asciimath_block', raw: 'first line\nsecond line' }];
            expect(lastexpr('', blocks)).toEqual({ result: 'second line' });
        });

        test('skips empty trailing lines in an asciimath_block', () => {
            const blocks = [{ type: 'asciimath_block', raw: 'only line\n\n' }];
            expect(lastexpr('', blocks)).toEqual({ result: 'only line' });
        });

        test('scans asciimath_block lines bottom-up to find last non-empty line', () => {
            const blocks = [{
                type: 'asciimath_block',
                raw: 'line1\nline2\nline3\n   '
            }];
            expect(lastexpr('', blocks)).toEqual({ result: 'line3' });
        });

        test('returns last code_inline over an earlier asciimath_block', () => {
            const blocks = [
                { type: 'asciimath_block', raw: 'math line' },
                { type: 'code_inline', raw: 'inline last' }
            ];
            expect(lastexpr('', blocks)).toEqual({ result: 'inline last' });
        });

        test('falls back to asciimath_block when last block is not eligible', () => {
            const blocks = [
                { type: 'asciimath_block', raw: 'math content' },
                { type: 'paragraph', raw: 'not eligible' }
            ];
            expect(lastexpr('', blocks)).toEqual({ result: 'math content' });
        });

        test('scans bottom-up: last code_inline wins when multiple exist', () => {
            const blocks = [
                { type: 'code_inline', raw: 'first' },
                { type: 'code_inline', raw: 'last' }
            ];
            expect(lastexpr('', blocks)).toEqual({ result: 'last' });
        });

        test('ignores blocks that are not code_inline or asciimath_block', () => {
            const blocks = [
                { type: 'heading', raw: 'ignored' },
                { type: 'code_inline', raw: 'first' },
                { type: 'calculation', raw: 'also ignored' }
            ];
            expect(lastexpr('', blocks)).toEqual({ result: 'first' });
        });

        test('returns translated error when no eligible block is found', () => {
            const blocks = [{ type: 'paragraph', raw: 'nothing' }];
            expect(lastexpr('', blocks)).toEqual({
                error: 'No expression or non-empty line was found to extract.'
            });
        });

        test('handles windows-style line endings in asciimath_block', () => {
            const blocks = [{ type: 'asciimath_block', raw: 'line one\r\nline two' }];
            expect(lastexpr('', blocks)).toEqual({ result: 'line two' });
        });
    });

    // ── Raw-fallback tests ────────────────────────────────────────────────────

    describe('without blocks (raw fallback)', () => {
        test('returns last non-empty line of raw', () => {
            expect(lastexpr('line one\nline two', null)).toEqual({ result: 'line two' });
        });

        test('skips trailing empty lines in raw', () => {
            expect(lastexpr('line one\nline two\n  \n', null)).toEqual({ result: 'line two' });
        });

        test('trims whitespace from matched raw line', () => {
            expect(lastexpr('  trimmed  ', null)).toEqual({ result: 'trimmed' });
        });

        test('returns translated error when all raw lines are empty', () => {
            expect(lastexpr('\n\n\n', null)).toEqual({
                error: 'No expression or non-empty line was found to extract.'
            });
        });

        test('returns translated error for empty raw with null blocks', () => {
            expect(lastexpr('', null)).toEqual({
                error: 'No expression or non-empty line was found to extract.'
            });
        });

        test('handles windows-style line endings in raw fallback', () => {
            expect(lastexpr('first\r\nsecond', null)).toEqual({ result: 'second' });
        });

        test('falls back to raw when blocks is an empty array', () => {
            expect(lastexpr('fallback', [])).toEqual({ result: 'fallback' });
        });
    });
});
