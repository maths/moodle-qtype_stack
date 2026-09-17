// This file is part of STACK - http://stack-assessment.org/
//
// STACK is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Add LaTeX-compatible \providecommand support to MathJax used by STACK.
 *
 * @module     qtype_stack/mathjax_providecommand
 * @copyright  2026 Oleksandr Kulkov
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([], function() {
    'use strict';

    const globalName = '__stackProvideCommand';
    const packageName = 'stack-providecommand';

    /**
     * Register the command map used by MathJax 3 and 4.
     *
     * @param {Object} mathJax MathJax object with loaded TeX modules.
     * @return {boolean} whether the package was registered or already existed.
     */
    function registerThreeOrFour(mathJax) {
        const tex = mathJax?._?.input?.tex;
        if (!tex) {
            return false;
        }
        const isVersionFour = Boolean(tex.TokenMap);
        const CommandMap = (tex.TokenMap || tex.SymbolMap).CommandMap;
        const {Configuration, ConfigurationHandler} = tex.Configuration;
        const utilExport = tex.newcommand.NewcommandUtil;
        const NewcommandUtil = utilExport.NewcommandUtil || utilExport.default || utilExport;
        const methodsExport = tex.newcommand.NewcommandMethods;
        const NewcommandMethods = methodsExport.default || methodsExport;

        if (ConfigurationHandler.get(packageName)) {
            return true;
        }

        const provide = (parser, name) => {
            const cs = NewcommandUtil.GetCsNameArgument(parser, name);
            const n = NewcommandUtil.GetArgCount(parser, name);
            const opt = parser.GetBrackets(name);
            const definition = parser.GetArgument(name);

            if (!parser.lookup('macro', cs) && !parser.lookup('delimiter', `\\${cs}`)) {
                NewcommandUtil.addMacro(parser, cs, NewcommandMethods.Macro, [definition, n, opt]);
            }
            if (isVersionFour) {
                parser.Push(parser.itemFactory.create('null'));
            }
        };

        if (isVersionFour) {
            new CommandMap(packageName, {providecommand: provide});
        } else {
            new CommandMap(packageName, {providecommand: 'ProvideCommand'}, {ProvideCommand: provide});
        }
        Configuration.create(packageName, {handler: {macro: [packageName]}});
        return true;
    }

    /**
     * Activate the package in a running MathJax 3 or 4 TeX input jax.
     *
     * @param {Object} mathJax running MathJax object.
     * @return {boolean} whether the running TeX input jax was available.
     */
    function activateThreeOrFour(mathJax) {
        const document = mathJax.startup?.document;
        const tex = document?.inputJax?.find((jax) => jax.name === 'TeX');
        if (!tex?.configuration) {
            return false;
        }
        const macroHandler = tex.configuration.handlers.get('macro');
        if (!macroHandler?.applicable('providecommand')) {
            tex.configuration.add(packageName, tex);
        }
        return true;
    }

    /**
     * Add the package to a MathJax 3 or 4 configuration before startup.
     *
     * @param {Object} config MathJax configuration object.
     * @return {boolean} whether the configuration was patched.
     */
    function patchConfig(config) {
        if (!config || typeof config !== 'object' || config.version || config.__stackProvideCommand) {
            return false;
        }
        config.__stackProvideCommand = true;
        config.tex ||= {};

        const packages = config.tex.packages;
        if (!packages) {
            config.tex.packages = {'[+]': [packageName]};
        } else if (Array.isArray(packages)) {
            if (!packages.includes(packageName)) {
                packages.push(packageName);
            }
        } else {
            packages['[+]'] ||= [];
            if (!packages['[+]'].includes(packageName)) {
                packages['[+]'].push(packageName);
            }
        }

        config.startup ||= {};
        const previousReady = config.startup.ready;
        config.startup.ready = function() {
            registerThreeOrFour(window.MathJax);
            return previousReady ? previousReady.call(this) : window.MathJax.startup.defaultReady();
        };
        return true;
    }

    /**
     * Register \providecommand in MathJax 2.
     *
     * @param {Object} mathJax MathJax 2 object.
     * @return {boolean} whether registration was possible.
     */
    function registerTwo(mathJax) {
        const install = () => {
            const tex = mathJax?.InputJax?.TeX;
            const definitions = tex?.Definitions;
            if (!tex?.Parse?.Augment || !definitions?.Add) {
                return false;
            }
            if (definitions.macros?.providecommand) {
                return true;
            }

            definitions.Add({macros: {providecommand: 'ProvideCommand'}}, null, true);
            tex.Parse.Augment({
                ProvideCommand(name) {
                    let cs = this.trimSpaces(this.GetArgument(name));
                    let n = this.GetBrackets(name);
                    const opt = this.GetBrackets(name);
                    const definition = this.GetArgument(name);
                    if (cs.charAt(0) === '\\') {
                        cs = cs.slice(1);
                    }
                    if (!cs.match(/^(.|[a-z]+)$/i)) {
                        tex.Error(['IllegalControlSequenceName',
                            'Illegal control sequence name for %1', name]);
                    }
                    if (n) {
                        n = this.trimSpaces(n);
                        if (!n.match(/^[0-9]+$/)) {
                            tex.Error(['IllegalParamNumber',
                                'Illegal number of parameters specified in %1', name]);
                        }
                    }
                    const hasOwn = (map, key) => Object.prototype.hasOwnProperty.call(map || {}, key);
                    if (!this.csFindMacro(cs) &&
                            !hasOwn(definitions.mathchar0mi, cs) &&
                            !hasOwn(definitions.mathchar0mo, cs) &&
                            !hasOwn(definitions.mathchar7, cs) &&
                            !hasOwn(definitions.delimiter, `\\${cs}`)) {
                        this.setDef(cs, ['Macro', definition, n, opt]);
                    }
                },
            });
            return true;
        };

        if (install()) {
            repairTwo(mathJax);
            return true;
        }
        if (mathJax?.Hub?.Register?.StartupHook) {
            mathJax.Hub.Register.StartupHook('TeX Jax Ready', () => {
                if (install()) {
                    repairTwo(mathJax);
                }
            });
            return true;
        }
        return false;
    }

    /**
     * Repair Moodle equations which MathJax 2 processed before registration.
     *
     * @param {Object} mathJax running MathJax 2 object.
     * @return {boolean} whether any equation was queued for reprocessing.
     */
    function repairTwo(mathJax) {
        const hub = mathJax?.Hub;
        if (!hub?.Queue) {
            return false;
        }
        const roots = Array.from(document.querySelectorAll('.filter_mathjaxloader_equation')).filter((root) => {
            return Array.from(root.querySelectorAll('.MathJax_Error, .mjx-noError, merror')).some((error) => {
                return /providecommand/i.test(error.textContent || '');
            });
        });
        roots.forEach((root) => hub.Queue(['Reprocess', hub, root]));
        return roots.length > 0;
    }

    /**
     * Repair STACK questions which were typeset before the package was available.
     *
     * @param {Object} mathJax running MathJax 3 or 4 object.
     * @return {Promise|null} re-typesetting promise, if repair was needed.
     */
    function repairThreeOrFour(mathJax) {
        if (!mathJax?.typesetClear || !mathJax?.typesetPromise) {
            return null;
        }
        const roots = Array.from(document.querySelectorAll('.que.stack')).filter((root) => {
            return Array.from(root.querySelectorAll('mjx-merror')).some((error) => {
                const message = `${error.getAttribute('data-mjx-error') || ''} ${error.textContent || ''}`;
                return /providecommand/i.test(message);
            });
        });
        if (!roots.length) {
            return null;
        }
        mathJax.typesetClear(roots);
        return mathJax.typesetPromise(roots);
    }

    /**
     * Install support in the current MathJax state.
     *
     * @return {boolean} whether a running MathJax instance was handled.
     */
    function installCurrent() {
        const mathJax = window.MathJax;
        if (!mathJax) {
            return false;
        }
        if (!mathJax.version) {
            patchConfig(mathJax);
            return false;
        }
        if (String(mathJax.version).startsWith('2.')) {
            return registerTwo(mathJax);
        }
        if (!registerThreeOrFour(mathJax)) {
            return false;
        }
        return activateThreeOrFour(mathJax);
    }

    /**
     * Watch scripts because Moodle creates the MathJax script dynamically.
     *
     * @param {Object} state page-global installation state.
     */
    function watchForMathJax(state) {
        if (state.observer || typeof MutationObserver === 'undefined') {
            return;
        }
        const watchScript = (script) => {
            if (!(script instanceof HTMLScriptElement) || !/mathjax/i.test(script.src || '')) {
                return;
            }
            patchConfig(window.MathJax);
            script.addEventListener('load', () => {
                installCurrent();
                state.observer?.disconnect();
                state.observer = null;
            }, {once: true});
        };

        document.querySelectorAll('script[src]').forEach(watchScript);
        state.observer = new MutationObserver((records) => {
            records.forEach((record) => record.addedNodes.forEach(watchScript));
        });
        state.observer.observe(document.head, {childList: true});
    }

    /**
     * Initialise support once per page.
     */
    function init() {
        let state = window[globalName];
        if (!state) {
            state = {observer: null};
            window[globalName] = state;
        }

        const runningMathJax = window.MathJax?.version ? window.MathJax : null;
        installCurrent();
        if (!runningMathJax) {
            watchForMathJax(state);
            return;
        }
        if (String(runningMathJax.version).startsWith('2.')) {
            return;
        }
        const ready = runningMathJax.startup?.promise || Promise.resolve();
        ready.then(() => {
            if (installCurrent()) {
                return repairThreeOrFour(runningMathJax);
            }
            return null;
        }).catch((error) => window.console.error('Could not install STACK MathJax support.', error));
    }

    return {
        init,
        patchConfig,
        registerTwo,
        repairTwo,
        registerThreeOrFour,
        activateThreeOrFour,
        repairThreeOrFour,
    };
});
