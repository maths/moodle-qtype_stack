import laststringremainderwhitespace from '../../corsscripts/ascii/extractors/laststringremainderwhitespace.js';

describe('laststringremainderwhitespace extractor', () => {
    test('returns ERROR when there is no match', () => {
        const operation = { search: 'f(x) =' };
        expect(laststringremainderwhitespace('a = 1\nb = 2', null, operation)).toBe('ERROR');
    });

    test('returns suffix of the last matching line', () => {
        const raw = 'f(x) = first\nother\n f(x) = last ';
        const operation = { search: 'f(x) =' };
        expect(laststringremainderwhitespace(raw, null, operation)).toBe('last');
    });

    test('returns basic match', () => {
        const raw = ' f(x) = x^2';
        const operation = { search: 'f(x) =' };
        expect(laststringremainderwhitespace(raw, null, operation)).toBe('x^2');
    });

    test('returns basic match no whitespace', () => {
        const raw = ' f(x) =x^2';
        const operation = { search: 'f(x) =' };
        expect(laststringremainderwhitespace(raw, null, operation)).toBe('x^2');
    });

    test('returns basic match backticks', () => {
        const raw = ' f(x) = `x^2`  ';
        const operation = { search: 'f(x) =' };
        expect(laststringremainderwhitespace(raw, null, operation)).toBe('x^2');
    });

    test('returns basic match backticks internal whitespace', () => {
        const raw = ' f(x) = ` x^7 `  ';
        const operation = { search: 'f(x) =' };
        expect(laststringremainderwhitespace(raw, null, operation)).toBe('x^7');
    });

    test('returns basic match no whitespace', () => {
        const raw = 'f(x)=x^2';
        const operation = { search: 'f(x) =' };
        expect(laststringremainderwhitespace(raw, null, operation)).toBe('x^2');
    });

    test('returns external match backticks internal whitespace', () => {
        const raw = '`f(x)=x^2`';
        const operation = { search: 'f(x)=' };
        expect(laststringremainderwhitespace(raw, null, operation)).toBe('x^2');
    });

    test('returns external match backticks lots of whitespace', () => {
        const raw = '` f(x) =x^2  `';
        const operation = { search: 'f(x) =' };
        expect(laststringremainderwhitespace(raw, null, operation)).toBe('x^2');
    });

    test('returns external match backticks lots of whitespace, full stop', () => {
        const raw = '` f(x) =x^3+1  ` .  ';
        const operation = { search: 'f(x) =' };
        expect(laststringremainderwhitespace(raw, null, operation)).toBe('x^3+1');
    });

    test('Fail to match because of previous text', () => {
        const raw = 'hence `f(x)=x^2`.';
        const operation = { search: 'f(x) =' };
        expect(laststringremainderwhitespace(raw, null, operation)).toBe('ERROR');
    });

    test('returns basic match backticks internal whitespace', () => {
        const raw = ' f(x)= x^2  ';
        const operation = { search: 'f(x)=' };
        expect(laststringremainderwhitespace(raw, null, operation)).toBe('x^2');
    });

    test('returns basic match backticks internal whitespace, full stop', () => {
        const raw = ' f(x)= x^2.  ';
        const operation = { search: 'f(x)=' };
        expect(laststringremainderwhitespace(raw, null, operation)).toBe('x^2');
    });

    test('returns error because of internal whitespace in line but not search string', () => {
        const raw = ' f(x) = x^2  ';
        const operation = { search: 'f(x)=' };
        expect(laststringremainderwhitespace(raw, null, operation)).toBe('ERROR');
    });

    test('returns last match', () => {
        const raw = ' a=1\n a = 2';
        const operation = { search: 'a =' };
        expect(laststringremainderwhitespace(raw, null, operation)).toBe('2');
    });
});
