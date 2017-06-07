# Authoring quick start 3: turning simplification off

This is the third part of the [authoring quick start](Authoring_quick_start.md).  It assumes you have already worked through [authoring quick start 1](Authoring_quick_start.md) and [authoring quick start 2](Authoring_quick_start_2.md). The purpose is to discuss some common issues which arise when authoring particularly elementary questions where the CAS might do too much.

### Example question ###

Given a complex number \(z=ae^{ib}\) determine \(|z^{n}|\) and \(\arg(z^{n})\).

Where \(a\), \(b\) and \(n\) are randomly generated numbers.

## Simplification off ##

It is tempting when writing questions such as this to operate at the _level of display._  We could randomly generate \(a\), \(b\) and \(n\) and insert them into the question text.  For example:

     \(\right({@a@}e^{{@b@} i}\left)^{@n@}\)

What we are doing here is to treat every variable separately, not to create a single CAS object for the complex number.  This is ok, but causes problems and is difficult to read because it mixes CAS and LaTeX.

The alternative is to switch simplification off and use the CAS to represent expressions more directly.  The following is a single Maxima expression.

     {@(a*%e^(b*%i))^n@}

Of course, we don't want Maxima to _actually calculate the power_ just to _represent it!_  To see the difference, you can copy the following into a Maxima desktop session.

    kill(all);
    simp:true;
    (3*%e^(%i*%pi/2))^4;
    simp:false;
    (3*%e^(%i*%pi/2))^4;

Solving problems at the level of the CAS, not at the level of the display, is often better.    To tell STACK to set `simp:false` throughout the question scroll towards the bottom of the form and under `Options` set `Question-level simplify` to be `No`.

This does have some drawbacks.  Having switched off all simplification, we now need to turn it back on selectively! To do this, we use Maxima commands such as the following.

    a : ev(2+rand(15),simp);

In particular, we are going to define the question variables as follows.

    a : ev(2+rand(15),simp);
    b : ev((-1)^rand(2)*((1+rand(10)))/(2+rand(15)),simp);
    n : ev(3+rand(20),simp);
    q : a*%e^(b*%i*%pi);
    p : ev(mod(b*n,2),simp);

A useful alternative when many consecutive expressions need to be simplified is to use the following.

    simp : true;
    a : 2+rand(15);
    b : (-1)^rand(2)*((1+rand(10)))/(2+rand(15));
    n : 3+rand(20);
    simp : false;
    q : a*%e^(b*%i*%pi);
    p : ev(mod(b*n,2),simp);

The particular circumstances will dictate if it is better to have lots of variables and use the display, or whether to turn `simp:false` and work with this.  The difficulty is often with the unary minus.  Inserting numbers into expressions such as `y={@m@}x+{@c@}` if \(c<0\) is that it will be displayed as \(y=3x+-5\), for example.  While simplification is "off", the display routines in Maxima will (often) cope with the unary minus in a sensible way.

## The importance of the question note ##

Notice in defining `b` we have a quotient which might well "simplify" when fractions cancel.  Hence, there is not a one-one correspondence between the values of the random variables and actual question versions.  In some situations there may similarly not be a one-one correspondence between the values of specific variables and actual questions.  We cannot use the values of the question variables as a unique key to the question versions (although in this case it would be fine because all algebraic cancelling occurs within the definition of `b` and so we end up with a unique key).

Hence the teacher must leave a meaningful question note.  Two versions of a question are _defined_ to be the same if and only if the question note is the same.

The question note field is ["CAS text"](CASText.md), just like the question text.  We could write something like

    {@[a,b,n]@}

Or we could leave something more meaningful:

    {@q^n = a^n*(cos(p*%i*%pi)+%i*sin(p*%i*%pi))@}

Notice, we probably don't want to evaluate `a^n` here as it isn't likely to be "simpler".  It is up to the teacher, but putting the answer in the answer note helps if students come and ask you for the answer to their version of the question...

## Multi-part question ##

This question has two independent parts.  Hence, it probably needs two separate potential response trees to assess each part.

The question text might look something like the following:

    Given a complex number \(\displaystyle z={@q@}\) determine
    \( |z^{@n@}|= \) [[input:ans1]] [[validation:ans1]] [[feedback:prt1]]
    and \( \arg(z^{@n@})= \) [[input:ans2]] [[validation:ans2]] [[feedback:prt2]]

Remove the tag `[[feedback:prt1]]` from the Specific feedback field.  It is placed there by default, but can only occur once.

Update the form.  Because there are two inputs and two potential response trees these will be automatically created.

We need to supply model answers for each part.  In terms of our question variables,

    ans1 : a^n
    ans2 : p*%pi

## Assessment of the answers ##

It is unlikely that the purpose of this question is to decide if the student can work out powers of integers.  So we will assume it is acceptable to enter an answer such as \(a^b\) for the first part, rather than calculating this as an integer.  If the randomization was more conservative, this calculation might be an additional goal of the question.

Hence, for `prt1` fill in the following information

    SAns:ans1
    TAns:a^n
    answertest:AlgEquiv

If you really want to test for the integer, you need to calculate `ev(a^n,simp)` and then use the `EqualComAss` test to establish the student has the right integer.

For `prt2` we need to establish the student has the right argument.  Since this is modulo \(2\pi\) we can use the trigonometrical functions.  Fill in the following information

    SAns:[cos(ans2),sin(ans2)]
    TAns:[cos(n*b*%pi),sin(n*b*%pi)]
    answertest:AlgEquiv
    quiet:yes

The `AlgEquiv` test is happy to compare lists, but it makes no sense to tell the student which list element is "incorrect". Indeed, to do so would be confusing so we have selected the `quiet` option to suppress the automatically generated answer test feedback.

Again, if you want to enforce a test for the principle argument you will need to check the student's value also falls within the correct range using the `NUM-GTE` tests to establish "greater or equal to".  This can be done by adding an additional node.  It is probably a sensible idea to give feedback on both properties here.  The variable `p` in the question variables will help with this.

## Question tests ##

Please create some question tests!  This will save time in the long term, by enabling you to automatically test your question for each random version you wish to deploy.  You should create one test case for each outcome you expect. Here, we need

    ans1:a^n
    ans2:n*b*%pi

as the two correct answers, and then incorrect answers to ensure these are being trapped.  If you have enforced the _form_ of the answer, i.e. _integer representation_ for `ans1` and _principal argument_ for `ans2`, you need to add tests to distinguish between these.  For the first part \(a^n\) and the integer it represents, i.e. `ev(a^n,simp)`.  For the second part between \(b\times n\) and the variable `q`.

## General feedback ##

The general feedback (previously known as the worked solution) can show some of the steps in working.  For example,

    It makes sense that the index laws should still apply.  This is called De Moivre's theorem.
    \[ {@q^n@} ={@a^n@} e^{@b*n*%i*%pi@}.\]
    Recall that
    \[ e^{i\theta} = \cos(\theta)+i\sin(\theta).\]
    Working with the principle argument \( 0\leq \theta \leq 2\pi \) gives us
    \[ {@q^n@} = {@a^n@} e^{@b*n*%i*%pi@} = {@a^n@} e^{@ev(b*n,simp)*%i*%pi@} = {@a^n@} e^{@p*%i*%pi@}.\]

# Next steps #

Further examples are give in the page on [matrices](../CAS/Matrix.md).

The XML of this question is included with the [sample questions](Sample_questions.md).  Please look at the other [sample questions](Sample_questions.md) which are distributed with STACK for more examples.

