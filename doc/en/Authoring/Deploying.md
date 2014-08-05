# Deploying

Computer aided assessment of mathematics works in the following phases.

1. [Authoring](../Authoring/index.md),
2. [Testing](Testing.md) and
3. [Deploying](Deploying.md) questions.
4. [Adding questions to a quiz](Quiz.md) and use by students.
5. [Reporting](Reporting.md) and statistical analysis.


Deploying a question variant chooses and fixes specific values for any random elements within the question variables.  When a student attempts the question they will be given a random selection from the deployed variants.  STACK questions are not randomly generated on the fly.  We have chosen to add this step, instead of generating on the fly, for a number of reasons.

1. The teacher can run the question tests on each deployed version before the student sees the question to establish the question is working.  This aids quality control.
2. The teacher can decide if each deployed version appears to be of equal difficulty.  Experience suggests there are unanticipated consequences of randomly generating questions.  (Really only statistics of use can establish fairness.)
3. The pre-generation combined with a cache helps minimise server load during the start of a large class which aids robustness of the whole experience.  This helps STACK to optimise its use of the CAS for efficiency and reliability via a [dynamic cache](../Developer/Question_state_caching.md).

Any number of instances can be requested and deployed but only one instance of each [question note](Question_note.md) can be deployed.  It is possible to deploy \(n\) variants in one go, but the system will give up if too many duplicate question notes are generated.  The teacher is responsible to ensure question variants are different if and only if the question notes are different.  The deployment management also allows specific variants to be dropped.  You can also return to the question preview window and try a specific deployed variant.

Also, it would be nice to loop to look for variants which have not been deployed yet, but this is yet to be implemented....

**If a question uses randomization then it must have at least one deployed instance before it can be presented to a student.**  Questions that don't use randomization cannot be deployed explicitly.  STACK automatically detects randomization.

Deployment is not required for authors to test questions: an instance is generated on-the-fly.

## How to deploy question variants ##

The deployment interface can be found on the top of the [Testing](Testing.md) page.

This page also contains the list of currently deployed versions.
