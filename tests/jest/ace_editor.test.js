/** @jest-environment jsdom */

import { init } from '../../amd/src/ace_editor.js';

describe('ace_editor.js Integration Layer', () => {
    beforeEach(() => {
        // Set up the Moodle global variables environment
        global.M = {
            cfg: {
                wwwroot: 'http://localhost'
            }
        };

        // Mock ResizeObserver for JSDOM environment
        global.ResizeObserver = jest.fn().mockImplementation(() => ({
            observe: jest.fn(),
            unobserve: jest.fn(),
            disconnect: jest.fn(),
        }));

        // Mock the global Ace framework API exposed after shimming
        global.ace = {
            config: { set: jest.fn(), setModuleUrl: jest.fn() },
            edit: jest.fn(() => ({
                setTheme: jest.fn(),
                setOptions: jest.fn(),
                session: { setMode: jest.fn(), setValue: jest.fn(), on: jest.fn() },
                resize: jest.fn(),
                getValue: jest.fn(() => 'updated content')
            }))
        };

        // Match JSDOM references to global scope expectations
        window.ace = global.ace;

        // 1. Mock RequireJS configuration manager
        global.require = Object.assign(
            jest.fn((modules, successCallback, errorCallback) => {
                // Instantly execute async callbacks to move the Promise forward
                if (successCallback) {
                    setTimeout(successCallback, 0);
                }
            }),
            {
                config: jest.fn()
            }
        );

        // Bind reference explicitly so window.require matches implementation layout
        window.require = global.require;

        // Render clean, mock state HTML fields inside the document context
        document.body.innerHTML = '<textarea id="id_test" data-ace="true">initial</textarea>';
    });

    test('init() configures AMD require, builds custom shims, and handles UI swapping', async () => {
        // Run the initialization runner
        await init();

        // 2. Validate RequireJS Configuration Setup
        expect(window.require.config).toHaveBeenCalledWith(
            expect.objectContaining({
                paths: {
                    'ace/ace': 'http://localhost/question/type/stack/ace/ace',
                    'qtype_stack/mode-stack': 'http://localhost/question/type/stack/ace_stack/mode-stack'
                },
                shim: {
                    'ace/ace': { exports: 'ace' },
                    'qtype_stack/mode-stack': { deps: ['ace/ace'], exports: 'ace' }
                }
            })
        );

        // 3. Validate Module Core Loading Sequence Execution
        expect(window.require).toHaveBeenCalledWith(['ace/ace'], expect.any(Function), expect.any(Function));
        expect(window.require).toHaveBeenCalledWith(['qtype_stack/mode-stack'], expect.any(Function), expect.any(Function));

        // 4. Validate Standard DOM Transform Manipulations
        const textarea = document.getElementById('id_test');
        expect(textarea.style.display).toBe('none');
        expect(document.querySelector('.stack-ace-wrapper')).toBeTruthy();
        expect(global.ace.edit).toHaveBeenCalled();
    });
});