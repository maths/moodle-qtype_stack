# Multiple choice questions

The whole point of STACK is not to use multiple choice questions, but instead to have the student enter an algebraic expression!  That said their are occasions where it is very useful, if not necessary, to use multiple choice questions in their various forms.  STACK's use of a CAS is then very helpful to generate random versions of multiple choice questions based on the mathematical values. 

This can also be one input in a multi-part randomly generated question. E.g. you might say "which method do you need to integrate \( \sin(x)\cos(x) \)?" and give students the choice of (i) trig functions first, (ii) parts, (iii) substitution, (iv) replace with complex exponentials.  (Yes, this is a joke: all these methods can be made to work here!)  Another algebraic input can then be used for the answer.

Please read the section on [inputs](Inputs.md) first.  If you are new to STACK please note that in STACK MCQs are *not* the place to start learning how to author questions.  Please look at the [authoring quick-start guide](Authoring_quick_start.md).  Multiple choice input types return a CAS object which is then assessed by the potential response tree.  For this reason, these inputs do not provide "feedback" fields for each possible answer, as does the Moodle multiple choice input type.

The goal of these input types is to provide *modest* facilities for MCQ.  An early design decision was to restrict each of the possible answers to be a CAS expression.  In particular, we decided *NOT* to make each possible answer [castext](CASText.md).  Adopting castext would have provided more flexibility but would have significantly increased the complexity of the internal code. If these features are extensively used we will consider modifying the functionality.  Please contact the developers with comments.

## Model answer ##

This input type uses the "model answer" both to input the teacher's answer and the other options. In this respect, this input type is unique, and the "model answer" field does *not* contain just the teacher's model answer.  Constructing a correctly formed model answer is complex, and so this input type should be considered "advanced".  New users are adviced to gain confidence writing questions with algebraic inputs first, and gain experience in using Maxima lists.

The "model answer" must be supplied in a particular form as a list of lists `[[value, correct(, display)], ... ]`.

* `value` is the value of the teacher's answer
* `correct` must be either `true` or `false`.  If it is not `true` then it will be considered to be `false`!
* (optional) `display` is another CAS expression to be displayed in place of `value`.  Be cautious!  This can be a string value here, but it will be passed through the CAS if you choose the LaTeX display option below.  `display` is only used in constructing the question.  The STACK will take `value` as the student's answer internally, regardless of what is set here.

For example

     ta:[[diff(p,x),true],[p,false],[int(p,x),false]]

At least one of the choices must be considered `correct`.  However, the `true` and `false` values are only used to construct the "teacher's correct answer".  You must still use a [potential response tree](Potential_response_trees.md) to assess the student's answer as normal. 

Note, that the optional `display` field is *only* used when constructing the choices seen by the student when displaying the question.  The student's answer will be the `value`, and this value is normally displayed to the student using the validation feedback, i.e. "Your last answer was interpreted as...".  A fundamental design principal of STACK is that the student's answer should be a mathematical expression, and this input type is no exception.  In situations where there is a significant difference between the optional `display` and the `value` which would be confusing, the only current option is to turn off validation feedback.  After all, this should not be needed anyway with this input type.  In the example above when a student is asked to choose the right method the `value` could be an integer and the display is some kind of string.  In this example the validation feedback would be confusing, since an integer (which might be shuffled) has no correspondence to the choices selected.  *This behaviour is a design decision and not a bug! It may change in the future if there is sufficient demand, but it requires a significant change in STACK's internals to have parallel "real answer" and "indicated answer".  Such a change might have other unintended and confusing consequences.* 

Normally we don't permit duplicate values in the values of the teacher's answer.  If the input type receives duplicate values STACK will throw an error.  This probably arises from poor randomisation.  However it may be needed.  If duplicate entries are permitted use the display option to create unique value keys with the same display. *This behaviour is a design decision may change in the future.*

When STACK displays the "teacher's answer", e.g. after a quiz is due, this will be constructed from the `display` fields corresponding to those elements for which `correct` is `true`.  I.e. the "teacher's answer" will be a list of things which the student could actually select.  Whether the student is able to select more than one, or if more than one is actually included, is not checked.   The teacher must indicate at least one choice as `true`.  

If you need "none of these" you must include this as an explicit option, and not rely on the student not checking any boxes in the checkbox type.  Indeed, it would be impossible to distinguish the active selection of "none of these" from a passive failure to respond to the question.

## Internals ##

The dropdown and radio inputs return the `value`, but the checkbox type returns the student's answer as Maxima list, even if they have only chosen one option.

If, when authoring a question, you switch from radio/dropdown to checkboxes or back, you will probably break a PRT because of mis-matched types.

For the select and radio types the first option on the list will always be "Not answered".  This enables a student to retract an answer and return a "blank" response.

For the checkbox type there is a fundamental ambiguity between a blank response and actively not selecting any of the provided choices, which indicates "none of the others".  Internally STACK has a number of "states" for a student's answer, including `BLANK`, `VALID`, `INVALID`, `SCORE` etc.  A student who has not answered will be considered `BLANK`. This is not invalid, and potential response trees which rely on this input type will not activate.  To enable a student to indicate "none of the others", the teacher must add this as an explicit option.  Note, this will not return an empty list as the answer as might be expected: it will be the `value` of that selection.  For the radio and dropdown types STACK always adds a "not answered" option as the first option.  This allows a student to retract their choice, otherwise they will be unable to "uncheck" a radio button, which will be stored, validated and possibly assessed (to their potential detriment).

We did not add support for a special internal "none of the others" because the teacher still needs to indicate wether this is the true or false answer to the question.  To support randomisation, this needs to be done as an option in the teacher's answer list.

## Extra options ##

These input types make use of the "Extra options" field of the input type to pass in options.  These options are not case sensitive.  This must be a comma separated list of values as follows, but currently the only option is to control the display of mathematical expressions.

The way the items are displayed can be controlled by the following options. 

* `LaTeX` The default option is to use LaTeX to display the options, using an inline maths environment `\(...\)`.  This is probably better for radio and checkboxes.  It sometimes works in dropdowns, but not always and we need to test this in a wider variety of browsers.
* `LaTeXdisplay` use LaTeX to display the options, using the display maths environment `\[...\]`.
* `LaTeXinline` use LaTeX to display the options, using the inline maths environment `\(...\)`.
* `casstring` does not use the LaTeX value, but just prints the casstring value in `<code>...</code>` tags.

## Randomly shuffling the options ##

To randomly shuffle the options create the list in the question variables and use the Maxima command `random_permutation` in the question variables.

For example, the question variables might look like the following.

    /* Create a list of potential answers. */
    p:sin(2*x);
    ta:[[diff(p,x),true],[p,false],[int(p,x),false],[cos(2*x)+c,false]];
    /* The actual correct answer.    */
    tac:diff(p,x)
    /* Randomly shuffle the list "ta". */
    ta:random_permutation(ta);
    /* Add in a "None of these" to the end of the list.  The Maxima value is the atom null. */
    tao:[null, false, "None of these"];
    ta:append(ta,[tao]);

These command ensure (1) the substantive options are in a random order, and (2) that the `None of these` always comes at the end of the list. Note, the value for the `None of these` is the CAS atom `null`.  In Maxima `null` has no special significance but it is a useful atom to use in this situation.

As the Question Note, you might like to consider just takeing the first item from each list, for example:

    @maplist(first,ta)@.  The correct answer is @tac@.

This note stores both the correct answer and the order shown to the student without the clutter of the `true/false` values or the optional display strings.  Remember, random versions of a question are considered to be the same if and only if the question note is the same, so the random order must be part of the question note if you shuffle the options.

## Dealing with strings in MCQ ##

A likely situation is that a teacher wants to include a language string as one of the options for a student's answer in a multiple choice question.

Recall: *A fundamental design principal of STACK is that the student's answer should be a mathematical expression which can be manipulated by the CAS as a valid expression.* Students are very limited in the keywords they are permitted to use in an input type.  It is very likely that strings will contain keywords forbidden in student expressions.

One option to overcome this is to do something like this as one option in the teacher's response:

    [C, false, "(C) None of the other options"]

The optional display part of this input is displayed to the student.  Their answer is the (valid) CAS atom `C` which the PRT will deal with appropriately.  This work-around is unlikely to sit well with the `shuffle` option.  As we said, the current goal is to only provide modest MCQ facilities.

The quotation marks will be removed from strings, and the strings will not be wrapped `<code>...</code>` tags or LaTeX mathematics environments.

Question authors should consider using the Moodle MCQ question type in addition to these facilities for purely text based answers.

## Writing question tests ##

Quality control of questions is important.  See the notes on [testing](Testing.md) questions.  

When entering test cases the question author must type in the CAS expression they expect to be the `value` of the student's answer (NOT the optional `display` field!).  For example, if the teacher's answer (to a checkbox) question is the following.

     ta:[[x^2-1,true],[x^2+1,false],[(x-1)*(x+1),true],[(x-i)*(x+i),false]]

Then the following test case contains all the "true" answers.

     [x^2-1,(x-1)*(x+1)]

There is currently minimal checking that the string entered by the teacher corresponds to a valid choice in the input type.  If your testcase returns a blank result this is probably the problem.     
     