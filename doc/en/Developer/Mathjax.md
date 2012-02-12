
STACK expects displayed mathematics to be double dollars and inline mathematics to be single dollars.

Instructions adapted from http://moodle.org/mod/forum/discuss.php?d=193064

# Option 1: link to external MathJax setup.

Configuration with moodle 2.x.x
If you want to use MathJax with all themes of your moodle 2.X.X the easiest way is to go to
1. Admin -> Appearance -> Additional HTML -> Within HEAD
2. Put Attached script in it (replace path of your MathJax with it)
3. Save

    <script type="text/x-mathjax-config">
      MathJax.Hub.Config({
        MMLorHTML: { prefer: "HTML" },
    	tex2jax: {displayMath: [['$$','$$']],  inlineMath: [['$','$']] }
      });
    </script>
    <script type="text/javascript" src="http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS_HTML"></script>

# Option 2: install MathJax locally.

1.Download MathJax
2.Rename folder "mathjax".
3.Place the (mathjax) folder at ...../moodle/lib
4.Edit the script above to reflect your path to mathjax and repeat the above procedure.

