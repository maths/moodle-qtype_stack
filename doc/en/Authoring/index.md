# Authoring

Computer aided assessment of mathematics works in the following phases.

1. [Authoring](../Authoring/)
2. [Testing](Testing)
3. [Deploying](Deploying)
4. [Including questions in a Moodle Quiz](../Components/Moodle#Including_questions)
5. [Reviewing](Reviewing)

## Authoring STACK questions  ##

Those new to STACK would probably prefer the [Authoring quick start](Authoring_quick_start).
There are also [Sample questions](Sample_questions).
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
| [KeyVals#Question variables](KeyVals#Question_variables)   | [KeyVals#Question Variables](KeyVals#Question_variables)   | These are potentially random variables which can be used to generate a question.                                                                                                   
| **[CASText#Question_stem](CASText#Question_stem)**         | [CASText](CASText)                                         | This is the question the student actually sees                                                                                                                                     
| [CASText#Worked_solution](CASText#Worked_solution)         | [CASText](CASText)                                         | The worked solution is only available after an item is closed.                                                                                                                     
| [Question note](Question_note)                             | [CASText](CASText)                                         | Two randomly generated question versions are different, if and only if the question note is different.  Use this field to store useful information which distinguishes versions.   
| [Interaction elements](Interaction_elements)               |                                                           | The interaction elements are the things, such as form boxes, with which the student actually interacts.                                                                            
| [Potential response trees](Potential_response_trees)       |                                                           | These are the algorithms which establish the mathematical properties of the students' answers and generate feedback.                                                               
| [Options](Options)                                         | Options                                                    | Many behaviours can be changed with the options.                                                                                                                                   
| [Testing](Testing)                                         |                                                           | These are used for automatic testing of an item and for quality control.                                                                                                           
| [Metadata](Metadata)                                       | Data about this question.                                  |                                                                                                                                                                                    

# See also

* [Answer tests](Answer_tests), 
* [Frequently Asked Questions](Author_FAQ),
* [Providing feedback](Feedback)
* [KeyVals](KeyVals)
* [Multi-part mathematical questions](Multi-part_mathematical_questions)
* [Question blocks](Question_blocks)
* [Question versioning](Question_versioning)
* [Units](Units)
* Specific adaptations of the [Maxima](../CAS/Maxima).