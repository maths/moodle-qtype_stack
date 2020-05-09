#  Propositional Logic

STACK loads the "logic" package from Maxima.

## Boolean functions

Maxima has Boolean operators `and`, `or`, and `not`.  These rely on the underlying LISP implementation and as a result the `simp:false` is ignored.  
To illustrate the problem, try the following.

    simp:false$
    true and true;
    x=1 or x=2;

The results respectively (of the second two) are

    true;
    false;

Note, there is no mechanism in Maxima to represent a list of assignments such as `x=1 or x=2`, 
which would be a natural way to express the solution to a quadratic equation.

To solve this problem STACK has introduced `nounand` and `nounor` which are commutative and associative operators.

Students do *not* need to use `nounand` and `nounor` in answers.  
Any `and` and `or` operators occurring in their answers are always automatically converted into these noun forms.

Teachers *always* need to use `nounand` and `nounor` in CAS expressions when they want to write non-simplifying expressions.  
For example, when defining the "teacher's answer" they should use the noun forms as appropriate.  
Teachers often need to use Boolean logic, and so need to consciously separate the difference between these operators and concepts.

Note, the answer tests do *not* convert noun forms to the Maxima forms.  
Otherwise both `x=1 or x=2` and `x=1 or x=3` would be evaluated to `false` and a teacher could not tell that they are different!  
To replace all `nounand` (etc) operators and replace them with the Maxima equivalent, use `noun_logic_remove(ex)`.

