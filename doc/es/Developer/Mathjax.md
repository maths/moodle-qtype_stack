# Displaying mathematics in moodle #

STACK generates LaTeX code on the fly and expects a moodle filter to convert this into something the user's browser will display.  There are a variety of mathematics filters for moodle.  Currently when installing STACK there is the option to choose which filter mechanism is used.   This enables STACK to fine tune the output.  Support is currently provided for [MathJax](../Installation/Mathjax.md).  This page is to record design decisions made in developing STACK.

### How do I get special symbols? ###

This is really a question for MathJax, rather than STACK.  MathJax has a lot of options, and configuration settings and so your resulting system will largely depend on this.  See [MathJax](https://www.mathjax.org/) for more details.

For example, blackboard bold symbols can be typeset with \(\mathbb{C}\), which is typeset as `\(\mathbb{C}\)`.
For actuarial symbols, such as \(\require{enclose} EPV = a _{[25]+5:\enclose{actuarial}{30}} ^ {\space 1}\) see the [documentation](../Actuarial.md).

### What about mathml? ###

STACK contains experimental code to generate presentation mathml on the fly from maxima expressions.  This is currently unused, and may be pursued again in a future version.  Note that since question authors will be writing LaTeX mathematics fragments in many fields, having Maxima generate mathml is not sufficient.  We still need to convert user-entered mathematics in to mathml.


