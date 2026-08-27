// Extractor: lastcalc
// Returns the trimmed content of the last calculation block.
export default function lastcalc(raw, blocks) {
    if (blocks) {
        for (let i = blocks.length - 1; i >= 0; i--) {
            if (blocks[i].type === 'calculation') {
                return blocks[i].rendered.trim();
            }
        }
    }
    return 'ERROR';
}
