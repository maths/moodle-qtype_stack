$package("org.mathdox.formulaeditor");

$identify("org/mathdox/formulaeditor/version.js");

$main(function(){
  /**
  * Version object (static)
  */

  org.mathdox.formulaeditor.version = {
    /**
     * Show an "About" popup with version info
     */
    showAboutPopup: function() {
      alert("MathDox Formulaeditor\n"+
	    "version: "+this.toString()+"\n"+
	    "http://mathdox.org/formulaeditor/\n"+
	    "info@mathdox.org");
    },
    /**
     * Return the version as a string
     */
    toString : function() {
      return this.versionInfo;
    },
    /**
     * variable containing the version information
     */
    versionInfo: "1.1.31f"
  };
});
