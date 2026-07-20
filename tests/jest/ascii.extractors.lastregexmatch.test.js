import lastregexmatch from '../../corsscripts/ascii/extractors/lastregexmatch.js';

describe('lastregexmatch extractor', () => {
    describe('guard clauses', () => {
        test('returns ERROR when operation is undefined', () => {
            expect(lastregexmatch('any raw', [], undefined)).toBe('ERROR');
        });

        test('returns ERROR when operation is null', () => {
            expect(lastregexmatch('any raw', [], null)).toBe('ERROR');
        });

        test('returns ERROR when operation.regex is missing', () => {
            expect(lastregexmatch('any raw', [], {})).toBe('ERROR');
        });
    });

    test('returns full last matching line from raw', () => {
        const raw = 'f(x) = first\nother\nf(x) = last';
        const operation = { regex: '^f\\(x\\)\\s*=\\s*' };
        expect(lastregexmatch(raw, null, operation)).toBe('f(x) = last');
    });

    test('trims lines before matching', () => {
        const raw = '  f(x) = expr  ';
        const operation = { regex: '^f\\(x\\)\\s*=\\s*' };
        expect(lastregexmatch(raw, null, operation)).toBe('f(x) = expr');
    });

    test('returns ERROR when there is no match', () => {
        const operation = { regex: '^f\\(x\\)\\s*=\\s*' };
        expect(lastregexmatch('a = 1\nb = 2', null, operation)).toBe('ERROR');
    });
});
