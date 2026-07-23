import laststringremainder from '../../corsscripts/ascii/extractors/laststringremainder.js';
import { setExtractorStrings } from '../../corsscripts/ascii/extractors/extractorhelper.js';

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

        test('returns translated error when operation is null', () => {
            expect(laststringremainder('any raw', null, null)).toEqual({
                error: 'This extractor requires a search parameter.'
            });
        });

        test('returns translated error when operation.search is missing', () => {
            expect(laststringremainder('any raw', null, { type: 'laststringremainder' })).toEqual({
                error: 'This extractor requires a search parameter. laststringremainder'
            });
        });

        test('returns translated error when operation.search is empty', () => {
            expect(laststringremainder('any raw', null, { type: 'laststringremainder', search: '' })).toEqual({
                error: 'This extractor requires a search parameter. laststringremainder'
            });
        });
    });

    describe('matching behavior', () => {
        test('returns remainder after matched prefix on the last matching line', () => {
            const raw = 'Answer = first\nother\nAnswer = last';
            const operation = { search: 'Answer =' };
            expect(laststringremainder(raw, null, operation)).toEqual({ result: 'last' });
        });

        test('supports optional backticks around the line', () => {
            const raw = '`Answer = value`';
            const operation = { search: 'Answer =' };
            expect(laststringremainder(raw, null, operation)).toEqual({ result: 'value' });
        });

        test('supports optional backticks around thevalue', () => {
            const raw = 'Answer =  ` value ` ';
            const operation = { search: 'Answer =' };
            expect(laststringremainder(raw, null, operation)).toEqual({ result: 'value' });
        });

        test('trims matching lines before processing', () => {
            const raw = '  Answer =  x^2   ';
            const operation = { search: 'Answer =' };
            expect(laststringremainder(raw, null, operation)).toEqual({ result: 'x^2' });
        });

        test('supports legacy string option when search is not present', () => {
            const raw = 'Answer = first\nother\nAnswer = last';
            const operation = { string: 'Answer =' };
            expect(laststringremainder(raw, null, operation)).toEqual({ result: 'last' });
        });

        test('prefers search over legacy string when both are present', () => {
            const raw = 'String = legacy\nSearch = current';
            const operation = { search: 'Search =', string: 'String =' };
            expect(laststringremainder(raw, null, operation)).toEqual({ result: 'current' });
        });
    });

    describe('no-match behavior', () => {
        test('returns translated error when no lines match', () => {
            const operation = { search: 'Answer =' };
            expect(laststringremainder('f(x) = x^2', null, operation)).toEqual({
                error: 'No line matched the requested search text. Answer ='
            });
        });
    });
});
