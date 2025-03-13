# Authoring individual STACK questions

This section of the documentation provides _reference_ of the authoring features of individual STACK questions.

## STACK question structure  ##

A  "question" is the basic object in the system. The following table shows the fields which make up a question, with links to the documentation for each one.

| Name                                                       | Details
| -------------------------------------------------------------------| ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
| Question name              | Names a question
| [Question variables](Variables.md#Question_variables)      | These are potentially random variables which can be used to generate a question.
| [Question text](CASText.md#question_text)                  | This is the question the student actually sees
| [General feedback](CASText.md#General_feedback/Worked_solution)            | The worked solution is only available after an item is closed.
| [Question note](../Authoring/Question_note.md)                          | Two randomly generated question variants are different, if and only if the question note is different.  Use this field to store useful information which distinguishes variants.
| [Inputs](Inputs/index.md)                                        | The inputs are the things, such as form boxes, with which the student actually interacts.
| [Potential response trees](Potential_response_trees.md)    | These are the algorithms which establish the mathematical properties of the students' answers and generate feedback.
| [Options](Question_options.md)                                      | Many behaviours can be changed with the options.

The authoring documentation also covers topics on:

* [Input types](Inputs/index.md)
* [Answer tests](Answer_Tests/index.md).
* [Question blocks](Question_blocks/index.md),
* Information on [the types of feedback in STACK](Feedback.md),

#### Other

* Creating [multilingual questions](Languages.md).
* [Frequently asked questions](Author_FAQ.md).

