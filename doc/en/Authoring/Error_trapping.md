# Error trapping

How to I trap errors generated from a student's answer?

Errors are generated for a number of reasons.  Mostly, they are important and should not be ignored!  Normally, students should not be penalised if a question "does not work" because the PRT generated an error.  Any runtime error during traversing a PRT will cause an error.  This error will stop further exectution of the tree, and students will see a runtime error message.  This will be flagged in the response summary as `[RUNTIME_ERROR]`.


A student's answer can generate mathematical errors for a number of reasons, but the most common is evaluating a function outside its mathematical domain.  Common elementary examples are

1. Division by zero.
2. Evaluating \(\tan(x)\) at points like \(\pi/2\).  (Try `tan(%pi/2)` in Maxima).

Sometimes you need to trap and ignore errors like this in the student's answer, or create a "guard clause" to decide if part of a PRT should be executed or not.

Sometimes these are inevitable and need to be ignored.

If one of the feedback variables throws an error then this will not stop the PRT executing.  If there is an error, this will be flagged in the response summary as `[RUNTIME_FV_ERROR]` (fv here means feedback variables).  You can, and should use the feedback variables to trap any errors you would like to condone.  E.g. you could define the following in the feedback variables.

    sa1:errcatch(tan(ans1));

If `ans1:%pi/2` then `sa1` will be the empty list `[]`.  Otherwise, `sa1` will be the list containg the result, and you can use `first(sa1)` in a PRT.

To trap a runtime error, or create a guard clause, you do need extra node in your tree (e.g. is `sa1` empty) or an `if` statement in your feedback variables.  The default is to show errors, so if you choose to test for and condone errors you need extra clauses.

