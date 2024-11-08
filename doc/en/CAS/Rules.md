# Rules and patterns

Maxima has a system for defining rules and patterns.  For example, in desktop maxima

```
matchdeclare([a],true);
let(sin(a)^2, 1-cos(a)^2);
letsimp(sin(x)^4);
```

will give \(\sin(x)^4 \rightarrow \cos^4(x)-2\,\cos^2(x)+1\).

Support for `let` was added in v4.8.0 (November 2024), and only partial support is currently available.

In particular, Maxima's `let` function makes use of a special operator `->` which is unsupported in the Maxima-PHP connection.  To accommodate this, you must place `let` commands inside a block which returns it's last element.

For example, put the following the question variables will work (but the above example will not):

```
matchdeclare([a],true);
p1:(let(sin(a)^2, 1-cos(a)^2), letsimp(sin(x)^4));
```
and `{@p1@}` in some castext (e.g. the question) will give \(\cos^4(x)-2\,\cos^2(x)+1\).  Typically, Maxima will not perform this simplification.

### Matrix example

Imagine we want `I` to represent the identity matrix.

```
orderless(I);
matchdeclare([a],true);
/* Note use of a block to make sure the return value ("true" here) can be parsed back into PHP. */
(let(I*a, a),let(I^2, I),true);
p:letsimp(expand((A+I)^3));
```

Then castext such as `{@p@}` gives \({A^3+3\cdot A^2+3\cdot A+I}\).