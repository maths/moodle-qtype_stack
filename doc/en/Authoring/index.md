# Authoring

Computer aided assessment of mathematics works in the following phases.

1. [Authoring](../Authoring/index.md),
2. [Testing](Testing.md) and
3. [Deploying](Deploying.md) questions.
4. [Adding questions to a quiz](Quiz.md) and use by students.
5. [Reporting](Reporting.md) and statistical analysis.

Those new to STACK would probably prefer the [Authoring quick start](Authoring_quick_start.md).

* [Authoring quick start 1](Authoring_quick_start.md) A basic question.
* [Authoring quick start 2](Authoring_quick_start_2.md) Multi-part mathematical questions.
* [Authoring quick start 3](Authoring_quick_start_3.md) Turning simplification off.
* [Authoring quick start 4](Authoring_quick_start_4.md) Equivalence reasoning inputs.

There are also [Sample questions](Sample_questions.md).
This page is a reference for all the fields in a question.

## How STACK questions behave  ##

* Guidelines to students on [answer assessment](../Students/Answer_assessment.md).
* [Providing feedback](Feedback.md).

## STACK question data structure  ##

A `stackQuestion` is the basic object in the system. Indeed, STACK is designed as a vehicle to manage these questions.
The table below shows the fields which make up a question.
The only field which is compulsory is in **bold**.

| Name                                                       | Type                                                       | Details
| ---------------------------------------------------------- | ---------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
| Name                                                       | Meta                                                       | Names a question
| [Question variables](KeyVals.md#Question_variables)        | [Question Variables](KeyVals.md#Question_variables)        | These are potentially random variables which can be used to generate a question.
| [Question text](CASText.md#question_text)                  | [CASText](CASText.md)                                      | This is the question the student actually sees
| [General feedback](CASText.md#General_feedback)            | [CASText](CASText.md)                                      | The worked solution is only available after an item is closed.
| [Question note](Question_note.md)                          | [CASText](CASText.md)                                      | Two randomly generated question versions are different, if and only if the question note is different.  Use this field to store useful information which distinguishes versions.
| [Inputs](Inputs.md)                                        |                                                            | The inputs are the things, such as form boxes, with which the student actually interacts.
| [Potential response trees](Potential_response_trees.md)    |                                                            | These are the algorithms which establish the mathematical properties of the students' answers and generate feedback.
| [Options](Options.md)                                      | Options                                                    | Many behaviours can be changed with the options.
| [Testing](Testing.md)                                      |                                                            | These are used for automatic testing of an item and for quality control.

# See also

* [Answer tests](Answer_tests.md),
* [Frequently Asked Questions](Author_FAQ.md),
* [KeyVals](KeyVals.md)
* [Deploying question versions](Deploying.md)
* Specific adaptations of [Maxima](../CAS/Maxima.md).
* [Import and Export](ImportExport.md) of STACK questions.
* [Question blocks](Question_blocks.md)


