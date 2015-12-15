# Inputs

Inputs are the points at which the student interacts with the question.
For example, it might be a form box into which the student enters their answer.

* Only the [question text](CASText.md#question_text) may have inputs.
* Inputs are not required. Hence it is possible for the teacher to make a
  statement which asks for no response from the student, i.e. a rhetorical question.
* A question may have as many inputs as needed.
* Inputs can be positioned anywhere within the
  [question text](CASText.md#question_text). MathJax does not currently support the inclusion of inputs within equations.

The position of an input in the [question text](CASText.md#question_text) is denoted by

    [[input:ans1]]

Here `ans1` denotes the name of a [Maxima](../CAS/Maxima.md) variable to which the student's answer is to be assigned.
This must only be letters (optionally) followed by numbers, as in this example. No special characters are permitted.
The input name cannot be more than 18 characters long.

Feedback as to the syntactic validity of a response is by default inserted just after
the input. Feedback is positioned using tags such as

    [[validation:ans1]]

where stuff is the name of the variable. This string is automatically generated if it
does not exist and is placed after the input. This feedback must be given.
Inputs have a number of options. Specific inputs may have extra options.

To see what sort of validation is done, look at the
[test suite for validation of student's input](../../../studentinputs.php).

## Input options ##

Each input may have a number of options.

### Student's Answer Key ###  {#Answer_Key}

The maxima variable to which the student's answer is assigned.
This is set in the Question text using the following syntax, where `ans1` is the variable name to which the student's answer is assigned.

    [[input:ans1]]

Internally you can refer to the student's answer using the variable name `ans1` in the potential response tree, feedback variables and feedback text. The worked solution may not depend on inputs.

### Input Type ### {#Input_Type}

Currently STACK supports the following kinds of inputs.

#### Algebraic input ####

The default: a form box.

#### True/False ####

Simple drop down. A Boolean value is assigned to the variable name.

#### Single Character ####

A single letter can be entered.  This is useful for creating multiple choice questions, but is not used regularly.

#### Text area ####

Enter algebraic expressions on multiple lines.  STACK passes the result to [Maxima](../CAS/Maxima.md) as a list.

#### Matrix ####

The size of the matrix is inferred from the model answer.
STACK then adds an appropriate grid of boxes (of size Box Size) for the student to fill in.
This is easier than typing in [Maxima](../CAS/Maxima.md)'s matrix command, but does give the game away about the size of the required matrix.

_The student may not fill in part of a matrix._  If they do so, the remaining entries will be completed with `?` characters which render the attempt invalid. STACK cannot cope with empty boxes here.

### Model answer ###  {#model_answer}

**This field is compulsory.** Every input must have an answer, although this answer is not necessarily the unique correct answer.
This value will be available as a question variable named `tans`**`n`** (where **`n`** is 1, 2, ...)

### Input Box Size ### {#Box_Size}

The width of the input box.

### Strict Syntax ### {#Strict_Syntax}

Both Strict Syntax and Insert Stars affect the way STACK treats the validation of CAS strings.

* Strict Syntax defines the patterns to look for.
* Insert Stars decides whether to insert stars automatically.

We need these options since \(x(t+1)\) means apply the function \(x\) to the argument \((t+1)\), whereas \(\sin(x)\) would be fine. How does one distinguish between the two?

This option decides if we expect strict Maxima syntax.  The default is `no`.  This option affects which patterns we search for when looking for missing stars.

Some patterns must always be wrong.  For example  `)(` must be missing a star, and so this pattern is always included.

If set to `no`, this increases the range of things into which stars might be inserted.  In particular when `no` we assume

* The student's expression does not contain any functions, so that `f(x+1)` is looked for, and we expect `f*(x+1)`.
* The student's expression does not contain any scientific notation, so that `3E2` or `3e2` (which Maxima interprets as `300.0`) is looked for, and expects `3*E*2` or `3e2`.

Please read the notes on [numbers](../CAS/Numbers.md#Floats).

### Insert Stars ### {#Insert_Stars}

There are three options.

* Don't insert stars:  This does not insert `*` characters automatically into any patterns identified by Strict Syntax as needing them.  Strict Syntax is true and there are any pattern identified the result will be an invalid expression.
* Insert `*`s for implied multiplication.  If any patterns identified by Strict Syntax as needing `*`s then they will automatically be inserted into the expression quietly.
* Insert `*`s assuming single character variable names.  In many situations we know that a student will only have single character variable names.  Identify variables in the students answer made up of more than one character then replace these with the product of the letters.
  * Note, the student's formula is interpreted and variables identified, so \(\sin(ax)\) will not end up as `s*i*n*(a*b)` but as `sin(a*v)`.
  * Note, in interpreting the student's formula we build an internal tree in order to identify variable names and function names.  Hence \(xe^x\) is interpreted as \( (xe)^x \).  We then identify the variable name `xe` and replace this as `x*e`.  Hence, using this option we have `xe^x` is interpreted as `(x*e)^x` NOT as `x*e^x` which you might expect.  

The above two conditions are in conflict: we can't have it both ways.  What would you expect to happen in \(\sin(in)\)? If we replace `in` by `i*n` in the original typed expression we end up in a mess.   For this reason it is essential to have some on-screen representation of multiplication, e.g. as a dot, so the student can see at the validation that `xe^x` is interpreted 

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

This is a comma separated list of text strings which are forbidden in a student's answer.  Note, any variable names used in the question variables are automatically forbidden (otherwise the student could potentially use the variable name you have defined, which might be the correct answer).
If one of these strings is present then the student's attempt will be considered invalid,
and no penalties will be given.  This is an unsophisticated string match.

Note that the string `*` is literally taken as `*` and is not a wild card.  Teachers may ask a student to calculate `2*3` and hence need to forbid multiplication in an answer.

If you wish to forbid commas, then escape it with a backslash.

There are groups of common keywords which you can forbid simply as

* `[[BASIC-ALGEBRA]]` common algebraic operations such as `simplify`, `factor`, `expand`, `solve` etc.
* `[[BASIC-CALCULUS]]` common calculus operations such as `int`, `diff`, `taylor` etc.
* `[[BASIC-MATRIX]]` common matrix operations such as `transpose`, `invert`, `charpoly` etc.

These lists are in the casstring class. If you have suggestions for more lists, or additional operations which should be added to the existing lists, please contact the developers.

### Allowed words ### {#Allowed_Words}

By default, arbitrary function or variable names of more than two characters in length are not permitted.  This is a comma separated list of function or variable names which are permitted in a student's answer.

Note the allowed words permit the teacher to override some (but not all) of the strings which are considered to be invalid by default for student input.  For example, `Sin` (capital "S") has specific feedback.  If you need this in a question you have to allow it here.  Similarly `In` ("India November") is mistakenly used by students for the natural logarithm rather than `ln` ("Lima November").  Hence by default this triggers specific feedback.  You can allow `In` here.

### Forbid Floats ### {#Forbid_Floats}

If set to `yes`, then any answer of the student which has a floating point number
will be rejected as invalid. Student's sometimes use floating point numbers when
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

Specifies whether the student's input is presented back to them before scoring as part of a two step validation process.  Typically the student's mathematical expression is displayed in traditional form.  This is useful for complex algebraic expressions but not needed for constrained input like `yes`/`no`.

Experience strongly supports the use of this two step verification process.  Errors will always be displayed and expressions with errors rejected as invalid. Potential response trees will not execute with invalid input.

The next option controls how the validation feedback is displayed. Note, it is not possible to require a two-step validation but not show some validation feedback.

### Show validation ### {#Show_validation}

Feedback to students is in two forms.

* feedback tied to inputs, in particular if the answer is invalid.
* feedback tied to each potential response tree.

Setting this option displays any feedback from this input, including echoing back their expression in traditional two dimensional notation.  Generally, feedback and verification are used in conjunction.  Errors will always be displayed.  In addition to simply displaying the student's expression, the teacher can display the list of variables which occurs in the expression.  From experience, this is helpful in letting students understand the idea of variable and to spot case insensitivity or wrong variable problems.

## Future plans ##

Adding new inputs should be a straightforward job for the developers.  We have plans to add inputs as follows.

| Package   | Functionality
| --------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
| Dragmath  | Adds the [DragMath](http://www.dragmath.bham.ac.uk) applet as an input.  The code is in place, but there are JavaScript bugs, so we have not given authors access to this feature for the time being.
| GeoGebra  | [GeoGebra](http://www.geogebra.org/) worksheets, for example.
| MCQs      | Add in check boxes and radio boxes as an input type to enable randomly generated multiple choice questions.

The only essential requirement is that the result is a valid CAS expression, which includes of course a string data type, or a list.
