# CASText

## Introduction ##

CASText is CAS-enabled text.  CASText is simply HTML into which LaTeX mathematics and CAS commands can be embedded. These CAS commands are executed before the question is displayed to the user. _Use only simple LaTeX mathematics structures_. Only a small part of core LaTeX is supported.

Many of the fields in a STACK question, such as the question text, are of this type.

Information about [Basic HTML](http://www.w3schools.com/html/) is available elsewhere.

Currently STACK does not process the LaTeX itself.  It is displayed on the user's browser in a variety of ways, such as using [MathJax](http://http://www.mathjax.org/).   If you do not know how to use LaTeX, some simple examples are given in the [author FAQ](Author_FAQ.md).

* Anything enclosed between `\( .... \)` symbols is treated as an _inline equation_, as is the case with normal LaTeX.
* Anything enclosed between matching `\[` and `\]` is treated as a _displayed equation_, in the centre of a new line.
* We do not support the use of dollar symbols such as `$...$` and `$$...$$` for denoting LaTeX mathematics environments.  See the notes on [currency](CASText.md#currency) below and also the page on [MathJax](../Installation/Mathjax.md#delimiters) for more information.
* Don't use LaTeX text formatting features such as `\\` outside equations, instead use the HTML versions.
* Anything enclosed between `{@` and `@}` delimiters is evaluated by the CAS and replaced by the LaTeX representing the result.  Some notes.
 * By default this is displayed as an _inline equation_.  This is analogous to using LaTeX symbols. Note however, that you don't need to use `\({@ stuff @}\)`, and that `{@ stuff @}` is sufficient.
 * To get a displayed equation centred on a line of its own, you must use `\[{@ stuff @}\]`, as in LaTeX.
 * The outer `{}` characters of `{@ stuff @}` will be left in the output to ensure that the output is considered as a single group by LaTeX.
* Anything enclosed between `{#` and `#}` delimiters is evaluated by the CAS and replaced by the Maxima representing the result. Basically, raw values usable in other tools or examples on how to input the value.
* If the Maxima variable `x` is a string then `{@x@}` produces the string contents without quote marks or LaTeX environment, while `{#x#}` produces the string contents enclosed by quote marks.
* If you want comma separated values without the list brackets then use `{@stack_disp_comma_separate( list )@}`.  This function turns a list into a string representation of its arguments, without braces.
* Multiple CAS expressions may appear in a single LaTeX equation, as needed.  For example `\[  \frac{@p@}{@q@} \]`.  Note that many problems are _best solved_ at the level of the CAS, e.g. by defining a variable `p/q` in the CAS, not at the level of display.  This is a design decision which needs experience to resolve efficiently in each case.  For an example of this, see the example [showing working](../CAS/Matrix.md#Showing_working).

Here is an example

    The derivative of {@sin(1/(1+x^2))@} is
    \[ \frac{\mathrm{d}}{\mathrm{d}x} \sin \left( \frac{1}{x^2+1} \right) = {@diff(sin(1/(1+x^2)),x)@} \]
    You can input this as <code>{#diff(sin(1/(1+x^2)),x)#}</code>

## Variables ##   {#Variables}

CASText may depend on variables previously defined in the [question variables](Variables.md#Question_variables) field.

Where the CASText appears in the fields of a [potential response trees](Potential_response_trees.md),
the variables in the [feedback variables](Variables.md#Feedback_variables) may also be included.

To control whether or not the CAS expressions are simplified, see the details about [selective simplification](../CAS/Simplification.md#selective-simplification).

## Question text {#question_text}

The question text what the student actually sees.  This was called "question text" in previous versions.

It is a slightly modified form of CAStext.  To allow a student to answer a question you must include an [inputs](../Authoring/Inputs/index.md) in the question text. For example, students need a box into which their answer will be put.

To place an [input](../Authoring/Inputs/index.md) into the question enclose the name of the [Maxima](../CAS/Maxima_background.md) variable to which the student's answer is assigned between inside the following tag.  If the student's answer is going to be assigned to the variable `ans1` then use the tag `[[input:ans1]]`.  You will also be required to place a corresponding tag to indicate the position of any validation feedback (whether or not this is shown to the student): `[[validation:ans1]]`.  You can use any legitimate variable name.

* When the question is created this is replaced with the appropriate [input](../Authoring/Inputs/index.md).
* When the student answers, this variable name is available to each [potential response trees](Potential_response_trees.md).
* Inputs are created and deleted by adding appropriate tags to the question text.  Therefore, beware if you delete the tags as this will also delete the input from the question.

To place another potential response tree in the question just choose a sensible name and add in a tag `[[feedback:prt1]]`.

* These tags are replaced by appropriate feedback as necessary.  Note, if you add the feedback to the question text this will always be shown by the STACK question, regardless of the quiz settings.  You may prefer to place the tags in the "specific feedback" block of the editing form.  Availability of the specific feedback is controlled by the Moodle quiz settings.  There is some compromise here between the ability to position the feedback tags anywhere in the question text (e.g. next to a particular input) and control over when it is shown.  This is most difficult in questions with many parts.  For a single part question we recommend you use the specific feedback block.
* Tags can be moved anywhere within the question text.
* Do **not** place feedback tags within LaTeX equations!
* PRTs are created and deleted by adding appropriate tags to the question text.  Therefore, beware if you delete the tags as this will also delete the PRT from the question, which may result in lost work.
* Some sites use a database which is not case sensitive (!), so please ensure tags differ by more than case sensitivity.  E.g. avoid `[[input:ans_m]]` and `[[input:ans_M]]` in questions.  Similarly with feedback tags.  Because most databases are case sensitive we do not check for this issue.

## General feedback/Worked solution {#general_feedback}

General feedback (called "worked solution" in previous versions) is shown to the student after they have attempted the question. Unlike feedback, which depends on the response the student gave, the same general feedback text is shown to all students.

The general feedback may depend on any question variables, but may _not_ depend on any of the inputs.
While this design decision is restrictive, it is a deliberate separation of feedback which should be done via potential response trees, from a model solution to this problem which can be written before a question is deployed.

## CASText and currency {#currency}

It is common to want to use the dollar sign for currency.  However, this conflicts with the use of the dollar sign for delimiters for mathematics.  For this reason we discourage the use of dollars to delimit mathematics in STACK.

If you are using dollars for currency then you must protect them with a backslash, i.e. `\$`, otherwise the CASText validation will fail.

## Facts and hints ##

STACK has an in-built formula sheet which is used for facts and hints".  Standard text can be added using the [fact sheet](../Authoring/Question_blocks/Fact_sheets.md)

## Reference materials ##

HTML and LaTeX are needed for authoring STACK questions, and some basic reference materials is give elsewhere.

* Some basic [HTML](../Reference/HTML.md) examples.
* Some basic [LaTeX](../Reference/Latex.md) examples.
* There is a specific page for [actuarial notation](../Reference/Actuarial.md).

## CASText generating functions ##

If a CASText area is to include several copies of repetitive content, for instance several versions of some text (or a SVG graphic) containing
different parameters, it is possible to define a CASText generating function within the Question variables using the STACK function `castext`.

For example, within the [Question variables](Variables.md) section, define

    explanation(x,y):=castext("Substituting {@x@} into the expression gives {@y@}.");

This can then be used several times within any CASText area:

    [[ comment ]] Generated text [[/ comment ]]
    {@explanation(a,b)@}
    {@explanation(c,d)@}

#### Notes ####

* The argument of castext must be a single atomic string, not a reference to one but a static string value.
* Since Maxima does not require new lines to be escaped, new lines can be started within the `castext` string argument.
* Care needs to be taken with any quotation marks within the castext argument. For HTML attributes within such text, use `'...'` .
* Two castext objects can be joined with `castext_concat()`

## Google Charts ##

The [Google charts](http://code.google.com/apis/chart/) API can be used to create a URL based on the random variables.

![](http://chart.apis.google.com/chart?cht=v&chs=200x100&chd=t:100,100,0,50&chdl=A|B)

