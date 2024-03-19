$package("org.mathdox.formulaeditor");

$identify("org/mathdox/formulaeditor/Palettes.js");

$main(function(){
  org.mathdox.formulaeditor.Palettes = {
    /**
     * The palettestring
     */
    defaultPalette: ""+
"<OMOBJ version='2.0' xmlns='http://www.openmath.org/OpenMath'>\n"+
"  <OMA>\n"+
"    <OMS cd='editor1' name='palette'/>\n"+
"    <OMA>\n"+
"      <OMS cd='editor1' name='palette_tab'/>\n"+
"      <OMA>\n"+
"        <OMS cd='editor1' name='palette_row'/>\n"+
"        <OMS cd='arith1' name='plus'/>\n"+
"        <OMS cd='arith1' name='minus'/>\n"+
"        <OMS cd='arith1' name='times'/>\n"+
"        <OMS cd='logic1' name='and'/>\n"+
"        <OMS cd='logic1' name='or'/>\n"+
"        <OMA>\n"+
"          <OMS cd='transc1' name='cos'/>\n"+
"          <OMS cd='editor1' name='input_box'/>\n"+
"        </OMA>\n"+
"        <OMA>\n"+
"          <OMS cd='arith1' name='root'/>\n"+
"          <OMS cd='editor1' name='input_box'/>\n"+
"          <OMI>2</OMI>\n"+
"        </OMA>\n"+
"        <OMA>\n"+
"          <OMS cd=\"calculus1\" name=\"int\"/>\n"+
"          <OMBIND>\n"+
"            <OMS cd=\"fns1\" name=\"lambda\"/>\n"+
"            <OMBVAR>\n"+
"              <OMV name=\"x\"/>\n"+
"            </OMBVAR>\n"+
"            <OMS cd='editor1' name='input_box'/>\n"+
"          </OMBIND>\n"+
"        </OMA>\n"+
"        <OMA>\n"+
"          <OMS cd=\"calculus1\" name=\"defint\"/>\n"+
"          <OMA>\n"+
"            <OMS cd=\"interval1\" name=\"interval\"/>\n"+
"            <OMS cd='editor1' name='input_box'/>\n"+
"            <OMS cd='editor1' name='input_box'/>\n"+
"          </OMA>\n"+
"          <OMBIND>\n"+
"            <OMS cd=\"fns1\" name=\"lambda\"/>\n"+
"            <OMBVAR>\n"+
"              <OMV name=\"x\"/>\n"+
"            </OMBVAR>\n"+
"            <OMS cd='editor1' name='input_box'/>\n"+
"          </OMBIND>\n"+
"        </OMA>\n"+
"        <OMS cd='editor1' name='palette_whitespace'/>\n"+
"        <OMA>\n"+
"          <OMS cd='interval1' name='interval_cc'/>\n"+
"  	  <OMS cd='editor1' name='input_box'/>\n"+
"  	  <OMS cd='editor1' name='input_box'/>\n"+
"        </OMA>\n"+
"      </OMA>\n"+
"      <OMA>\n"+
"        <OMS cd='editor1' name='palette_row'/>\n"+
"        <OMS cd='relation1' name='lt'/>\n"+
"        <OMS cd='relation1' name='leq'/>\n"+
"        <OMS cd='relation1' name='eq'/>\n"+
"        <OMS cd='relation1' name='geq'/>\n"+
"        <OMS cd='relation1' name='gt'/>\n"+
"        <OMA>\n"+
"          <OMS cd='transc1' name='sin'/>\n"+
"          <OMS cd='editor1' name='input_box'/>\n"+
"        </OMA>\n"+
"        <OMA>\n"+
"          <OMS cd='arith1' name='root'/>\n"+
"          <OMS cd='editor1' name='input_box'/>\n"+
"          <OMS cd='editor1' name='input_box'/>\n"+
"        </OMA>\n"+
"        <OMA>\n"+
"          <OMS cd=\"arith1\" name=\"product\"/>\n"+
"          <OMA>\n"+
"            <OMS cd=\"interval1\" name=\"integer_interval\"/>\n"+
"            <OMS cd='editor1' name='input_box'/>\n"+
"            <OMS cd='editor1' name='input_box'/>\n"+
"          </OMA>\n"+
"          <OMBIND>\n"+
"            <OMS cd=\"fns1\" name=\"lambda\"/>\n"+
"            <OMBVAR>\n"+
"              <OMV name=\"n\"/>\n"+
"            </OMBVAR>\n"+
"            <OMS cd='editor1' name='input_box'/>\n"+
"          </OMBIND>\n"+
"        </OMA>\n"+
"        <OMA>\n"+
"          <OMS cd=\"arith1\" name=\"sum\"/>\n"+
"            <OMA>\n"+
"              <OMS cd=\"interval1\" name=\"integer_interval\"/>\n"+
"              <OMS cd='editor1' name='input_box'/>\n"+
"              <OMS cd='editor1' name='input_box'/>\n"+
"            </OMA>\n"+
"          <OMBIND>\n"+
"            <OMS cd=\"fns1\" name=\"lambda\"/>\n"+
"            <OMBVAR>\n"+
"              <OMV name=\"n\"/>\n"+
"            </OMBVAR>\n"+
"            <OMS cd='editor1' name='input_box'/>\n"+
"          </OMBIND>\n"+
"        </OMA>\n"+
"        <OMA>\n"+
"          <OMS cd='linalg1' name='determinant'/>\n"+
"  	  <OMS cd='editor1' name='input_box'/>\n"+
"        </OMA>\n"+
"        <OMA>\n"+
"          <OMS cd='interval1' name='interval_co'/>\n"+
"  	  <OMS cd='editor1' name='input_box'/>\n"+
"  	  <OMS cd='editor1' name='input_box'/>\n"+
"        </OMA>\n"+
"      </OMA>\n"+
"      <OMA>\n"+
"        <OMS cd='editor1' name='palette_row'/>\n"+
"        <OMS cd='nums1' name='pi'/>\n"+
"        <OMS cd='nums1' name='e'/>\n"+
"        <OMS cd='nums1' name='i'/>\n"+
"        <OMS cd='nums1' name='infinity'/>\n"+
"        <OMS cd='editor1' name='palette_whitespace'/>\n"+
"        <OMA>\n"+
"          <OMS cd='transc1' name='tan'/>\n"+
"          <OMS cd='editor1' name='input_box'/>\n"+
"        </OMA>\n"+
"        <OMA>\n"+
"          <OMS cd='list1' name='list'/>\n"+
"          <OMS cd='editor1' name='input_box'/>\n"+
"        </OMA>\n"+
"        <OMA>\n"+
"          <OMS cd='linalg2' name='vector'/>\n"+
"          <OMS cd='editor1' name='input_box'/>\n"+
"          <OMS cd='editor1' name='input_box'/>\n"+
"        </OMA>\n"+
"        <OMA>\n"+
"          <OMS cd='linalg2' name='matrix'/>\n"+
"          <OMA>\n"+
"            <OMS cd='linalg2' name='matrixrow'/>\n"+
"            <OMS cd='editor1' name='input_box'/>\n"+
"            <OMS cd='editor1' name='input_box'/>\n"+
"          </OMA>\n"+
"          <OMA>\n"+
"            <OMS cd='linalg2' name='matrixrow'/>\n"+
"            <OMS cd='editor1' name='input_box'/>\n"+
"            <OMS cd='editor1' name='input_box'/>\n"+
"          </OMA>\n"+
"        </OMA>\n"+
"        <OMA>\n"+
"          <OMS cd='linalg2' name='matrix'/>\n"+
"          <OMA>\n"+
"            <OMS cd='linalg2' name='matrixrow'/>\n"+
"            <OMS cd='editor1' name='input_box'/>\n"+
"            <OMS cd='editor1' name='input_box'/>\n"+
"            <OMS cd='editor1' name='input_box'/>\n"+
"          </OMA>\n"+
"          <OMA>\n"+
"            <OMS cd='linalg2' name='matrixrow'/>\n"+
"            <OMS cd='editor1' name='input_box'/>\n"+
"            <OMS cd='editor1' name='input_box'/>\n"+
"            <OMS cd='editor1' name='input_box'/>\n"+
"          </OMA>\n"+
"        </OMA>\n"+
"        <OMA>\n"+
"          <OMS cd='interval1' name='interval_oc'/>\n"+
"  	  <OMS cd='editor1' name='input_box'/>\n"+
"  	  <OMS cd='editor1' name='input_box'/>\n"+
"        </OMA>\n"+
"      </OMA>\n"+
"      <OMA>\n"+
"        <OMS cd='editor1' name='palette_row'/>\n"+
"        <OMA>\n"+
"          <OMS cd='arith1' name='divide'/>\n"+
"          <OMS cd='editor1' name='input_box'/>\n"+
"          <OMS cd='editor1' name='input_box'/>\n"+
"        </OMA>\n"+
"        <OMA>\n"+
"          <OMS cd='arith1' name='power'/>\n"+
"          <OMS cd='editor1' name='input_box'/>\n"+
"          <OMS cd='editor1' name='input_box'/>\n"+
"        </OMA>\n"+
"        <OMA>\n"+
"          <OMS cd='arith1' name='abs'/>\n"+
"          <OMS cd='editor1' name='input_box'/>\n"+
"        </OMA>\n"+
"        <OMA>\n"+
"          <OMS cd='integer1' name='factorial'/>\n"+
"          <OMS cd='editor1' name='input_box'/>\n"+
"        </OMA>\n"+
"        <OMA>\n"+
"          <OMS cd='arith1' name='power'/>\n"+
"          <OMS cd='nums1' name='e'/>\n"+
"          <OMS cd='editor1' name='input_box'/>\n"+
"        </OMA>\n"+
"        <OMA>\n"+
"          <OMS cd='transc1' name='ln'/>\n"+
"          <OMS cd='editor1' name='input_box'/>\n"+
"        </OMA>\n"+
"        <OMA>\n"+
"          <OMS cd='transc1' name='log'/>\n"+
"          <OMI>10</OMI>\n"+
"          <OMS cd='editor1' name='input_box'/>\n"+
"        </OMA>\n"+
"        <OMA>\n"+
"          <OMS cd='linalg2' name='vector'/>\n"+
"          <OMS cd='editor1' name='input_box'/>\n"+
"          <OMS cd='editor1' name='input_box'/>\n"+
"          <OMS cd='editor1' name='input_box'/>\n"+
"        </OMA>\n"+
"        <OMA>\n"+
"          <OMS cd='linalg2' name='matrix'/>\n"+
"          <OMA>\n"+
"            <OMS cd='linalg2' name='matrixrow'/>\n"+
"            <OMS cd='editor1' name='input_box'/>\n"+
"            <OMS cd='editor1' name='input_box'/>\n"+
"          </OMA>\n"+
"          <OMA>\n"+
"            <OMS cd='linalg2' name='matrixrow'/>\n"+
"            <OMS cd='editor1' name='input_box'/>\n"+
"            <OMS cd='editor1' name='input_box'/>\n"+
"          </OMA>\n"+
"          <OMA>\n"+
"            <OMS cd='linalg2' name='matrixrow'/>\n"+
"            <OMS cd='editor1' name='input_box'/>\n"+
"            <OMS cd='editor1' name='input_box'/>\n"+
"          </OMA>\n"+
"        </OMA>\n"+
"        <OMA>\n"+
"          <OMS cd='linalg2' name='matrix'/>\n"+
"          <OMA>\n"+
"            <OMS cd='linalg2' name='matrixrow'/>\n"+
"            <OMS cd='editor1' name='input_box'/>\n"+
"            <OMS cd='editor1' name='input_box'/>\n"+
"            <OMS cd='editor1' name='input_box'/>\n"+
"          </OMA>\n"+
"          <OMA>\n"+
"            <OMS cd='linalg2' name='matrixrow'/>\n"+
"            <OMS cd='editor1' name='input_box'/>\n"+
"            <OMS cd='editor1' name='input_box'/>\n"+
"            <OMS cd='editor1' name='input_box'/>\n"+
"          </OMA>\n"+
"          <OMA>\n"+
"            <OMS cd='linalg2' name='matrixrow'/>\n"+
"            <OMS cd='editor1' name='input_box'/>\n"+
"            <OMS cd='editor1' name='input_box'/>\n"+
"            <OMS cd='editor1' name='input_box'/>\n"+
"          </OMA>\n"+
"        </OMA>\n"+
"        <OMA>\n"+
"          <OMS cd='interval1' name='interval_oo'/>\n"+
"  	  <OMS cd='editor1' name='input_box'/>\n"+
"  	  <OMS cd='editor1' name='input_box'/>\n"+
"        </OMA>\n"+
"      </OMA>\n"+
"    </OMA>\n"+
"  </OMA>\n"+
"</OMOBJ>\n"+
  ""};
});
