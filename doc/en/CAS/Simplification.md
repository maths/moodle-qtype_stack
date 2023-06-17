# Simplification & ordering

## Algebraic equivalence

Is \((a^x)^y \equiv a^{x\,y}\)?  Well, it depends!  In particular you can easily derive the contradiction
\[ -1 = (-1)^1 = (-1)^{2\times \frac{1}{2}} \]
and using our rule \((a^x)^y \equiv a^{x\,y}\)
\[ = \left({(-1)^{2}}\right)^{\frac{1}{2}} = 1^{\frac{1}{2}} = 1.\]
To avoid problems like this we therefore have decided that

    ATAlgEquiv((a^x)^y, a^(x*y)) = [0, ""]

If you are teaching rules of indices to students for the first time this might come as a surprise!  If you would like STACK to implement this rule, then you need to also `assume(a>0)`.  This can be done in the feedback variables.  This is a design decision and not a bug (and is recorded in the system unit tests)!

Note the Maxima function `rootscontract` which converts products of roots into roots of products.

## Ordering terms

Maxima chooses an order in which to write terms in an expression. By default, this will use reverse lexicographical order for simple sums, so that we have \(b+a\) instead of \(a+b\).
In elementary mathematics this looks a little odd!  One way to overcome this is to use simplification below but another way is to alter the order in which expressions are transformed.

To alter the order in STACK you can use the Maxima commands `orderless` and `ordergreat`.  To have \(a+b\) you can use

    ordergreat(a,b);

See Maxima's documentation for more details.

Only one `orderless` or `ordergreat` command can be issued in any session.  The last one encountered will be used and the others ignored.  
No warnings or errors are issued if more than one is encountered.

## Logarithms to an arbitrary base

By default, Maxima does not provide logarithms to an arbitrary base.  To overcome this, STACK provides a function `lg` for student entry.

* `lg(x)` is log of \(x\) to the base 10.
* `lg(x, a)` is log of \(x\) to the base \(a\).

STACK provides no simplification rules for these logarithms.  To simplify you must transform back to natural logarithms.

For example (with `simp:true` or `simp:false`)

    p:lg(27, 3)
    q:ev(p, lg=logbasesimp)

results in `p=lg(27, 3)`, and `q=3`.

The algebraic equivalence function `algebraic_equivalence`, and so anything upon which it depends, will automatically remove logarithms to other bases.  
This includes the answer tests as needed.

## Selective simplification {#selective-simplification}

The level of simplification performed by Maxima can be controlled by changing Maxima's global variable `simp`, e.g.

    simp:true
    
When `simp` is set to `false`, no simplification is performed and Maxima is quite happy to deal with an expression such as \(1+4\) without actually performing the addition.
This is most useful for dealing with very elementary expressions, and for [showing working](../CAS/Matrix.md#Showing-working).

This variable can be set at the question level using the [options](../Authoring/Options.md) or for each [Potential response tree](../Authoring/Potential_response_trees.md).

When `simp` is set to `false`, you can evaluate an expression with simplification turned on by using `ev(..., simp)`, for example:

    simp:false;
    a:ev(1+1,simp);
    
will give \(a=2\).

### Within CASText (question text, general feedback, etc.)

Sometimes it is useful to control the level of simplification applied to expressions included within [CASText](../Authoring/CASText.md) using `{@...@}`.
In particular, to show steps in working, it is often necessary to turn simplification off.

To selectively control simplification within CASText (including the general feedback), you can use the following methods:

1. Set `simp:false` in the question options, or at the end of your question variables. That way all expressions in the CASText will be unsimplified, but you can use `{@ev(...,simp)@}` to simplify selectively.
2. Use evaluation flags to control the level of simplification for an individual CAS expression, for example:
```
{@3/9,simp=false@}
```
3. Use a [define block](../Authoring/Question_blocks/Static_blocks.md#define-block) to set the value of `simp`, e.g.
```
[[define simp="false"/]]
\({@3/9@} \neq {@1+1@}\)
[[define simp="true"/]]
\({@3/9@} \neq {@1+1@}\)
```
will produce \(\frac{3}{9}\neq1+1\) followed by \(\frac{1}{3}\neq2\).
4. Switch simplification
```
{@(simp:false,3/9)@}
```
This command sets the value of `simp` for this expression, and all others which follow, much like the define block above.

## Unary minus and simplification

There are still some problems with the unary minus, e.g. sometimes we get the display \(4+(-3x)\) when we would actually like to always display as \(4-3x\).  
This is a problem with the unary minus function `-(x)` as compared to binary infix subtraction `a-b`.

To reproduce this problem type in the following into a Maxima session:

    simp:false;
    p:y^3-2*y^2-8*y;

This displays the polynomial as follows.

    y^3-2*y^2+(-8)*y

Notice the first subtraction is fine, but the second one is not.  To understand this, we can view the internal tree structure of the expression by typing in

    ?print(p);
    ((MPLUS) ((MEXPT) $Y 3) ((MMINUS) ((MTIMES) 2 ((MEXPT) $Y 2))) ((MTIMES) ((MMINUS) 8) $Y))

In the structure of this expression the first negative coefficient is `-(2*y^2)` BUT the second is `-(8)*y`.   
This again is a crucial but subtle difference!  
To address this issue we have a function

    unary_minus_sort(p);

which pulls "-" out the front in a specific situation: that of a product with a negative number at the front.  
The result here is the anticipated `y^3-2*y^2-8*y`.

Note that STACK's display functions automatically apply `unary_minus_sort(...)` to any expression being displayed.

## Really insisting on printing the parentheses

Why does STACK (i.e. Maxima) not print out the parentheses?  For example, try the following.

    simp:false;
    p1:(a+b)+c;
    tex(p1);

The result is \(a+b+c\).  Where have the parentheses gone?  On the other hand `p2:a+(b+c)` is displayed as \(a+\left(b+c\right)\).  Why are these displayed differently?  Assuming `simp:false` and using Maxima's `?print` command we can see the internal structure.

* `?print(a+b+c)` gives `((MPLUS) $A $B $C)`.  This means we have the flattened (nary) sum of the three variables.  This will always not have brackets.
* `?print((a+b)+c)` gives `((MPLUS) ((MPLUS) $A $B) $C)`.  This is not yet flattened to an nary sum, but Maxima's tex routines suppress the parentheses, even with `simp:false`.  This is part of the problem.
* `?print(a+(b+c))` gives `((MPLUS) $A ((MPLUS) $B $C))`.  This is not yet flattened to an nary sum, and in this case it displayed as \(a+\left(b+c\right)\) by Maxima's TeX function.

Note, this display problem is not a bug.  Experts would interpret \(a+b+c\) as \((a+b)+c\) not as \(a+(b+c)\).  This is only a problem in teaching when we want to display (arguably not needed) parentheses.  To solve this display problem STACK has an inert `disp_parens` function.  All this function does is print round brackets (parentheses) around its argument.

For example, try the following.

    simp:false;
    p1:disp_parens(a+b)+c;
    tex(p1);

The result is \({\left( a+b \right)+c}\).

Parentheses can also be added to other expressions which, strictly speaking, do not need them. For example `int(disp_parens(x-2),x)` is displayed as \({\int {\left( x-2 \right)}{\;\mathrm{d}x}}\).

It may be necessary to remove the `disp_parens` from an expression.  STACK provides the function `remove_disp_parens(ex)` to remove this inert display function.  Actually, this function is remarkably simple.

    remove_disp_parens(ex) := ev(ex, disp_parens=lambda([ex2], ex2))$

The function `disp_parens` has no mathematical definition.  It just changes the TeX output.  The above function re-evaluates the expression, with this function equal to the identity function (`lambda([ex2], ex2))`).  Giving `disp_parens` this mathematical definition effectively removes it.

Note that the answer tests do not remove the `disp_parens` function from a teacher's expression.  Hence, `a+b+c` and ``disp_parens(a+b)+c` are not algebraically equivalent.  Teachers who use these display functions must remove them before answer tests are applied.  Students cannot use the `disp_parens` function.  Indeed, currently a student's input of `(a+b)+c` is displayed as Maxima does without the brackets (yes, this might be considered a bug).

## Selecting, and highlighting part of an expression

Like `disp_parens`, STACK provides a function `disp_select` which highlights part of an expression.  All this function does is colour the argument red and underline it.  For example `1+disp_select(x^2+2)` is displayed as \({1+\color{red}{\underline{x^2+2}}}\).  Note, the combination of colour and the underline is because it is considered poor practice to use colour alone to convey meaning.

STACK provides the function `remove_disp_select(ex)` to remove this inert display function.  The function `remove_disp(ex)` removes all inert display functions.

When creating feedback it is often useful to select, and highlight, part of an expression.  STACK provides a function `select(p1, ex)` to do this.  The select function traverses the expression tree for `ex` and when it encounters a sub-tree for which the predicate `p1` is true it adds `disp_select` to the sub-tree and stops traversing any further down that sub-tree.  While nested `disp_select` are possible (and will dispaly multiple underlines: another reason for having underline) this particular function stops once `p1` is true.  You will need to build nested display by hand.

For example, to select all the integers in an expression you can use the predicate `integerp` and `select(integerp, 1+x+0.5*x^2)` gives \(\color{red}{\underline{1}}+x+0.5\cdot x^{\color{red}{\underline{2}}}\).

It is possible to use any of the existing predicate functions, or to define your own function in the question variables.

The function `select_apply(f1, ex)` traverses the expression and when it encounters the `disp_select` function it applied the function `f1` to that sub-tree of the expression.  This allows for selective simplification/modification of highlighted sub-trees.  For example,

    simp:false;
    p1:select(zeroMulp, (1-1)*x^2+0*x+1);
    p2:select_apply(simplify, p1);
    p3:select_apply(simplify, p1, false);

generates the following displayed expressions.

* `p1` displays as \({\left(1-1\right)\cdot x^2+\color{red}{\underline{0\cdot x}}+1}\).  We have selected all the parts for which the predicate `zeroMulp` is true.  This is the predicate which checks if the rule \(0 \times x \rightarrow 0 \) is applicable.  While the coefficient of \(x^2\) is equivalent to zero, it is unsimplified and the predicate `zeroMulp(1-1)` is false. This sub-tree is not selected by this predicate.
* `p2` displays as \({\left(1-1\right)\cdot x^2+0+1}\).  The displayed expression is subjected to the function `simplify`, and the displayed part replaced.  The rest of the expression is unchanged. By default the `disp_select` is removed and so the result is not coloured and underlined.
* `p3` displays as \({\left(1-1\right)\cdot x^2+\color{red}{\underline{0}}+1}\).  Notice the third, optional boolean, argument to `select_apply` in `p3`.  This argument will decide whether to continue to display the `disp_select` display or remove it (now the function has been applied).  The default is `true`, so here the red underline is not removed.

## If you really insist on a kludge....

In some situations you may find you really do need to work at the display level, construct a string and display this to the student in Maxima.
Please avoid doing this!

    a:sin(x^2);
    b:1+x^2;
    f:sconcat("\\frac{",stack_disp(a,""),"}{",stack_disp(b,""),"}");

Then you can put in `\({@f@}\)` into one of the CASText fields. Note, you need to add LaTeX maths delimiters, because when the CAS returns a string the command `{@f@}` will just display the contents of the string without maths delimiters.

## Tips for manipulating expressions

How do we do the following in Maxima?
\[ (1-x)^a \times (x-1) \rightarrow  -(1-x)^{a+1}.\]
Try

    q:(1-x)^a*(x-1);
    q:ratsubst(z,1-x,q);
    q:subst(z=1-x ,q);


How do we do the following in Maxima?
\[ (x-1)(k(x-1))^a \rightarrow  (x-1)^{a+1}k^a.\]

     factor(radcan((x-1)*(k*(x-1))^a))


Maxima's internal representation of an expression sometimes does not correspond with what you expect -- in that case, `dispform` may help to bring it into the form you expect. For example, the output of `solve` in the following code shows the \(b\) in the denominator as \(b^{-1}\) which gives unnatural-looking output when a value is substituted in -- this is fixed by using `dispform` and substituting into that variants instead.

    simp:true;
    eqn:b = 1/(6*a+3);
    ta1: expand(rhs(solve(eqn,a)[1]));
    dispta1:dispform(ta1);
    simp:false;
    subst(2,b,ta1);
    subst(2,b,dispta1);


## Creating sequences and series

One problem is that `makelist` needs simplification.  To create sequences/series, try something like the following

    an:(-1)^n*2^n/n!
    N:8
    S1:ev(makelist(k,k,1,N),simp)
    S2:maplist(lambda([ex],ev(an,n=ex)),S1)
    S3:ev(S2,simp)
    S4apply("+",S3)

Of course, to print out one line in the worked solution you can also `apply("+",S2)` as well.

To create the binomial coefficients

    simp:false;
    n:5;
    apply("+",map(lambda([ex],binomial(n,ex)*x^ex), ev(makelist(k,k,0,5),simp)));

## Surds

Imagine you would like the student to expand out \( (\sqrt{5}-2)(\sqrt{5}+4)=2\sqrt{5}-3 \). 
There are two tests you probably want to apply to the student's answer.

1. Algebraic equivalence with the correct answer: use `ATAlgEquiv`.
2. That the expression is "expanded": use `ATExpanded`.

You probably then want to make sure a student has "gathered" like terms.  In particular you'd like to make sure a student has either
\[ 2\sqrt{5}-3 \mbox{ or } \sqrt{20}-3\]
but not \[ 5+4\sqrt{2}-2\sqrt{2}+6.\]
This causes a problem because `ATComAss` thinks that \[ 2\sqrt{5}-3 \neq \sqrt{20}-3.\]
So you can't use `ATComAss` here, and guarantee that all random variants will work by testing that we really have \(5+4\sqrt{2}\) for example.

What we really want is for the functions `sqrt` and `+` to appear precisely once in the student's answer, or that the answer is a sum of two things.

### Control of surds ###

See also the Maxima documentation on `radexpand`.  For example

    radexpand:false$
    sqrt((2*x+10)/10);
    radexpand:true$
    sqrt((2*x+10)/10);

The first of these does not pull out a numerical denominator.  The second does.

### Trig simplification ###

Maxima does have the ability to make assumptions, e.g. to assume that \(n\) is an integer and then simplify \(3\cos(n\pi/2)^2\) to \( \frac{3}{2}(1+(-1)^n)\).  Assume the student's answer is `ans1` then then define the following feedback variables:

    declare(n,integer);
    sans1:ev(trigrat(ans1),simp);

The variable `sans1` can then be used in the PRT.  Just note that `trigrat` writes powers of trig functions in terms of multiple angles.  This can have an effect of "expanding" out an expression.  E.g. `trigrat(cos(n)^20)` is probably still fine, but `trigrat(cos(n)^2000)` is probably not!  For this reason `trigrat` is not part of the default routines to establish equivalence.  Trig simplification, especially when we make assumptions on variables like \(n\), needs to be done on a question by question basis.

## Boolean functions

See the page on [propositional logic](../Topics/Propositional_Logic.md).

## Further examples

Some further examples are given elsewhere:

* Matrix examples in [showing working](Matrix.md#Showing-working).
* An example of a question with `simp:false` is discussed in [authoring quick start 7](/AbInitio/Authoring_quick_start_7.md).
* Generating [random algebraic expressions](Random.md) which need to be "gathered and sorted".

Note also that [question tests](../Authoring/Testing.md#Simplification) do not simplify test inputs.
