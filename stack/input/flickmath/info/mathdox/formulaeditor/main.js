(function(){

  var baseurl = "";
  var lastadded;

  var scripts = document.getElementsByTagName("script");
  var scriptfinder1 = /^(.*)info\/mathdox\/formulaeditor\/main\.js/;
  var scriptfinder2 = /^(http:\/\/[^/]*mathdox\.info\/formulaeditor\/)main.js/;
  for (var i=0; i<scripts.length; i++) {
    var url = scripts[i].src;
    var match = url.match(scriptfinder1);
    if (match === null) {
      match = url.match(scriptfinder2);
    }
    if (match !== null) {
      baseurl = match[1];
      lastadded = scripts[i];
      break;
    }
  }

  var addScript = function(url) {
    var script = document.createElement("script");
    script.type = "text/javascript";
    script.src = baseurl + url;
    script.charset = "utf-8";
    lastadded.parentNode.insertBefore(script, lastadded.nextSibling);
    lastadded = script;
  };

  addScript("info/mathdox/formulaeditor/FEConcatenation.js");

})();
