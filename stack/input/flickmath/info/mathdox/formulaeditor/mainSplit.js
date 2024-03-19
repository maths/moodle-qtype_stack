(function(){

  var baseurl = "";
  var lastadded;

  var scripts = document.getElementsByTagName("script");
  var scriptfinder1 = /^(.*)org\/mathdox\/formulaeditor\/main\.js/;
  var scriptfinder2 = /^(http:\/\/[^/]*mathdox\.org\/formulaeditor\/)main.js/;
  for (var i=0; i<scripts.length; i++) {
    var url = scripts[i].src;
    var match = url.match(scriptfinder1) || url.match(scriptfinder2);
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
    lastadded.parentNode.insertBefore(script, lastadded.nextSibling);
    lastadded = script;
  };

  addScript("org/mathdox/javascript/core.js");
  addScript("org/mathdox/formulaeditor/OrbeonForms.js");

})();
