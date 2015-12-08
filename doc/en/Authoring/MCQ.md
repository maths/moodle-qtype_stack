# Multiple choice questions

The whole point of STACK is not to use multiple choice questions, but instead to have the student enter an algebraic expression!  That said their are occasions where it is very useful, if not necessary, to use multiple choice questions in their various forms.  STACK's use of a CAS is then very helpful to generate randomly generated multiple choice questions based on the mathematical values. 

This can also be one input in a multi-part randomly generated question. E.g. you might say "which method do you need to integrate \( \sin(x)\cos(x) \)?" and give students the choice of (i) trig functions first, (ii) parts, (iii) substitution, (iv) replace with complex exponentials.  (Yes, this is a joke: all these methods can be made to work here!)  Then another algebraic input can be used for the answer.

Please read the section on [inputs](Inputs.md) first.

## model answer ##

The "model answer" must be supplied in a particular form as a list of `[value, correct(, display)]`.

* `value` is the value of the teacher's answer
* `correct` must be either `true` or `false`.  If it is not `true` then it will be considered to be `false`!
* (optional) `display` is another cas expression to be displayed in place of `value`.  Be cautious!  This can be a string value here, but it will be passed through the CAS if you choose the LaTeX display option below.

For example

     ta:[[diff(p,x),true],[p,false],[int(p,x),false]]

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



