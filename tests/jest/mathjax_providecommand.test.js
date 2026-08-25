/** @jest-environment jsdom */

const path = require('path');
const {loadAmdModule} = require('./loadAmdModule');

function loadModule() {
    const modulePath = path.resolve(__dirname, '../../amd/src/mathjax_providecommand.js');
    return loadAmdModule(modulePath, {});
}

describe('amd/src/mathjax_providecommand.js', () => {
    beforeEach(() => {
        delete window.MathJax;
        delete window.__stackProvideCommand;
        document.head.querySelectorAll('script').forEach((script) => script.remove());
    });

    test('adds the package while preserving an existing startup callback', () => {
        const module = loadModule();
        const previousReady = jest.fn();
        const config = {tex: {packages: {'[+]': ['existing']}}, startup: {ready: previousReady}};

        expect(module.patchConfig(config)).toBe(true);
        expect(config.tex.packages['[+]']).toEqual(['existing', 'stack-providecommand']);
        expect(config.startup.ready).not.toBe(previousReady);
    });

    test('does not patch the same MathJax configuration twice', () => {
        const module = loadModule();
        const config = {};

        expect(module.patchConfig(config)).toBe(true);
        expect(module.patchConfig(config)).toBe(false);
        expect(config.tex.packages['[+]']).toEqual(['stack-providecommand']);
    });

    test.each([
        ['MathJax 3', false],
        ['MathJax 4', true],
    ])('registers the command map for %s', (_label, versionFour) => {
        const module = loadModule();
        const commandMaps = [];
        const CommandMap = jest.fn((...args) => commandMaps.push(args));
        const create = jest.fn();
        const addMacro = jest.fn();
        const Macro = jest.fn();
        const texModules = {
            Configuration: {
                Configuration: {create},
                ConfigurationHandler: {get: jest.fn(() => null)},
            },
            newcommand: {
                NewcommandUtil: {
                    GetCsNameArgument: jest.fn(() => 'stackmatrix'),
                    GetArgCount: jest.fn(() => 3),
                    addMacro,
                },
                NewcommandMethods: {default: {Macro}},
            },
        };
        if (versionFour) {
            texModules.TokenMap = {CommandMap};
        } else {
            texModules.SymbolMap = {CommandMap};
        }
        const mathJax = {_: {input: {tex: texModules}}};

        expect(module.registerThreeOrFour(mathJax)).toBe(true);
        expect(create).toHaveBeenCalledWith('stack-providecommand', {
            handler: {macro: ['stack-providecommand']},
        });

        const parser = {
            GetBrackets: jest.fn(() => null),
            GetArgument: jest.fn(() => 'definition'),
            lookup: jest.fn(() => null),
            Push: jest.fn(),
            itemFactory: {create: jest.fn(() => 'null-item')},
        };
        if (versionFour) {
            commandMaps[0][1].providecommand(parser, '\\providecommand');
            expect(parser.Push).toHaveBeenCalledWith('null-item');
        } else {
            commandMaps[0][2].ProvideCommand(parser, '\\providecommand');
            expect(parser.Push).not.toHaveBeenCalled();
        }
        expect(addMacro).toHaveBeenCalledWith(parser, 'stackmatrix', Macro, ['definition', 3, null]);
    });

    test('does not replace a command already defined by the course', () => {
        const module = loadModule();
        const CommandMap = jest.fn();
        const addMacro = jest.fn();
        const texModules = {
            SymbolMap: {CommandMap},
            Configuration: {
                Configuration: {create: jest.fn()},
                ConfigurationHandler: {get: jest.fn(() => null)},
            },
            newcommand: {
                NewcommandUtil: {
                    GetCsNameArgument: jest.fn(() => 'stackmatrix'),
                    GetArgCount: jest.fn(() => 3),
                    addMacro,
                },
                NewcommandMethods: {default: {Macro: jest.fn()}},
            },
        };
        module.registerThreeOrFour({_: {input: {tex: texModules}}});
        const provide = CommandMap.mock.calls[0][2].ProvideCommand;
        const parser = {
            GetBrackets: jest.fn(() => null),
            GetArgument: jest.fn(() => 'fallback'),
            lookup: jest.fn((kind) => kind === 'macro'),
        };

        provide(parser, '\\providecommand');

        expect(addMacro).not.toHaveBeenCalled();
    });

    test('registers a MathJax 2 parser method without replacing an existing command', () => {
        const module = loadModule();
        const Add = jest.fn();
        const Augment = jest.fn();
        const mathJax = {InputJax: {TeX: {
            Definitions: {Add, macros: {}},
            Parse: {Augment},
            Error: jest.fn(),
        }}};

        expect(module.registerTwo(mathJax)).toBe(true);
        expect(Add).toHaveBeenCalledWith({macros: {providecommand: 'ProvideCommand'}}, null, true);
        expect(Augment).toHaveBeenCalledTimes(1);

        const ProvideCommand = Augment.mock.calls[0][0].ProvideCommand;
        const setDef = jest.fn();
        const parser = {
            trimSpaces: jest.fn((value) => value.trim()),
            GetArgument: jest.fn()
                .mockReturnValueOnce('\\stackmatrix')
                .mockReturnValueOnce('fallback'),
            GetBrackets: jest.fn()
                .mockReturnValueOnce('3')
                .mockReturnValueOnce(null),
            csFindMacro: jest.fn(() => null),
            setDef,
        };
        ProvideCommand.call(parser, '\\providecommand');
        expect(setDef).toHaveBeenCalledWith('stackmatrix', ['Macro', 'fallback', '3', null]);

        parser.GetArgument
            .mockReturnValueOnce('\\stackmatrix')
            .mockReturnValueOnce('ignored');
        parser.GetBrackets
            .mockReturnValueOnce('3')
            .mockReturnValueOnce(null);
        parser.csFindMacro.mockReturnValue(['Macro', 'course definition', '3']);
        setDef.mockClear();
        ProvideCommand.call(parser, '\\providecommand');
        expect(setDef).not.toHaveBeenCalled();
    });

    test('repairs only Moodle equations with MathJax 2 providecommand errors', () => {
        document.body.innerHTML = `
            <span id="repair-classic" class="filter_mathjaxloader_equation">
                <span class="MathJax_Error">Undefined control sequence \\providecommand</span>
            </span>
            <span id="repair-noerrors" class="filter_mathjaxloader_equation">
                <span class="mjx-noError">\\providecommand{\\vect}</span>
            </span>
            <span id="repair-mathml" class="filter_mathjaxloader_equation">
                <math><merror><mtext>\\providecommand{\\mat}</mtext></merror></math>
            </span>
            <span id="other" class="filter_mathjaxloader_equation">
                <span class="MathJax_Error">Undefined control sequence \\other</span>
            </span>
        `;
        const module = loadModule();
        const Queue = jest.fn();
        const mathJax = {Hub: {Queue}};

        expect(module.repairTwo(mathJax)).toBe(true);
        expect(Queue).toHaveBeenCalledWith([
            'Reprocess', mathJax.Hub, document.getElementById('repair-classic'),
        ]);
        expect(Queue).toHaveBeenCalledWith([
            'Reprocess', mathJax.Hub, document.getElementById('repair-noerrors'),
        ]);
        expect(Queue).toHaveBeenCalledWith([
            'Reprocess', mathJax.Hub, document.getElementById('repair-mathml'),
        ]);
        expect(Queue).toHaveBeenCalledTimes(3);
    });

    test('patches a Moodle configuration which replaces an earlier MathJax object', () => {
        const module = loadModule();
        window.MathJax = {};
        module.init();

        window.MathJax = {};
        const script = document.createElement('script');
        script.src = 'https://cdn.example.test/mathjax@4/tex-chtml.js';
        document.head.appendChild(script);

        return Promise.resolve().then(() => {
            expect(window.MathJax.tex.packages['[+]']).toContain('stack-providecommand');
        });
    });

    test('repairs only STACK questions with providecommand errors', async() => {
        document.body.innerHTML = `
            <div id="repair" class="que stack"><mjx-merror data-mjx-error="Undefined \\providecommand"></mjx-merror></div>
            <div id="other" class="que stack"><mjx-merror data-mjx-error="Undefined \\other"></mjx-merror></div>
        `;
        const module = loadModule();
        const typesetClear = jest.fn();
        const typesetPromise = jest.fn(() => Promise.resolve());

        await module.repairThreeOrFour({typesetClear, typesetPromise});

        const repaired = document.getElementById('repair');
        expect(typesetClear).toHaveBeenCalledWith([repaired]);
        expect(typesetPromise).toHaveBeenCalledWith([repaired]);
    });
});
