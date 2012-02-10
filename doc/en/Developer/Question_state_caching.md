# Question state caching

## Background ##

To increase efficiency and reliability STACK uses a dynamic cache to store versions of the items. 

In STACK 1.0 question versions were generated on the fly from a stored seed which was used to
generate any random question variables.  Each of these initial instantiations and all subsequent
states required a relatively costly call to the CAS.

Reconstructing an attempt was costly since the list of previous attempts was used to rebuild the
attempt.   Based on the observation that the vast majority of these question states will be
generated multiple times, a mechanism for storing and traversing question states was developed.

Although generally referred to as a cache, it is really a tree of all known question states.
When a student provides a novel input from a state, the CAS is used to produce a new transition.
This will usually produce a new state also, although in some cases, e.g. where there is no change
in the state, the transition loops back to the originating state.

The tree also provides a knowledge base of students' attempts and the associated outcomes which
is used in [reviewing](../Authoring/Reviewing).

## Motivation ##

The advantages of this extra step, as opposed to creating random versions on the fly, are as follows.

* Time is saved in pre-generation.  This makes a significant difference at the beginning of a large class.
* Since the cache remembers students' previous attempts at questions, time is saved when two individuals have the same version.  
* Individual versions can be [tested](../Authoring/Testing) throughly before students use them.
* The cache, combined with a table of how students traversed it and when, is needed in [reviewing](../Authoring/Reviewing) students' attempts.

## Deployment ##

Deployment is the process of generating initial states for the questions, i.e. the different starting 'blank' versions.  Each version has an internal seed for the random variable generation.

_Versions of a question must be cached via deployment before they can be used in a quiz._

When a student attempts a question, one of these versions is randomly _selected_ from this list.

Questions without the [`rand()`](../CAS/Maxima#rand) function in the
[question variables](../Authoring/KeyVals#Question_variables) do not have random versions,
and hence can only have a single deployment at any time (we call them singletons). Other items can
be deployed multiple times.  In the future we plan to allow systematic deployment of multiple versions.

Internally in the database, initial states are identified as such by having a transition from a 'zero' root state to themselves.

## Implementation ##

In this model attempts at a STACK question becomes a traverse around the cache. 
Since the cache is dynamic, there is no loss of flexibility when a student's response is novel.
In a mature cache this should be a rapid process of retrieving XHTML fragments for the student to interact with.

More details of how the tables are given is destiled in the [response processing](Response_processing) page.




