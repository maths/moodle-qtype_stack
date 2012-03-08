# Authoring

Computer aided assessment of mathematics works in the following phases.

1. [Authoring](../Authoring/)
2. [Testing](Testing.md)
3. [Deploying](Deploying.md)
4. [Including questions in a Moodle Quiz](../Components/Moodle.md#Including_questions)
5. [Reviewing](Reviewing.md)

## Authoring STACK questions  ##

Those new to STACK would probably prefer the [Authoring quick start](Authoring_quick_start.md).
There are also [Sample questions](Sample_questions.md).
This page is a reference for all the fields in a question.  

A `stackQuestion` is the basic object in the system. Indeed, STACK is designed as a vehicle to manage these questions.
The table below shows the fields which make up a question.
The only field which is compulsory is in **bold**.

| Name                                                       | Type                                                       | Details                                                                                                                                                                            
| ---------------------------------------------------------- | ---------------------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- 
| Question ID                                                | Int                                                        | Unique database key (Automatic)                                                                                                                                                    
| Name                                                       | Meta                                                       | Names a question                                                                                                                                                                   
| Description                                                | Meta                                                       | Allows a description to be provided.                                                                                                                                               
| Keywords                                                   | Meta                                                       | Comma separated text.  Used for searching etc.                                                                                                                                     
| [Question variables](KeyVals.md#Question_variables)        | [Question Variables](KeyVals.md#Question_variables)        | These are potentially random variables which can be used to generate a question.                                                                                                   
| [Question_stem](CASText.md#Question_stem)                  | [CASText](CASText.md)                                      | This is the question the student actually sees                                                                                                                                     
| [Worked_solution](CASText.md#Worked_solution)              | [CASText](CASText.md)                                      | The worked solution is only available after an item is closed.                                                                                                                     
| [Question note](Question_note.md)                          | [CASText](CASText.md)                                      | Two randomly generated question versions are different, if and only if the question note is different.  Use this field to store useful information which distinguishes versions.   
| [inputs](Inputs.md)                                        |                                                            | The inputs are the things, such as form boxes, with which the student actually interacts.                                                                            
| [Potential response trees](Potential_response_trees.md)    |                                                            | These are the algorithms which establish the mathematical properties of the students' answers and generate feedback.                                                               
| [Options](Options.md)                                      | Options                                                    | Many behaviours can be changed with the options.                                                                                                                                   
| [Testing](Testing.md)                                      |                                                            | These are used for automatic testing of an item and for quality control.                                                                                                           

# See also

* [Answer tests](Answer_tests.md), 
* [Frequently Asked Questions](Author_FAQ.md),
* [Providing feedback](Feedback.md)
* [KeyVals](KeyVals.md)
* [Multi-part mathematical questions](Multi-part_mathematical_questions.md)
* [Question blocks](Question_blocks.md)
* [Question versioning](Question_versioning.md)
* [Units](Units.md)
* Specific adaptations of the [Maxima](../CAS/Maxima.md).
