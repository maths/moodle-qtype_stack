<?php
// This file is part of Stack - http://stack.bham.ac.uk/
//
// Stack is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Stack is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stack.  If not, see <http://www.gnu.org/licenses/>.


/**
 * STACK maths output methods for using MathJax.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_maths_output_mathjax extends stack_maths_output {

    /**
     * @return string code that should be pasted into Admin -> Appearance ->
     * Additional HTML -> Head code to make MathJax work the way STACK expects.
     */
    public static function get_mathjax_code() {
        return <<<END
<script type="text/x-mathjax-config">
MathJax.Hub.Config({
    MMLorHTML: { prefer: "HTML" },
    tex2jax: {
        displayMath: [['\\\\[', '\\\\]']],
        inlineMath:  [['\\\\(', '\\\\)']],
        processEscapes: true
    },
    TeX: { extensions: ['enclose.js'] }
});
</script>
<script type="text/javascript" src="http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS_HTML"></script>
END;
    }
}
