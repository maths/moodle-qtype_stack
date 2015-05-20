# Simplification & ordering

## Ordering terms

Maxima chooses an order in which to write terms in an expression.  By default, this will use reverse lexicographical order for simple sums, so that we have \(b+a\) instead of \(a+b\).  In elementary mathematics this looks a little odd!  One way to overcome this is to use simplification below but another way is to alter the order in which expressions are transformed.

To alter the order in STACK you can use the Maxima commands `orderless` and `ordergreat`.  To have \(a+b\) you can use

    ordergreat(a,b);
    
See Maxima's documentation for more details.  

Only one `orderless` or `ordergreat` command can be issued in any session.  The last one encountered will be used and the others ignored.  No warnings or errors are issued if more than one is encountered.

## Selective simplification

The level of simplification performed by Maxima can be controlled by changing Maxima's global variable `simp`, e.g.

    simp:true

This variable can be set at the question level using the [options](../Authoring/Options.md) or for each [Potential response tree](../Authoring/Potential_response_trees.md).

When this is `false`, no simplification is performed and Maxima is quite happy to deal with an expression such as \(1+4\) without actually performing the addition.
This is most useful for dealing with very elementary expressions.

## Selective simplification

If you are using `simp:false` to evaluate an expression with simplification on you can use

    ev(ex,simp)

## Unary minus and simplification

There are still some problems with the unary minus, e.g. sometimes we get the display \(4+(-3x)\) when we would actually like to always display as \(4-3x\).  This is a problem with the unary minus function `-(x)` as compared to binary infix subtraction `a-b`.

To reproduce this problem type in the following into a Maxima session:

    simp:false;
    p:y^3-2*y^2-8*y;

This displays the polynomial as follows.

    y^3-2*y^2+(-8)*y

Notice the first subtraction is fine, but the second one is not.  To understand this, we can view the internal tree structure of the expression by typing in

    ?print(p);
    ((MPLUS) ((MEXPT) $Y 3) ((MMINUS) ((MTIMES) 2 ((MEXPT) $Y 2))) ((MTIMES) ((MMINUS) 8) $Y))
   
In the structure of this expression the first negative coefficient is `-(2*y^2)` BUT the second is `-(8)*y`.   This again is a crucial but subtle difference!  To address this issue we have a function
   
    unary_minus_sort(p);

which pulls "-" out the front in a specific situation: that of a product with a negative number at the front.  The result here is the anticipated `y^3-2*y^2-8*y`.

Note that STACK's display functions automatically apply `unary_minus_sort(...)` to any expression being displayed. 

## If you really insist on a cludge....

In some situations you may find you really do need to work at the display level, construct a string and display this to the student in Maxima.  Please avoid doing this!

    a:sin(x^2);
    b:1+x^2;
    f:concat("\\frac{",StackDISP(a,""),"}{",StackDISP(b,""),"}");

Then you can put in `@f@` into one of the CASText fields.

## Tips for manipulating expressions

How do we do the followin in Maxima?
\[ (1-x)^a \times (x-1) \rightarrow  -(1-x)^{a+1}.\]
Try

    q:(1-x)^a*(x-1); 
    q:ratsubst(z,1-x,q);
    q:subst(z=1-x ,q);


How do we do the followin in Maxima?
\[ (x-1)(k(x-1))^a \rightarrow  (x-1)^{a+1}k^a.\]

     factor(radcan((x-1)*(k*(x-1))^a)) 

## Further examples

Some further examples are given elsewhere:

* Matrix examples in [showing working](Matrix.md#Showing_working).
* An example of a question with `simp:false` is discussed in [authoring quick start 3](../Authoring/Authoring_quick_start_3.md).
* Generating [random algebraic expressions](Random.md) which need to be "gathered and sorted".

Note also that [question tests](../Authoring/Testing.md#Simplification) do not simplify test inputs.
