# Future plans

How to report bugs and make suggestions is described on the [community](../About/Community.md) page.

Note, where the feature is listed as "(done)" means we have prototype code in the testing phase.

## Features to add for STACK 4.4 or later ##

### Units inputs ###

* Conversion from Celsius to Kelvin?  What units to choose for degrees Celsius which don't conflict with Coulomb?
* Support for United States customary units?
* (Parser can do this) Add an option to validation to require compatible units with the teacher's answer, not just some units.
* Create a mechanism to distinguish between `m/s` and `m*s^-1`, both at validation and answer test levels.
* Create a mechanism to distinguish between `m/s/s` and `m/s^2`, both at validation and answer test levels.
* Add support for testing for error bounds in units.  E.g. `9.81+-0.01m/s^2`.  There is already CAS code for this, and the error bounds are an optional 3rd argument to `stackunits`.  This is currently only used to reject students' answers as invalid.

### Inputs ###

* (Parser can do this)  Add support for coordinates, so students can type in (x,y).  This should be converted internally to a list.
* Add new input types
 1. DragMath (actually, probably use JavaScript from NUMBAS instead here, or the MathDox editor).
 2. Sliders - do this via JSXGraph.
* It is very useful to be able to embed input elements in equations, and this was working in STACK 2.0. However is it possible with MathJax or other Moodle maths filters?
  This might offer one option:  http://stackoverflow.com/questions/23818478/html-input-field-within-a-mathjax-tex-equation
* In the MCQ input type: Add choose N (correct) from M feature (used at Aalto).
* A new MCQ input type with a "none of these" option which uses JavaScript to degrade to an algebraic input: https://community.articulate.com/articles/how-to-use-an-other-please-specify-answer-option
* (Parser can do this) Add an option for "no functions" which will always insert stars and transform "x(" -> "x*(" even when x occurs as both a function and a variable.
* Make the syntax hint CAS text, to depend on the question variables.

### Improve the editing form ###

* A button to remove a given PRT or input, without having to guess that the way to do it is to delete the placeholders from the question text.
* A button to add a new PRT or input, without having to guess that the way to do it is to add the placeholders to the question text.
* A button to save the current definition and continue editing. This would be a Moodle core change. See https://tracker.moodle.org/browse/MDL-33653.
* Add functionality to add a "warning" to the castext class.  Warnings should not prevent execution of the code but will stop editing.

### Other ideas ###

* Document ways of using JSXGraph  `http://jsxgraph.org` for better support of graphics.
* Better options for automatically generated plots.  (Aalto use of tikzpicture?)  (Draw package?)
* 3D Graphics.  Can we use: https://threejs.org/
* Implement "Banker's rounding" option which applies over a whole question, and for all answer tests.
* (Parser can do this) Implement "CommaError" checking for CAS strings.  Make comma an option for the decimal separator.
* (Parser can do this) Implement "BracketError" option for inputs.  This allows the student's answer to have only those types of parentheses which occur in the teacher's answer.  Types are `(`,`[` and `{`.  So, if a teacher's answer doesn't have any `{` then a student's answer with any `{` or `}` will be invalid.
* It would be very useful to have finer control over the validation feedback. For example, if we have a polynomial with answer boxes for the coefficients, then we should be able to echo back "Your last answer was..." with the whole polynomial, not just the numbers.
* Decimal separator, both input and output.
* Check CAS/maxima literature on -inf=minf.

* (Done in Stateful) Make the mark and penalty fields accept arbitrary maxima statements.
* (Done in Stateful) Introduce a variable so the maxima code "knows the attempt number". [Note to self: check how this changes reporting].  This is now being done with the "state" code in the abacus branch.
* (Done in Stateful) Make the PRT Score element CAS, so that a value calculated in the "Feedback variables" could be included here.

## Answer tests

Refactor answer tests so they are all in Maxima.

* Answer tests should be like inputs. We should return an answer test object, not a controller object.
* at->get_at_mark() really ought to be at->matches(), since that is how it is used.
* Use `defstruct` in Maxima for the return objects. (Note to self: `@` is the element access operator).
* Investigate how a whole PRT might make only one CAS call.

## Features that might be attempted in the future - possible self-contained projects

* Read other file formats into STACK.  In particular
  * AIM
  * WeBWorK, including the Open Problem Library:  http://webwork.maa.org/wiki/Open_Problem_Library
  * Maple T.A. (underway: see https://github.com/maths/moodle-qformat_mapleta)
  * WIRIS
* Possible Maxima packages:
  * Better support for rational expressions, in particular really firm up the PartFrac and SingleFrac functions with better support.
* Auto deploy.  E.g. if the first variable in the question variables is a single a:rand(n), then loop a=0..(n-1).
* When validating the editing form, also evaluate the Maxima code in the PRTs, using the teacher's model answers.
* (Done in Stateful) You cannot use one PRT node to guard the evaluation of another, for example Node 1 check x = 0, and only if that is false, Node 2 do 1 / x. We need to change how PRTs do CAS evaluation.

### Authoring and execution of PRTs

Can we write the whole PRT as Maxima code?  YES! see stateful! This seems like an attractive option, but there are some serious problems which make it probably impractical.

1. Error trapping.  Currently, the arguments each answer test are evaluated with Maxima's `errcatch` command independently before the answer test is executed.  This helps track down the source of any error. If we write a single Maxima command for the PRT (not just one node) then it is likely that error trapping will become much more difficult.
2. Not all answer tests are implemented in pure Maxima!  Answer tests are accessed through this class `moodle-qtype_stack/stack/answertest/controller.class.php` only those which make use of `stack_answertest_general_cas` are pure maxima.  Many of the numerical tests use PHP code to infer the number of significant figures.  While we could (perhaps) rewrite some of these in Maxima, they were written in PHP as it is significantly easier to do so.

So, while it is attractive to ask for the PRT as a single Maxima function it is currently difficult to do so.

The current plan is to produce a solid YAML markup language for PRTs.

Other (past ideas) were http://zaach.github.com/jison/ or https://github.com/hafriedlander/php-peg.

## "Reveal block"

The functionality we want to develop is a block in which the contents is revealed or hidden by JavaScript, depending on the value of a separate input.

    [[ reveal input="ans1" value="true" ]]
    
    This will be shown if the value of "ans1" is true.
    
    [[ else if value="false" ]]
    
    [[ else ]]
    
    This will be shown otherwise. (optional)  Perhaps?
    
    [[/ reveal ]]

This implements a JavaScript listener on input "ans1", which reveals or hides the appropriate content.

1. These blocks can be nested.
2. Inputs can be inside reveal blocks (that is sort of the whole point!).  This works well with the new `EMPTYANSWER` functionality, allowing an input to expect not to be used in a correct response.
3. What do we do about values of inputs inside the block, when the reveal condition fails and the block is hidden. Is this deleted with warning, or retained? (Option to block?)
4. Only implement for true/false, or MCQ inputs to start with.
5. What do we do about two reveal blocks listening to the same input?
6. On page load, we need the JS to "do the right thing", i.e. interrogate each input and hide/reveal the content.

An example question is included as samplequestions/reveal_block_example.xml

In this example, we have only revealed the first level, which should be linked to ans1.  If this functionality were available and nested then we would add an MCQ checkbox input within the second reveal block, which is linked to reveal further inputs.


## Improvements to the "equiv" input type

* Improve spacing of comments, e.g. \intertext{...}?
* Auto identify what the student has done in a particular step.

Model solutions.

* Follow a "model solution", and give feedback based on the steps used.  E.g. identify where in the students' solution a student deviates from the model solution.
* Develop a metric to measure the distance between expressions.  Use this as a measure of "step size" when working with expressions.

Add mathematical support in the following order.

1. Logarithms and simple logarithmic equations.
2. Allow students to create and use functions of their own (currently forbidden).
3. Add a "Not equals" operator.  For example:

    infix("<>");
    p:x<>y;
    texput("<>","{\neq}", infix);
    tex(p);


## STACK custom reports

Basic reports now work.

* Really ensure "attempts" list those with meaningful histories.  I.e. if possible filter out navigation to and from the page etc.
* Add better maxima support functions for off-line analysis.
* A fully maxima-based representation of the PRT?
