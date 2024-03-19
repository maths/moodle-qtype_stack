$package("org.mathdox.formulaeditor");

$identify("org/mathdox/formulaeditor/Services.js");

// load XMLHttpRequest methods
$require("com/oreilly/javascript/tdg/XML.js");
$require("com/oreilly/javascript/tdg/XMLHttp.js");

$main(function(){

  /**
   * Class that represents calls to services
   */
  org.mathdox.formulaeditor.Services = {
    url : "/phrasebook/",

    perform : function (service, action, data, callback) {
      var HTTP = com.oreilly.javascript.tdg.XMLHttp;
      var xmlValueOf = this.xmlValueOf;
      var onreturn = function(result) {
        /* 
          check if the result was ok 
          if it was call the callback function with the data
          otherwise show an alert window with the error message
        */
	var xmlData = com.oreilly.javascript.tdg.XML.parse(result);
	
        var statusNodeList = xmlData.documentElement.getElementsByTagName(
	  "status");
        if (statusNodeList.length === 0) {
          alert("Error: no status element found in service response");
          return;
        }
        var statusText = xmlValueOf(statusNodeList.item(0));

        if (statusText != "ok") { /* error */
          var errorNodeList = xmlData.documentElement.getElementsByTagName(
	    "error");
          var errorText = xmlValueOf(errorNodeList.item(0));
        
          alert("ERROR (while using service " + service + "/" + action + 
            "): " + errorText);
          return;
        } 
        
        /* everything went ok */
        var resultNodeList = xmlData.documentElement.getElementsByTagName(
	  "data");

        callback(xmlValueOf(resultNodeList.item(0)));
      };
      var values = {
        output: "xml",
        service: service,
        action: action,
        data: data
      };
      HTTP.post(this.url, values, onreturn); 
    },

    openmath2gap : function(openmath, callback) {
      return this.perform("gap", "translate_openmath_native", openmath, 
        callback);
    },

    /* 
      function like xsl:value-of, converts an XML element to the contained 
      text 
    */
    xmlValueOf : function(node) {
      var i;
      var buffer = []; // use a buffer for efficiency
     
      switch (node.nodeType) {
        case 1: // ELEMENT_NODE
          for (i=0; i<node.childNodes.length; i++) {
            buffer.push(arguments.callee(node.childNodes[i]));
          }
          return buffer.join("");
        case 2: // ATTRIBUTE_NODE
        case 3: // TEXT_NODE
        case 4: // CDATA_SECTION_NODE
          return node.nodeValue;
        case 7: // PROCESSING_INSTRUCTION_NODE
          return "";
        case 8: // COMMENT_NODE
          return "";
        case 9: // DOCUMENT_NODE
          return arguments.callee(node.Element);
        case 10: // DOCUMENT_TYPE_NODE
          return "";
        case 11: // DOCUMENT_FRAGMENT_NODE
          return "";
      }
      return "";
    }
  };
});

