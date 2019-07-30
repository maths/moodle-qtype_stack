# Authoring quick start 7: turning simplification off

[1 - First question](Authoring_quick_start.md) | [2 - Question variables](Authoring_quick_start_2.md) | [3 - Feedback](Authoring_quick_start_3.md) | [4 - Randomisation](Authoring_quick_start_4.md) | [5 - Question tests](Authoring_quick_start_5.md) | [6 - Multipart questions](Authoring_quick_start_6.md) | 7 - Simplification | [8 - Quizzes](Authoring_quick_start_8.md)



This part of the authoring quick start guide deals with turning simplification off. The following video explains the process:

<iframe width="560" height="315" src="https://www.youtube.com/embed/Et1O2dibsDI" frameborder="0" allowfullscreen></iframe>
### Example question

Given a complex number \(z=ae^{ib}\), determine \(z^{n}\).

Where \(a\), \(b\) and \(n\) are randomly generated numbers.

## Simplification off

It is tempting when writing questions such as this to operate at the _level of display._  We could randomly generate \(a\), \(b\) and \(n\) and insert them into the question text.  For example:

```
 \(({@aa@}e^{{@bb@} i})^{@nn@}\)
```

What we are doing here is treating every variable separately instead of creating a single CAS object for the complex number.  This is ok, but causes problems and is difficult to read because it mixes CAS and LaTeX.

Hence, we would much rather have everything in one CAS object that is not simplified. The following is a single Maxima expression:

```
 {@(aa*%e^(bb*%i))^nn@}
```

(Notice that we are using variables names with more than one character. This is good practice, as single-character variables are meant for student input. Notice also that we precede standard mathematical variables with `%` when writing in Maxima. This is not mandatory, but is considered good practice.)

Of course, we don't want Maxima to _actually calculate the power_ just to _represent it!_ To see the difference, copy the following a STACK question's question variables:

```
simp:true;
a1: (3*%e^(%i*%pi/2))^4;
simp:false;
a2: (3*%e^(%i*%pi/2))^4;
```

Then type `Simplified: {@a1@} Not simplified: {@a2@}` somewhere in the question text. Preview to see the difference.

Solving problems at the level of the CAS instead at the level of the display is often better. To tell STACK to set `simp:false` throughout the question, scroll towards the bottom of the form and under `Options`, set `Question-level simplify` to `No`. Now Maxima will not simplify expressions before displaying them, so `{@2+5@}` will display as `2+5` instead of `7`.

This does have some drawbacks. Having switched off all simplification, we now need to turn it back on selectively! There are two ways to do this. Firstly, we can use commands of the following type:

```
aa : ev(2+rand(10),simp);
```

In particular, we can define the question variables as follows.

```
aa : ev(2+rand(10),simp);
bb : ev(2+rand(10),simp);
nn : ev(2+rand(5),simp);
qq : (aa*%e^(bb*%i))^nn;
```

An alternative, when many consecutive expressions need to be simplified, is the following:

```
simp : true;
aa : 2+rand(10);
bb : 2+rand(10);
nn : 2+rand(5);
simp : false;
qq : (aa*%e^(bb*%i))^nn;
```

### Unary minus

The particular circumstances will dictate if it is better to have lots of variables and use the display, or whether to turn `simp:false` and work with this.  A common problem arises with the unary minus. Consider a question text such as `Find {@aa@}+{@bb@}`. If \(`bb`<0\), the expression will be displayed as \(3+-5\), for example.  While simplification is "off", the display routines in Maxima will (often) cope with the unary minus in a sensible way.

# Next step

You should now be able to determine when it is sensible to turn off simplification. If you have been following this quick-start guide, you should already know some steps you can take to improve this question. For example, you could add [more specific feedback](Authoring_quick_start_3.md), [randomise your question](Authoring_quick_start_4.md) and add [question tests](Authoring_quick_start_5.md).

##### **The next part of the authoring quick start guide looks at [setting up a quiz](Authoring_quick_start_8.md).**