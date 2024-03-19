/**
 * Loads Google's Explorer Canvas library, when there is no native support for
 * the <canvas/> tag.
 */
$identify("org/mathdox/formulaeditor/Canvas.js");

var G_vmlCanvasManager;
if (!window.CanvasRenderingContext2D) {

  $require(
    "com/google/code/excanvas/excanvas.js",
    function() { return window.CanvasRenderingContext2D });

  $main(function(){

    /**
     * Workaround for bug in Google's Explorer Canvas. The standard fixElement
     * method will remove all siblings following the canvas tag. In addition to
     * not executing the fixElement of Explorer Canvas, this method wraps any
     * canvas element in a span inside a table, to work around the fact that
     * Internet Explorer doesn't like VML groups (which are used by Explorer
     * Canvas) inside block elements, such as <p>'s. See also:
     * http://keithdevens.com/weblog/archive/2006/Oct/18/invalid-source-html
     */
    G_vmlCanvasManager.fixElement_ = function(element) {

        if (element.tagName.toLowerCase() == "canvas") {

          // create table element
          var table = document.createElement("table");
          table.style.display = "inline";
          table.style.verticalAlign = element.style.verticalAlign;

          // create span element, and insert it into the table
          var span  = document.createElement("span");
          table.insertRow(0).insertCell(0).appendChild(span);

          // replace the canvas element by the table
          element.parentNode.replaceChild(table, element);

          // insert the canvas element into the span
          span.appendChild(element);

        }

        return element;

    };

    /**
     * Because the script tag that references excanvas.js file is dynamically
     * added to the DOM after the document is already loaded, it is necessary
     * to call the initialization code explicitly, instead of letting it wait
     * forever on the 'onreadystatechange' event.
     */
    if (document.readyState && document.readyState == "complete") {
      G_vmlCanvasManager.init_(document);
    }

  });

}
else {

  $main(function(){
    // skip
  });

}
