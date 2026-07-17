import lastregexremainder from '../../corsscripts/ascii/extractors/lastregexremainder.js';

describe('lastregexremainder extractor', () => {
    test('returns empty string when regex consumes whole matching line', () => {
        const raw = 'abc\n42\n99';
        expect(lastregexremainder(raw, null, { regex: '^\\d+$' })).toBe('');
    });

    test('returns ERROR when there is no match', () => {
        const operation = { regex: '^f\\(x\\)\\s*=\\s*' };
        expect(lastregexremainder('a = 1\nb = 2', null, operation)).toBe('ERROR');
    });

    test('returns suffix of the last matching line', () => {
        const raw = 'f(x) = first\nother\n f(x) = last ';
        const operation = { regex: '^f\\(x\\)\\s*=\\s*' };
        expect(lastregexremainder(raw, null, operation)).toBe('last');
    });

    test('returns basic match', () => {
        const raw = ' f(x) = x^2';
        const operation = { regex: '^f\\(x\\)\\s*=\\s*' };
        expect(lastregexremainder(raw, null, operation)).toBe('x^2');
    });

    test('returns basic match no whitespace', () => {
        const raw = ' f(x)=x^2';
        const operation = { regex: '^f\\(x\\)\\s*=\\s*' };
        expect(lastregexremainder(raw, null, operation)).toBe('x^2');
    });

    test('returns basic match backticks', () => {
        const raw = ' f(x)= `x^2`  ';
        const operation = { regex: '^f\\(x\\)\\s*=\\s*' };
        expect(lastregexremainder(raw, null, operation)).toBe('`x^2`');
    });
});