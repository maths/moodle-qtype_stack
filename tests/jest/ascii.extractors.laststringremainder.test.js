import laststringremainder from '../../corsscripts/ascii/extractors/laststringremainder.js';
import { setExtractorStrings } from '../../corsscripts/ascii/extractors/extractorresult.js';

describe('laststringremainder extractor', () => {
    beforeEach(() => {
        setExtractorStrings({
            asciistringextractorsearchrequired: 'This extractor requires a search parameter.',
            asciistringextractorsearchnotfound: 'No line matched the requested search text.'
        });
    });

    describe('guard clauses', () => {
        test('returns translated error when operation is undefined', () => {
            expect(laststringremainder('any raw', null, undefined)).toEqual({
                error: 'This extractor requires a search parameter.'
            });
        });

        test('returns translated error when operation.string is missing', () => {
            expect(laststringremainder('any raw', null, {})).toEqual({
                error: 'This extractor requires a search parameter.'
            });
        });
    });

    describe('matching behavior', () => {
        test('returns remainder after matched prefix on the last matching line', () => {
            const raw = 'Answer = first\nother\nAnswer = last';
            const operation = { string: 'Answer =' };
            expect(laststringremainder(raw, null, operation)).toEqual({ result: 'last' });
        });

        test('supports optional backticks around the line', () => {
            const raw = '`Answer = value`';
            const operation = { string: 'Answer =' };
            expect(laststringremainder(raw, null, operation)).toEqual({ result: 'value' });
        });

        test('supports optional backticks around thevalue', () => {
            const raw = 'Answer =  ` value ` ';
            const operation = { string: 'Answer =' };
            expect(laststringremainder(raw, null, operation)).toEqual({ result: 'value' });
        });

        test('trims matching lines before processing', () => {
            const raw = '  Answer =  x^2   ';
            const operation = { string: 'Answer =' };
            expect(laststringremainder(raw, null, operation)).toEqual({ result: 'x^2' });
        });
    });

    describe('no-match behavior', () => {
        test('returns translated error when no lines match', () => {
            const operation = { string: 'Answer =' };
            expect(laststringremainder('f(x) = x^2', null, operation)).toEqual({
                error: 'No line matched the requested search text.'
            });
        });
    });
});
