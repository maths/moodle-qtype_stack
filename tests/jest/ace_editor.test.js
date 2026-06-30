/** @jest-environment jsdom */

import { init } from '../../amd/src/ace_editor.js';

describe('ace_editor.js Integration Layer', () => {
    beforeEach(() => {
        global.M = { cfg: { wwwroot: 'http://localhost' } };
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
        document.body.innerHTML = '<textarea id="id_test" data-ace="true">initial</textarea>';
    });

    test('init() replaces textarea with editor and masks native input', async () => {
        jest.spyOn(document.head, 'appendChild').mockImplementation((script) => {
            if (script.onload) setTimeout(script.onload, 0);
        });

        await init();

        const textarea = document.getElementById('id_test');
        expect(textarea.style.display).toBe('none');
        expect(document.querySelector('.stack-ace-wrapper')).toBeTruthy();
        expect(global.ace.edit).toHaveBeenCalled();
    });
});