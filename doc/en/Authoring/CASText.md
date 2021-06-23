# CASText

## Introduction ##

CASText is CAS-enabled text.  CASText is simply HTML into which LaTeX mathematics and CAS commands can be embedded. These CAS commands are executed before the question is displayed to the user. _Use only simple LaTeX mathematics structures_. Only a small part of core LaTeX is supported.

Many of the fields in a STACK question, such as the question text, are of this type.

Information about [Basic HTML](http://www.w3schools.com/html/) is available elsewhere.

Currently STACK does not process the LaTeX itself.  It is displayed on the user's browser in a variety of ways, such as using [MathJax](http://http://www.mathjax.org/).   If you do not know how to use LaTeX, some simple examples are given in the [author FAQ](Author_FAQ.md).

* Anything enclosed between `\( .... \)` symbols is treated as an _inline equation_, as is the case with normal LaTeX.
* Anything enclosed between matching `\[` and `\]` is treated as a _displayed equation_, in the centre of a new line.
* We do not support the use of dollar symbols such as `$...$` and `$$...$$` for denoting LaTeX mathematics environments.  See the notes on [currency](CASText.md#currency) below and also the page on [MathJax](../Installation/Mathjax.md#delimiters) for more information.
* Don't use LaTeX text formatting features such as `\\`, instead use the HTML versions.
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

## Question text {#question_text}

The question text what the student actually sees.  This was called "question text" in previous versions.

It is a slightly modified form of CAS text.  To allow a student to answer a question you must include an [inputs](Inputs.md) in the question text. For example, students need a box into which their answer will be put.

To place an [input](Inputs.md) into the question enclose the name of the [Maxima](../CAS/Maxima.md) variable to which the student's answer is assigned between inside the following tag.  If the student's answer is going to be assigned to the variable `ans1` then use the tag `[[input:ans1]]`.  You will also be required to place a corresponding tag to indicate the position of any validation feedback (whether or not this is shown to the student): `[[validation:ans1]]`.  You can use any legitimate variable name.

* When the question is created this is replaced with the appropriate [input](Inputs.md).
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
While this design decision is restrictive, it is a deliberate separation of feedback
which should be done via potential response trees, from a model solution to this
problem which can be written before a question is deployed.

## CASText and currency {#currency}

It is common to want to use the dollar sign for currency.  However, this conflicts with the use of the dollar sign for delimiters for mathematics.  For this reason we discourage the use of dollars to delimit mathematics in STACK.

* If you are using dollars for currency then you must protect them with a backslash, i.e. `\$`, otherwise the CASText validation will fail.

## Facts ##

STACK has an in-built formula sheet.  This used to be called a "hints" system, but the word hint is used elsewhere in Moodle so this is now called "facts".  Parts of this can be added to CASText  using the [fact sheet](Fact_sheets.md)

## Most useful HTML ##

HTML Paragraphs (don't forget the end tag!)

    <p>This is a paragraph</p>
    <p>This is another paragraph</p>

HTML Line Breaks

Use the `<br />` tag if you want a line break (a new line) without starting a new paragraph:

    <p>This is<br />a para<br />graph with line breaks</p>

Some formatting

    <em>This is emphasis</em>
    
    <b>This text is bold</b>
    
    <big>This text is big</big>
    
    <i>This text is italic</i>
    
    <code>This is computer output</code>
    
    This is <sub>subscript</sub> and <sup>superscript</sup>

## Useful LaTeX ##

LaTeX notation can specify inline or display mode for maths by delimiting with `\(` or `\[` respectively.  Here are some simple examples:

* `x^2` gives \(x^2\)
* `x_n` gives \(x_n\)
* `x^{2x}` gives \(x^{2x}\)
* `\alpha\beta` gives \(\alpha\beta\)
* `\sin(3\pi x)` gives \(\sin(3\pi x)\)
* `\frac{1}{1-n^2}` gives \(\frac{1}{1-n^2}\) when inline.  In display mode it gives:

\[ \frac{1}{1-n^2} \]

* `\int_a^b x^2\ dx` gives \(\int_a^b x^2\ dx\) when inline.  In display mode it gives:

\[ \int_a^b x^2\ dx \]

There is a specific page for [actuarial notation](Actuarial.md).

## Google Charts ##

The [Google charts](http://code.google.com/apis/chart/) API can be used to create a URL based on the random variables.

![](http://chart.apis.google.com/chart?cht=v&chs=200x100&chd=t:100,100,0,50&chdl=A|B)

Details are given in the section on [plots](../CAS/Plots.md#google).
