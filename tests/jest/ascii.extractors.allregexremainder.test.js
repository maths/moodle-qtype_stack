import allregexremainder from '../../corsscripts/ascii/extractors/allregexremainder.js';
import { setExtractorStrings } from '../../corsscripts/ascii/extractors/extractorresult.js';

describe('allregexremainder extractor', () => {
    beforeEach(() => {
        setExtractorStrings({
            asciistringextractorregexnotfound: 'No line matched the requested regular expression.'
        });
    });

    test('returns matched lines with the regex prefix removed', () => {
        const raw = 'f(x) = x\ny = 3\nf(x) = x^2';
        const operation = { regex: '^f\\(x\\)\\s*=\\s*' };
        const result = allregexremainder(raw, null, operation);
        expect(result).toEqual({
            result: JSON.stringify({ matches: ['x', 'x^2'] })
        });
    });

    test('returns empty strings when regex consumes whole matching lines', () => {
        const raw = '42\nabc\n99';
        const operation = { regex: '^\\d+$' };
        const result = allregexremainder(raw, null, operation);
        expect(result).toEqual({
            result: JSON.stringify({ matches: ['', ''] })
        });
    });

    test('returns translated error when no lines match', () => {
        const operation = { regex: '^f\\(x\\)\\s*=\\s*' };
        expect(allregexremainder('y = x\na = 1', null, operation)).toEqual({
            error: 'No line matched the requested regular expression.'
        });
    });
});
