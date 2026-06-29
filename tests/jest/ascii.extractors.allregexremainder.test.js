import allregexremainder from '../../corsscripts/ascii/extractors/allregexremainder.js';

describe('allregexremainder extractor', () => {
    test('returns matched lines with the regex prefix removed', () => {
        const raw = 'f(x) = x\ny = 3\nf(x) = x^2';
        const operation = { regex: '^f\\(x\\)\\s*=\\s*' };
        const result = allregexremainder(raw, null, operation);
        expect(result).toBe(JSON.stringify({ matches: ['x', 'x^2'] }));
    });

    test('returns empty strings when regex consumes whole matching lines', () => {
        const raw = '42\nabc\n99';
        const operation = { regex: '^\\d+$' };
        const result = allregexremainder(raw, null, operation);
        expect(result).toBe(JSON.stringify({ matches: ['', ''] }));
    });

    test('returns ERROR when no lines match', () => {
        const operation = { regex: '^f\\(x\\)\\s*=\\s*' };
        expect(allregexremainder('y = x\na = 1', null, operation)).toBe('ERROR');
    });
});