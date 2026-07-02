ace.define("ace/mode/stack", [
    "require",
    "exports",
    "module",
    "ace/lib/oop",
    "ace/mode/text",
    "ace/mode/text_highlight_rules"
], function(require, exports, module) {

    "use strict";

    const oop = require("../lib/oop");
    const TextMode = require("./text").Mode;
    const TextHighlightRules = require("./text_highlight_rules").TextHighlightRules;

    // =========================
    // Highlight Rules
    // =========================
    const StackHighlightRules = function() {

        // 1. Core Logic Keywords (Maxima)
        var keywords = [
            "if", "then", "else", "elseif", "do", "while", "for", "step",
            "thru", "block", "return", "break", "continue", "go"
        ].join("|");

        // 2. Maxima + STACK Built-in Functions
        var builtInFunctions = [
            // STACK Specific APIs
            "rand", "rand_with_prohib", "rand_with_step", "rand_selection",
            "make_mult_sgn", "trig_standardise", "make_trig",
            "stack_disp", "stack_disp_comma", "stack_include",
            "castext", "tex", "disp", "display",
            // STACK Answer Tests
            "AlgEquiv", "CasEqual", "SubstEquiv", "SysEquiv", "FacForm",
            // Maxima Base Functions (Evaluation & Manipulation)
            "at", "ev", "subst", "nounify", "unary", "binary", "part",
            "first", "rest", "append", "map", "float", "ratsimp", "expand",
            "factor", "simplify", "fullratsimp", "int", "rhs", "lhs",
            // Maxima Calculus
            "diff", "integrate", "limit", "sum", "product", "taylor", "laplace", "ilt",
            // Maxima Algebra & Matrices
            "solve", "linsolve", "algsys", "matrix", "determinant", "invert",
            "transpose", "eigenvalues", "eigenvectors", "ident", "zeromatrix",
            // Maxima Trigonometry & Math
            "sin", "cos", "tan", "asin", "acos", "atan", "sinh", "cosh", "tanh",
            "log", "exp", "sqrt", "abs", "max", "min", "signum", "mod"
        ].join("|");

        // 3. Variables & Constants
        var stackVariables = [
            // STACK PRT and Inputs
            "ans1", "ans2", "ans3", "ans4", "ans5",
            "prt1", "prt2", "prt3", "prt4", "prt5",
            "ta", "ta1", "ta2", "ta3", "sa"
        ].join("|");

        var maximaSettings = [
            // Common Maxima environment variables
            "fpprec", "fpprintprec", "display2d", "keepfloat",
            "simp", "radexpand", "algebraic", "ratfac", "trigsign"
        ].join("|");

        var booleanOps = ["and", "or", "not"].join("|");

        // Expanded Maxima Constants
        var constants = [
            "%pi", "%e", "%i", "%gamma", "%phi",
            "inf", "minf", "und", "ind", "infinity",
            "true", "false", "done"
        ].join("|");

        // 4. Keyword Mapper Configuration
        var keywordMapper = this.createKeywordMapper({
            "keyword": keywords,
            "support.function": builtInFunctions,
            "variable.language": stackVariables + "|" + maximaSettings,
            "constant.language": constants,
            "keyword.operator": booleanOps
        }, "identifier", false);

        // RULES
        this.$rules = {
            "start": [
                { token: "comment.line", regex: "//.*$" },
                { token: "comment.block", regex: "/\\*", next: "comment" },

                // CASText tags remain in start state for text outside strings
                { token: "support.other.castext", regex: "\\{@|@\\}|\\{#|#\\}" },

                // Redirect to stateful string handling
                { token: "string", regex: '"', next: "string_double" },
                { token: "string", regex: "'", next: "string_single" },

                { token: "keyword.operator.absolute", regex: "\\|" },
                { token: "keyword.operator.assignment.stack", regex: "#" },
                { token: "keyword.operator.assignment", regex: ":=" },
                { token: "keyword.operator", regex: "=|<=|>=|<|>|\\+|\\-|\\*|/|\\^|\\.|!!|!|::|''" },
                { token: "constant.numeric", regex: "\\b\\d+(\\.\\d+)?\\b" },

                // Prioritize function names before keyword mapper catches them as general identifiers
                { token: "entity.name.function", regex: "[a-zA-Z_][a-zA-Z0-9_]*(?=\\s*\\([^\\)]*\\)\\s*:=)"},
                { token: "support.function.call", regex: "[a-zA-Z_][a-zA-Z0-9_]*(?=\\s*\\()" },

                // Map identifiers (This uses the keyword mapper we defined above)
                { token: keywordMapper, regex: "%?[a-zA-Z_][a-zA-Z0-9_]*\\b" },

                { token: "paren.lparen", regex: "[\\(\\[\\{]" },
                { token: "paren.rparen", regex: "[\\)\\]\\}]" },
                { token: "punctuation.operator", regex: "," },
                { token: "invalid.illegal", regex: "[@`$&\\\\]" }
            ],
            "string_double": [
                // 1. Priority: CASText must come first
                { token: "support.other.castext", regex: "\\{@|@\\}|\\{#|#\\}" },
                // 2. Escape sequences
                { token: "string", regex: '\\\\.', next: "string_double" },
                // 3. String content (explicitly stop at the CASText characters)
                { token: "string", regex: '[^"\\\\{@#]+', next: "string_double" },
                // 4. Closing quote
                { token: "string", regex: '"', next: "start" }
            ],
            "string_single": [
                { token: "support.other.castext", regex: "\\{@|@\\}|\\{#|#\\}" },
                { token: "string", regex: '\\\\.', next: "string_single" },
                { token: "string", regex: "[^'\\\\{@#]+", next: "string_single" },
                { token: "string", regex: "'", next: "start" }
            ],
            "comment": [
                { token: "comment.block", regex: ".*?\\*/", next: "start" },
                { token: "comment.block", regex: ".+" }
            ]
        };

        this.normalizeRules();
    };

    oop.inherits(StackHighlightRules, TextHighlightRules);

    // =========================
    // Mode
    // =========================
    var Mode = function() {
        this.HighlightRules = StackHighlightRules;
    };

    oop.inherits(Mode, TextMode);

    (function() {
        this.lineCommentStart = "//";
        this.blockComment = { start: "/*", end: "*/" };
        this.$id = "ace/mode/stack";
    }).call(Mode.prototype);

    exports.Mode = Mode;
});