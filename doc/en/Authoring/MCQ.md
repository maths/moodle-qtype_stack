# Multiple choice questions

The whole point of STACK is not to use multiple choice questions, but instead to have the student enter an algebraic expression!  That said their are occasions where it is very useful, if not necessary, to use multiple choice questions in their various forms.  STACK's use of a CAS is then very helpful to generate randomly generated multiple choice questions based on the mathematical values. 

This can also be one input in a multi-part randomly generated question. E.g. you might say "which method do you need to integrate \( \sin(x)\cos(x) \)?" and give students the choice of (i) trig functions first, (ii) parts, (iii) substitution, (iv) replace with complex exponentials.  (Yes, this is a joke: all these methods can be made to work here!)  Then another algebraic input can be used for the answer.

Please read the section on [inputs](Inputs.md) first.

## Model answer ##

The "model answer" must be supplied in a particular form as a list of `[value, correct(, display)]`.

* `value` is the value of the teacher's answer
* `correct` must be either `true` or `false`.  If it is not `true` then it will be considered to be `false`!
* (optional) `display` is another cas expression to be displayed in place of `value`.  Be cautious!  This can be a string value here, but it will be passed through the CAS if you choose the LaTeX display option below.  `display` is only used in constructing the question.  The STACK will take `value` as the student's answer internally, regardless of what is set here.

For example

     ta:[[diff(p,x),true],[p,false],[int(p,x),false]]

Note, that the optional `display` field is *only* used when constructing the choices seen by the student when displaying the question.  The student's answer will be the `value`, and this value is normally displayed to the student using the validation feedback, i.e. "Your last answer was interpreted as...".  A fundamental design principal of STACK is that the student's answer should be a mathematical expression, and this input type is no exception.  In situations where there is a significant difference between the optional `display` and the `value` which would be confusing, the only current option is to turn off validation feedback.  Afterall, this should not be needed anyway with this input type.  In the example above when a student is asked to choose the right method the `value` could be an integer and the display is some kind of string.  In this example the validation feedback would be confusing, since an integer (which might be suffled) has no correspondence to the choices selected.  *This behaviour is a design decision and not a bug! It may change in the future if there is sufficient demand, but it requires a significant change in STACK's internals to have parallel "real answer" and "indicated answer".  Such a change might have other unintended and confusing consequences.* 

## Internals ##

This input type turns the student' answer into a Maxima list.  Hence, if you are expecting a single value (e.g. from a select or radio type) and you want an expression in the potential response tree you need to use the following to take the first element of the list.

    first(ans1)

This design decision ensures there is no abiguity in the type of object returned.  Switching from radio to checkboxes will not break a PRT because of mis-matched types.

## Extra options ##

The dropdown input type makes use of the Extra options field to pass in options.  These options are not case sensitive.  This must be a comman separated list of values as follows.

* `select` use the dropdown select method.  There should be no need to use this option as it is the default.
* `radio` Changes the interaction to radio buttons to select one or more.  This is probably better for more complex expressions which benefit from being displayed using LaTeX.
* `checkbox` Changes the interaction to allow more than one option to be returned via checkboxes.  

We can reorder the values by using shuffle.

* `shuffle` If this option is encoutered, then the question type will randomly shuffle the non-trivial options. The default is not to shuffle the options, but to list them as ordered in the list.

The way the items are displayed can be controlled by the following options. 

* `LaTeX` The defaut option is to use LaTeX to display the options, using a displayed maths environment `\[...\]`.  This is probably better for radio and checkboxes.  It sometimes works in dropdowns, but not always and we need to test this in a wider variety of browsers.
* `LaTeXinline` use LaTeX to display the options, using the inline maths environment `\(...\)`.
* `casstring` does not use the LaTeX value, but just prints the castring value in `<code>...</code>` tags.



