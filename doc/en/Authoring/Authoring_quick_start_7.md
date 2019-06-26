# Authoring Quick Start 7: Turning Simplification Off

Authoring Quick Start: [1 - First Question](Authoring_quick_start.md) | [2 - Question Variables](Authoring_quick_start_2.md) | [3 - Feedback](Authoring_quick_start_3.md) |[4 - Randomisation](Authoring_quick_start_4.md) | [5 - Question Tests](Authoring_quick_start_5.md) | [6 - Multiple-part Questions](Authoring_quick_start_6.md) | <u>7 - Simplification</u> | [8 - Quizzes](Authoring_quick_start_8.md)



This part of the Authoring Quick Start Guide deals with turning simplification off. The following video explains the process:

EMBED VIDEO HERE

### Example question

Given a complex number \(z=ae^{ib}\), determine \(|z^{n}|\).

Where \(a\), \(b\) and \(n\) are randomly generated numbers.

## Simplification off

It is tempting when writing questions such as this to operate at the _level of display._  We could randomly generate \(a\), \(b\) and \(n\) and insert them into the question text.  For example:

```
 \(\right({@aa@}%e^{{@bb@} %i}\left)^{@nn@}\)
```

(Notice that we are using variables names with more than one character. This is good practice, as single-character variables are meant for student input. Notice also that we precede standard mathematical variables with `%` . This is not mandatory, but is considered good practice.)

What we are doing here is treating every variable separately instead of creating a single CAS object for the complex number.  This is ok, but causes problems and is difficult to read because it mixes CAS and LaTeX.

Hence, we would much rather have everything in one CAS object that is not simplified. The following is a single Maxima expression:

```
 {@(aa*%e^(bb*%i))^nn@}
```

Of course, we don't want Maxima to _actually calculate the power_ just to _represent it!_  To see the difference, copy the following a STACK question's question variables:

```
simp:true;
a1: (3*%e^(%i*pi/2))^4;
simp:false;
a2: (3*%e^(%i*pi/2))^4;
```

Then type `Simplified: {@a1@} Not simplified: {@a2@}` somewhere in the question text. Preview to see the difference.

Solving problems at the level of the CAS instead at the level of the display is often better. To tell STACK to set `simp:false` throughout the question, scroll towards the bottom of the form and under `Options`, set `Question-level simplify` to `No`.

This does have some drawbacks.  Having switched off all simplification, we now need to turn it back on selectively! There are two ways to do this. Firstly, we can use commands of the following type:

```
aa : ev(2+rand(15),simp);
```

In particular, we can define the question variables as follows.

```
aa : ev(2+rand(10),simp);
bb : ev(2+rand(10),simp);
nn : ev(2+rand(5),simp);
qq : aa*%e^(bb*%i*pi);
```

An alternative, when many consecutive expressions need to be simplified, is the following:

```
simp : true;
aa : 2+rand(10);
bb : 2+rand(10);
nn : 2+rand(5);
simp : false;
qq : aa*%e^(bb*%i*pi);
```

### Unary minus

The particular circumstances will dictate if it is better to have lots of variables and use the display, or whether to turn `simp:false` and work with this.  A common problem arises with the unary minus. Consider a question text such as `y={@aa@}+{@cc@}`. If \(`cc`<0\), the expression will be displayed as \(y=3+-5\), for example.  While simplification is "off", the display routines in Maxima will (often) cope with the unary minus in a sensible way.

# Next step

You should now be able to determine when it is sensible to turn off simplification. If you have been following this quick-start guide, you should already know some steps you can take to improve this question. For example, you could add [more specific feedback](Authoring_quick_start_3.md), [randomise your question](Authoring_quick_start_4.md) and add [question tests](Authoring_quick_start_5.md).

##### **The next part of the authoring quick start guide looks at [setting up a quiz](Authoring_quick_start_8.md).**