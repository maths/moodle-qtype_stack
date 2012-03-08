PHP Markdown Extra Math is an extension of Michel Fortin's [PHP Markdown Extra][1], a PHP script for converting text written in [Markdown][2] to HTML. The extension consist of adding support for mathematical equations written in LaTeX to be processed by either the [MathJax][4] (default) or [jsMath][3] math rendering libraries.

## Use ##

Here's how it works. The author, writing in Markdown, inserts inline equations like this

    where \(\alpha = (t_1 - t_0)/L\) is the rate at which the thickness increases

enclosing the math in a `\( … \)` pair, just as if writing in LaTeX. PHP Markdown Extra Math converts that to

    where <span class="MathJax_Preview">[math]</span><script type="math/tex"> \alpha = (t_1 - t_0)/L </script> is the rate at which the thickness increases

for MathJax, or

    where <span class="math"> \alpha = (t_1 - t_0)/L </span> is the rate at which the thickness increases

for jsMath. This is then converted by whichever math rendering library is being used to

![inline math example](http://www.leancrew.com/all-this/images/math-inline-example.png)

(The example is shown as an image but will be rendered as selectable and scalable text if the server has MathJax or jsMath installed.)

Similarly, display Math is enclosed in `\[ … \]` like this:

    Putting this into Castigliano's equation, we get
    
    \[\Delta = \frac{\partial U^*}{\partial F} = \frac{12F}{Eb} \int_0^L \frac{x^2}{(t_0 + \alpha x)^3} dx\]

which PHP Markdown Extra Math will turn into this HTML

    <p>Putting this into the Castigliano equation, we get</p>

    <span class="MathJax_Preview">[math]</span><script type="math/tex; mode=display">\Delta = \frac{\partial U^*}{\partial F} = \frac{12F}{Eb} \int_0^L \frac{x^2}{(t_0 + \alpha x)^3} dx</script>

for MathJax, or

    <p>Putting this into the Castigliano equation, we get</p>

    <div class="math">\Delta = \frac{\partial U^*}{\partial F} = \frac{12F}{Eb} \int_0^L \frac{x^2}{(t_0 + \alpha x)^3} dx</div>

for jsMath. This will be rendered as:

![display math example](http://www.leancrew.com/all-this/images/math-display-example.png)

## Configuration ##

The choice of MathJax or jsMath output is made on Line 42 of `markdown.php`:

    @define( 'MARKDOWN_MATH_TYPE',      "mathjax" );

Change the "mathjax" to "jsmath" to change the rendering library.

## A MathJax detail ##
The `<span class="MathJax_Preview">[…]</span>` that precedes the MathJax code serves three purposes:

1. It's a placeholder that's displayed briefly while the equations are rendered.
2. It eliminates [a rendering problem associated with Internet Explorer 8][5].
3. It gives Instapaper users a sense of what the rendered equation would be (if they understand LaTeX).

## License ##

PHP Markdown Extra Math is licensed under the same terms as PHP Markdown Extra. See the License.text file.

[1]: http://michelf.com/projects/php-markdown/extra/
[2]: http://daringfireball.net/projects/markdown/
[3]: http://www.math.union.edu/~dpvc/jsMath/
[4]: http://www.mathjax.org/
[5]: http://www.mathjax.org/resources/docs/?options/hub.html
