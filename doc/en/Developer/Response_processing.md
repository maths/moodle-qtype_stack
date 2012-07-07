# Response processing

This document is a work in progress as we move from STACK 2 to STACK 3.  We may not implement the design proposed here.

This document describes how STACK processes the student's attempt at a question, and describes the purpose and
interactions between different parts of the database tables.

This document is intended for developers only.

The reasons why this design was adopted are given in the page on [question state caching](Question_state_caching.md).

### Table `question`

This table contains the information authored by the teacher.   There are other tables which link to this.

1. `interaction_element`.  This holds details of each separate input.  Many inputs, unique for each question.
2. `response_tree`.  This holds details of each separate potential response tree.  Many potential response trees, unique for each question.
3. `keywords` and `question_keywords` link in a many-many way between keywords and questions.

[Question tests](../Authoring/Testing.md) are only needed during teacher testing.

Remember that we have a total disconnect between [inputs](../Authoring/Inputs.md)
and [potential response trees](../Authoring/Potential_response_trees.md) within a [multi-part mathematical question](../Authoring/Authoring_quick_start_2.md).


### Table `deployed_question`

We pre-generate "deployed" versions of each question.  It enables us to "seed" the cache with un-attempted questions.  This also prevents many identical calls to the CAS
at the start of a lab-session.   We can ensure all questions which are the "same" have a common database tree.  See [question note](../Authoring/Question_note.md).  This table stores only the information which changes when we create a deployed version.

To do:  what happens when a teacher edits a question which has been (i) deployed and (ii) for which there are attempts by students?  Best thing to start with is issue a warning and force a "save as new".

## Deploying a question.

1. Do we have any random variables?  If not, just create a singleton question.
2. Generate a new `seed`.
3. Instantiate the question variables.
4. Instantiate the [question note](../Authoring/Question_note.md).  Does this note already appear in `deployed_question` for this `id.question`? Yes. Repeat 1-3 a reasonable number of times.
5. Instantiate all other fields.
6. Create a new line in `deployed_question`.
7. Take `stem.deployed_question` and (i) replace all feedback tags in `stem.deployed_question` with empty strings (ii) add all input html.
8. Create a new line in the `cache` to seed this deployed version.
9. Create a new line in `cache_sequence` where `current.cache_sequence` has the special value of 0 to signify the root of the tree.

## When a student submits an answer.

Note that in STACK students must submit the whole form in one go.  There is no submission of each answer box individually.  The logic would become impossible!
So we use `$_POST` and "student's answer" to be synonymous.

1. STACK receives a submit request.  This includes the following information.
    1. `user_id` - i.e. who.
    2. `q_id`    - i.e. which question. This is `id.question`.
    3. `seed`    - selects which deployed random version, if any.
    4. `$_POST`  - the form containing students' answers, if any.
    5. `event`   - What has the user done? Submitted a new answer, wants a solution after the due date, navigates away?
2. Take `q_id` and `seed` and resolve which random version.  This gives us an `id` in `deployed_question`.
3. Has the student attempted this `deployed_question` before?  We do this by looking at the last entry by time in `attempts` for this user and this `deployed_question`.
   If so find `id.cache`, their position in the `cache` table, if not find the root of this `deployed_question` in the `cache` table.
   The root position has a (special) value of 0 in `current.cache_sequence`
4. Has STACK processed an identical response before?  We check `cache_sequence` table looking for (`post`, `event`, `current`).
   1. Yes.  Add a line to `attempts` setting `cache_id`=`next.cache_sequence`.  Return to the user the `html.cache` where `id.cache`=`next.cache_sequence`.
   2. Have the answers actually changed? Check in `history.cache`. If not, we loop back to the current position in the `cache`.  This counts as an attempt, so we add a line in `cache_sequence` and `attempt`, but does not add a new line to the `cache` itself and hence won't add another penalty.  It also reduces the number of lines in the `cache` substantially.
   3. We now have a new and different answer, so we must actually process this new attempt.

Not currently sure we maintain 2. & 3. in STACK 3.  We may devise a new way to get `id.cache`.

Not sure how we deal with navigation away from the page.

## Processing a genuinely new attempt.

Ok, so now the student has submitted this response to this position in the `cache`.  We now actually have some work to do.

1. Take each `input` for the question.  `history.cache` contains an array giving the previous answer and its status.  Has the answer changed?
  1. Yes.  Validate the answer, and set new status.
  2. No.   If `valid`, update the status from `valid` to `score`.
  3. (Note, that at this point something has changed!)  Create new entry in `attempt_answer`.
  3. Replace IE feedback in `stem.deployed_question`.   This creates HTML of form fields, with student's current answer as `value`.
2. Take each `response_tree` for the question.  Do we need to execute this PRT?
   If yes, execute the tree and create entry in `attempt_prt`. Replace PRT feedback in `stem.deployed_question`.
3. Update the `cache` table, e.g. new `history.cache` in the light of new IEs and any PRTs executed, `html.cache`.
4. Create new linking node in `cache_sequence`.
5. Add a line to `attempts` setting `cache_id`= new id of this `cache` entry.
6. Return to the user the `html.cache` for this new entry.

