import calculation from '../../corsscripts/ascii/filters/calculation.js';

describe('calculation filter', () => {
    test('evaluates text between {@ and @}', () => {
        expect(calculation('The answer is {@2^2 + 1@} here')).toBe('The answer is 5 here');
    });

    test('handles multiple {@...@} on one line as separate blocks', () => {
        expect(calculation('A: {@2+3@}, B: {@10/2@}')).toBe('A: 5, B: 5');
    });

    test('falls back to raw content when evaluation fails', () => {
        expect(calculation('A: {@2+@}, B: {@3*3@}')).toBe('A: 2+, B: 9');
    });

    test('falls back to raw content when validate blocks a function in calculation', () => {
        expect(calculation('A: {@derivative("x^2", "x")@}, B: {@3*3@}')).toBe('A: derivative("x^2", "x"), B: 9');
    });

    test('falls back to raw content when validate blocks an operator in calculation', () => {
        expect(calculation('A: {@2 > 1@}, B: {@3*3@}')).toBe('A: 2 > 1, B: 9');
    });

    test('falls back to raw content when validate blocks a node type in calculation', () => {
        expect(calculation('A: {@x = 2@}, B: {@3*3@}')).toBe('A: x = 2, B: 9');
    });

    test('does not throw for malformed content ending at a single @}', () => {
        expect(calculation('A: {@2+3, B: {@4+1@}')).toBe('A: 2+3, B: {@4+1');
    });

    test('requires opening brace and closing brace around markers', () => {
        expect(calculation('A: @2+3@ and {@3*3@} and @4-1@')).toBe('A: @2+3@ and 9 and @4-1@');
    });

    test('does not match across newlines', () => {
        expect(calculation('A: {@x\n@y@}')).toBe('A: {@x\n@y@}');
    });

    test('returns input unchanged if no {@...@} present', () => {
        expect(calculation('No special markers')).toBe('No special markers');
    });

    test('does not transform old @...@ syntax', () => {
        expect(calculation('A: @2+3@, B: @4+5@')).toBe('A: @2+3@, B: @4+5@');
    });

    test('populates blockCollector with calculation blocks', () => {
        const collector = { blocks: [] };
        calculation('A: {@2+3@}, B: {@2^3@}', collector);
        expect(collector.blocks).toEqual([
            { type: 'calculation', raw: '2+3', rendered: '5' },
            { type: 'calculation', raw: '2^3', rendered: '8' }
        ]);
    });

    test('clears blockCollector.blocks if provided', () => {
        const collector = { blocks: [{ type: 'old', raw: 'z' }] };
        calculation('A: {@7-4@}', collector);
        expect(collector.blocks).toEqual([
            { type: 'calculation', raw: '7-4', rendered: '3' }
        ]);
    });
});
