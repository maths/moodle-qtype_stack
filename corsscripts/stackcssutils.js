/**
 * This is a library for dealing with styled IFRAME -> VLE style transfer.
 * Basically, a set of tools for mapping stylesheets to inline styles.
 * 
 * @copyright  2025 Aalto University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

"use strict";

/**
 * Takes potenttially many selectors, and splits them to separate singular selectors.
 * 
 *  `".foo:not(a, b), bar[sep=',']"` -> `[".foo:not(a, b)","bar[sep=',']"]`.
 * 
 * Basically, tracks nested selector lists and strings when deciding if
 * a comma leads to a split.
 */
function split_selectors(selectorText) {
    if (selectorText.includes(',')) {
        let selectors = [];
        let indq = false;
        let insq = false;
        let lastslash = false;
        let current = '';
        let p = 0;
        for (const c of selectorText) {
            current = current + c;
            switch (c) {
                case '(':
                    if (!indq && !insq) {
                        p = p + 1;
                    }
                    break;
                case ')':
                    if (!indq && !insq) {
                        p = p - 1;
                    }
                    break;
                case ',':
                    if (!indq && !insq && p == 0) {
                        /* Drop the extra comma. */
                        current = current.substring(0, current.length - 1);
                        current = current.trim();
                        if (current != '') {
                            selectors.push(current);
                        }
                        current = '';
                    }
                    lastslash = false;
                    break;
                case '\\':
                    lastslash = !lastslash;
                    break;
                case '"':
                    if (indq) {
                        if (!lastslash) {
                            indq = false;
                        } else {
                            lastslash = false;
                        }
                    } else {
                        indq = !insq;
                        lastslash = false;
                    }
                    break;
                case "'":
                    if (insq) {
                        if (!lastslash) {
                            insq = false;
                        } else {
                            lastslash = false;
                        }
                    } else {
                        insq = !indq;
                        lastslash = false;
                    }
                    break;
                default:
                    lastslash = false;
            } 
        }
        current = current.trim();
        if (current != '') {
            selectors.push(current);
        }
        return selectors;
    }
    return [selectorText.trim()];
}

function extract_matching_paren(text, from) {
    let indq = false;
    let insq = false;
    let lastslash = false;
    let current = '';
    let p = 0;
    for (const c of text.substring(from)) {
        current = current + c;
        switch (c) {
            case '(':
                if (!indq && !insq) {
                    p = p + 1;
                }
                break;
            case ')':
                if (!indq && !insq) {
                    p = p - 1;
                }
                if (p == 0) {
                    return current;
                }
                break;
            case '\\':
                lastslash = !lastslash;
                break;
            case '"':
                if (indq) {
                    if (!lastslash) {
                        indq = false;
                    } else {
                        lastslash = false;
                    }
                } else {
                    indq = !insq;
                    lastslash = false;
                }
                break;
            case "'":
                if (insq) {
                    if (!lastslash) {
                        insq = false;
                    } else {
                        lastslash = false;
                    }
                } else {
                    insq = !indq;
                    lastslash = false;
                }
                break;
            default:
                lastslash = false;
        } 
    }

    return current;
}

/**
 * Returns a CSS-identifier starting at the index.
 * Identifier defined as in https://developer.mozilla.org/en-US/docs/Web/CSS/ident
 * 
 * Does not take into account rules related to first and second chars.
 * This whole library assumes reasonably correct CSS-selectors.
 */
function extract_css_id(text, from) {
    let current = '';
    const txt = text.substring(from);
    const iterator = txt[Symbol.iterator]();
    let i = iterator.next();
    while (!i.done && i.value !== ' ') {
        let c = i.value;
        if (c.match(/[a-zA-Z_\-0-9]/) !== null || c.codePointAt(0) >= 160) {
            current = current + c;
        } else if (c === '\\') {
            current = current + c;
            /* Three options. Next chars are hex, for two chars or six chars. Or one char that is any char. */
            i = iterator.next();
            c = i.value;
            if (c.match(/[a-fA-F0-9]/) === null) {
                current = current + c;
            } else {
                current = current + c;
                i = iterator.next();
                c = i.value;
                if (c.match(/[a-fA-F0-9]/) !== null) {
                    current = current + c;
                }
                i = iterator.next();
                c = i.value;
                /* Is it not hex, basically a space? */
                if (c.match(/[a-fA-F0-9]/) === null) {
                    current = current + c;
                } else {
                    /* Assume we get the remaining three chars just fine. */
                    i = iterator.next();
                    current = current + i.value;
                    i = iterator.next();
                    current = current + i.value;
                    i = iterator.next();
                    current = current + i.value;
                }
            }
        } else {
            break;
        }
        i = iterator.next();
    }
    return current;
}


/**
 * There are plenty of libraries implementing specificity calculations, 
 * but they tend to be big and have full on selector-parsers underneath.
 * This implementation aims to be short and to drop as much detais as 
 * possible as fast as possible.
 */
function simplified_css_specificity(selector) {
    let counts = [0,0,0]; /* ID, CLASS, ELEMENT/TYPE */
    /* First remove extra whitespace */
    selector = selector.replace(/\s+/g, ' ').trim();
    /* Second drop any and all 'strings', they might lurk inside attribute selectors. */
    let indq = false;
    let insq = false;
    let lastslash = false;
    let nostrings = '';
    for (let i = 0; i < selector.length; i++) {
        const c = selector[i];
        if (!indq && !insq && c !== '"' && c !== "'") {
            nostrings = nostrings + c;
        }
        switch (c) {
            case '\\':
                lastslash = !lastslash;
                break;
            case '"':
                if (indq) {
                    if (!lastslash) {
                        indq = false;
                    } else {
                        lastslash = false;
                    }
                } else {
                    if (!insq) {
                        nostrings = nostrings + "s";
                    }
                    indq = !insq;
                    lastslash = false;
                }
                break;
            case "'":
                if (insq) {
                    if (!lastslash) {
                        insq = false;
                    } else {
                        lastslash = false;
                    }
                } else {
                    if (!indq) {
                        nostrings = nostrings + "s";
                    }
                    insq = !indq;
                    lastslash = false;
                }
                break;
            default:
                lastslash = false;
        } 
    }
    selector = nostrings;

    /* Strip out all pseudo-elements, present at the top level. */
    let pseudos = selector.match(/\:\:[a-zA-Z\-]+/g);
    while (pseudos !== null && pseudos.length > 0) {
        /* Each pseudoelement is +1 to ELEMENT */
        counts[2] = counts[2] + 1;
        let pseudo = pseudos[0];
        /* Does it have arguments. Currently we ignore all arguments. */
        if (selector[selector.indexOf(pseudo) + pseudo.length] == '(') {
            let args = extract_matching_paren(selector, selector.indexOf(pseudo) + pseudo.length);
            /* For pseudo-elements we ignore those args. */
            pseudo = pseudo + args;
            selector = selector.replace(pseudo, '');
        }
        pseudos = selector.match(/\:\:[a-zA-Z\-]+/g);
    }

    /* The the same for pseudo-classes, present at the top level. */
    pseudos = selector.match(/\:[a-zA-Z\-]+/g);
    while (pseudos !== null && pseudos.length > 0) {
        let pseudo = pseudos[0];
        let args = [];
        let argsstring;
        /* Does it have arguments. */
        if (selector[selector.indexOf(pseudo) + pseudo.length] == '(') {
            argsstring = extract_matching_paren(selector, selector.indexOf(pseudo) + pseudo.length);
            selector = selector.replace(pseudo + argsstring, '');
            args = split_selectors(argsstring.substring(1, argsstring.length-1));
        } else {
            selector = selector.replace(pseudo, '');
        }

        switch (pseudo) {
            case ':where':
                /* Always without specificity */
                break;
            case ':is':
            case ':has':
            case ':not':
                /* For these identify the most specific argument but have no own specificity. */
                if (args.length > 0) {
                    let scores = args.map(simplified_css_specificity);
                    scores.sort();
                    scores.reverse()
                    counts[0] = counts[0] + scores[0][0];
                    counts[1] = counts[1] + scores[0][1];
                    counts[2] = counts[2] + scores[0][2];
                }
                break;
            case ':nth-last-of-type':
            case ':nth-of-type':
            case ':nth-child':
            case ':nth-last-child':
                /* These may or may not include a selector. Which will be counted in addition to the pseudo-class. */
                /* We only care about the first split here. Recursion handles the rest. */
                if (argsstring.includes(' of ')) {
                    args = [args[0].substring(args[0].indexOf(' of ') + 4).trim()];
                } else {
                    // Remove extra work from the fall-through logic.
                    args = [];
                }
            case ':host':
            case ':host-context':
                /* For these take the arguments specificity. */
                /* There should only be one there, but might as well have a way to fail. */
                if (args.length > 0) {
                    let scores = args.map(simplified_css_specificity);
                    scores.sort();
                    scores.reverse()
                    counts[0] = counts[0] + scores[0][0];
                    counts[1] = counts[1] + scores[0][1];
                    counts[2] = counts[2] + scores[0][2];
                }
                /* Fall through to the general case and get one CLASS point. */
            default:
                counts[1] = counts[1] + 1;
        }
        pseudos = selector.match(/\:[a-zA-Z\-]+/g);
    }

    /* Then strip out all the attribute selectors. Each ups the CLASS count*/
    const atribute_selectors = selector.match(/\[[^\]]+\]/g);
    if (atribute_selectors !== null) {
        for (const as of atribute_selectors) {
            counts[1] = counts[1] + 1;
            selector = selector.replace(as, '');
        }
    }

    /* Namespaces are something we can remove now, they have no value. */
    /* Note that column-combinator `||` will be a problem for this. */
    const namespaces = selector.match(/[a-zA-Z\-]\|/g);
    if (namespaces !== null) {
        for (const ns of namespaces) {
            selector = selector.replace(ns, '');
        }
    }
    /* Cases of every, these have no value at all. */
    selector = selector.replaceAll('*|', '');
    selector = selector.replaceAll('*', '');

    /* After those steps we should have ".classes", "#identifiers" and "elements" left. */
    /* Also some combinators. */
    /* Remove the ones that use CSS-identifiers first to deal with those combinator chars in escapes. */
    while (selector.includes('.') || selector.includes('#')) {
        const first_class = selector.indexOf('.');
        const first_id = selector.indexOf('#');
        if (first_id === -1 || (first_class >= 0 && (first_class < first_id))) {
            // Hunt for class id.
            const id = extract_css_id(selector, first_class + 1);
            selector = selector.replace("." + id, '');
            counts[1] = counts[1] + 1;
        } else {
            // Hunt for element id.
            const id = extract_css_id(selector, first_id + 1);
            selector = selector.replace("#" + id, '');
            counts[0] = counts[0] + 1;
        }
    }

    /* Clear those combinators away, now that they can no longer be in identifiers. */
    selector = selector.replaceAll('+', '');
    selector = selector.replaceAll('>', '');
    selector = selector.replaceAll('~', '');
    selector = selector.replaceAll('&', '');

    /* Trim some extra spaces that may have leaked in. */
    selector = selector.replace(/\s+/g, ' ').trim();

    /* Everything remaining in the selector is an "element". */
    const els = selector.split(" ");
    if (els[0] !== '') {
        counts[2] = counts[2] + els.length;
    }

    return counts;
}

/*
function test(a,b) {
    const result = simplified_css_specificity(a);
    if (result[0] !== b[0]) {
        return false;
    }
    if (result[1] !== b[1]) {
        return false;
    }
    if (result[2] !== b[2]) {
        return false;
    }
    return true;
}

console.assert(test('*', [0,0,0]));
console.assert(test('p', [0,0,1]));
console.assert(test('p.foo', [0,1,1]));
console.assert(test('.foo', [0,1,0]));
console.assert(test('p#foo', [1,0,1]));
console.assert(test('p#foo', [1,0,1]));
console.assert(test('#foo', [1,0,0]));
console.assert(test('#foo:where(bar)', [1,0,0]));
console.assert(test('div#foo p.bar', [1,1,2]));
console.assert(test(':not(.active):not(button)', [0,1,1]));
console.assert(test(':is(.active):is(button)', [0,1,1]));
console.assert(test(':is(.active):is(button.foo, #bar)', [1,1,0]));
console.assert(test(':root #myApp input:required', [1,2,1]));
console.assert(test('h2:nth-last-of-type(n + 2)', [0,1,1]));
console.assert(test(':is(p)', [0,0,1]));
console.assert(test('div:not(.inner) p', [0,1,2]));
console.assert(test('h1:has(+ h2, > #fakeId)', [1,0,1]));
console.assert(test('a:not(#fakeId#fakeId#fakeID)', [3,0,1]));
console.assert(test(':where(#defaultTheme)', [0,0,0]));
console.assert(test('input[type="password"]:required', [0,2,1]));
console.assert(test('foo:nth-child(2n+1 of p#bar)', [1,1,2]));

*/

export const stack_css_utils = {
    /**
     * Reads all the stylesheets from `document.styleSheets` and maps all
     * the rules as inline style to all the matching elements of the document.
     * 
     * NOTE! The no dynamic rules will work, `:hover` cannot be stamped to
     * the element. Also behaviour with `@media` rules is undefined, so
     * this might not work for everything.
     * 
     */
    inline: function() {
        /* Assuming no styles elsewhere than stylesheets. */
        /* Find all the rules that match any elements. */
        let rules = [];
        let specificitymap = {};

        for (const sheet of document.styleSheets) {
            for (const rule of sheet.cssRules) {
                for (const selector of split_selectors(rule.selectorText)) {
                    let trial = document.querySelector(selector);
                    if (trial !== null) {
                        rules.push({'selectorText': selector, 'style': {'cssText': rule.style.cssText}});
                        specificitymap[selector] = simplified_css_specificity(selector);
                    }
                }
                if (rule.cssRules.length > 0) {
                    let nestedrules = [];
                    for (const nested of rule.cssRules) {
                        nestedrules.push([rule.selectorText, nested]);
                    }
                    while (nestedrules.length > 0) {
                        const [prefix, nested] = nestedrules.shift();
                        const selector = prefix + nested.selectorText;
                        let trial = document.querySelector(selector);
                        if (trial !== null) {
                            rules.push({'selectorText': selector, 'style': {'cssText': nested.style.cssText}});
                            specificitymap[selector] = simplified_css_specificity(selector);
                        }
                        if (nested.cssRules.length > 0) {
                            for (let i = nested.cssRules.length - 1; i >= 0; i--) {
                                /* Order matters. */
                                nestedrules.unshift([selector, nested.cssRules[i]]);
                            }       
                        }
                    }
                }
                
            }
        }

        /* Now that we have all the rules that are relevant we can sort them by specificity. */
        /* Note that we assume >=ES2019 sort, as it is a stable sort and to us the declaration order matters. */
        /* We sort these in raising order of specificity and then apply in reverse order, this to keep declaration order in play. */
        rules.sort((a,b) => {
            const aspec = specificitymap[a.selectorText];
            const bspec = specificitymap[b.selectorText];
            if (aspec < bspec) {
                return -1;
            } else if (aspec > bspec) {
                return 1;
            }
            return 0;
        });
        for (const rule of rules.reverse()) {
            const els = document.querySelectorAll(rule.selectorText);
            for (let i = 0; i < els.length; i++) {
                let inlinestyle = els[i].getAttribute('style') || '';
                /* Prepend the rule as inline always beats in specificity. */
                inlinestyle = rule.style.cssText + ';' + inlinestyle;
                /* TODO clean duplicates and shadowed ones from that. */
                els[i].setAttribute('style', inlinestyle);
            }
        }
    }
};

export default stack_css_utils;