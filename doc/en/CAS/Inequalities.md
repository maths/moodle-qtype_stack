# Inequalities #

The non-strict inequalities \(\geq\) and \(\leq\) are created as infix operators with the respective syntax

    >=,  <=

Maxima allows single inequalities, such as \(x-1>y\), and also support for inequalities connected by logical operators, e.g. \( x>1 \mbox{ and } x<=5\).

You can test if two inequalities are the same using the algebraic equivalence test, see the comments on this below.  

Chained inequalities, for example \(1\leq x \leq2\mbox{,}\) are not permitted.  They must be joined by logical connectives, e.g. "\(x>1\) and \(x<7\)". 

Support for inequalities in Maxima (and hence STACK) is currently poor. This is on our list of possible projects, and help would be welcome!

# Functions to support inequalities

### * `ineqprepare(ex)`

This function ensures an inequality is written in the form `ex>0` or `ex>=0` where `ex` is always simplified.  This is designed for use with the algebraic equivalence answer test in mind.

### * `ineqorder(ex)`

This function takes an expression, applies `ineqprepare()`, and then orders the parts.  For example,

     ineqorder(x>1 and x<5);

returns

      5-x > 0 and x-1 > 0

It also removes duplicate inequalities.  Operating at this syntactic level will enable a relatively strict form of equivalence to be established, simply manipulating the form of the inequalities.  It will respect commutativity and associativity and `and` and `or`, and will also apply `not` to chains of inequalities.

If the algebraic equivalence test detects inequalities, or systems of inequalities, then this function is automatically applied.

However, to establish the equivalence of `x^2>1` with `x>1 or x<-1` will require significantly more work.  This is an interesting and open mathematical and CAS challenge!

## See also

[Maxima reference topics](index.md#reference).


