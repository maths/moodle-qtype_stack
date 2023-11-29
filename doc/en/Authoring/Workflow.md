# Question authoring workflow

This document contains suggestions for effective question authoring workflow, especially when working collaboratively.

### 1. Minimal working question

The first task is to create a minimal working question.  At the outset


1. add minimal [question variables](Variables.md) to prevent repetition of information in the "Model answer" field of the inputs, and PRT nodes;
2. use question variables for key information in the question, especially if you intend to create [random variants](../CAS/Random.md) later;
3. add minimal feedback in the PRTs.

It is helpful if the question name is meaningful and consistent in a course.  E.g. it can match up to course, section and topic.  E.g. `2018ILA-Wk3-Q2: equation of plane`.  This makes finding questions later much easier.

Create any multi-parts needed, but we recommend at this stage _not_ to add random variants.  Random variants will be added later.

If you already have a good idea of common mistakes, misconception, or intended feedback then you can use the potential response trees to test for this.  This can be added now, however it might be better to wait until step 5 below to add feedback.

Consider

1. Is the phrasing of the question clear to students?
2. Will students know how to input an answer?
   * Could a "syntax hint" or message in the question help?
   * Can "validation" help, e.g. by telling students how many significant figures are expected?  Advanced users might consider [bespoke validation](../CAS/Validator.md) functions.


### 2. Add basic question tests

It is important to test questions for quality control, as described in the [testing](Testing.md) documentation.  Navigate to the "STACK question dashboard" page by following the link at the top of the question editing page, or on the preview page.  At this stage the preview question page will warn "Question is missing tests or variants."

We recommend the following two test cases:

1. Ensure what you have given in the inputs as the "Model answer" is really awarded full marks by your PRT.
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

You can use the `[[todo]]...[[/todo]]` question block to indicate unfinished questions.  Authors with the capability to use the "STACK diagnostic tools" can create a list of questions containing this block, making it easy for them to locate questions needing attention.

You can use the `[[escape]]...[[/escape]]` and `[[comment]]...[[comment]]` blocks to remove broken content which is preventing a question from being saved.  Maxima code can be removed with code comments: `/* .... */`.

