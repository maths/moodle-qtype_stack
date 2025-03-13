## Matrices and answer tests

Some answer tests accept matrices as arguments. E.g. algebraic equivalence (`ATAlgEquiv`) will accept matrices, even of different sizes, and return feedback underlining which elements are different.  This is a rather blunt tool, since it is all or nothing.  And, it's often necessary to apply other answer tests to corresponding elements of matrices.

1. Every answer test in STACK has a corresponding function in Maxima.  The Maxima function name of AlgEquiv is `ATAlgEquiv`, et.
2. The Maxima code for every answer in STACK returns a list of four elements.  The _second_ element is the result of the test.  See the [documentation](../../Authoring/Answer_Tests/index.md) for details of the other elements.  For example `second(ATComAss(x+y,y+x))` is `true` because \(x+y=y+x\) up to commutativity etc (using `ATComAss`).
3. Some answer tests take an optional agument, e.g. numerical accuracy.  to use `zip_with_matrix` we need to create an un-named (lambda) function of two arguments.  E.g. `lambda([ex1,ex2], ATNumAbsolute(ex1,ex2,0.01)` can be used to test corresponding matrix elements are within \(0.01\) of each other.

Once you have a matrix `M` of boolean values, you could count the number which are false.

    n:length(sublist(flatten(args(M)),lambda([ex],not(ex))));

Or you could locate the false elements and give specific feedback.

#### Example 1. ####

Here we test two matrices with elements with `ATEqualComAss`.  This returns a matrix `M` of boolean values.

````
M1:matrix([x*(x+1),sqrt(x^2)],[2/3,1/2]);
M2:matrix([x^2+x,abs(x)],[0.666,0.5]);
M:matrixmap(second,zip_with_matrix(ATEqualComAss, M1, M2));
````

#### Example 2. ####

Here we test two matrices with elements with `ATNumAbsolute`, and argument \(0.01\).  This returns a matrix `M` of boolean values.

````
M1:matrix([3.1415,10.0]);
M2:matrix([%pi,%pi^2]);
M:matrixmap(second,zip_with_matrix(lambda([ex1,ex2], ATNumAbsolute(ex1,ex2,0.01)), M1, M2));
````

