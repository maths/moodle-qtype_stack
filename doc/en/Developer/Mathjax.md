# Displaying mathematics in moodle #

STACK generates LaTeX code on the fly and expects a moodle filter to convert this into something the user's browser will display.  There are a variety of mathematics filters for moodle, but STACK currently has been tuned to work with MathJax, <http://www.mathjax.org>.

One reason for adopting MathJax is that it speaks mathematics, so will aid [accessibility](../Students/Accessibility.md).

These instructions are adapted from http://moodle.org/mod/forum/discuss.php?d=193064

### Option 1: link to external MathJax setup. ###

If you want to use MathJax with all themes of your moodle 2.x.x the easiest way is to include it in the head of every page.

1. Admin -> Appearance -> Additional HTML -> Within HEAD
2. Put the following script in it and save

`<script type="text/x-mathjax-config"> MathJax.Hub.Config({`<br>
`        MMLorHTML: { prefer: "HTML" },`<br>
`        tex2jax: {`<br>
`            displayMath: [['$$', '$$'],['\\[', '\\]']],`<br>
`            inlineMath:  [['$',  '$' ],['\\(', '\\)']],`<br>
`            processEscapes: true`<br>
`        }`<br>
`      });`<br>
`</script>`<br>
`<script type="text/javascript" src="http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS_HTML"></script>`

Please note that this enables both types of LaTeX maths environments.

### Option 2: install MathJax locally. ###

A local installation may be preferable, e.g., if you want your STACK-equipped Moodle to work off-line, or if you want to try to speed up LaTeX rendering.

1. Download [MathJax](http://www.mathjax.org/)
2. Unpack the archive and rename folder to "mathjax".
3. Place the mathjax folder in .../moodle/lib and set appropriate ownership, e.g., `chown -R root.root mathjax`.
4. Carry out the procedure in Option 1, editing the script above to reflect your path to MathJax. The last line can resemble the following:<br>
`<script type="text/javascript" src="http://localhost/moodle/lib/mathjax/MathJax.js?config=TeX-AMS_HTML"></script>`

### What about mathml? ###

STACK contains experimental code to generate presentation mathml on the fly from maxima expressions.  This is currently unused, and may be pursued again in a future version.  Note that since question authors will be writing LaTeX mathematics fragments in many fields, having Maxima generate mathml is not sufficient.  We still need to convert user-entered mathematics in to mathml.

## Accessibility ##

The accessibility features supported by MathJax are given detailed [here](http://www.mathjax.org/resources/articles-and-presentations/accessible-pages-with-mathjax/).

