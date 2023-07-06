# Options

Options affect the behaviour of each question.

### Question Level Simplify  ###

See the entry on [simplification](../CAS/Simplification.md).  Default is `true`.

### Assume Positive  ### {#Assume_Positive}

This option sets the value of [Maxima](../CAS/Maxima.md)'s

    assume_pos

variable.

If `true` and the sign of a parameter \(x\) cannot be determined from the current context or
other considerations, `sign` and `asksign(x)` return `true`. This may forestall some automatically-generated
asksign queries, such as may arise from integrate or other computations

Default is False

### Question Penalty ### {#Question_penalty}

This is the percentage of the marks deducted from each different and valid attempt which is not
completely correct, when the penalty mark modification scheme is in use.
The default is \(10\%\) of the marks available for this question, entered at \(0.1\).

Note that Moodle stores scores to 7 decimal places, so, \(1/3\) should be entered as \(0.3333333\),
and \(2/3\) as \(0.6666667\). If you input any number close to \(1/3\), but with less precision,
then the extra digits will automatically be added. The exact range affected is that
any penalty \(\ge 0.33\) and \(\le 0.34\) is changed to \(0.3333333\), and
any penalty \(\ge 0.66\) and \(\le 0.67\) is changed to \(0.6666667\).

## Output  ##

The following options affect how mathematics is displayed.

### Multiplication Sign ### {#multiplication}

* (none), e.g. \(x(x+1)\)
* Dot, e.g. \(x\cdot(x+1)\)
* Cross, e.g. \(x\times (x+1)\)
* Numbers only, e.g. \(3\times 5\, x\).

In practice it is very helpful to have some kind of multiplication sign displayed to the student.  The difference between
\[ xe^x \mbox{ and } x\,e^x\]
is very subtle.  Notice the spacing?  The first means `xe^x=(xe)^x` the second is `x*e^x`.  Could be quite confusing to students if there is no multiplication sign.  Using \(x\cdot e^x\) neatly solves this problem.

Internally the display of multiplication signs is controlled by the STACK function `make_multsgn(ex)`, where the argument can be one of the strings `"cross"`, `"dot"` or `"blank"`.  This can be switched part-way through a session. E.g. consider the following castext.

    Default: {@a*b@}.
    Switch to cross: {@(make_multsgn("cross"), a*b)@}.
    Cross remains: {@a*b@}.

The expression `(make_multsgn("cross"), a*b)` uses parentheses as an abbreviation for Maxima's `block` command.  So, the first expression `make_multsgn("cross")` is evaluated which changes the display option to a cross.  Then the second expression is evaluated and displayed as \(a\times b\).  The new option persists in the next expression.

The value of this option `onum` will only put a multiplication sign between numbers.  This means you will see \(3\times 5\, x\) and not \(3\, 5\, x\) as you would if you have "none".

There is a special atom which controls the multiplication symbol.  If you would like a dot then define

    texput(multsgnonlyfornumberssym, "\\times");

in the question variables.

### Logic symbols ### {#logicsymbol}

How logical symbols should be displayed. The values are language, e.g. \(A \mbox{ and } B\) or symbol, e.g. \(A\land B\).

### Surd for Square Root ### {#surd}

This option sets the value of [Maxima](../CAS/Maxima.md)'s

    sqrtdispflag

When false the prefix function `sqrt(x)` will be displayed as \(x^{1/2}\).
Please note that Maxima (by default) does not like to use the \(\sqrt{}\) symbol.
The internal representation favours fractional powers, for very good reasons.
In  Maxima 5.19.1 we get:

    (%i1) 4*sqrt(2);
    (%o1) 2^(5/2)
    (%i2) 6*sqrt(2);
    (%o2) 3*2^(3/2)

Do you really want to continue using \(\sqrt{}\) in your teaching?  In his *Elements of Algebra*, L. Euler wrote the following.

> \(\S 200\) We may therefore entirely reject the radical signs at present made use of, and employ in their stead
> the fractional exponents which we have just explained: but as we have been long accustomed to
> those signs, and meet with them in most books of Algebra, it might be wrong to banish them entirely from 
> calculations; there is, however, sufficient reason also to employ, as is now frequently done, the other method of 
> notation, because it manifestly corresponds with the nature of the thing. In fact we see immediately
> that \(a^\frac12\) is the square root of \(a\), because we know that the square of \(a^\frac12\), that is to say 
> \(a^\frac12\) multiplied by \(a^\frac12\) is equal to \(a^1\), or \(a\).

A lot of elementary mathematics involves converting from one form to another and back again.  Sometimes these forms have important differences of use, e.g. factored form or completed square form for a quadratic.  However, sometimes these equivalent forms are more customary than because it *"manifestly corresponds with the nature of the thing"* in question.  I digress...

### sqrt(-1) {#sqrt_minus_one}

In Maxima `%i` is the complex unit satisfying `%i^2=-1`.  However, students would
like to type `i` and physicists and engineers `j`.
We also sometimes need to use symbols `i` and `j` for vectors.
To accommodate these needs we have an option `ComplexNo` which provides a context for these symbols
and affects the way they are displayed.

| Option   | Interpretation   | Display   | ~     | ~    | ~     | ~
| -------- | ---------------- | --------- | ----- | ---- | ----- | -----
|          | %i^2             | i^2       | j^2   | %i   | i     | j
| -------- | ---------------- | --------- | ----- | ---- | ----- | -----
| i        | -1               | -1        | j^2   | i    | i     | _j_
| j        | -1               | i^2       | -1    | j    | _i_   | j
| symi     | -1               | i^2       | j^2   | i    | _i_   | _j_
| symj     | -1               | i^2       | j^2   | j    | _i_   | _j_

Note the use of both Roman and italic symbols in this table.

### Matrix parentheses

See the entry on [matrices](../CAS/Matrix.md#matrixparens).

### Inline and displayed fractions.

The display of fractions can take two forms: inline \( 1/x \) and displayed \( \frac{1}{x} \).

The default behaviour is displayed, i.e. using LaTeX `\frac{}{}`.

The function `stack_disp_fractions(ex)` can be used to control the display.

* `stack_disp_fractions("i")` switches display to inline.
* `stack_disp_fractions("d")` switches display to display.

Note, for CASText the display is controlled by the prevailing setting at the moment the text is displayed, not when a variable is defined in the question variables. Hence, if you would like a single inline fraction within a CASText you will need to use

    Normally fractions are displayed {@1/x@}. This switches to inline {@(stack_disp_fractions("i"), 1/x)@}, which persists {@1/a@}.  Switch explicitly back to displayed {@(stack_disp_fractions("d"),1/x)@}.  
