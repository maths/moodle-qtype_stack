$package("org.mathdox.parsing");

$identify("org/mathdox/parsing/Parser.js");

$main(function(){

  org.mathdox.parsing.Parser = $extend(Object, {

    start : null,

    parse : function(string, index, backward, start) {

      if (index === null || index === undefined) {
        index = 0;
      }
      if (backward === null || backward === undefined) {
        backward = false;
      }
      if (start === null || start === undefined) {
        start = "start";
      }

      var context = {
        input    : string,
        backward : backward,
        parser   : this,
        cache    : {}
      };

      var endIndex  = null;
      var endResult = null;

      var continuation = function(newIndex, newResult) {
        if (context.backward) {
          if (endIndex === null || newIndex < endIndex) {
            endIndex  = newIndex;
            endResult = newResult;
          }
        }
        else {
          if (endIndex === null || newIndex > endIndex) {
            endIndex  = newIndex;
            endResult = newResult;
          }
        }
      };

      this[start](context, index, [], continuation);

      if (endResult !== null && endResult.length == 1) {
        endResult = endResult[0];
      }

      return { index: endIndex, value : endResult };

    }

  });

});
