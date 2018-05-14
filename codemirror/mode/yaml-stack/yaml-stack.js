// CodeMirror, copyright (c) by Marijn Haverbeke and others
// Distributed under an MIT license: http://codemirror.net/LICENSE

(function(mod) {
  if (typeof exports == "object" && typeof module == "object") // CommonJS
    mod(require("../../lib/codemirror"), require("../markdown/markdown"), require("html-code-blocks"));
  else if (typeof define == "function" && define.amd) // AMD
    define(["../../lib/codemirror", "../markdown/markdown", "html-code-blocks"], mod);
  else // Plain browser env
    mod(CodeMirror);
})(function(CodeMirror) {
"use strict";

CodeMirror.defineMode("yaml-stack", function(config) {
  var paramPattern = /^\s*(?:[,\[\]{}&*!|>'"%@`][^\s'":]|[^,\[\]{}#&*!|>'"%@`])[^#]*?(?=\s*:($|\s))/;
  var HTML = 1, MARKDOWN = 2;

  var makeRegex = function (words) {
    return new RegExp("\\b(("+words.join(")|(")+"))$", 'i');
  }

  var cons = ['true', 'false', 'on', 'off', 'yes', 'no'];
  var contentFields = ['specific_feedback', 'question', 'worked_solution', 'prt_correct',
    'prt_partially_correct', 'prt_incorrect', 'feedback'];
  var contentFieldsHtml = [];
  for (var key in contentFields) {
    contentFieldsHtml.push(contentFields[key] + '_html');
  }

  var keywordRegex = makeRegex(cons);
  var contentFieldsRegex = makeRegex(contentFields);
  var contentFieldsHtmlRegex = makeRegex(contentFieldsHtml);

  var markdownMode = CodeMirror.getMode(config, "markdown");
  var htmlMode = CodeMirror.getMode(config, "html-code-blocks");

  function curMode(state) {
    switch (state.contentField) {
      case HTML:
        return htmlMode;
      case MARKDOWN:
        return markdownMode;
      default:
        return null;
    }
  }

  function processContentField(state, stream) {
    var mode = curMode(state);
    if (mode) {
      state.contentModeState = state.contentModeState || CodeMirror.startState(mode);
      return mode.token(stream, state.contentModeState);
    }
  }

  return {
    startState: function() {
      return {
        pair: false,
        pairStart: false,
        keyCol: 0,
        inlinePairs: 0,
        inlineList: 0,
        literal: false,
        escaped: false,
        contentField: false,
        contentModeState: null,
        indentation: 0,
        processLine: false
      };
    },

    copyState: function(state) {
      var mode = curMode(state);

      return {
        pair: state.pair,
        pairStart: state.pairStart,
        keyCol: state.keyCol,
        inlinePairs: state.inlinePairs,
        inlineList: state.inlineList,
        literal: state.literal,
        escaped: state.escaped,
        contentField: state.contentField,
        contentModeState: mode ? CodeMirror.copyState(mode, state.contentModeState) : null,
        indentation: state.indentation,
        processLine: state.processLine
      }
    },

    token: function(stream, state) {
      var ch = stream.peek();
      var esc = state.escaped;
      state.escaped = false;
      state.indentation = stream.indentation();

      if (state.processLine) {
        if (stream.sol()) {
          state.processLine = false;
        } else {
          return processContentField(state, stream);
        }
      }

      /* comments */
      if (ch == "#" && (stream.pos == 0 || /\s/.test(stream.string.charAt(stream.pos - 1)))) {
        stream.skipToEnd();
        return "comment";
      }

      if (stream.match(/^('([^']|\\.)*'?|"([^"]|\\.)*"?)/))
        return "string";

      if (state.literal && stream.indentation() > state.keyCol) {
        if (state.contentField) {
          state.processLine = true;
          return processContentField(state, stream);
        }
        stream.skipToEnd(); return "string";
      } else if (state.literal) { state.literal = false; }
      if (stream.sol()) {
        state.keyCol = 0;
        state.pair = false;
        state.pairStart = false;
        /* document start */
        if(stream.match(/---/)) { return "def"; }
        /* document end */
        if (stream.match(/\.\.\./)) { return "def"; }
        /* array list item */
        if (stream.match(/\s*-\s+/)) { return 'meta'; }
      }
      /* inline pairs/lists */
      if (stream.match(/^(\{|\}|\[|\])/)) {
        if (ch == '{')
          state.inlinePairs++;
        else if (ch == '}')
          state.inlinePairs--;
        else if (ch == '[')
          state.inlineList++;
        else
          state.inlineList--;
        return 'meta';
      }

      /* list separator */
      if (state.inlineList > 0 && !esc && ch == ',') {
        stream.next();
        return 'meta';
      }
      /* pairs separator */
      if (state.inlinePairs > 0 && !esc && ch == ',') {
        state.keyCol = 0;
        state.pair = false;
        state.pairStart = false;
        state.contentField = false;
        state.contentModeState = null;
        stream.next();
        return 'meta';
      }

      /* start of value of a pair */
      if (state.pairStart) {
        /* block literals */
        if (stream.match(/^\s*((\||\>)(\+|-)?)\s*/)) { state.literal = true; return 'meta'; }
        /* references */
        if (stream.match(/^\s*(\&|\*)[a-z0-9\._-]+\b/i)) { return 'variable-2'; }
        /* numbers */
        if (state.inlinePairs == 0 && stream.match(/^\s*-?[0-9\.\,]+\s?$/)) { return 'number'; }
        if (state.inlinePairs > 0 && stream.match(/^\s*-?[0-9\.\,]+\s?(?=(,|}))/)) { return 'number'; }
        /* keywords */
        if (stream.match(keywordRegex)) { return 'keyword'; }
      }

      /* pairs (associative arrays) -> key */
      if (!state.pair) {
        var match = stream.match(paramPattern);
        if (match) {
          state.processLine = false;
          if (match[0].match(contentFieldsRegex)) {
            state.contentField = MARKDOWN;
          } else if (match[0].match(contentFieldsHtmlRegex)) {
            state.contentField = HTML;
          } else {
            state.contentField = false;
            state.contentModeState = null;
          }
          if (state.contentField) {
            state.contentModeState = CodeMirror.startState(curMode(state))
          }
          state.pair = true;
          state.keyCol = stream.indentation();
          return "atom";
        }
      }
      if (state.pair && stream.match(/^:\s*/)) {
        state.pairStart = true;
        state.processLine = !stream.match(/^\s*(\||\>)\s*/, false) && !!state.contentField;
        return 'meta';
      }

      /* nothing found, continue */
      state.pairStart = false;
      state.escaped = (ch == '\\');
      stream.next();
      return null;
    }
  };
});
});
