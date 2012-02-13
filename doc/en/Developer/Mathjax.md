# Displaying mathematics in moodle #

STACK generates LaTeX code on the fly and expects a moodle filter to convert this into something the user's browser will display.  There are a variety of mathematics filters for moodle, but STACK currently has been tuned to work with MathJax, <http://www.mathjax.org>.

These instructions are adapted from http://moodle.org/mod/forum/discuss.php?d=193064

### Option 1: link to external MathJax setup. ###

If you want to use MathJax with all themes of your moodle 2.x.x the easiest way is to include it in the head of every page.

1. Admin -> Appearance -> Additional HTML -> Within HEAD
2. Put the script in it and save

    <script type="text/x-mathjax-config">
      MathJax.Hub.Config({
        MMLorHTML: { prefer: "HTML" },
    	tex2jax: {displayMath: [ ['$$','$$'], ["\\[","\\]"] ], inlineMath: [ ['$','$'], ["\\(","\\)"]]}
      });
    </script>
    <script type="text/javascript" src="http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS_HTML"></script>

Please note that this enables both types of LaTeX maths environments.

### Option 2: install MathJax locally. ###

1. Download MathJax
2. Rename folder "mathjax".
3. Place the (mathjax) folder in .../moodle/lib
4. Edit the script above to reflect your path to mathjax and repeat the above procedure.

### What about mathml? ###

STACK contains experimental code to generate presentation mathml on the fly from maxima expressions.  This is currently unused, and may be pursued again in a future version.
