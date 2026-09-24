// Filter: plain. Does nothing.
export function inputToolbarButtons() {
    return [];
}

export default function cas(text, blockCollector) {
    if (blockCollector) {
        blockCollector.isHTML = false;
        blockCollector.blocks = [];
    }

    return text;
}
