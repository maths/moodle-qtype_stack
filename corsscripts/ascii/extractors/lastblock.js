// Extractor: lastblock
// Returns the raw content of the last code_inline, or the full content
// of the last asciimath_block, in document order.
// Falls back to the final non-empty line of raw when no blocks are available.
export default function lastblock(raw, blocks) {
    if (blocks && blocks.length > 0) {
        for (let i = blocks.length - 1; i >= 0; i--) {
            const block = blocks[i];
            if (block.type === 'code_inline') {
                return block.raw;
            }
            if (block.type === 'asciimath_block') {
                return block.raw;
            }
        }
        return 'ERROR';
    }

    // Fallback: send the final non-empty line when blocks are unavailable.
    const lines = raw.split(/\r?\n/);
    for (let i = lines.length - 1; i >= 0; i--) {
        const trimmed = lines[i].trim();
        if (trimmed !== '') {
            return lines[i];
        }
    }
    return 'ERROR';
}
