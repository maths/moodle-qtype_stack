# Authoring validation errors

This document aims to explain certain errors that may appear during authoring, specifically during
validation/saving of a question.  We are constantly improving the error trapping for question 
authors and so some of these errors might be new and appear during upgrades, and break, previously functional materials.

In some cases we have good reasons for now preventing actions which may have been permitted in the past.
For some situations one can do things differently to lift the suspicions of the validation system.

Summary of advice.

1. Do not attempt to redefine function names or constants which are already defined as part of Maxima.
2. Avoid using the same name for functions and variables.
3. Avoid complex substitutions.
4. Do not attempt to redefine the variables which are the names of inputs.
5. Use `subst` or `at` to do substitutions instead of `ev`.

## Forbidden functions and variables

Some functions affecting the underlying system are forbidden to protect
the environment, others are forbidden due to excessive load, and finally some
have been disabled due to the way they return their values. Likewise, some 
variables/constants representing the state of the underlying system have
been either marked as forbbiden to access or to modify. Use other names
if this was just a collision of identifiers, otherwise this is just the way 
it is.

## Redefining internal-functions

Some functions are part of the core functionality and the system relies
on their behaviour. For example redefining what `append()` does for lists
would have significant effects around the system, this is why (re)defining
functions with some specific names is forbidden. You are free to use other
names for your functions. Note that not all internal functions or functions
they use are blocked by this, they might get blocked in the future or some
might be left as something that can be redefined to tune the logic.

We suggest you do not attempt to redefine function names or constants which are already defined as part of Maxima.

## Substitutions unclear or otherwise

Should you do substitutions of values in your code and the target of
the substitution is an identifier that is later used as a function-name,
things may become difficult. Avoid using the same name for functions and variables. 
Also if you use complex means to construct the substitutions themselves the system may need to assume that all
possible identifiers in the expression that the substitutions are applied
to are being targetted by unknown values, this can be avoided by avoiding
complex construction of the substs itself.

Note, these issues are now much rarer after the security system changed in 4.4. Yo should not even see these errors with the new system.

### Example 1

```
v:1; /* particular case of rand([1,2]) */
trig:[sin,cos][v];
sub:[(sin(x))^2=1-(cos(x))^2,(cos(x))^2=1-(sin(x))^2][v];
f:(trig(x))^3;
df:diff(f,x);
df_simp:subst(sub,df);
```
This produces the error message
> The function name "sin" is potentially redefined in unclear substitutions. The function name "diff" is potentially redefined in unclear substitutions.

The issue is that `sub` is a complicated expression here, so the validation system is not able to check that this code is not doing something suspicious.

It may be worth trying to use `ev` rather than `subst` - for this example, the question works again if we change the final line to the following:

```
df_simp:ev(df, sub);
```

### Example 2

```
solution : rhs(ode2(eqn,y,x));
vars : delete(x,listofvars(solution));

TAns11 : subst([vars[1]=A,vars[2]=B],solution);
TAns12 : subst([vars[2]=A,vars[1]=B],solution);
```

Here the error messsages will claim that pretty much everything that goes into `solution` is potentially redefined in unclear substitutions. What the code above does is that it solves a differential equation with CAS and then asks the CAS for the names of the constants being used. Depending on the shape of the equation there might be more than one but in this case there are always exactly two of them. `%k1` and `%k2`, if there were only one it would probably get called `%c` so there is a reason for checking what they are.

Why the example asks for those constants names is because the author has chosen to force the student to use `A` and `B` as the constants and constructs different correct answers for the two ways of selecting the order of the constants. The problem arises from the substitution of the constants which now finds the names of them from a list (`vars`) that is not directly visible to the validation system. There are two ways forward from this. Firstly, the question could use the SubstEquiv test that allows any names for those constants to be used and then just confirms that the correct ones were used if that matters, however then the teachers answer might look silly with those CAS-style constants so you would still need to replace them. Secondly, one can hard-code the names of the constants, in this case they are always the same so that should not be a problem:
```
solution : rhs(ode2(eqn,y,x));

TAns11 : subst([%k1=A,%k2=B],solution);
TAns12 : subst([%k2=A,%k1=B],solution);
```

## Use of the students answer

Since 4.3 you have been forbidden from writing to the variable storing 
the students answer, feel free to simply store whatever you wanted to 
store in any other valid variable. You are now also forbidden from using
the students input directly as a function-name, if you need to do so 
rewrite the logic as a "switch", unfortunately this also applies to MCQ
inputs:

```
/* Not like this. */
val: ans1(4);

/* Instead write it like this to restrict the set of possible names. */
if is(ans1=sqrt) then
 val: sqrt(4)
else if(ans1=sin) then
 val: sin(4)
...

```

## `ev` in 4.4

The new security model of STACK 4.4 modifies the order of execution in such 
a way that `ev` does not quite match the behaviour of `ev` in Maxima. For 
most use cases you will not notice this but there are situations where
things won't work as expected. In general, these are the situations currently 
known:

 1. Placing `expand` inside of `ev` and doing a substitution in the same `ev`:
 ```
 /* This might lead to the expansion happening before the substitution. */
 simp:false;
 tmp:ev(expand(x^3),x=x+1);

 /* Instead do the substitution before the expansion. */
 tmp: ev(x^3,x=x+1);
 tmp: ev(expand(tmp));
 ```
 2. Substitutions in general may happen too late if the target expression contains calls requiring security validation. The recommended method for dealing with this is to do the substitutions using `subst` or `at` and applying any evaluation related modifications with `ev` separately.
 3. Use of the `noeval` evaluation flag might not work, in general this flag is unlikely to be useful in the context of STACK and if you believe you need it look into nounification and other means of doing similar things.
 
In some cases, it is possible to simply add `eval` as an extra flag to 
the `ev`-call, however this is only possible if all substitutions in the call are
such that the LHS of the substitution is not present in the RHS.

Do note that all this also applies to evaluation flags.
```
/* These are equivalent: */
tmp: a+1,a=2;
tmp: ev(a+1,a=2);
```


# Some recomendations

The validation of certain things evaluates dependency graphs of 
identifiers so if you can keep those graphs small things tend to work
faster. To do so avoid redefining the same variable if at all possible
and just create a new variable when needed, i.e. do not reuse some 
`tmp`-var for all the calculations, if you do then the graph of that
variable will become quite large and that may slow things down. Many 
small graphs tend to be faster to deal with than few large ones and 
a large graph may mean that features of node in the graph may block
other nodes from doing certain things, e.g. being used as function
identifiers.

Likewise, avoid reusing same variable names for multiple different
types of things in the question, i.e. if `A` is a matrix let it stay
as such and don't turn it into a list or a function if you wish 
the validation during saving and first execution after import/upgrade
to be fast.

If you are using `local()` to define local-scope variables always call
it as the first thing in a `block` (if you use the list of identifiers
logic then you should not use `local()` in the same block) or in a `lambda` 
(after arguments). This allows the graph building to identify the point of 
disconnection of the graphs and to separate the scopes. Calling `local` 
after any variables have been used will confuse the logic and we do not
want to invest into dealign with that special case.
