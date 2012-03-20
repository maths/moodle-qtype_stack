# Inequalities #

Support for inequalities in Maxima (and hence STACK) is currently very poor. [This is on our list of possible projects, and help would be welcome]

The non-strict inequalities \(\geq\) and \(\leq\) are created as infix operators with the respective syntax
	
	>=,  <= 


Maxima only allows single inequalities, such as \(x-1>y\mbox{.}\)

You can test if two inequalities are the same using the algebraic equivalence test.

Chained inequalities, for example \(1\leq x \leq2\mbox{,}\) or inequalities joined by logical connectives, e.g. "\(x>1\) and \(x<7\)", are not currently supported.

## See also

[Maxima reference topics](index.md#reference).


