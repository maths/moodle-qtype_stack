# I have a misbehaving STACK question in a live Moodle quiz.  What should I do?

You can't remove or add a question to a live Moodle quiz, but you can modify it.  If you edit a live STACK question then students will see the updates.

1. Did you add any [question tests](Testing.md) to your question?  If not, add these now.  The testing page has a lot of information, including values of the question variables, and gives specific information of what is going into the answer tests and what values are returned.  This page will also show some runtime errors, not all of which are displayed to students (and so are invisible on the preview testing page).
2. Duplicate the question, rather than editing a live question in a quiz.  Decide what changes need to be made before editing the broken live question.  The database has no "undo" so you cannot revert once the question is saved, and you can only test a saved question...
3. There are "regrade" and "dry-run regrade" options in the Moodle quiz.  If you are happy with the new behaviour of the question you can regrade. You can also manually assign a mark in the Moodle quiz. 
4. It is ok to add nodes to an existing PRT and "regrade".  However, the Moodle DB caches outcomes, so unless the score changes you will not see new stats.  If you are using the basic question usage report to generate stats you might need to change the marks, regrade, change the marks back and regrade again to update the usage report stats.

If you cannot apply a patch, then create a copy of the question and put it in a new category "for next year".  It is normally best to fix it now!

It is possible to change the weight of an individual question within a live quiz and re-grade. If the live question is impossible to fix you do have the option to disregard the question by assigning it a mark of \(0\) in the quiz.

## Fixing broken MathJax and Javascript

A relatively common problem is that MathJax stops working half way through a question resulting in an equation showing up as a `[Math Error]` or similar error message, (or just showing the source).

MathJax is a javascript library, and sometimes when it encounters malformed HTML it gives up.

The first place to look is to check the integrity of the HTML in your question.  Do not use the WYSIWYG editors in Moodle.  The editors cut and paste all sorts of HTML formatting into LaTeX equations, e.g. "span" tags, which break MathJax.

1. Choose the Moodle "plain textarea" editor from the user preferences.
2. Edit your question and carefully tidy up your html.  Things to look for:
  * span tags inside LaTeX equations.  Make sure your LaTeX has no HTML inside it, (entities like `&lt;` are fine).
  * the `[[input:....]]` and other tags should not be inside random span tags either.

If your javascript code, e.g. JSXGraph, also stops working this is almost certainly because it has been edited with a WYSIWYG editor which has "protected" your code and so broken it.  Check with the plain textarea editor.

## Sorting out broken random versions.

__Do not change anything which alters the randomisation of variants.__

In particular, do not add, remove or re-order `rand` statements or other statement which will increment the state of the pseudo random number generation. Students get a "seed" which starts the pseudo-random number generator.  If your changes change the random version, then the students' previous answers will still stand, and will now most likely be "wrong".  Minor typographical mistakes can be fixed, but often there is nothing you can do to fix seriously bad random versions.  This is why we have the "deploy" system and [question tests](Testing.md) so question variants can be checked in advance....

Modifying the question variables in a way which does not alter random versions should be fine. E.g. adding variables for test case consturction or improving a worked solution.

If you have a single mis-behaving random variant, you can try the following type of approach.

Imagine your question variables are

    n1:rand(10);

and your question says _``Find a numerical approximation to {@1/n1@} to three decimal places''_.  Clearly the case `n1=0` will throw a division by zero error, `n1=1` will be (basically) pointless from an educational perspective and so on.  However, the other students who have already taken a live quiz will have been given, and answered, the question you intended.  It is tempting to change the question variables to

    n1:2+rand(8);

but of course this will change the question for everyone.  Instead you could just change the question variables to the following.

    n1:rand(10);
    n1:if is(n1<2) then 3 else n1;

This will fix the broken variants, without changing all the others. Of course, you still have to deal with students who originally got a broken variant (grant an extra attempt at the quiz using the user override feature of the quiz?) and your `n1=3` version will be three times as likely to occur.  If you use

    n1:if is(n1<2) then (2+rand(8)) else n1;

then any subsequent random number generation will be changed as well.  Indeed, since the pseudo-random number generation is not really random then the second `rand` statement will only return a single value anyway because random variants are seeded from a single seed.  To generate a second random number you would need to have

    n1:rand(10);
    n2:2+rand(8);
    n1:if is(n1<2) then n2 else n1;

and make the question note include both `n1` and `n2` to show there are distinct random versions including both `n1` and `n2` as random variables.

Of course, if the question author has created [question tests](Testing.md) in the first place, and deployed random variants to check in advance, this problem would never have occured!

## I forgot to deploy random variants.

Moodle creates a random integer to seed the random number generation for each question.  The seed is stored in the
question-attempt and it is picked at the initiation of the quiz either from deployed ones or, if no variants have been deployed, is chosen freely.

Students will therefore have their seed picked at the moment they start their quiz.  The seed remains unchanged regardless of whether any variants are subsequenetly deployed, removed etc.  Hence, deploying or undeploying variants will have no effect on students who already started the quiz.

