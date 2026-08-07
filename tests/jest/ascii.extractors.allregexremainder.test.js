import allregexremainder from '../../corsscripts/ascii/extractors/allregexremainder.js';
import { setExtractorStrings } from '../../corsscripts/ascii/extractors/extractorhelper.js';

describe('allregexremainder extractor', () => {
    beforeEach(() => {
        setExtractorStrings({
            asciistringextractorregexrequired: 'This extractor requires a regular expression.',
            asciistringextractorregexnotfound: 'No line matched the requested regular expression.'
        });
    });

    describe('guard clauses', () => {
        test('returns translated error when operation is undefined', () => {
            expect(allregexremainder('any raw', null, undefined)).toEqual({
                error: 'This extractor requires a regular expression.'
            });
        });

        test('returns translated error when operation is null', () => {
            expect(allregexremainder('any raw', null, null)).toEqual({
                error: 'This extractor requires a regular expression.'
            });
        });

        test('returns translated error when operation.regex is missing', () => {
            expect(allregexremainder('any raw', null, { type: 'allregexremainder' })).toEqual({
                error: 'This extractor requires a regular expression. allregexremainder'
            });
        });

        test('returns translated error when operation.regex is empty', () => {
            expect(allregexremainder('any raw', null, { type: 'allregexremainder', regex: '' })).toEqual({
                error: 'This extractor requires a regular expression. allregexremainder'
            });
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
            error: 'No line matched the requested regular expression. ^f\\(x\\)\\s*=\\s*'
        });
    });
});
