import cas from '../../corsscripts/ascii/filters/cas.js';

describe('cas filter', () => {
    test('evaluates text between {@ and @}', () => {
        expect(cas('The answer is {@2^2 + 1@} here')).toBe('The answer is 5 here');
    });

    test('does not transform old @...@ syntax', () => {
        expect(cas('A: @2+3@, B: @4+5@')).toBe('A: @2+3@, B: @4+5@');
    });

    test('falls back to raw content when evaluation fails', () => {
        expect(cas('A: {@2+@}, B: {@3*3@}')).toBe('A: 2+, B: 9');
    });

    test('falls back to raw content when evaluation returns a function', () => {
        expect(cas('A: {@derivative@}, B: {@3*3@}')).toBe('A: derivative, B: 9');
    });

    test('evaluates calculus between {@ and @}', () => {
        expect(cas('The derivative is {@derivative("sin(2*x^3)", "x")@}.')).toBe('The derivative is 6 * x ^ 2 * cos(2 * x ^ 3).');
    });

    test('populates blockCollector with cas blocks', () => {
        const collector = { blocks: [] };
        cas('A: {@2+3@}, B: {@2^3@}', collector);
        expect(collector.blocks).toEqual([
            { type: 'calculation', raw: '2+3', rendered: '5' },
            { type: 'calculation', raw: '2^3', rendered: '8' }
        ]);
    });

    test('clears blockCollector.blocks if provided', () => {
        const collector = { blocks: [{ type: 'old', raw: 'z' }] };
        cas('A: {@7-4@}', collector);
        expect(collector.blocks).toEqual([
            { type: 'calculation', raw: '7-4', rendered: '3' }
        ]);
    });

    test('populates blockCollector with errors from failed evaluations', () => {
        const collector = { blocks: [] };
        cas('A: {@2+@}, B: {@3*3@}', collector);
        expect(collector.blocks).toHaveLength(2);
        expect(collector.blocks[0]).toMatchObject({
            type: 'calculation',
            raw: '2+',
            rendered: '2+',
            errormsg: expect.any(String)
        });
        expect(collector.blocks[1]).toEqual({
            type: 'calculation',
            raw: '3*3',
            rendered: '9'
        });
    });

    test('populates blockCollector with raw content for function-valued results', () => {
        const collector = { blocks: [] };
        cas('A: {@derivative@}', collector);
        expect(collector.blocks).toEqual([
            { type: 'calculation', raw: 'derivative', rendered: 'derivative' }
        ]);
    });

    test('manages a node type that calculation filter rejects', () => {
        expect(cas('A: {@x = 2@}, B: {@3*3@}')).toBe('A: 2, B: 9');
    });

    test('manages an operator that calculation filter rejects', () => {
        expect(cas('A: {@2 > 1@}, B: {@3*3@}')).toBe('A: true, B: 9');
    });
});
