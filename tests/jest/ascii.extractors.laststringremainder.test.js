import laststringremainder from '../../corsscripts/ascii/extractors/laststringremainder.js';
import { setAsciiStrings } from '../../corsscripts/ascii/asciihelper.js';
import { stackStrings, stackStringWithDetail } from './ascii.teststrings.js';

const strings = stackStrings([
    'asciistringextractorsearchrequired',
    'asciistringextractorsearchnotfound'
]);

describe('laststringremainder extractor', () => {
    beforeEach(() => {
        setAsciiStrings(strings);
    });

    describe('guard clauses', () => {
        test('returns translated error when operation is undefined', () => {
            expect(laststringremainder('any raw', null, undefined)).toEqual({
                error: strings.asciistringextractorsearchrequired
            });
        });

        test('returns translated error when operation is null', () => {
            expect(laststringremainder('any raw', null, null)).toEqual({
                error: strings.asciistringextractorsearchrequired
            });
        });

        test('returns translated error when operation.search is missing', () => {
            expect(laststringremainder('any raw', null, { type: 'laststringremainder' })).toEqual({
                error: stackStringWithDetail('asciistringextractorsearchrequired', 'laststringremainder')
            });
        });

        test('returns translated error when operation.search is empty', () => {
            expect(laststringremainder('any raw', null, { type: 'laststringremainder', search: '' })).toEqual({
                error: stackStringWithDetail('asciistringextractorsearchrequired', 'laststringremainder')
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
                error: stackStringWithDetail('asciistringextractorsearchnotfound', operation.search)
            });
        });
    });
});
