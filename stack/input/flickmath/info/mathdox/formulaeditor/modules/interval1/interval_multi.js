$identify("org/mathdox/formulaeditor/modules/interval1/interval_multi.js");

$require("org/mathdox/formulaeditor/Options.js");
$require("org/mathdox/formulaeditor/presentation/Boxed.js");
$require("org/mathdox/formulaeditor/presentation/Bracket.js");
$require("org/mathdox/formulaeditor/presentation/Bracketed.js");
$require("org/mathdox/formulaeditor/presentation/PseudoRow.js");
$require("org/mathdox/formulaeditor/presentation/Row.js");
$require("org/mathdox/formulaeditor/presentation/Symbol.js");
$require("org/mathdox/formulaeditor/semantics/MultaryListOperation.js");

$main(function(){

  /**
   * Defines a semantic tree node that represents an interval.
   */
  org.mathdox.formulaeditor.semantics.Interval1Interval_multi =
    $extend(org.mathdox.formulaeditor.semantics.MultaryListOperation, {

      /* to be filled in by extending classes */
      symbol : null,
      leftOpen: null,
      rightOpen: null,
      className: null,

      getPresentation: function (context) {
        var presentation = org.mathdox.formulaeditor.presentation;
        var semantics = org.mathdox.formulaeditor.semantics;

	var contents = [];
	var children = [];
	var child;

	var option = context.optionInterval1Brackets;

	var bracket;

	if (this.leftOpen) {
	  bracket = option.lo;
	} else {
	  bracket = option.lc;
	}

	var left = new presentation.Bracket(bracket);

	child = new presentation.Row(this.operands[0].getPresentation(context));
	children.push(child);
	contents.push(child);

	/* use the fixed list separator string from the context */
	var listSep = context.listSeparatorFixed;

	var i;
	for (i = 0; i<listSep.length; i++) {
	  contents.push(new presentation.Symbol(listSep.charAt(i)));
	}

	child = new presentation.Row(this.operands[1].getPresentation(context));
	children.push(child);
	contents.push(child);

	if (this.rightOpen) {
	  bracket = option.ro;
	} else {
	  bracket = option.rc;
	}

        var right = new presentation.Bracket(bracket);
	var prow = new presentation.PseudoRow();
	prow.initialize.apply(prow, contents);

	var row = new presentation.Row(new presentation.Bracketed(left, prow, right));

	return new presentation.Boxed(semantics[this.className], children, row);
      }

    });
});
