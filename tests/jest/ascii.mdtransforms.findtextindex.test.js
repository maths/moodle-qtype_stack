import findtextindex from '../../corsscripts/ascii/markdownittransforms/findtextindex.js';

describe('findtextindex helper', () => {
    test('finds the first top-level needle', () => {
        expect(findtextindex('a = b', ['='])).toBe(2);
    });

    test('ignores needles inside braces', () => {
        expect(findtextindex('a {b = c} d', ['='])).toBe(false);
    });

    test('ignores needles inside braces, complex', () => {
        const testString = 'a {=b{=} {=[==][]=} c} [=] d=';
        expect(findtextindex(testString, ['='])).toBe(testString.length - 1);
    });

    test('ignores needles inside parentheses and brackets', () => {
        expect(findtextindex('a (b = c) [d = e]', ['='])).toBe(false);
    });

    test('tracks latex brace tokens', () => {
        expect(findtextindex('a \\lbrace b = c \\rbrace = d', ['='])).toBe(24);
    });

    test('respects the without list', () => {
        expect(findtextindex('a = b', ['='], ['='])).toBe(false);
    });

    test('respects the without list with multiple entries', () => {
        expect(findtextindex('a > b = c < d', ['=', '<', '>'], ['=', '>'])).toBe(10);
    });

    test('finds the first match among multiple needles', () => {
        expect(findtextindex('a > b = c', ['=', '>'])).toBe(2);
    });

    test('returns false when nothing matches', () => {
        expect(findtextindex('plain text', ['='])).toBe(false);
    });
});