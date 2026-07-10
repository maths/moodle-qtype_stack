import laststringremainder from '../../corsscripts/ascii/extractors/laststringremainder.js';

const blockCollector = {blocks: [], delimiter: '`', closingdelimiter: '`'};

describe('laststringremainder extractor', () => {
    describe('guard clauses', () => {
        test('returns ERROR when operation is undefined', () => {
            expect(laststringremainder('any raw', blockCollector, undefined)).toBe('ERROR');
        });

        test('returns ERROR when operation.string is missing', () => {
            expect(laststringremainder('any raw', blockCollector, {})).toBe('ERROR');
        });
    });

    describe('matching behavior', () => {
        test('returns remainder after matched prefix on the last matching line', () => {
            const raw = 'Answer = first\nother\nAnswer = last';
            const operation = { string: 'Answer =' };
            expect(laststringremainder(raw, blockCollector, operation)).toBe('last');
        });

        test('supports optional backticks around the line', () => {
            const raw = '`Answer = value`';
            const operation = { string: 'Answer =' };
            expect(laststringremainder(raw, blockCollector, operation)).toBe('value');
        });

        test('supports optional backticks around thevalue', () => {
            const raw = 'Answer =  ` value ` ';
            const operation = { string: 'Answer =' };
            expect(laststringremainder(raw, blockCollector, operation)).toBe('value');
        });

        test('trims matching lines before processing', () => {
            const raw = '  Answer =  x^2   ';
            const operation = { string: 'Answer =' };
            expect(laststringremainder(raw, blockCollector, operation)).toBe('x^2');
        });

        test('supports asymmetric configured delimiters', () => {
            const raw = '<<Answer = value>>';
            const operation = { string: 'Answer =' };
            const collector = { blocks: [], delimiter: '<<', closingdelimiter: '>>' };
            expect(laststringremainder(raw, collector, operation)).toBe('value');
        });

        test('supports symmetric configured delimiters', () => {
            const raw = '##Answer = value##';
            const operation = { string: 'Answer =' };
            const collector = { blocks: [], delimiter: '##', closingdelimiter: '##' };
            expect(laststringremainder(raw, collector, operation)).toBe('value');
        });

        test('supports regex-special configured delimiters', () => {
            const raw = '[Answer = *x + 1*]';
            const operation = { string: 'Answer =' };
            const collector = { blocks: [], delimiter: '[', closingdelimiter: ']' };
            expect(laststringremainder(raw, collector, operation)).toBe('*x + 1*');
        });

        test('trims whitespace around asymmetric custom delimiters', () => {
            const raw = '  Answer =  <<  value + 1  >>   ';
            const operation = { string: 'Answer =' };
            const collector = { blocks: [], delimiter: '<<', closingdelimiter: '>>' };
            expect(laststringremainder(raw, collector, operation)).toBe('value + 1');
        });

        test('trims whitespace around symmetric custom delimiters', () => {
            const raw = '  Answer =  ##  value + 2  ##   ';
            const operation = { string: 'Answer =' };
            const collector = { blocks: [], delimiter: '##', closingdelimiter: '##' };
            expect(laststringremainder(raw, collector, operation)).toBe('value + 2');
        });

        test('trims custom delimiters when the whole line is wrapped', () => {
            const raw = '  <<  Answer =  value + 3  >>   ';
            const operation = { string: 'Answer =' };
            const collector = { blocks: [], delimiter: '<<', closingdelimiter: '>>' };
            expect(laststringremainder(raw, collector, operation)).toBe('value + 3');
        });

        test('falls back to opening delimiter when closing delimiter is empty', () => {
            const raw = '*Answer = value*';
            const operation = { string: 'Answer =' };
            const collector = { blocks: [], delimiter: '*', closingdelimiter: '' };
            expect(laststringremainder(raw, collector, operation)).toBe('value');
        });
    });

    describe('no-match behavior', () => {
        test('returns ERROR when no lines match', () => {
            const operation = { string: 'Answer =' };
            expect(laststringremainder('f(x) = x^2', blockCollector, operation)).toBe('ERROR');
        });
    });
});
