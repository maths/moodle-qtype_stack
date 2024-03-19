$identify("org/mathdox/formulaeditor/modules/editor1/palette.js");

$package("org.mathdox.formulaeditor.semantics");

$require("org/mathdox/formulaeditor/modules/keywords.js");
$require("org/mathdox/formulaeditor/presentation/PArray.js");
$require("org/mathdox/formulaeditor/presentation/PTabContainer.js");
$require("org/mathdox/formulaeditor/semantics/MultaryListOperation.js");

$main(function(){
  /**
   * Define a semantic tree node that represents the editor1.palette
   */
  org.mathdox.formulaeditor.semantics.Editor1Palette =
    $extend(org.mathdox.formulaeditor.semantics.MultaryListOperation, {

    symbol : {
      mathml   : ["<mtr><mtd>","</mtd><mtd>","</mtd></mtr>"],
      onscreen : ["[", ",", "]"],
      openmath : "<OMS cd='editor1' name='palette_row'/>"
    },
    
    precedence : 0,

    getPresentation : function(context) {
      var tabs=[];
      var i;

      for (i=0;i<this.operands.length;i++) {
        tabs.push(this.operands[i].getPresentation(context));
      }

      var result = new org.mathdox.formulaeditor.presentation.PTabContainer();

      result.initialize.apply(result,tabs);

      return result;
    }
  });

  /**
   * Define a semantic tree node that represents the editor1.palette_row
   */
  org.mathdox.formulaeditor.semantics.Editor1Palette_row =
    $extend(org.mathdox.formulaeditor.semantics.MultaryOperation, {
    
    symbol : {
      mathml   : ["<mtr><mtd>","</mtd><mtd>","</mtd></mtr>"],
      onscreen : ["[", ",", "]"],
      openmath : "<OMS cd='editor1' name='palette_row'/>"
    },
    
    precedence : 0

  });

  /**
   * Define a semantic tree node that represents the editor1.palette_tab
   */
  org.mathdox.formulaeditor.semantics.Editor1Palette_tab =
    $extend(org.mathdox.formulaeditor.semantics.MultaryOperation, {
  
    symbol : {
      mathml   : ["<mtable>","","</mtable>"],
      onscreen : ["[", ",", "]"],
      openmath : "<OMS cd='editor1' name='palette'/>"
    },
    
    precedence : 0,

    title : null,

    getPaletteEntry : function(context,semanticEntry) {
      var modifiedContext = {};
      for (var name in context) {
        modifiedContext[name] = context[name];
      }
      modifiedContext.inPalette = true;

      if (semanticEntry === null || semanticEntry === undefined) {
        semanticEntry = org.mathdox.formulaeditor.parsing.openmath.KeywordList["editor1__palette_whitespace"];
      }
      
      // presentation for the palette
      var entry = semanticEntry.getPresentation(modifiedContext);
      entry.semanticEntry = semanticEntry;
      // add presentation to insert
      entry.insertablePresentation = function() { 
        return this.semanticEntry.getPresentation(context);
      };
      // add function to insert, XXX possibly add library function to
      // presentation node ?
      entry.insertCopy = function(position) {

        if (this.insertablePresentation === null || 
            this.insertablePresentation === undefined ) {
          return; // nothing to insert
        }

        var toInsert = this.insertablePresentation();

        for (var i=0;i<toInsert.children.length;i++) {
          //alert("inserting: "+i+" : "+toInsert.children[i]);

          var moveright = position.row.insert(position.index, 
            toInsert.children[i], (i === 0));
          if (moveright) {
            position.index++;
          }
        }
      };
      return entry;

    },

    getPresentation : function(context) {
      var rows = [];
      var row; // counter
      var col; // counter
      for (row=0;row<this.operands.length;row++) {
        var cols = [];
        for (col=0;col<this.operands[row].operands.length;col++) {
          // semantic version of the entry
          var semanticEntry = this.operands[row].operands[col];
          
          // presentation for the palette
          var entry = this.getPaletteEntry(context,semanticEntry);

          cols.push(entry);
        }
        rows.push(cols);  
      }
      // calculate the (maximum) number of columns
      var numcols = 0;
      for (row=0;row<rows.length;row++){
        if (numcols < rows[row].length) {
          numcols = rows[row].length;
        }
      }
      for (row=0;row<rows.length;row++) {
        if (rows[row].length<numcols) {
          cols = rows[row];
          for (col = cols.length; col<numcols; col++) {
            cols.push(this.getPaletteEntry(context));
          }
        }
      }
      
      var pArray = new org.mathdox.formulaeditor.presentation.PArray();
      pArray.margin = 10.0;
      pArray.initialize.apply(pArray,rows);

      return pArray;
    }, 

    initialize : function() {
      if (arguments[0] instanceof org.mathdox.formulaeditor.semantics.Editor1Palette_tabname) {
        this.title = arguments[0];
	/* arguments is not really an array, arguments.slice(1) has to be done in a different way */
        this.operands = Array.prototype.slice.call(arguments,[1]);
      } else {
        this.title = null;
        this.operands = arguments;
      }
    }
  });

  org.mathdox.formulaeditor.semantics.Editor1Palette_tabname =
    $extend(org.mathdox.formulaeditor.semantics.MultaryListOperation, {

    symbol : {
      mathml   : ["<mtr><mtd>","","</mtd></mtr>"],
      onscreen : ["", "" , ""],
      openmath : "<OMS cd='editor1' name='palette_tabname'/>"
    },
    
    precedence : 0,
  });

  // XXX todo parse parsing palette/palette_row in OM
  /**
   * Extend the OpenMathParser object with parsing code for editor1.palette
   */
  org.mathdox.formulaeditor.parsing.openmath.OpenMathParser =
    $extend(org.mathdox.formulaeditor.parsing.openmath.OpenMathParser, {

    /**
     * Returns a Editor1Palette object based on the OpenMath node.
     */
    handleEditor1Palette : function(node) {

      // parse the operands of the OMA
      var children = node.childNodes;
      var operands = [];
      var child;
      for (var i=1; i<children.length; i++) {
        child = this.handle(children.item(i));
        if (child !== null) {
          // not a comment
          operands.push(child);
        }
      }

      var result = new org.mathdox.formulaeditor.semantics.Editor1Palette();
      /* check if the first child is a palette tab */
      child = children.item(1);
      if ((child !==null) && (child !== undefined) &&
        (child.localName=="OMA")) {
        child = child.childNodes.item(0);
      }
      if ((child !==null) && (child !== undefined) &&
        (child.localName=="OMS") &&
        (child.getAttribute("cd") == "editor1") &&
        (child.getAttribute("name") == "palette_tab")) {

        // first child is a tab
        // construct a Editor1Palette object
        result.initialize.apply(result,operands);
      } else {
        // first child is not a tab : construct a Editor1Palette object with a
        // single tab
        var tab = new org.mathdox.formulaeditor.semantics.Editor1Palette_tab();

        tab.initialize.apply(tab,operands);

        result.initialize.apply(result,[tab]);
      }

      return result;
    },

    /**
     * Returns a Editor1Palette_row object based on the OpenMath node.
     */
    handleEditor1Palette_row : function(node) {

      // parse the children of the OMA
      var children = node.childNodes;
      var operands = [];
      for (var i=1; i<children.length; i++) {
        operands.push(this.handle(children.item(i)));
      }

      // construct a Editor1Palette_row object
      var result = new org.mathdox.formulaeditor.semantics.Editor1Palette_row();
      result.initialize.apply(result,operands);
      return result;

    },

    /**
     * Returns a Editor1Palette_tab object based on the OpenMath node.
     */
    handleEditor1Palette_tab : function(node) {

      // parse the children of the OMA
      var children = node.childNodes;
      var operands = [];
      for (var i=1; i<children.length; i++) {
        operands.push(this.handle(children.item(i)));
      }

      // construct a Editor1Palette_row object
      var result = new org.mathdox.formulaeditor.semantics.Editor1Palette_tab();
      result.initialize.apply(result,operands);
      return result;

    },

    /**
     * Returns a Editor1Palette_tabname object based on the OpenMath node.
     */
    handleEditor1Palette_tabname : function(node) {

      // parse the children of the OMA
      var children = node.childNodes;
      var operands = [];
      for (var i=1; i<children.length; i++) {
        operands.push(this.handle(children.item(i)));
      }

      // construct a Editor1Palette_tabname object
      var result = new org.mathdox.formulaeditor.semantics.Editor1Palette_tabname();
      result.initialize.apply(result,operands);
      return result;
    }
  });
});
