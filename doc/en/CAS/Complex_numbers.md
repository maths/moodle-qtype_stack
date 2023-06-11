# Complex Numbers in STACK

Complex numbers, especially the display of complex numbers, is a surprisingly subtle issue.   This is because there is some genuine ambiguity in whether \(a+\mathrm{i}\, b\) is a single object or the sum of two parts.  In mathematics we use this ambiguity to our advantage, but in online assesment we need to be more precise.  There are also issues of unary minus, e.g. _not_ displaying \(1 + (-2)\mathrm{i}\). Similarly we typically do _not_ display numbers like \(0+1\mathrm{i}\), unless of course we want to at which point we need the option to do so!

The general rules when displaying a complex number in Cartesian form "\(a+\mathrm{i}\, b\)" are

1. the real part should always appear to the left of the imaginary part;
2. \(i\) (or whatever symbol is being used for the imaginary unit) should appear on the right of its coefficient if and only if the coefficient is a numerical value. By numerical value, we mean something like \(2\sqrt{2}\pi\) but not things like \( a \pi \), even if \( a \) is a constant.

Some examples:

* `3+2*%i` should display as \(3+2\,\mathrm{i}\).
* `3-%i` should display as \(3-\mathrm{i}\).
* `-a+b*%i` should display as \(-a+\mathrm{i}\,b\).
* `-b*%i` should display as \(-\mathrm{i}\,b\) (not normally as \(0-\mathrm{i}\,b\)).

STACK provides two functions, one which simplifies and one which does not.

1. `display_complex(ex)` takes an expression `ex` and tries to display this as a complex number obeying the above rules.  In particular, this function makes use of Maxima's `realpart` and `imagpart` function to split up `ex` into real and imaginary parts.  To do this it must assume `simp:true`, and so the real and imaginary part will be simplified.  For example, `display_complex(1+2*%i/sqrt(2))` is displayed as \(1+\sqrt{2}\,\mathrm{i}\).  If you really want \(1+\frac{2}{\sqrt{2}}\,\mathrm{i}\) then you will need to use the non-simplifying alternative below.  This function respects normal conventions, e.g. when `realpart` returns zero this function will not print \(0+2\,\mathrm{i}\), it just prints \(2\,\mathrm{i}\), etc.  
2. `disp_complex(a, b)` assumes `a` is the real part and `b` is the imaginary part (no checking is done).  This function (mostly) does not simplify its arguments.  So `disp_complex(0, 2)` will appear as \(0+2\,\mathrm{i}\); `disp_complex(2/4, 1)` will appear as \(\frac{2}{4}+1\,\mathrm{i}\); and `disp_complex(2, 2/sqrt(2))` will appear as \(2+\frac{2}{\sqrt{2}}\,\mathrm{i}\).  Use the atom `null` if you do not want to print a zero for the real part, or print one times the imaginary part.  `disp_complex(null, 2)` will appear as \(2\,\mathrm{i}\) and `disp_complex(null, null)` will appear as just \(\mathrm{i}\).  Think of `null` as a non-printable unit (additive or multiplicative).

There is one exception.  In order to pull out a unary minus to the front, `disp_complex(a, b)` will simplify `b` if `b` is not a number and it contains a unary minus.  So, for example `disp_complex(a, (-b^2)/b)` is displayed \(a-\mathrm{i}\,b\).  (We _might_ be able to fix this but this edge case requires disproportionate effort: ask the developers if this is essential).

You cannot use these functions to display complex numbers in this form \(\frac{\mathrm{i}}{2}\), both these function will always display as \(\frac{1}{2}\,\mathrm{i}\).

Display respects the multiplication sign used elsewhere within expressions, so that you may have \(\frac{2\cdot \pi}{3}\,\mathrm{i}\) rather than \(\frac{2\, \pi}{3}\,\mathrm{i}\).

Note that the function `display_complex(ex)` returns the inert form `disp_complex(a, b)`.  The expression `disp_complex(a, b)` is an "inert form", which is only used to fine-tune the display.  This function is not actually defined and so Maxima always returns it unevaluated.  To remove the intert form from an expression, which is needed to manipulate this further, use `remove_disp_complex`, e.g., with the following.

    p1:disp_complex(a, b);
    p2:ev(p1, disp_complex=remove_disp_complex);

(Because `null` has two different meanings within an expression it isn't sufficient to just define `disp_complex(ex1, ex2) := ex1+ex2*%i`.)

There are occasions when you will need to explicitly add brackets to the displayed form of a complex number, e.g. to emphasise it is a single entity.  To add brackets there is a further "inert form" `disp_parens` which does nothing but add parentheses when the expression is displayed with the `tex()` function.  For example,

    p1:disp_parens(display_complex(1+%i))*x^2+disp_parens(display_complex(1-%i));

will display as \(\left( 1+\mathrm{i} \right)\cdot x^2+\left( 1-\mathrm{i} \right)\).  To remove these inert forms evaluate

    p2:ev(p1, disp_complex=remove_disp_complex, disp_parens=lambda([ex],ex));

You must remove inert forms before expressions are evaluated by the potential response tree, for example in the feedback variables.  For example, `disp_complex(a, b)` is not algebraically equivalent to `a+b*%i`.

## Polar forms

A complex number written as \(r e^{i\theta}\) is in _polar form_.  The Maxima function `polarform` re-writes a complex number in this form, however with `simp:false` it does not simplfy the expressions for the modulus \(r\) or argument \(\theta\) (in STACK). Attempting to re-simplify the expression only returns the number to Cartesian form!

As a minimal example, try the following.

    simp:false;
    p1:polarform(1+%i);
    p2:ev(polarform(1+%i), simp);
    p3:ev(p2, simp);

First we have `p1` is  \( \left(\left(1\right)^2 + \left(1\right)^2\right)^{{{1}\over{2}}}\,e^{i\,{\rm atan2}\left(1 , 1\right)} \). Of course, we really need some simplification of the \(r\) and the \(\theta\) values.

Notice the difference between `p2`: \(\sqrt{2}\,e^{{{i\,\pi}\over{4}}}\), and `p3`: \(\sqrt{2}\,\left({{i}\over{\sqrt{2}}}+{{1}\over{\sqrt{2}}}\right)\) (which of course is not even \(1+i\) either!).

The problem is that in this case `ev( ... , simp)` is not _idempotent_, (i.e. \( \mbox{simplify}(\mbox{simplify}(ex)) \neq \mbox{simplify}(ex) \) in all cases) and the PHP-maxima connection inevitibly passes an expression to and from Maxima multiple times.  If `simp:true` then we get multiple simplifications, in this example back to `p3`.

Instead, use `polarform_simp` to rewrite the expression in polar form, and do some basic simplification of \(r\) and \(\theta\).

    simp:false;
    p1:polarform_simp(1+%i);

returns `p1` as \(\sqrt{2}\,e^{\frac{i\,\pi}{4}}\).

Here are some design choices.

1. Positive numbers are returned as real numbers, not as \(r e^{i \times 0}\).  E.g. `polarform_simp(3)` is \(3\).
2. If \(r=1\) then this is not displayed. E.g. `polarform_simp(1/sqrt(2)*(-1+%i))` is \(e^{\frac{3\,i\,\pi}{4}}\).

If question level simplification is on, then the value will probably get re-simplified to Cartesian form.
