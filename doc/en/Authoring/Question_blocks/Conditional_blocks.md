## Conditional blocks ##

### If blocks ###

The common **if** statement is written as:

    [[ if test="some_CAS_expression_evaluating_to_true_or_false" ]]
    The expression seems to be true.
    [[/ if ]]

The if block requires a parameter called `test` and the value must be a Maxima expression which evaluates to `true` or `false`.

The **if** block uses a special syntax expansion that provides it a way to handle **else** cases. For example,

    [[ if test='oddp(rand(5))' ]]
    This is an odd block!
    [[ else ]]
    This is an even block!
    [[/ if]]

There is an *else if* type of structure using **elif** (Python coders won the syntax selection vote),

    [[ if test='oddp(var)' ]]
    This is an odd block!
    [[ elif test='is(var=0)' ]]
    It might be even but it is also zero.
    [[ else ]]
    This is an even block!
    [[/ if]]

Note that you may have to evaluate your expression explicitly.  Maxima does not always evaluate predicates to `true` or `false`.  For example, you might expect `p<1` in the following to evaluate to `true`.

    [[ define p="0" /]] \(p\) is now {@p@}.
    [[ if test="p<1"]]
    \(p\) is less than 1.
    [[ else ]]
    \(p\) is not less than 1.
    [[/ if ]]

However, it remains unevaluated. The true branch is therefore not satisfied. Since 4.4 the behaviour of this block matches normal if-else behaviour in Maxima, you may still meet this problem if you happen to turn simplification off.

To address this explicitly evaluate your expressions as predicates.

    [[ if test="is(p<1)"]]

Teachers can also use evaluation with simplification and predicates as follows:

    [[ if test="ev(p<1,simp,pred)"]]

It is the responsibility of the question author to ensure that every test in an if block evaluates to `true` or `false`.

## Foreach loop ##

Foreach blocks iterate over lists or sets and repeat their content redefining variables for each repetition.

    [[ foreach x="[1,2,3]" ]]{#x#} [[/ foreach ]]

You may have multiple variables and they will be iterated over in sync and the variables may also come from Maxima. Should one of
the lists or set be shorter/smaller the iteration will stop when the first one ends.

    [[ foreach x="[1,2,3]" y="makelist(x^2,x,4)" ]] ({#x#},{#y#}) [[/ foreach ]]

Because the foreach block needs to evaluate the lists/sets before it can do the iteration, using foreach blocks will require one
additional CAS evaluation for each nested level of foreach blocks. This has not applied since 4.4. no additional cost is related
to this block and it is recommended that any repeption that can be removed is removed using this block.

## Define block {#define-block}

The define block is a core component of the foreach block, but it may also be used elsewhere. Its function is to change the value of
a CAS variable in the middle of CASText. For example:

    [[ define x='1' /]] {#x#}, [[ define x='x+1' /]] {#x#}, [[ define x='x+1' /]] {#x#}

should print "1, 2, 3". You may define multiple variables in the same block and the order of define operations is from left to right
so "[[ define a='1' b='a+1' c='a+b' /]] {#a#}, {#b#}, {#c#}" should generate the same output.

Note, the use of define provides an alternative to using the question variables.  Variables here are defined on the fly.  However, we do **not** recommend this is done routinely.

1. the readability of the code will suffer.
2. question variables are available elsewhere in the question, but `define` blocks are only available in that CASText.  This feature can also be used to your advantage.
