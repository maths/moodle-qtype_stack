# Future plans

Note, where the feature is listed as "(done)" means we have prototype code in the testing phase.

## Features to add for STACK 4.5 or later ##

### Units inputs ###

* Conversion from Celsius to Kelvin?  What units to choose for degrees Celsius which don't conflict with Coulomb?
* Support for United States customary units?
* (Parser can do this) Add an option to validation to require compatible units with the teacher's answer, not just some units.
* Create a mechanism to distinguish between `m/s` and `m*s^-1`, both at validation and answer test levels.
* Create a mechanism to distinguish between `m/s/s` and `m/s^2`, both at validation and answer test levels.
* Add support for testing for error bounds in units.  E.g. `9.81+-0.01m/s^2`.  There is already CAS code for this, and the error bounds are an optional 3rd argument to `stackunits`.  This is currently only used to reject students' answers as invalid.

### Inputs ###

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
* Expand support for context variables, so variables and other functions will be considered as context variables as well.  This will expand the utility of `texput`.
* Implement "Banker's rounding" option which applies over a whole question, and for all answer tests.
* (Parser can do this) Implement "CommaError" checking for CAS strings.  Make comma an option for the decimal separator.
* (Parser can do this) Implement "BracketError" option for inputs.  This allows the student's answer to have only those types of parentheses which occur in the teacher's answer.  Types are `(`,`[` and `{`.  So, if a teacher's answer doesn't have any `{` then a student's answer with any `{` or `}` will be invalid.
* It would be very useful to have finer control over the validation feedback. For example, if we have a polynomial with answer boxes for the coefficients, then we should be able to echo back "Your last answer was..." with the whole polynomial, not just the numbers.
* Decimal separator, both input and output.
* Check CAS/maxima literature on -inf=minf.

* add in support for pdf_binomial, in particular add in these test cases to `studentinput_test.php`.

        array('pdf_binomial(n,m,p)', 'php_true', 'pdf_binomial(n,m,p)', 'cas_true', '{{m}\choose{n}}\cdot p^{n}\cdot {\left(1-p\right)}^{m-n}', '', ""),
        array('pdf_binomial(2,6,0.07)', 'php_true', 'pdf_binomial(6,2,0.07)', 'cas_true', '{{6}\choose{2}}\cdot 0.07^{2}\cdot {\left(1-0.07\right)}^{6-2}', '', ""),



* (Done in Stateful) Introduce a variable so the maxima code "knows the attempt number". [Note to self: check how this changes reporting].  This is now being done with the "state" code in the abacus branch.


## Answer tests

* Answer tests should be like inputs. We should return an answer test object, not a controller object.
* at->get_at_mark() really ought to be at->matches(), since that is how it is used.

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

## Suggestions from STACK Professionals Network, 9th Dec 2022

* Text based potential response trees (would allow for easier copying of complicated trees, etc).
* Changes to preset feedback to certain answer tests which might be more appropriate for different audiences. Could a 'simplified' English language pack allow for this (future changes might allow this to be done on a question-by-question basis).
* Check for potential issues with default correct/incorrect feedback for different languages (defaults can already be set on the server level by a Moodle administrator).
* Metadata on language for questions.
* Tools for languge integrity (e.g. making it easier to identify what languages are in each question).
* Making sure Maxima knows the intended language (will allow for Maxima code to choose from the available languages).
* May want to have further discussions on how scores and penalties are handled (there is already a new feature in the latest version of STACK so that you can include functions in the "score" field.
* DONE: Compile some more detailed release notes for new version containing common issues with questions.
* MathJax sometimes stops rendering -- this is usually an issue with the html in the question text or something being added to the editor, but sometimes this is an intermittent issue, but this is probably not directly a STACK issue.
* Accessing Moodle via an LTI connection sometimes prompts students on Macs and old PCs to login with a password (which they don't have) when they click on a quiz. This isn't a direct STACK issue, but it would be a good idea to raise this on Moodle support forums.
* DONE:  Improve the question tests page (now "STACK question dashboard") -- make sure most useful features are highlighted. For example, make it more clear when a specific variant is being considered, and when all variants are being looked at. After an individual variant is undeployed, it still shows test information for this seed on the page, requiring a few extra clicks to switch to a different seed (though that makes it easier to restore that seed if you accidently undeploy it).
* Forcing editor choice to prevent errors caused by editors which add in html tags (make sure it is clear to users why we do this).
* Consider prioritising the STACK API? Documentation on this definately needs to be improved. Volunteers are probably needed to help out on this.
* We should probably have a discussion for which individual questions are best to promote STACK.

Good to document know-how and communicate this to avoid problems on updates, and to generate new suggestions. New folders can be added in doc/en -- small suggestions can go straight into master, more complete changes should be discussed with the group.
