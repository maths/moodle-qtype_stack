import allregexremainder from '../../corsscripts/ascii/extractors/allregexremainder.js';
import { setAsciiStrings } from '../../corsscripts/ascii/asciihelper.js';
import { stackStrings, stackStringWithDetail } from './ascii.teststrings.js';

const strings = stackStrings([
    'asciistringextractorregexrequired',
    'asciistringextractorregexnotfound'
]);

describe('allregexremainder extractor', () => {
    beforeEach(() => {
        setAsciiStrings(strings);
    });

    describe('guard clauses', () => {
        test('returns translated error when operation is undefined', () => {
            expect(allregexremainder('any raw', null, undefined)).toEqual({
                error: strings.asciistringextractorregexrequired
            });
        });

        test('returns translated error when operation is null', () => {
            expect(allregexremainder('any raw', null, null)).toEqual({
                error: strings.asciistringextractorregexrequired
            });
        });

        test('returns translated error when operation.regex is missing', () => {
            expect(allregexremainder('any raw', null, { type: 'allregexremainder' })).toEqual({
                error: stackStringWithDetail('asciistringextractorregexrequired', 'allregexremainder')
            });
        });

        test('returns translated error when operation.regex is empty', () => {
            expect(allregexremainder('any raw', null, { type: 'allregexremainder', regex: '' })).toEqual({
                error: stackStringWithDetail('asciistringextractorregexrequired', 'allregexremainder')
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
            error: stackStringWithDetail('asciistringextractorregexnotfound', operation.regex)
        });
    });
});
