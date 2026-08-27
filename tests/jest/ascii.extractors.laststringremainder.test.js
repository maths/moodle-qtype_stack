import laststringremainder from '../../corsscripts/ascii/extractors/laststringremainder.js';

describe('laststringremainder extractor', () => {
    describe('guard clauses', () => {
        test('returns ERROR when operation is undefined', () => {
            expect(laststringremainder('any raw', null, undefined)).toBe('ERROR');
        });

        test('returns ERROR when operation.string is missing', () => {
            expect(laststringremainder('any raw', null, {})).toBe('ERROR');
        });
    });

    describe('matching behavior', () => {
        test('returns remainder after matched prefix on the last matching line', () => {
            const raw = 'Answer = first\nother\nAnswer = last';
            const operation = { string: 'Answer =' };
            expect(laststringremainder(raw, null, operation)).toBe('last');
        });

        test('supports optional backticks around the line', () => {
            const raw = '`Answer = value`';
            const operation = { string: 'Answer =' };
            expect(laststringremainder(raw, null, operation)).toBe('value');
        });

        test('supports optional backticks around thevalue', () => {
            const raw = 'Answer =  ` value ` ';
            const operation = { string: 'Answer =' };
            expect(laststringremainder(raw, null, operation)).toBe('value');
        });

        test('trims matching lines before processing', () => {
            const raw = '  Answer =  x^2   ';
            const operation = { string: 'Answer =' };
            expect(laststringremainder(raw, null, operation)).toBe('x^2');
        });
    });

    describe('no-match behavior', () => {
        test('returns ERROR when no lines match', () => {
            const operation = { string: 'Answer =' };
            expect(laststringremainder('f(x) = x^2', null, operation)).toBe('ERROR');
        });
    });
});
