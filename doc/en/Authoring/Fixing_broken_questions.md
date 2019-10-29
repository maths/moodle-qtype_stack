
# I have a misbehaving STACK question in a live Moodle quiz.  What should I do?

You can't remove or add a question to a live Moodle quiz, but you can modify it.  If you edit a live STACK question then students will see the updates.

There are "regrade" and "dry-run regrade" options in the Moodle quiz.  If you are happy with the new behaviour of the question you can regrade. You can also manually assign a mark in the Moodle quiz. 

Do not change anything which alters the randomisation of variants.  Students get a "seed" which starts the pseudo-random number generator.  If your changes change the random version, then the students' previous answers will still stand, and will now most likely be "wrong".  Minor typographical mistakes can be fixed, but often there is nothing you can do to fix seriously bad random versions.  This is why we have the "deploy" system and [question tests](Testing.md) so question variants can be checked in advance....

The Moodle database has no "undo"!  The best thing to do with a live question which is broken is copy it, play around and be completely sure you are happy.  Then, change the live version with confidence.

If you cannot apply a patch, then create a copy of the question and put it in a new category "for next year".  It is normally best to fix it now!
