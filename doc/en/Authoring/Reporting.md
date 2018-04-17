# Reporting

Computer aided assessment of mathematics works in the following phases.

1. [Authoring](../Authoring/index.md),
2. [Testing](Testing.md) and
3. [Deploying](Deploying.md) questions.
4. [Adding questions to a quiz](Quiz.md) and use by students.
5. [Reporting](Reporting.md) and statistical analysis.

Reviewing students' answers closes the learning cycle for teachers by allowing them to understand what students are doing. Basic reporting is undertaken through the Moodle quiz.  For example, lists of scores etc. are available there.  The Moodle quiz also calculates basic statistics based on the numerical information.

To review work in more detail we need to use two important parts of the question. Please read the following two entries before continuing with this article.

* The [question note](Question_note.md).
* The [answer note](Potential_response_trees.md#Answer_note).

## Moodle quiz features ##

Most of the day to day reporting is done through the Moodle quiz features.

For detailed reporting go to

    Quiz administration -> Results -> Reporting

The "response" column shows the raw Maxima input value entered by the student and the validity status, e.g. "blank" (not reported), "invalid", "valid", "score".  The "response" column also shows the value of the [answer note](Potential_response_trees.md#Answer_note) for each potential response tree.

An example note is shown below.  In this question a student has answered with `(x-4)^6+c` for the input `ans1`.
The potential response tree `prt1` has executed and the note is `ATInt_generic. | 1-0-F`.

    ans1: (x-4)^6+c [score]; prt1: ATInt_generic. | 1-0-F

Notes:

1. If the potential response tree does not execute it will be reported as `prt1: #` to indicate it exists but was not used.
2. Reporting is stored in the database, so you may need to "regrade" attempts if you update the answer notes.
3. You can download the responses for offline analysis.  Split over `;` to separate inputs and separate prt answer notes.  Split the individual answer note over `|` to get the route through the tree, and the notes added by the answer tests.

## Individual STACK item analysis ##

STACK has a bespoke reporting mechanism for analysing responses to questions, grouped by the [answer note](Potential_response_trees.md#Answer_note).  To make us of this you need to install the [STACK quiz report](../Installation/index.md#Report) module.

**Please note that the STACK quiz report is still under development.**  If you have specific requests for features, please contact the developers.

To access the quiz reports you need to navigate to the quiz.  Then follow the chain

    Administration -> Quiz administration -> Results -> STACK response analysis.

This page lists all the STACK questions in a particular quiz.  Click on a link to perform an analysis of attempts at an individual question.

The page contains a number of parts.

* A summary of the question.  This includes the question variables, uninstantiated (i.e. raw) question text, general feedback, uninstantiated question note as a reminder to the teacher what the question is about.
* Summary tables of the frequencies of answer notes for each potential response tree, and each variant.
* A detailed summary of all attempts at a particular variant.
* Maxima code to facilitate off-line analysis.

### Summary tables ###

The first table provides a summary of the frequency of each answer note for each question note, both as a numerical frequency and as a percentage.  Each potential response tree is listed separately.  A second table shows similar data for splits answer notes, to show the frequency each branch of the tree is traversed.  Depending on the form of the trees in the question, each table provides potentially useful comparisons of the frequencies of outcomes of different variants.

### Variants ###

For each question variant we provide a number of tables of data.

* The first table lists all inputs at one question, their frequency, the score generated and each outcome from the potential response trees.
* Next we produce tables for each input, and count the number of times each expression is entered and its validity.  Where an input is invalid a note records the reason.  Remember, validity is more than syntactic soundness so a syntactically valid expression might be "invalid" for this input. For example, we might forbid floating point numbers.

### Maxima code ###

We create a number of lists in Maxima code which record all the valid inputs.   These can be copied into a desktop version of Maxima for off-line analysis.  This reduces server load, but is also more flexible enabling teachers to manipulate answers without having to update the server.  In order to use the analysis you will need to set up the [STACK - Maxima sandbox](../CAS/STACK-Maxima_sandbox.md).

The code generates three maxima lists

* the list `inputs` creates the names of the inputs to the question. These are assumed to be valid maxima variable names, so are maxima objects.  E.g. `inputs:[ans1,ans2,ans3]$`.
* the list `variants` which is a list of strings which are the question notes used.
* the list of lists called `stackdata`. This holds all the input data.  The elements of the list show the input data for each variant.  E.g. `stackdata[1]` is the input data for `variant[1]`.  For each variant, the elements are a list of all data for each corresponding input.  E.g. `stackdata[1][2]` is the input data for `variant[1]` and `input[2]`.  

This data is available for off line analysis using functions in the file `stack\stack\maxima\stackreporting.mac`

More work on this functionality is needed.
