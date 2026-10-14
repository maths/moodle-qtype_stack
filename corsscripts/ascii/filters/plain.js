// Filter: plain. Does nothing.
export const inputToolbarButtons = [];

export default function cas(text, blockCollector) {
    if (blockCollector) {
        blockCollector.isHTML = false;
        blockCollector.blocks = [];
    }

    return text;
}
