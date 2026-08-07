/**
 * Ace editor integration for STACK.
 *
 * @module qtype_stack/ace_editor
 */
/* global ace */

/**
 * Loads the Ace editor core and custom mode using Moodle's native RequireJS.
 * Satisfies the reviewer requirement to eliminate manual <script> tags.
 */
const loadAce = () => {
    return new Promise((resolve, reject) => {
        // 1. Map paths AND declare robust shims with explicit exports
        window.require.config({
            paths: {
                'ace/ace': M.cfg.wwwroot + '/question/type/stack/ace/ace',
                'qtype_stack/mode-stack': M.cfg.wwwroot + '/question/type/stack/ace_stack/mode-stack'
            },
            shim: {
                'ace/ace': {
                    exports: 'ace' // Verifies window.ace exists after loading core
                },
                'qtype_stack/mode-stack': {
                    deps: ['ace/ace'],
                    exports: 'ace' // Crucial: Tells RequireJS to check window.ace to confirm this file ran fine!
                }
            }
        });

        // 2. Safely load the core engine with its shim protection active
        window.require(['ace/ace'], () => {
            // 3. Load your custom syntax definitions right after
            window.require(['qtype_stack/mode-stack'], () => {
                if (window.ace) {
                    resolve(window.ace);
                } else {
                    reject(new Error('Ace engine loaded but global window instance not found.'));
                }
            }, (err) => reject(new Error('Failed to load STACK mode file: ' + err.message)));

        }, (err) => reject(new Error('Failed to load Ace core engine: ' + err.message)));
    });
};

const createEditor = (textarea) => {
    const wrapper = document.createElement('div');
    const height = textarea.getBoundingClientRect().height || textarea.offsetHeight || 180;

    wrapper.className = 'stack-ace-wrapper';
    wrapper.style.width = '100%';
    wrapper.style.height = `${height}px`;

    textarea.parentNode.insertBefore(wrapper, textarea.nextSibling);

    // Initialized from the instance loaded via our promise
    const editor = ace.edit(wrapper);
    editor.session.setMode("ace/mode/stack");
    editor.setOptions({
        fontSize: '16px',
        showPrintMargin: false,
        wrap: true,
        tabSize: 4,
        useSoftTabs: true,
    });

    editor.session.setValue(textarea.value);

    editor.session.on('change', () => {
        textarea.value = editor.getValue();
    });

    const resizeObserver = new ResizeObserver(() => {
        editor.resize();
    });
    resizeObserver.observe(wrapper);

    textarea.dataset.fieldtype = 'editor';
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
