$package("org.mathdox.formulaeditor.parsing.xml");

$identify("org/mathdox/formulaeditor/parsing/xml/XMLParser.js");

$main(function(){

  org.mathdox.formulaeditor.parsing.xml.XMLParser = $extend(Object, {

    name: "XMLParser",

    /**
     * Parses the supplied OpenMath xml, and returns a
     * org.mathdox.formulaeditor.semantics.Node.
     */
    parse: function(xml, context) {
      var rootnode;
      var xmlDoc;

      if (window.DOMParser)
      {
        parser=new DOMParser();
        xmlDoc=parser.parseFromString(xml,"text/xml");
      } else {
        // XXX: old Internet Explorer
        // test in IE 8 without this to see if we can remove legacy code

        xmlDoc=new ActiveXObject("Microsoft.XMLDOM");
        xmlDoc.async=false;
        xmlDoc.loadXML(xml); 
      } 
      rootnode = xmlDoc.documentElement;

      /* remove comment nodes, since we don't want to parse them */
      if (rootnode !== null) {
        this.removeComments(rootnode);
        this.removeWhitespace(rootnode);
      } else {
        return null;
      }
      
      /* do the actual parsing */
      if (rootnode !== null) {
        return this.handle(rootnode, context);
      } else {
        return null;
      }

    },

    /**
     * Extracts the local name of the node, and uses that to figure out which
     * method should be called to handle this node. For instance, when an
     * <OMI> node is encountered, the handleOMI method is called.
     */
    handle: function(node, context) {
      if (node.localName === null) {
        // XML comment or text
        return null;
      }

      var handler = "handle" + node.localName;

      if (handler in this) {
	if (context !== null && context!== undefined) {
          return this[handler](node, context);
	} else {
          return this[handler](node);
	}
      }
      else {
        throw new Error( this.name+" doesn't know how to handle this "+
            "node: " + node +". INFO: 1.");
      }

    },

    /**
     * Removes all comment nodes from a DOM XML tree
     */
    removeComments: function(node) {
      var children = node.childNodes;

      for (var i=children.length - 1; i>=0; i--) {
        var child = children.item(i);

        if (child) {
          if (child.nodeType == 8) { // 8: COMMENT_NODE
            node.removeChild(child);
          } else if (child.hasChildNodes()) {
            this.removeComments(child);
          }
        }
      }
    },

    /**
     * Removes all whitespace text nodes from a DOM XML tree
     */
    removeWhitespace: function(node) {
      var children = node.childNodes;

      for (var i=children.length - 1; i>=0; i--) {
        var child = children.item(i);

        if (child) {
          if (child.nodeType == 3) { // 3: TEXT_NODE
            var value = child.nodeValue.trim();
	    if (value === "") {
              node.removeChild(child);
	    }
          } else if (child.hasChildNodes()) {
            this.removeWhitespace(child);
          }
        }
      }
    }

  });

});
