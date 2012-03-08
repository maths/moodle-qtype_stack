# Deploying

Computer aided assessment of mathematics works in the following phases.

1. [Authoring](../Authoring/)
2. [Testing](Testing.md)
3. [Deploying](Deploying.md)
4. [Including questions in a Moodle Quiz](../Components/Moodle.md#Including_questions)
5. [Reviewing](Reviewing.md)

**Before a question can be presented to a student it must have at least one deployed instance**

Deploying a question instance is analogous to printing a question paper (which can then be photocopied many times).

All the question variables are assigned values (usually random) to produce a number of complete, answerable question instances.

For example, ''Differentiate \(3x^2+4x\) with respect to \(x\)'' where \(3\) and \(4\) are the randomly sampled values.  If a question has only a single possible instance then we call it a ''singleton'' and deployment is trivial.  An example is ''Give an example of an even function.''

Deployed instances can be included in quizzes.  The instance that a student is presented with is selected at random from this pool of instances.  So, in a cohort of 50 students, each instance of a deployment of 5 versions would expect to be used 10 times.

Any number of instances can be requested but the deployment automatically discards any generated duplicates (according to the question  note). The deployment management also allows specific instances to be dropped. 

Deployment is not required for authors to test questions: an instance is generated on-the-fly.

Where these are several historical versions of a question, only one of them can be deployed at any time.  This will generally be the most recent stable one.

## Optimising CAS Use ##

A deployment phase allows STACK to optimises its use of the CAS for efficiency and reliability via a [dynamic cache](../Developer/Question_state_caching.md).

To increase system performance, there is the option to ''prime'' question instances.  This processes [question tests](Testing.md) as if they were student submissions to add the responses to the cache.  Where question tests include likely real submissions, this can bring forward the performance benefit of a well-populated cache.

## Future development ##
More systematic control to the curation of a question instance set.
Priming can be requested on the deployment page.  It can be done upon creation of a new instance (auto-priming) or on a set of existing instances.  Priming instances that already have responses cached for the question tests will produce no change in the cache.
