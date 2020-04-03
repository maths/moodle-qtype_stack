# Authoring

The authoring documentation provides information on common authoring topics.

## Authoring quick start

Those new to STACK would probably prefer the [authoring quick start guide](Authoring_quick_start.md). These guides cover the most important topics of question authoring, and include embedded screencast videos to guide you.

* [Authoring quick start 1](Authoring_quick_start.md): A basic question.
* [Authoring quick start 2](Authoring_quick_start_2.md): Question variables.
* [Authoring quick start 3](Authoring_quick_start_3.md): Improving feedback.
* [Authoring quick start 4](Authoring_quick_start_4.md): Randomisation.
* [Authoring quick start 5](Authoring_quick_start_5.md): Question Testing.
* [Authoring quick start 6](Authoring_quick_start_6.md): Multi-part mathematical questions.
* [Authoring quick start 7](Authoring_quick_start_7.md): Turning simplification off.
* [Authoring quick start 8](Authoring_quick_start_8.md): Importing and Quizzes.

## STACK question structure  ##

A  `stackQuestion` is the basic object in the system. The following table shows the fields which make up a question, with links to the documentation for each one.

| Name                                                       | Details
| -------------------------------------------------------------------| ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
| Question name              | Names a question
| [Question variables](Variables.md#Question_variables)      | These are potentially random variables which can be used to generate a question.
| [Question text](CASText.md#question_text)                  | This is the question the student actually sees
| [General feedback](CASText.md#General_feedback/Worked_solution)            | The worked solution is only available after an item is closed.
| [Question note](Question_note.md)                          | Two randomly generated question variants are different, if and only if the question note is different.  Use this field to store useful information which distinguishes variants.
| [Inputs](Inputs.md)                                        | The inputs are the things, such as form boxes, with which the student actually interacts.
| [Potential response trees](Potential_response_trees.md)    | These are the algorithms which establish the mathematical properties of the students' answers and generate feedback.
| [Options](Options.md)                                      | Many behaviours can be changed with the options.

## Other authoring topics

The authoring documentation also covers topics on:

#### [CASText](CASText.md)
  
* [Fact Sheets](Fact_sheets.md),
* [Question blocks](Question_blocks.md),
* [Actuarial notation,](Actuarial.md)
* [Using JSXGraph](JSXGraph.md).
  
#### [Input types](Inputs.md)
  
* [Numerical input](Numerical_input.md),
* [Units in input](Units.md#Input_type),
* [Equivalence reasoning](Equivalence_reasoning.md),
* [Multiple choice questions](Multiple_choice_questions.md),
* [Curve sketching](Curve_sketching.md).
  
#### Giving feedback with [potential response trees](Potential_response_trees.md)
  
* Information on [the types of feedback in STACK](Feedback.md),
* Using [Answer tests](Answer_tests.md) and [numerical answer tests](Answer_tests_numerical.md),
* [Answer tests for units](Units.md#Answer_tests).
  
#### Testing and reporting
  
* [Creating question tests](Testing.md),
* [Deploying variants](Deploying.md),
* [Reporting](Reporting.md),
* [Ensuring questions work in the future](Future_proof.md).
  
#### Using Moodle
  
* [Finding the question bank](Question_bank.md),
* [Creating a quiz](Authoring_quick_start_8.md),
* [Importing and exporting](ImportExport.md).
  
#### Other

* Creating [multilingual questions](Languages.md).
* Where to find [sample questions](Sample_questions.md).
* [Frequently asked questions](Author_FAQ.md).

## See also

If you cannot find documentation on the topic you are looking for, it may be located in the [CAS](../CAS/index.md) section of the documentation. This includes documentation on working with Maxima in a question, and so covers topics like

* [Inequalities](/CAS/Inequalities.md),
* [Randomisation](/CAS/Random.md),
* [Plotting graphs](/CAS/Plots.md),
* [Simplification](/CAS/Simplification.md).
