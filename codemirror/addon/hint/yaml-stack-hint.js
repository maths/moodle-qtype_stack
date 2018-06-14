// CodeMirror, copyright (c) by Marijn Haverbeke and others
// Distributed under an MIT license: http://codemirror.net/LICENSE

(function(mod) {
  if (typeof exports == "object" && typeof module == "object") // CommonJS
    mod(require("../../lib/codemirror"));
  else if (typeof define == "function" && define.amd) // AMD
    define(["../../lib/codemirror"], mod);
  else // Plain browser env
    mod(CodeMirror);
})(function(CodeMirror) {
  "use strict";

  var Pos = CodeMirror.Pos;

  function updateResult(result, childList, match) {
    for (var i = 0; i < childList.length; ++i) {
      if (childList[i] !== '*' && childList[i].indexOf(match) === 0) {
        result.push(childList[i]);
      }
    }
  }

  function findTag(tags, param) {
    var keys = [];
    var keysFiltered = [];
    for(var k in tags) keys.push(k);

    var paramKeyLength = param.split('->').length;

    for (var index in keys) {
      if (keys[index].split('->').length === paramKeyLength) {
        keysFiltered.push(keys[index]);
      }
    }

    keysFiltered.sort(function (a, b) {
      var aLength = param.indexOf(a) === 0 ? a.length : -1;
      var bLength = param.indexOf(b) === 0 ? b.length : -1;
      return bLength - aLength;
    });
    return keysFiltered[0];
  }

  function getHints(cm, options) {
    var tags = options && options.schemaInfo;
    if (!tags) return;

    var cur = cm.getCursor(), token = cm.getTokenAt(cur);
    var inner = CodeMirror.innerMode(cm.getMode(), token.state);

    if (inner.mode.name !== "yaml-stack") return;

    var result = [];

    var before = cm.getLine(cur.line).substr(0, cur.ch);
    if (inner.state.literal) {
      var litIndent = before.match(/^\s+$/) ? before.length : inner.state.indentation;
      if (litIndent > inner.state.keyCol) {
        return;
      }
    }

    var match = before.match(/^\s*(\w*)$/);
    if (!match) {
      return;
    }
    var replaceToken = !!match[1];
    var indent = Math.min(cur.ch, inner.state.indentation);
    var childList = [];
    var sequence = [];
    if (indent === 0) {
      childList = tags['root'].children;
      updateResult(result, childList, match[1]);
    } else {
      var resFound = false;
      var params = [];
      var prevLineIndex = cur.line;
      var prevLineIndent = 0;
      while (prevLineIndex > 0) {
        prevLineIndex--;
        prevLineIndent = cm.getTokenAt(Pos(prevLineIndex, Number.MAX_SAFE_INTEGER)).state.indentation;
        if (prevLineIndent < indent) {
          indent = prevLineIndent;
          var prevLine = cm.getLine(prevLineIndex);
          var paramMatch = prevLine.match(/^\s*(\w*):/);
          if (paramMatch) {
            params.push(paramMatch[1]);
            var matchedParam = paramMatch[1];
            sequence.unshift(tags[matchedParam] ? matchedParam : null);
            if (tags[matchedParam]) {
              var tagToFind = matchedParam;
              if (params.length > 1) {
                tagToFind = findTag(tags, params.reverse().join('->'));
              }
              sequence.splice(0, params.length, tagToFind);
              params = [];
              if (!resFound) {
                childList = tagToFind ? tags[tagToFind].children : [];
                updateResult(result, childList, match[1]);
                resFound = true;
              }
            }
          }
        }
      }
    }
    var lastSeqItem = sequence.length !== 0 && sequence[sequence.length - 1];
    var tagSeq = lastSeqItem && tags[lastSeqItem] && tags[lastSeqItem].sequence;
    if (tagSeq && tagSeq !== sequence.join(';')) {
      return;
    }
    return {
      list: result.sort(),
      from: replaceToken && match ? Pos(cur.line, cur.ch - match[1].length) : cur,
      to: replaceToken && match ? Pos(cur.line, token.end) : cur
    };
  }

  CodeMirror.registerHelper("hint", "yaml-stack", getHints);
});
