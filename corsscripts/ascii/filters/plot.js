// Filter: plot - identifies !!p blocks and replaces them with plot placeholders.
import { renderPlotPlaceholder } from './plotrules.js';

export default function plot(text, blockCollector) {
    if (blockCollector) {
        blockCollector.isHTML = false;
        blockCollector.blocks = [];
    }

    const lines = text.split(/\r?\n/);
    const output = [];

    for (let index = 0; index < lines.length; index++) {
        if (!isPlotMarker(lines[index])) {
            output.push(lines[index]);
            continue;
        }

        const contentLines = [];
        let closingIndex = -1;

        for (let seek = index + 1; seek < lines.length; seek++) {
            if (isPlotMarker(lines[seek])) {
                closingIndex = seek;
                break;
            }
            contentLines.push(lines[seek]);
        }

        if (closingIndex === -1) {
            output.push(lines[index]);
            continue;
        }

        const raw = contentLines.join('\n');
        const rendered = renderPlotPlaceholder(raw);
        if (blockCollector) {
            blockCollector.isHTML = true;
            blockCollector.blocks.push({ type: 'plot_block', raw, rendered });
        }
        output.push(rendered);
        index = closingIndex;
    }

    return output.join('\n');
}

function isPlotMarker(line) {
    return line.trim() === '!!p';
}
