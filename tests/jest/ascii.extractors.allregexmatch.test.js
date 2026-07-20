import allregexmatch from '../../corsscripts/ascii/extractors/allregexmatch.js';

describe('allregexmatch extractor', () => {
    describe('guard clauses', () => {
        test('returns ERROR when operation is undefined', () => {
            expect(allregexmatch('any raw', null, undefined)).toBe('ERROR');
        });

        test('returns ERROR when operation is null', () => {
            expect(allregexmatch('any raw', null, null)).toBe('ERROR');
        });

        test('returns ERROR when operation.regex is missing', () => {
            expect(allregexmatch('any raw', null, {})).toBe('ERROR');
        });

        test('returns ERROR when operation.regex is empty', () => {
            expect(allregexmatch('any raw', null, { regex: '' })).toBe('ERROR');
        });
    });

    describe('matching lines', () => {
        test('returns all matching lines as JSON', () => {
            const raw = 'f(x) = x\ny = 3\nf(x) = x^2';
            const operation = { regex: '^f\\(x\\)\\s*=\\s*' };
            const result = allregexmatch(raw, null, operation);
            expect(result).toBe(JSON.stringify({ matches: ['f(x) = x', 'f(x) = x^2'] }));
        });

        test('trims lines before matching and keeps order', () => {
            const raw = '  f(x) = a  \n\n  f(x) = b';
            const operation = { regex: '^f\\(x\\)\\s*=\\s*' };
            const result = allregexmatch(raw, null, operation);
            expect(result).toBe(JSON.stringify({ matches: ['f(x) = a', 'f(x) = b'] }));
        });

        test('returns ERROR when no lines match', () => {
            const operation = { regex: '^f\\(x\\)\\s*=\\s*' };
            expect(allregexmatch('y = x\na = 1', null, operation)).toBe('ERROR');
        });
    });
});
