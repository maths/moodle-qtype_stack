# Maintaining the STACK library using Gitsync

If you want to include a course and its quizzes in the STACK library you can do so in the following way using Gitsync. The quizzes should only contain questions from within their own question bank. The questions can be spread through multiple question categories and this category structure will be replicated on import via the library.

## Initial export
- In your local STACK repo, create an ordinary folder in the STACK library to hold everything e.g. `Stuff`. (Don't use `git init`.)
- Set your Gitsync root folder in your config file as `/var/www/html/your_moodle/question/type/stack/samplequestions/stacklibrary`.
- Set `usegit` to `true` if you want to use all Gitsync's tracking functionality. Set `usegit` to `false` if you're just doing
a one-off export and/or really want to avoid automatic commits.
- Using Gitsync version 2025012200+, create a whole course repo:  
`php createwholecourserepo.php -n 7 -d 'Stuff/Stuff-course'`  
where `n` is the course id. The course questions will go into the supplied directory and each quiz will go into an automatically
created sibling directory. 
- Purge the MUC cache to see the results in  the STACK library. The displayed directory ignores the `top` folders, category/manifest files, etc. If `top` contains just a single question folder, that folder will also be ignored. `Stuff-course_quiz_quiz-1\top\Default-for-Quiz-1\Question-1.xml` appears as `Stuff-course_quiz_quiz-1\Question-1.xml` in the displayed directory tree with the quiz data file `quiz-1_quiz.json` at the same level as the questions.

## Maintaining an individual quiz
- If you make changes to your original version of a quiz and its questions in Moodle, you can export them to the STACK repo by targeting
the manifest of the specific quiz:  
`php exportrepofrommoodle.php -f 'Stuff/Stuff-course_quiz_quiz-2/instance1_module_a-course-full-of-quizzes_quiz-2_question_manifest.json'`  
- If you make changes to the questions in the repo, you can import them back into Moodle:  
`php importrepotomoodle.php -f 'Stuff/Stuff-course_quiz_quiz-2/instance1_module_a-course-full-of-quizzes_quiz-2_question_manifest.json'`  
Quiz structure changes will not be imported into Moodle.

## Maintaining the whole course
You can also import/export the course and all its quizzes at once by using the whole course scripts and targeting the course manifest:  
`php exportwholecoursefrommoodle.php -f 'Stuff/Stuff-course/instance1_course_a-course-full-of-quizzes_question_manifest.json'`  
`php importwholecoursetomoodle.php -f 'Stuff/Stuff-course/instance1_course_a-course-full-of-quizzes_question_manifest.json'`

## Incorporating changes made in a quiz imported via the STACK library
If a quiz has been imported via the STACK library and updated and you want to capture those changes, you can't simply use `exportrepo` since `importrepo` was not used to create the quiz and so there is no link between the questions in Moodle and the STACK repo.

Use `createrepo` on the quiz but target a new folder and manually copy the output to the STACK library. (Don't create the new repo using Git in the STACK library as spurious auto-commits will clog the commit history. Either set `usegit` to `false` in config and delete the repo after copying OR create the repo somewhere else entirely.) Take care with files where the question name has changed (and so the file name has changed) or with questions that are in the question bank but not in the quiz (and so won't be in the new quiz repo).