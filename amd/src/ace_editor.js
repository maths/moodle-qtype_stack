/**
 * Ace editor integration for STACK.
 *
 * @module qtype_stack/ace_editor
 */
/* global ace */

let acePromise = null;

/**
 * Load Ace once.
 *
 * @returns {Promise<void>}
 */
const loadAce = () => {

    if (acePromise) {
        return acePromise;
    }

    acePromise = new Promise((resolve, reject) => {

        const script = document.createElement('script');

        script.src = M.cfg.wwwroot +
            '/question/type/stack/ace/ace.js';

        script.onload = () => {

            ace.config.set(
                'basePath',
                M.cfg.wwwroot + '/question/type/stack/ace'
            );
            ace.config.setModuleUrl(
                'ace/mode/stack',
                M.cfg.wwwroot + '/question/type/stack/ace_stack/mode-stack.js'
            );
            resolve();
        };

        script.onerror = reject;

        document.head.appendChild(script);
    });

    return acePromise;
};

/**
 * Create an Ace editor from a textarea.
 *
 * @param {HTMLTextAreaElement} textarea
 */
const createEditor = (textarea) => {

    const wrapper = document.createElement('div');

    wrapper.className = 'stack-ace-wrapper';

    wrapper.style.width = '100%';
    wrapper.style.height = '180px';

    textarea.parentNode.insertBefore(wrapper, textarea.nextSibling);

    const editor = ace.edit(wrapper);

    editor.setTheme('ace/theme/textmate');

    editor.session.setMode("ace/mode/stack");

    editor.setOptions({
        fontSize: '14px',
        showPrintMargin: false,
        wrap: true,
        tabSize: 4,
        useSoftTabs: true,
    });

    editor.session.setValue(textarea.value);

    editor.session.on('change', () => {
        textarea.value = editor.getValue();
    });

    textarea.style.display = 'none';

    editor.resize();

    return editor;
};

/**
 * Initialise all STACK Ace editors.
 */
export const init = async() => {

    await loadAce();
    document.querySelectorAll('textarea[data-ace]').forEach(createEditor);
};
