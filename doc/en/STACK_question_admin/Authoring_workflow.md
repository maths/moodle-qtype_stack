# Question authoring workflow

This document contains suggestions for effective question authoring workflow, especially when working collaboratively.

### 1. Minimal working question

The first task is to create a minimal working question.  At the outset

1. Give the question a meaningful name. E.g. it can match up to course, section and topic.  E.g. `2018ILA-Wk3-Q2: equation of plane`.
2. By default the variable `ta` is used as the "teacher's answer".  Give this a value in the [question variables](../Authoring/Variables.md).
    * The default "model answer" for input `ans1` is `ta`.
    * The default potential response tree checks `ATAlgEquiv(ans1, ta)`
3. Write the question itself.

The above gives a minimal working question. Then you can do the following.
  
* Add minimal [question variables](../Authoring/Variables.md), especially if you intend to create [random variants](../CAS/Random.md) later. We recommend you **add random variants later**.
* Add minimal feedback in the PRTs.
* Create any multi-parts needed.
 
If you already have a good idea of common mistakes, misconception, or intended feedback then you can use the potential response trees to test for this.  This can be added now, however it might be better to wait until step 5 below to add feedback.

Consider

1. Is the phrasing of the question clear to students?
2. Will students know how to input an answer?
   * Could a "syntax hint" or message in the question help?
   * Can "validation" help, e.g. by telling students how many significant figures are expected?  Advanced users might consider [bespoke validation](../CAS/Validator.md) functions.


### 2. Add basic question tests

It is important to [test questions](Testing.md) for quality control, via the "STACK question dashboard".  At this stage the preview question page will warn "Question is missing tests or variants."

We recommend the following two test cases:

1. Ensure the "Model answer" is really awarded full marks by your PRT.
2. Ensure that not every answer is given full marks!

The STACK question dashboard makes it relatively easy to check the mode answer:  press the button marked "Add test case assuming teacher's answer gets full marks".  You will have to think about an answer which is wrong, and the format of this will depend on the types of inputs used.

### 3. Write a robust worked solution

From the STACK question dashboard follow the link to "Send general feedback to the CAS".  This is a special page, only available to the STACK question type.  The page is loaded with the question variables, model answers, and the general feedback (worked solution) fields.  The page allows you to immediately preview the general feedback, edit variables, and then preview the instantiated general feedback.

Add in further question variables, calculations etc. as needed and use these in the worked solution.  _An effective way to create equivalent random variants is to ensure invariance of the worked solution._  If you cannot create a single worked solution for all variants then you can consider using question blocks, or separate questions and selecting questions in the quiz.  A single question need not be the most general possible.

Once the you are happy with the general feedback press the "Save back to question" button. This replaces the question variables and general feedback.  Be aware that if you still have the edit form open, and you subsequently save the edit form again, then you will replace your question variables and general feedback with the contents of the edit form.

### 4. Add random variants

If you need to do so, at this point add in random variants.

1. Edit the question variables to add in randomisation (if you did not do so in step 3.)
2. Add a question note.
3. Return to the Stack question dashboard and _deploy variants_.  The STACK question dashboard link on the edit form will change to "No variants of this question have been deployed yet." to remind you to deploy variants.
4. Confirm any question tests added in step 2 continue to work for all variants.  There is a button "Run all tests on all deployed variants (Slow)" to help you check this.

### 5. Add better feedback

Add in better feedback to the question.

_Feedback is likely to be effective when it is specific in helping student improve on the task._

It is sensible to use the question variables to create variables for each answer which will trigger feedback.  This answer can be based on any random variables, and can be used both in the potential response tree and to create a test case to confirm the feedback and any partial credit is really awarded for each random variant.

In theory one test case should be created for each anticipated response which gets feedback.  In very complex PRTs this is sometimes not possible/practical.

When updating a PRT at this stage we would _expect_ test cases added in step 2 to fail.  This is reassuring as it indicates something significant has changed!  You can easily confirm the new behaviour of the testcase is now what is intended.

### 6. Use data obtained from one cycle of attempts by students.

Rather than second-guess what students _might_ get wrong it is more effective to look at what they _do_.  See the section on [reporting](Reporting.md) for documentation on how to review students' answers.  When feedback/marks are delayed (e.g. online exam) this can be done between students taking the assessment and results being released.  If feedback/marks are immediate then better quality feedback can still be usefully added later.

1. Did the question operate correctly?  E.g. were correct answers correctly marked, and incorrect answers rejected?
2. What did students get wrong?  Is there a reason for these answers such as a common misconception?  If so, add nodes to the PRTs to test for this and improve feedback.
3. Add further question tests to test each misconception.
4. Is there any significant difference between random variants?


# Authoring collaboratively

The question description, and descriptions within PRT nodes, can be used to describe intentions and problems to other team members.  These fields are only available question authors, and are never shown to students.

You can use the `[[todo]]...[[/todo]]` question block to indicate unfinished questions.  See below.

You can use the `[[escape]]...[[/escape]]` and `[[comment]]...[[/comment]]` blocks to remove broken content which is preventing a question from being saved.  Maxima code can be removed with code comments: `/* .... */`, but these comments cannot be used in castext.

## Using tags within the `[[todo]]` blocks

Authoring collaboratively can be better organised by using tags within the `[[todo]]` blocks.  For example

    [[todo tags="tag1,tag2,..."]]Please fix ...[[/todo]]
    
Tags are a comma separated list of strings.  Whitespace will be trimmed from the ends of tags.

* Tags can represent a user (e.g. using their name, username) to alert an issue is for them to resolve.
* Tags can represent a stage in an agreed workflow, e.g. "draft", "for review", "stage 2".
* Tags can represent an issue, e.g. "iss1231" to remind authors to update the question when a fix to a bug/issue finally goes live on a production server.

It is entirely up to individual users to decide on what and how to use tags.

By design, "todo" tags are _not_ tied to userid fields in the moodle site.  Instead tags are site-neutral so they can be preserved in cross-site collaboration.

## Finding questions with `[[todo]]` blocks

Any logged-in user can navigate to the URL

    ../moodle/question/type/stack/adminui/todo.php
    
on the moodle site.  This page will list

1. Any courses in which they are teacher.
2. Any questions in that course which contain STACK questions with `[[todo]]` blocks.
3. Questions which contain `[[todo]]` blocks are arranged into groups by tag, and additionally there is a list of questions with `[[todo]]` blocks without any tags.

Notice that, by design, any teachers on the course can see all tags. The purpose of the `[[todo]]` block is _not_ to provide a private list of tickets for an individual user to address.  Rather, it is a public list of tags.  In this way, if users choose to use tags to flag issues for users, every teacher can see outstanding tags including those intended for them and those raised by them for someone else.

We anticipate lists of questions with `[[todo]]` blocks will be rather short, and so questions are displayed on a simple page.

Since `[[todo]]` blocks can be added anywhere, they could be used in the Question Description field.  Once these are resolved, the Question Description field can be used to store an audit trail, and any metadata, as required by a particular collaborative workflow.

## Future ideas.

If using tags within the `[[todo]]` blocks becomes popular may

1. Hook into Moodle's "cron" system to alert teachers to questions with `[[todo]]` blocks via email.
2. Hook into the Moodle question bank to flag questions with outstanding `[[todo]]` blocks more visibly in the question bank.

