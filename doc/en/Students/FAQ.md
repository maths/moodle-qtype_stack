# Frequently asked questions

This page is designed to help student users of STACK, rather than question authors.

## How do I enter my answers?

You often need to enter an answer which is an algebraic expression. Details and examples are given on the [answer input](Answer_input.md) page. The short answer to this question is that you should type in your answers using the same syntax as that used in the symbolic mathematics package [Maxima](../CAS/Maxima.md) (this page is written for teachers, but you may find it interesting).

## Why is my answer "invalid"?

STACK validates students' answers before they are graded, checking if the answers are in an acceptable syntax and giving the student a chance to confirm their answer is correct. By default, answers not explicitly specifying multiplication (fx. \(2x\) instead of \(2*x\)) are not accepted. This is linked to a core [philosophy of STACK](../About/The_philosophy_of_STACK.md); students should not be penalized for poor computer syntax. It is better to bug students until their answer is unambiguous, than to risk students being marking wrong for typic in "sinx" instead of "sin(x)" and having the computer interpret it as "s\*i\*n\*x".

That being said, there are options in STACK to be less strict with student syntax, so that the system may accept "2x" as a valid input. The question author has to activate this function, so contact your teacher if you think they should choose this option.

There may be other reasons your answer is not allowed. By default, STACK does not allow variables that are longer than two characters, and your teacher may also have forbidden names and functions they do not want you to use in your answer.

## Why is my quiz acting differently?

Different quizzes can have different [question behaviours](https://docs.moodle.org/37/en/Question_behaviours). Some quizzes may only accept one answer, others may accept an unlimited amount. Some quizzes may allow you to "check" if each question is correct before moving on to the next, others may not include this. Some quizzes may give you feedback immediately, other only after a while. Your teacher may have picked different question behaviours for your different quizzes.

## How do I change the language?

Click on your profile, and go to `Preferences`, ` Preferred language` and then select the language. [Here](https://stack2.maths.ed.ac.uk/demo2018/user/language.php) is a direct link to this page (for sites using Moodle). If your language is not available, contact your site administrator - it is likely that they have not installed the relevant language package. Currently, only some languages are supported by STACK. See [Language packages](../Developer/Language_packs.md) for a full list. If the interface is translated, but the question text and feedback is not, it is because your teacher has not added support for [multilingual questions](../Authoring/Languages.md) when authoring the question.

## Where can I report bugs?

Depends. Is it an issue in the question authoring? Fx.

- The feedback contains strange symbols, like "@nn@".
- My answer was marked "wrong", but I think it should be marked "correct".
- My quiz is not open, but I think it should be.
- There is a spelling error in the question text.

In this case, it is likely your teacher has made an error when authoring the question, so you should contact them first. In general, if you are unsure what is responsible for your bug, the safest option is to contact your teacher, as they are likely to have a better idea of how to categorize the issue.

If you think you have found a genuine STACK bug, you should report it in the [STACK GitHub](https://github.com/maths/moodle-qtype_stack/issues).

If the bug seems to concern something outside of the question, for example the quiz or the website, you may have found a bug in the learning management system your institution uses. This should be reported to their respective bug report system, for example [the one for Moodle](https://tracker.moodle.org/secure/Dashboard.jspa).

## How do I make the fonts bigger?

See the information on [Accessibility](Accessibility.md).