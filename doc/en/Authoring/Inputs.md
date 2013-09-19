# Inputs

Inputs are the points at which the student interacts with the question.
For example, it might be a form box into which the student enters their answer.

* Only the [question text](CASText.md#question_text) may have inputs.
* Inputs are not required. Hence it is possible for the teacher to make a
  statement which asks for no response from the student, i.e. a rhetorical question.
* A question may have as many inputs as needed.
* Inputs can be positioned anywhere within the
  [question text](CASText.md#question_text).  If JSMath is used for display this includes within equations.  MathJax does not currently support this feature.

The position of an input in the [question text](CASText.md#question_text) is denoted by

    [[input:ans1]]

Here `ans1` denotes the name of a [Maxima](../CAS/Maxima.md) variable to which the student's answer is to be assigned.
This must only be letters (optionally) followed by numbers, as in this example. No special characters are permitted.

Feedback as to the syntactic validity of a response is by default inserted just after
the input. Feedback is positioned using tags such as

    [[validation:ans1]]

where stuff is the name of the variable. This string is automatically generated if it
does not exist and is placed after the input. This feedback must be given.
Inputs have a number of options. Specific inputs may have extra options.

To see what sort of vaidation is done, look at the
[test suite for validation of student's input](../../../studentinputs.php).

## input options ##

Each input may have a number of options.

## Student's Answer Key ##  {#Answer_Key}

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

A single letter can be entered.  This is useful for creating multiple choice questions.

#### Text area ####

Enter algebraic expressions on multiple lines.  STACK passes the result to [Maxima](../CAS/Maxima.md) as a list.

#### Drop down list ####

(_Not currently re-implemented in STACK 3.0_)  Use the Input Type Options field to indicate a comma separated list of possible values.

#### Matrix ####

The size of the matrix is inferred from the model answer.
STACK then adds an appropriate grid of boxes (of size Box Size) for the student to fill in.
This is easier than typing in [Maxima](../CAS/Maxima.md)'s matrix command, but does give the game away about the size of the required matrix.

_The student may not fill in part of a matrix._  If they do so, the remaining entries will be completed with `?` characters which render the attempt invalid. STACK cannot cope with empty boxes here.

#### Slider ####

(_Not currently re-implemented in STACK 3.0_)  Dragable slider bar resulting in a numerical value.

### Model answer ###  {#model_answer}

**This field is compulsory.** Every input must have an answer, although this answer is not necessarily the unique correct answer.
This value will be available as a question variable named `tans`**`n`** (where **`n`** is 1, 2, ...)

### Box Size ### {#Box_Size}

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
* The student's expression does not contain any scientific notation, so that `3E2` (which Maxima interprets as `300.0`) is looked for, and expects `3*E*2`.

### Insert Stars ### {#Insert_Stars}

If set to `yes`  then the system will automatically insert *s into any patterns identified by Strict Syntax as needing them and will not throw a validation error.
So, for example \(2(1-4x)\) will be changed to `2*(1-4*x)` on validation.

### Syntax Hint ### {#Syntax_Hint}

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

This is a comma separated list of text strings which are forbidden in a student's answer.  Note, any variable names used in the question variables are automatically forbiden (otherwise the student could potentially use the variable name you have defined, which might be the correct answer).
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

### Forbid Floats ### {#Forbid_Floats}

If set to `yes`, then any answer of the student which has a floating point number
will be rejected as invalid. Student's sometimes use floating point numbers when
they should use fractions. This option prevents problems with approximations being used.

### Require lowest terms ### {#Require_lowest_terms}

When this option is set to `yes`, any coefficients or other rational numbers in an
expression, must be written in lowest terms.  Otherwise the answer is rejected as "invalid".
This enables the teacher to reject answers, and not consider them further.

### Check Students answer's type ### {#Check_Type}

If this option is set to `yes` then unless the student's expression is the same
[Maxima](../CAS/Maxima.md#Types_of_object) as the teacher's correct answer,
then the attempt will be rejected as invalid.

This is very useful for ensuring the student has typed in an "equation", such as \(y=mx+c\)
and not an expression such as \(mx+c\).  Remember, you can't compare an expression with an equation!

Another useful way of avoiding this problem is to put a LaTeX string such as \(y=\) just before the input.  E.g.

    \(y=\)[[input:ans1]].

### Student must verify ### {#Student_must_verify}

Specifies whether the student's input is presented back to them before scoring.  Useful for complex algebraic expressions but not needed for constrained input like `yes`/`no`.

Experience strongly supports the use of verification by "validating" input whenever a student types in an expression.  Errors will always be displayed and rejected as invalid. Potential response trees will not execute with invalid input.

### Show validation ### {#Show_validation}

Feedback to students is in two forms.

* feedback tied to inputs, in particular if the answer is invalid.
* feedback tied to each potential response tree.

Setting this option displays any feedback from this input, including echoing back their expression in traditional two dimensional notation.  Generally, feedback and verification are used in conjunction.  Errors will always be displayed.

### Options ### {#Options}

Different types of inputs have various options.   These are described under the IE type.

### List {#List}

This allows the following kinds of interactions to be included in STACK questions.

* Radio buttons.
* Dropdown lists.
* Check boxes.

The teacher can choose to construct an input which displays a random selection,
in a random order, from a list of potential "distractors".  The top element, named "Correct answer",
is always included, although this isn't really needed for the checkbox type.

You have to enter a content form ([maxima](../CAS/Maxima.md) format) and displayed form
(i.e. [CASText](CASText.md)) for each of these.  Both may depend on the question variables.

STACK will automatically add space to ensure you have at least two blank distractors when
you update the question. In the case of the radio button or dropdown list a single expression will be returned.
In the case of the check boxes, we return a list of expressions.  Note,

* The model answer in the input needs to be a list of objects, even if only one is correct.
* The order of elements in this list is not certain, because we display them in a random order to students.
  It will be necessary to `setify()` this to compare with a set of answers without order becoming a problem.

## Future plans ##

Adding new inputs should be a straightforward job for the developers.  We have plans to add inputs as follows.

| Package   | Functionality
| --------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
| Dragmath  | Adds the [DragMath](http://www.dragmath.bham.ac.uk) applet as an input.  The code is in place, but there are JavaScript bugs, so we have not given authors access to this feature for the time being.
| GeoGebra  | [GeoGebra](http://www.geogebra.org/) worksheets, for example.

The only essential requirement is that the result is a valid CAS expression, which includes of course a string data type, or a list.
