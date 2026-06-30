/** @jest-environment node */

const fs = require('fs');
const path = require('path');

describe('mode-stack.js Syntax Tokenizer', () => {
    let mockMode;
    let tokenizer;

    beforeAll(() => {
        // Path to your local ace library and mode-stack.js
        // Replace the path lines with this to force the correct lookup:
        const acePath = path.resolve(__dirname, '../../ace/ace.js');
        const modeStackPath = path.resolve(__dirname, '../../ace_stack/mode-stack.js');

        // Setup global scope so Ace finds 'window'
        global.window = global;

        // Execute the files directly
        const aceSource = fs.readFileSync(acePath, 'utf8');
        eval(aceSource); // Defines 'ace' globally

        const modeSource = fs.readFileSync(modeStackPath, 'utf8');
        eval(modeSource); // Defines your custom mode

        const Mode = ace.require("ace/mode/stack").Mode;
        mockMode = new Mode();
        tokenizer = mockMode.getTokenizer();
    });

    test('Identifies core STACK variables (e.g., prt1, ans1)', () => {
        const { tokens } = tokenizer.getLineTokens("prt1 : true;", "start");
        expect(tokens.find(t => t.value === "prt1").type).toBe("variable.language");
    });

    test('Correctly identifies function definitions with lookahead', () => {
        const { tokens } = tokenizer.getLineTokens("my_test(x) := x^2;", "start");
        expect(tokens.find(t => t.value === "my_test").type).toBe("entity.name.function");
        expect(tokens.find(t => t.value === ":=").type).toBe("keyword.operator.assignment");
    });

    test('Greedily parses double factorial operator (!!)', () => {
        const { tokens } = tokenizer.getLineTokens("val : 5!!;", "start");
        expect(tokens.find(t => t.value === "!!").type).toBe("keyword.operator");
        expect(tokens.filter(t => t.value === "!").length).toBe(0);
    });

    test('Correctly handles CASText injection tags', () => {
        const { tokens } = tokenizer.getLineTokens('str : "{@ta1@}"', "start");

        const casToken = tokens.find(t => t.value === "{@");

        if (!casToken) {
            throw new Error(`Token '{@' not found. Tokens found: ${tokens.map(t => t.value).join(', ')}`);
        }

        expect(casToken.type).toBe("support.other.castext");
    });

    test('Distinguishes reserved keywords from substrings (drift vs if)', () => {
        const { tokens } = tokenizer.getLineTokens("drift : 10;", "start");
        const driftToken = tokens.find(t => t.value === "drift");
        expect(driftToken.type).toBe("identifier");
    });

    test('Tokenizer transitions through string states and identifies CASText', () => {
        const { tokens } = tokenizer.getLineTokens('var : "contains {@tag@}"', "start");

        // Check that it identified the CASText even inside quotes
        const casOpen = tokens.find(t => t.value === "{@");
        const casClose = tokens.find(t => t.value === "@}");

        expect(casOpen).toBeDefined();
        expect(casClose).toBeDefined();
        expect(casOpen.type).toBe("support.other.castext");
    });

    test('Tokenizer correctly handles escaped quotes inside strings', () => {
        // This ensures your \\. regex rule is working
        const { tokens } = tokenizer.getLineTokens('str : "he said \\"hello\\""', "start");

        // Verify it didn't terminate the string prematurely at the escaped quote
        const stringContent = tokens.filter(t => t.type === "string");
        expect(stringContent.length).toBeGreaterThan(0);
    });
});