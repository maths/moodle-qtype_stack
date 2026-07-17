// Extractor: lastexpr
// Returns the trimmed content of the last code_inline, or the last non-empty line
// of the last asciimath_block, in document order.
// Falls back to the final non-empty line of raw when no blocks are available.
export default function lastexpr(raw, blocks) {
    if (blocks && blocks.length > 0) {
        for (let i = blocks.length - 1; i >= 0; i--) {
            const block = blocks[i];
            if (block.type === 'code_inline') {
                return block.raw.trim();
            }
            if (block.type === 'asciimath_block') {
                const lines = block.raw.split(/\r?\n/);
                for (let j = lines.length - 1; j >= 0; j--) {
                    const trimmed = lines[j].trim();
                    if (trimmed !== '') {
                        return trimmed;
                    }
                }
            }
        }
    }

    // Fallback: send the final non-empty line when blocks are unavailable or return nothing.
    const lines = raw.split(/\r?\n/);
    for (let i = lines.length - 1; i >= 0; i--) {
        const trimmed = lines[i].trim();
        if (trimmed !== '') {
            return trimmed;
        }
    }
    return 'ERROR';
}
