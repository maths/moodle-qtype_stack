import lastcalc from '../../corsscripts/ascii/extractors/lastcalc.js';

describe('lastcalc extractor', () => {

    describe('with blocks', () => {
        test('returns trimmed content of a single calculation block', () => {
            const blocks = [{ type: 'calculation', rendered: '1 + 1' }];
            expect(lastcalc('', blocks)).toBe('1 + 1');
        });

        test('trims whitespace from the calculation block rendered', () => {
            const blocks = [{ type: 'calculation', rendered: '  x^2  ' }];
            expect(lastcalc('', blocks)).toBe('x^2');
        });

        test('returns trimmed content of the last calculation block when multiple exist', () => {
            const blocks = [
                { type: 'calculation', rendered: 'first calc' },
                { type: 'calculation', rendered: 'last calc' }
            ];
            expect(lastcalc('', blocks)).toBe('last calc');
        });

        test('scans bottom-up: last calculation block wins over earlier ones', () => {
            const blocks = [
                { type: 'code_inline', rendered: 'irrelevant' },
                { type: 'calculation', rendered: 'calc one' },
                { type: 'code_inline', rendered: 'also irrelevant' },
                { type: 'calculation', rendered: 'calc two' }
            ];
            expect(lastcalc('', blocks)).toBe('calc two');
        });

        test('ignores non-calculation blocks', () => {
            const blocks = [
                { type: 'code_inline', rendered: 'not a calc' },
                { type: 'calculation', rendered: 'calc one' },
                { type: 'asciimath_block', rendered: 'also not a calc' }
            ];
            expect(lastcalc('', blocks)).toBe('calc one');
        });

        test('returns ERROR when blocks array contains no calculation blocks', () => {
            const blocks = [{ type: 'paragraph', rendered: 'some text' }];
            expect(lastcalc('', blocks)).toBe('ERROR');
        });

        test('returns ERROR for an empty blocks array', () => {
            expect(lastcalc('', [])).toBe('ERROR');
        });
    });

    describe('without blocks', () => {
        test('returns ERROR when blocks is null', () => {
            expect(lastcalc('anything', null)).toBe('ERROR');
        });

        test('returns ERROR when blocks is undefined', () => {
            expect(lastcalc('anything', undefined)).toBe('ERROR');
        });
    });
});
