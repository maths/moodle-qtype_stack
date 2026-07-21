import lastcalc from '../../corsscripts/ascii/extractors/lastcalc.js';
import { setExtractorStrings } from '../../corsscripts/ascii/extractors/extractorhelper.js';

describe('lastcalc extractor', () => {
    beforeEach(() => {
        setExtractorStrings({
            asciistringextractorlastcalcnotfound: 'No calculation block was found to extract.'
        });
    });

    describe('with blocks', () => {
        test('returns trimmed content of a single calculation block', () => {
            const blocks = [{ type: 'calculation', rendered: '1 + 1' }];
            expect(lastcalc('', blocks)).toEqual({ result: '1 + 1' });
        });

        test('trims whitespace from the calculation block rendered', () => {
            const blocks = [{ type: 'calculation', rendered: '  x^2  ' }];
            expect(lastcalc('', blocks)).toEqual({ result: 'x^2' });
        });

        test('returns trimmed content of the last calculation block when multiple exist', () => {
            const blocks = [
                { type: 'calculation', rendered: 'first calc' },
                { type: 'calculation', rendered: 'last calc' }
            ];
            expect(lastcalc('', blocks)).toEqual({ result: 'last calc' });
        });

        test('scans bottom-up: last calculation block wins over earlier ones', () => {
            const blocks = [
                { type: 'code_inline', rendered: 'irrelevant' },
                { type: 'calculation', rendered: 'calc one' },
                { type: 'code_inline', rendered: 'also irrelevant' },
                { type: 'calculation', rendered: 'calc two' }
            ];
            expect(lastcalc('', blocks)).toEqual({ result: 'calc two' });
        });

        test('ignores non-calculation blocks', () => {
            const blocks = [
                { type: 'code_inline', rendered: 'not a calc' },
                { type: 'calculation', rendered: 'calc one' },
                { type: 'asciimath_block', rendered: 'also not a calc' }
            ];
            expect(lastcalc('', blocks)).toEqual({ result: 'calc one' });
        });

        test('returns translated error when blocks array contains no calculation blocks', () => {
            const blocks = [{ type: 'paragraph', rendered: 'some text' }];
            expect(lastcalc('', blocks)).toEqual({
                error: 'No calculation block was found to extract.'
            });
        });

        test('returns translated error for an empty blocks array', () => {
            expect(lastcalc('', [])).toEqual({
                error: 'No calculation block was found to extract.'
            });
        });
    });

    describe('without blocks', () => {
        test('returns translated error when blocks is null', () => {
            expect(lastcalc('anything', null)).toEqual({
                error: 'No calculation block was found to extract.'
            });
        });

        test('returns translated error when blocks is undefined', () => {
            expect(lastcalc('anything', undefined)).toEqual({
                error: 'No calculation block was found to extract.'
            });
        });
    });
});
