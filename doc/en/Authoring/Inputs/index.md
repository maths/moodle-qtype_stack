# Inputs

Inputs are the points at which the student interacts with the question and enter their answer

The default (and prototype) is an HTML input box into which a student is expected to type an algebraic expression.

* Only the [question text](../../Authoring/CASText.md#question_text) may have inputs.
* Inputs are not required. Hence it is possible for the teacher to make a statement which asks for no response from the student, i.e. a rhetorical question.
* A question may have as many inputs as needed.
* Inputs can be positioned anywhere within the [question text](../../Authoring/CASText.md#question_text). MathJax does not currently support the inclusion of inputs within equations.
* Typically inputs return a Maxima expression.  This might be just the student's answer (in the case of an algebraic input).  MCQ inputs also return a valid Maxima expression.
* Some inputs return JSON.
* In a multi-part question avoid having inputs which differ only by case sensitivity.  E.g. do not have `[[input:a]]` and `[[input:A]]` in the same question.  (Some database defaults have case insensitive unique keys (!) and in that case this will cause a database error.  See the [installation instructions](../../Installation/index.md)).

The position of an input in the [question text](../../Authoring/CASText.md#question_text) is denoted by

    [[input:ans1]]

Here `ans1` is the name of a [Maxima](../../CAS/Maxima_background.md) variable to which the student's answer is to be assigned.
This must only be letters followed (optionally) by numbers, as in this example. No special characters are permitted.
The input name cannot be more than 18 characters long.

Feedback as to the syntactic validity of a response is positioned using a corresponding tag

    [[validation:ans1]]

This tag must be included even if validation is suppressed with an option (see below) and is automatically generated after the input if it does not exist.

We expose the exact behaviour of the validation by giving registered users access to STACK's test suite validation of student's answers.  This can be found on a live server at `https://stack-demo.maths.ed.ac.uk/demo/question/type/stack/adminui/studentinputs.php`

Each input may have a number of options and this is potentially complex area with a large range of possibilities.

The basic idea is to reject things as "invalid" to stop students being penalized on a technicality.  This might be requiring an equation, or making floating-point numbers within an expression forbidden.

## Student's Answer Key ##  {#Answer_Key}

Every input must have a unique answer key.  This is set in the Question text using the following tag, where `ans1` is the variable name to which the student's answer is assigned.

    [[input:ans1]]

Internally you can refer to the student's answer using the variable name `ans1` in the potential response tree, feedback variables and feedback text. The worked solution (general feedback) may not depend on the inputs.

## Model answer ##  {#model_answer}

**This field is compulsory.** Every input must have an answer, although this answer is not necessarily the unique correct answer, or even "correct"!  This value be displayed to the student as the correct answer.  We recommend you use a question variable for this field so it can be used in the other parts of the question, e.g. the potential response trees.

## Input type ##

Currently STACK supports the following kinds of inputs.  These have a variety of options, as explained below.

1. **Algebraic** The default: a form box into which a student is expected to type an algebraic expression.
2. **Numerical** This input type _requires_ the student to type in a number of some kind.  Any expression with a variable will be rejected as invalid.  See the specific documentation for more information:  [Numerical input](Numerical_input.md).
3. **Scientific units** The support for scientific units includes an input type which enables teachers to check units as valid/invalid. See the separate documentation for [units](../../Topics/Units.md).
4. **Matrix** This provides a grid for students to type in their answer.
5. **Variable size matrix** This provides a textarea. Students type in expressions and spaces separate items in rows.
6. **True/False** Simple drop down. A Boolean value is assigned to the variable name.
7. **Single charater** A single letter can be entered.  This is useful for creating multiple-choice questions, but is not used regularly.
8. **String/Notes** Resulting in text-strings being sent to Maxima, or stored. [Text-based inputs](Text_input.md).
9. **Multi-line input**, either the equivalence reasoning input or the textarea input. 

Some of the special blocks provide interactions which can be linked to inputs.  These include

1. **JSXGraph** diagrams.
2. **GeoGebra** diagrams.
3. **Drag and drop** problems.

#### True/False inputs ####

If the teacher's correct answer should leave this blank (e.g. not answered at all) then use the tag `EMPTYANSWER`. (There are some edge cases where only some inputs are used in the correct answer to a question, so not answering is correct here).  If you use the extra option `allowempty` then empty answers are considered valid, and the value of this input is `EMPTYANSWER`.

## Input Options ##

### Input Box Size ### {#Box_Size}

The width of the input box.

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

These list are hard-wired into the code [here](https://github.com/maths/moodle-qtype_stack/blob/master/stack/cas/cassecurity.class.php#L56).

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
[Maxima](../../CAS/Maxima_background.md#Types_of_object) as the teacher's correct answer,
then the attempt will be rejected as invalid.

Type checking here is very simple, basically checking the student's answer is an equation, inequality, list, set, matrix to match that of the teacher.  The intention is not to be completely comprehensive, but to avoid obvious type mismatch.  E.g. this is very useful for ensuring the student has typed in an "equation", such as \(y=mx+c\)
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


## Adding new input types ##

Adding new inputs, or options for existing inputs, is a job for the developers.
The only essential requirement is that the result is a valid CAS expression, which includes of course a string data type, or a list.
