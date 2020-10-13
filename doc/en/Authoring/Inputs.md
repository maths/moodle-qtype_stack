# Inputs

Inputs are the points at which the student interacts with the question.  
The default (and prototype) is an HTML input box into which a student is expected to type an algebraic expression.

* Only the [question text](CASText.md#question_text) may have inputs.
* Inputs are not required. Hence it is possible for the teacher to make a statement which asks for no response from the student, i.e. a rhetorical question.
* A question may have as many inputs as needed.
* Inputs can be positioned anywhere within the [question text](CASText.md#question_text). MathJax does not currently support the inclusion of inputs within equations.
* All inputs return a Maxima expression.  This might be just the student's answer (in the case of an algebraic input).  MCQ inputs also return a valid Maxima expression.
* In a multi-part question avoid having inputs which differ only by case sensitivity.  E.g. do not have `[[input:a]]` and `[[input:A]]` in the same question.  (Some database defaults have case insensitive unique keys (!) and in that case this will cause a database error.  See the [installation instructions](../Installation/index.md)).

The position of an input in the [question text](CASText.md#question_text) is denoted by

    [[input:ans1]]

Here `ans1` is the name of a [Maxima](../CAS/Maxima.md) variable to which the student's answer is to be assigned.
This must only be letters followed (optionally) by numbers, as in this example. No special characters are permitted.
The input name cannot be more than 18 characters long.

Feedback as to the syntactic validity of a response is positioned using a corresponding tag

    [[validation:ans1]]

This tag must be included even if validation is suppressed with an option (see below) and is automatically generated after the input if it does not exist.

We expose the exact behaviour of the validation by giving registered users access to STACK's test suite validation of student's answers.  This can be found on a live server at https://stack-demo.maths.ed.ac.uk/demo/question/type/stack/studentinputs.php

Each input may have a number of options and this is potentially complex area with a large range of possibilities.

The basic idea is to reject things as "invalid" to stop students being penalized on a technicality.  This might be requiring an equation, or making floating-point numbers within an expression forbidden.

## Basic options ##

### Student's Answer Key ###  {#Answer_Key}

Every input must have a unique answer key.  This is set in the Question text using the following tag, where `ans1` is the variable name to which the student's answer is assigned.

    [[input:ans1]]

Internally you can refer to the student's answer using the variable name `ans1` in the potential response tree, feedback variables and feedback text. The worked solution (general feedback) may not depend on the inputs.

### Model answer ###  {#model_answer}

**This field is compulsory.** Every input must have an answer, although this answer is not necessarily the unique correct answer, or even "correct"!  This value be displayed to the student as the correct answer.  We recommend you use a question variable for this field so it can be used in the other parts of the question, e.g. the potential response trees.

## Input type ##

Currently STACK supports the following kinds of inputs.  These have a variety of options, as explained below.

#### Algebraic input ####

The default: a form box into which a student is expected to type an algebraic expression.

#### Numerical ####

This input type _requires_ the student to type in a number of some kind.  Any expression with a variable will be rejected as invalid.

Note, some things (like forbid floats) can be applied to any numbers in an algebraic input; other tests (like require n decimal places) cannot and can only be applied to a single number in this input type.

See the specific documentation for more information:  [Numerical input](Numerical_input.md).

#### Scientific units ####

The support for scientific units includes an input type which enables teachers to check units as valid/invalid. See the separate documentation for [units](Units.md).

#### Matrix ####

The size of the matrix is inferred from the model answer.
STACK then adds an appropriate grid of boxes (of size Box Size) for the student to fill in.
This is easier than typing in [Maxima](../CAS/Maxima.md)'s matrix command, but does give the game away about the size of the required matrix.

_The student may not fill in part of a matrix._  If they do so, the remaining entries will be completed with `?` characters which render the attempt invalid. STACK cannot cope with empty boxes here.

We cannot use the `EMPTYANSWER` tag for the teacher's answer with the matrix input, because the size of the matrix is inferred from the model answer.  If a teacher really wants a correct answer to be empty inputs then they must use a correctly formatted matrix with `null` values

    ta:transpose(matrix([null,null,null]));

#### Text area ####

Enter algebraic expressions on multiple lines.  STACK passes the result to [Maxima](../CAS/Maxima.md) as a list.
Note, the teacher's answer and any syntax hint must be a list!  If you just pass in an expression strange behaviour may result.

#### Equivalence reasoning input ####

The purpose of this input type is to enable students to work line by line and reason by equivalence.  See the specific documentation for more information:  [Equivalence reasoning](../CAS/Equivalence_reasoning.md).
Note, the teacher's answer and any syntax hint must be a list!  If you just pass in an expression strange behaviour may result.

#### True/False ####

Simple drop down. A Boolean value is assigned to the variable name.

If the teacher's correct answer should leave this blank (e.g. not answered at all) then use the tag `EMPTYANSWER`. (There are some edge cases where only some inputs are used in the correct answer to a question, so not answering is correct here).  If you use the extra option `allowempty` then empty answers are considered valid, and the value of this input is `EMPTYANSWER`.

#### Dropdown/Checkbox/Radio ####

The dropdown, checkbox and radio input types enable teachers to create [multiple-choice](Multiple_choice_questions.md) questions.  See the separate documentation.

#### String input ####

This is a normal input into which students may type whatever they choose.  It is always converted into a Maxima string internally.
Note that there is no way whatsoever to parse the student's string into a Maxima expression.  If you accept a string, then it will always remain a string! You can't later check for algebraic equivalence. The only tests available will be simple string matches, etc.

#### Notes input ####

This input is a text area into which students may type whatever they choose.  It can be used to gather their notes or "working".  However, this input is always considered as "invalid", so that any potential response tree which relies on this input will never get evaluated!

This input type can be used for

1. surveys;
2. answers which are not automatically marked, contributing to [semi-automatic marking](Semi-automatic_Marking.md).

The notes input has a special extra option `manualgraded`, and the default option value is `manualgraded:false`.  If you specify `manualgraded:true` then the _whole STACK quesion_ will require manual grading!

#### Single Character ####

A single letter can be entered.  This is useful for creating multiple-choice questions, but is not used regularly.

## Options ##

### Input Box Size ### {#Box_Size}

The width of the input box.

### Insert Stars ### {#Insert_Stars}

Insert Stars affect the way STACK treats the validation of CAS strings.

Some patterns must always be wrong.  For example  `)(` must be missing a star, and so this pattern is always included.

There are six options.

* Don't insert stars:  This does not insert `*` characters automatically.  If there are any pattern identified the result will be an invalid expression.
* Insert `*`s for implied multiplication.  If any patterns are identified as needing `*`s then they will automatically be inserted into the expression quietly.
* Insert `*`s assuming single character variable names.  In many situations we know that a student will only have single character variable names.  Identify variables in the student's answer made up of more than one character then replace these with the product of the letters.
  * Note, the student's formula is interpreted and variables identified, so \(\sin(ax)\) will not end up as `s*i*n*(a*x)` but as `sin(a*x)`.
  * Note, in interpreting the student's formula we build an internal tree in order to identify variable names and function names.  Hence \(xe^x\) is interpreted as \( (xe)^x \).  We then identify the variable name `xe` and replace this as `x*e`.  Hence, using this option we have `xe^x` is interpreted as `(x*e)^x` NOT as `x*e^x` which you might expect.  

There are also additional options to insert multiplication signs for spaces.

* Insert stars for spaces only
* Insert stars for implied multiplication and for spaces
* Insert stars assuming single-character variable names and for spaces

If a space is taken for multiplication what should we do with \(\sin\ x\)?  Currently this is transformed to \(\sin \times x\) and then rejected as invalid as you can't multiply the function name by its argument.  Use these latter options with caution: in the long run students are likely to need to use a strict syntax with machines, and letting them use spaces now might be a disservice.

It is often very important to have some on-screen representation of multiplication, e.g. as a dot, so the student can see at the validation that `xe^x` is interpreted 

1. as \( (x\cdot e)^x\) if we assume single character variable names, and
2. as \( xe^x\) if we just "Insert `*`s for implied multiplication".  The absence of the dot here is key.

### Syntax Hint {#Syntax_Hint}

A syntax hint allows the teacher to give the student a pro-forma in the input box.
This can include '?' characters.
The syntax hint will appear in the answer box whenever this is left blank by the student.
For example, rather than having to type

    matrix([1,2],[3,4])

the teacher may want to provide an answer box which already contains the string

    matrix([?,?],[?,?])

instead. The student then need only to edit this, to replace ?s with their values.
This helps reduce syntax error problems with more difficult syntax issues.
The ? may also be used to give partial credit. Of course it could also be used for general expressions such as:

    x^2+?*x+1

### Forbidden words ### {#Forbidden_Words}

This is a comma-separated list of text strings which are forbidden in a student's answer.  If one of these strings is present then the student's attempt will be considered invalid, and no penalties will be given. This is an unsophisticated string match.

Note, any question variable names used in the question variables are automatically forbidden (otherwise the student could potentially use the variable name you have defined, which might be the correct answer).  If you want to allow question variables, you must explicitly use the allowed words field, see below.

Note that the string `*` is literally taken as `*` and is not a wild card.  Teachers may ask a student to calculate `2*3` and hence need to forbid multiplication in an answer.

If you wish to forbid commas, then escape it with a backslash.

There are groups of common keywords which you can forbid simply as

* `[[BASIC-ALGEBRA]]` common algebraic operations such as `simplify`, `factor`, `expand`, `solve`, etc.
* `[[BASIC-CALCULUS]]` common calculus operations such as `int`, `diff`, `taylor`, etc.
* `[[BASIC-MATRIX]]` common matrix operations such as `transpose`, `invert`, `charpoly`, etc.

If you have suggestions for more lists, or additional operations which should be added to the existing lists, please contact the developers.


### Allowed words ### {#Allowed_Words}

By default, arbitrary function or variable names of more than two characters in length are not permitted.  This is a comma-separated list of function or variable names which are permitted in a student's answer.

Note the allowed words permit the teacher to override some (but not all) of the strings which are considered to be invalid by default for student input.  For example, `Sin` (capital "S") has specific feedback.  If you need this in a question you have to allow it here.  Similarly `In` ("India November") is mistakenly used by students for the natural logarithm rather than `ln` ("Lima November").  Hence by default this triggers specific feedback.  You can allow `In` here.

### Forbid Floats ### {#Forbid_Floats}

If set to `yes`, then any answer of the student which has a floating-point number
will be rejected as invalid. Students sometimes use floating-point numbers when
they should use fractions. This option prevents problems with approximations being used.

### Require lowest terms ### {#Require_lowest_terms}

When this option is set to `yes`, any coefficients or other rational numbers in an
expression, must be written in lowest terms.  Otherwise the answer is rejected as "invalid".
This enables the teacher to reject answers, and not consider them further.  Note that at most one number
can have a minus sign and two unary minus signs are considered to be something which should be cancelled.

### Check Students answer's type ### {#Check_Type}

If this option is set to `yes` then unless the student's expression is the same
[Maxima](../CAS/Maxima.md#Types_of_object) as the teacher's correct answer,
then the attempt will be rejected as invalid.

This is very useful for ensuring the student has typed in an "equation", such as \(y=mx+c\)
and not an expression such as \(mx+c\).  Remember, you can't compare an expression with an equation!

Another useful way of avoiding this problem is to put a LaTeX string such as \(y=\) just before the input.  E.g.

    \(y=\)[[input:ans1]].

### Student must verify ### {#Student_must_verify}

Specifies whether the student's input is presented back to them before scoring as part of a two-step validation process.  
Typically the student's mathematical expression is displayed in traditional form.  
This is useful for complex algebraic expressions but not needed for constrained input like `yes`/`no`.

Experience strongly supports the use of this two-step verification process.  
Errors will always be displayed and expressions with errors rejected as invalid. 
Potential response trees will not execute with invalid input.

The next option controls how the validation feedback is displayed. 
Note, it is not possible to require a two-step validation but not show some validation feedback.

### Show validation ### {#Show_validation}

Feedback to students is in two forms.

* feedback tied to inputs, in particular if the answer is invalid.
* feedback tied to each potential response tree.

Setting this option displays any feedback from this input, including echoing back their expression in traditional two-dimensional notation.  Generally, feedback and verification are used in conjunction.  Errors will always be displayed.  In addition to simply displaying the student's expression, the teacher can display the list of variables which occurs in the expression.  From experience, this is helpful in letting students understand the idea of variable and to spot case insensitivity or wrong variable problems.

The "compact" version removes most of the styling.  This is needed when the answer is part of a table.

### Extra option: hideanswer ###

Users are increasingly using inputs to store _state_, which makes no sense for a user to see.  For example, when using [JSXGraph](JSXGraph.md) users transfer the configuration of the diagram into an input via JavaScript.  In many situations, it makes no sense for the student to see anything about this input.  The validation can be switched off with the regular "show validation" option, the input box itself can be hidden with JavaScript/CSS.  Putting `hideanswer` in the extra options stops displaying the "teacher's answer", e.g. at the end of the process.

Do not use this option in questions in place of the normal quiz settings.  For this reason it is only supported in the string input type.

### Extra option: allowempty ###

Normally a _blank_, i.e. empty, answer has a special status and are not considered "valid".  Hence, a PRT relying on an input left blank will not be traversed.  Answers consisting only of whitespace are also considered as empty.  The extra option `allowempty` allows the input to be empty.  Internally an empty answer will be replaced by the Maxima atom `EMPTYANSWER`.  Internally it is essential that the variable name of the input, (e.g. `ans1`) is really assigned a specific value. The teacher will need to deal with `EMPTYANSWER` tags in the PRT.

We strongly recommend (with many years of experience) that teachers do not use this option without very careful thought!

For example, if you don't want to give away how many answers you expect, then ask the student to provide a _set_ of answers.  Another option is to use the "TextArea" input type.  Each line of the TextArea is validated separately, and the resulting mathematical expression is a list.  The student is therefore free to choose how many expressions to type in, as the circumstances require, without a pre-defined number of input boxes.  By design, it is better to use these methods than trying to combine separate inputs, some of which are empty, in the PRT later.

Our experience strongly suggests this option should only be used for edge cases, and not for routine use.

If you use this option when students navigate away from a page the system will "validate" the inputs, and hence any empty boxes will be considered an active empty choice by the student and will be assessed.  If you use this option there is no way to distinguish between an active empty answer choice, and a student who deletes their answer.  (The same problem occurs with radio buttons....)

There are (unfortunately) some edge cases where it is useful to permit the execution of a PRT without all the inputs containing significant content.  

Assume you have three inputs `ans1`, `ans2`, `ans3` contributing to a PRT, all of which have the `allowempty` option set because you don't want to tell the student which might be empty.  Assume the correct answer has at least one entry non-empty.  Then, make the first node of the PRT check

    ATAlgEquiv({ans1,ans2,ans3},{EMPTYANSWER})

This checks if all inputs are empty, so if true set the score and the penalty to be zero and stop.  This prevents the student accruing a penalty if they navigate away with all the boxes empty, but the PRT will still execute an "attempt".

If a teacher has three inputs `ans1`, `ans2`, `ans3`, then they can define a set in the feedback variables as follows

    sa:setdifference({ans1,ans2,ans3},{EMPTYANSWER})

The variable `sa` will be a set containing the non-empty answers (if any).  

The teacher can use the `EMPTYANSWER` tag as a "correct answer".

### Extra option: simp ###

Actually simplify the student's answer during the validation process.  This will allow students to type in something like

    makelist(k^2,k,1,8)

If teacher's want this kind of thing, then a syntax hint is probably in order as well.

You may need to `ev(ans1,simp)` explicitly in any potential response tree.

It makes no sense to simplify the equivalence reasoning input type, so this has been omitted.

### Extra option: align ###

Controls if the student's answer is aligned 'left' or 'right' within the input box.

### Extra option: nounits ###

As of STACK 4.3, if units are declared in a question then the whole question will use a units context for parsing inputs.  For example, in a multi-part question you may use a matrix input.  If you do so, and use variable names, then these will be parsed expecting them to be usits.  To prevent this in a particular input, use the `nounits` option

## Extra options ##

In the future we are likely to add additional functionality via the _extra options_ fields.  This is because the form-based support becomes ever more complex, intimidating and difficult to navigate.

## Input tips and tricks ##

It is often sensible to use a prefix just in front of the form box.  For example

    \(f(x)=\) [[input:ans1]].

This avoids all kinds of problems with students also trying to enter the prefix themselves.  
You could also specify units afterwards, but you might also want the student to type these in!

In Maxima the input `(a,b,c)` is a programmatic block element (see Maxima's manual for `block`). 
Hence we cannot use this directly for the input of coordinates.  Instead, have the students type in an unnamed function like

    P(x,y)

This technique can be used to enter a set of points

    {A(1,2), B(2,3)}

as an answer.  The `op` command can be used to filter out a particular point, and the `args` command becomes a list of coordinates.

## Options summary table ##

This table lists all options, and which inputs use/respect them.  The `.` means the option is ignored.

Options           | Alg | Num | Units | Matrix | Check | Radio | Drop | T/F | TextArea | Equiv | String | Notes
------------------|-----|-----|-------|--------|-------|-------|------|-----|----------|-------|--------|------
Box size          |  Y  |  Y  |  Y    |   Y    |   .   |   .   |   .  |  .  |    Y     |   Y   |   Y    |   Y  
Strict Syn        |  Y  | (1) |  (1)  |   Y    |   .   |   .   |   .  |  .  |    Y     |   Y   |   .    |   .  
Insert stars      |  Y  |  Y  |  Y    |   Y    |   .   |   .   |   .  |  .  |    Y     |   Y   |   .    |   .  
Syntax hint       |  Y  |  Y  |  Y    |   Y    |   .   |   .   |   .  |  .  |    Y     |   Y   |   Y    |   Y  
Hint att          |  Y  |  Y  |  Y    |   Y    |   .   |   .   |   .  |  .  |    Y     |   Y   |   Y    |   Y  
Forbidden words   |  Y  |  Y  |  Y    |   Y    |   .   |   .   |   .  |  .  |    Y     |   Y   |   .    |   .  
Allowed words     |  Y  |  Y  |  Y    |   Y    |   .   |   .   |   .  |  .  |    Y     |   Y   |   .    |   .  
Forbid float      |  Y  |  Y  |  Y    |   Y    |   .   |   .   |   .  |  .  |    Y     |   Y   |   .    |   .  
Lowest terms      |  Y  |  Y  |  Y    |   Y    |   .   |   .   |   .  |  .  |    Y     |   Y   |   .    |   .  
Check type        |  Y  |  Y  |  Y    |   Y    |   .   |   .   |   .  |  .  |    .     |   .   |   .    |   .  
Must verify       |  Y  |  Y  |  Y    |   Y    |   Y   |   Y   |   Y  |  Y  |    Y     |   Y   |   Y    |   .  
Show validation   |  Y  |  Y  |  Y    |   Y    |   Y   |   Y   |   Y  |  Y  |    Y     |   Y   |   Y    |   .  
**Extra options:**|     |     |       |        |       |       |      |     |          |       |        |      
`rationalize`   |  Y  |  Y  |  .    |   .    |   .   |   .   |   .  |  .  |    .     |   .   |   .    |   .  
min/max sf/dp     |  .  |  Y  |  Y    |   .    |   .   |   .   |   .  |  .  |    .     |   .   |   .    |   .  
`floatnum`      |  .  |  Y  |  .    |   .    |   .   |   .   |   .  |  .  |    .     |   .   |   .    |   .  
`intnum`        |  .  |  Y  |  .    |   .    |   .   |   .   |   .  |  .  |    .     |   .   |   .    |   .  
`rationalnum`   |  .  |  Y  |  .    |   .    |   .   |   .   |   .  |  .  |    .     |   .   |   .    |   .  
`negpow`        |  .  |  .  |  Y    |   .    |   .   |   .   |   .  |  .  |    .     |   .   |   .    |   .  
`allowempty`   |  Y  |  Y  |  Y    |   Y    |   .   |   .   |   .  |  Y  |    .     |   .   |   Y    |   .  
`hideanswer`   |  Y  |  Y  |  .    |   .    |   .   |   .   |   .  |  Y  |    .     |   .   |   Y    |   Y  
`simp`            |  Y  |  Y  |  Y    |   Y    |   .   |   .   |   .  |  .  |    Y     |   .   |   .    |   .  
`align`        |  Y  |  Y  |  Y    |   .    |   .   |   .   |   .  |  .  |    .     |   .   |   .    |   .  
`nounits`      |  Y  |  Y  |  Y    |   Y    |   Y   |   Y   |   Y  |  .  |    .     |   Y   |   .    |   .  

For documentation about the various options not documented on this page look at the pages for the specific inputs in which each option is used.

Notes:

1. The numerical and units input type ignore the strict syntax option and always assume strict syntax is "true".  
Otherwise patterns for scientific numbers such as `2.23e4` will have multiplication characters inserted.

## Other input types ##

Adding new inputs, or options for existing inputs, is a job for the developers.  
The only essential requirement is that the result is a valid CAS expression, which includes of course a string data type, or a list.
