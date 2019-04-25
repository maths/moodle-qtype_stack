# Adding questions to a quiz

Computer-aided assessment of mathematics works in the following phases.

1. [Authoring](../Authoring/index.md),
2. [Testing](Testing.md) and
3. [Deploying](Deploying.md) questions.
4. [Adding questions to a quiz](Quiz.md) and use by students.
5. [Reporting](Reporting.md) and statistical analysis.

## Adding questions to a quiz  ##

STACK questions form part of the Moodle quiz.
You may form quizzes with a mix of STACK questions and other Moodle question types.
Please consult the [Moodle documentation](http://docs.moodle.org/en/Quiz_module) for details of how to create
and use Moodle quizzes.

## Setting quiz options ##

Beware, once a student has attempted the quiz you may not alter the options!

Also note that when authoring a STACK question there are two forms of "feedback".

* for each input there is validation feedback.
* for each potential response tree there is "feedback" based on the mathematical properties established.

We *always* want to give validation feedback, and this must be placed in the Question text.  

The potential response tree feedback can be placed anywhere in the Question text, or in the "Specific feedback"
field of the question.  It usually makes sense for multi-part questions to put the feedback in the question text.
However, if you do so it will always be displayed and will not respect the "specific feedback" settings in the
"Review options" section of the quiz settings.

## Behaviours ##

Levels of [feedback](Feedback.md) are controlled by the [quiz settings](http://docs.moodle.org/en/Quiz_settings),
and in particular the [question behaviours](http://docs.moodle.org/en/Question_behaviours).



