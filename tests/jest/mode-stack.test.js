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
    test('Identifies Maxima mathematical constants and environment variables', () => {
        const { tokens } = tokenizer.getLineTokens("%pi + %phi + inf + done;", "start");
        expect(tokens.find(t => t.value === "%pi").type).toBe("constant.language");
        expect(tokens.find(t => t.value === "%phi").type).toBe("constant.language");
        expect(tokens.find(t => t.value === "inf").type).toBe("constant.language");
        expect(tokens.find(t => t.value === "done").type).toBe("constant.language");

        const { tokens: settingTokens } = tokenizer.getLineTokens("simp : false; fpprec : 16;", "start");
        expect(settingTokens.find(t => t.value === "simp").type).toBe("variable.language");
        expect(settingTokens.find(t => t.value === "fpprec").type).toBe("variable.language");
    });

    test('Identifies Maxima built-in functions when used as standalone identifiers', () => {
        // When mapping functions as arguments without immediate evaluation parentheses:
        const { tokens } = tokenizer.getLineTokens("diff linsolve determinant zeromatrix sin log sqrt", "start");

        const types = tokens.filter(t => t.value.trim() !== "").map(t => t.type);
        types.forEach(type => {
            expect(type).toBe("support.function");
        });
    });

    test('Distinguishes standalone functions from immediate function calls', () => {
        const { tokens } = tokenizer.getLineTokens("map(integrate, [x]);", "start");

        // 'map' is followed directly by an open parenthesis, triggering lookahead call tokenization
        expect(tokens.find(t => t.value === "map").type).toBe("support.function.call");

        // 'integrate' inside the array parameters passes to the plain keyword mapper fallback
        expect(tokens.find(t => t.value === "integrate").type).toBe("support.function");
    });

    test('Identifies expanded control flow keywords and logical boolean operators', () => {
        const { tokens } = tokenizer.getLineTokens("elseif thru step go and or not", "start");

        expect(tokens.find(t => t.value === "elseif").type).toBe("keyword");
        expect(tokens.find(t => t.value === "thru").type).toBe("keyword");
        expect(tokens.find(t => t.value === "step").type).toBe("keyword");
        expect(tokens.find(t => t.value === "go").type).toBe("keyword");

        expect(tokens.find(t => t.value === "and").type).toBe("keyword.operator");
        expect(tokens.find(t => t.value === "or").type).toBe("keyword.operator");
        expect(tokens.find(t => t.value === "not").type).toBe("keyword.operator");
    });

    test('Processes line comments and tracks block comment state mutations', () => {
        const lineResult = tokenizer.getLineTokens("// clear all math flags", "start");
        expect(lineResult.tokens[0].type).toBe("comment.line");

        // Verifies the structural state engine shifts safely when hitting multi-line tags
        const blockResult = tokenizer.getLineTokens("/* processing multiline matrix block", "start");
        expect(blockResult.tokens[0].type).toBe("comment.block");
        expect(blockResult.state).toBe("comment");
    });
});