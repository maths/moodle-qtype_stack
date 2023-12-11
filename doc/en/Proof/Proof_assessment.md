# Assessing Parson's problems and proofs

Parsons problems result in a _tree_ representing the student's proof.  This document explains how to assess Parson's problems.

[The Damerau-Levenshtein distance](../Topics/Levenshtein_distance.md) is a metric for measuring the difference between two strings. Informally, this is the _edit distance_ measuring  the minimum number of single-character edits (insertions, deletions, transition or substitutions) required to change one string into the other. STACK uses this metric to assess answers which are text strings.  The problem of assessing Parson's problems is very similar.

1. We want to establish the distance between the student's proof and a teacher's proof, and identify the closest proof from a list deemed acceptable by the teacher.
2. We want to automatically provide feedback detailing which edits will transform the student's proof into a "correct" proof, e.g. "Swap these two lines", "Insert a line here".

Rather than a string of characters, we apply the metric to a list of `keys` tags in the `proof_steps` list, as defined in the [CAS library for representing text-based proofs](Proof_CAS_library.md).  Further, by tracking the steps in the algorithm we can provide automatic feedback about which edits are required to transform one list into another.

## General automatic assessment tools

We assume that the teacher's answer is `ta` is expressed using ["proof construction functions"](Proof_CAS_library.md) e.g.

````
ta:proof_iff("A","B");
````

where the keys are part of a `proof_steps` list.

Define `tal` to be the _list of teacher's answers_.  This can be created automatically, e.g. `tal:proof_alternatives(ta)`
or a teacher can define a bespoke list of proofs.

The assessment function `proof_assessment(sa, tal)` takes a proof provided by a student and a list of acceptable proofs and does the following.

1. All proofs are flattened to lists of keys.  The Damerau-Levenshtein distance only applies to lists, not trees.
2. Calculate the distance between the student's proof and each of the teacher's proofs (in `tal`), and identify the closest proof.
3. Return a list `[d, [edits]]`, where `d` is the shortest distance and `edits` is the list of edits (if any) needed for each step.

To provide feedback the list of edits can be sent directly to a display function.

To use this in practice define the following feedback variables.

```
sa:proof_parsons_interpret(ans1);
[pd, saa]:proof_assessment(sa, proof_alternatives(ta));
```

The variable `pd` now contains the edit distance from the student's proof to the closest teacher's.  The teacher can decide if this is close enough (zero, of course means exact match) and whether to display the feedback.  For example, use `ATAlgEquiv(pd,0)` to check if the student's proof matches one of the teacher's proofs.  If the number of edits required is at least the length of the teacher's proof then everything needs editing and there is little point displaying feedback!

To display feedback use `{@proof_assessment_display(saa, proof_steps)@}` in a PRT feedback (or other castext).

## Bespoke feedback

In addition to the automatic feedback, or as an alternative, a teacher can check other properties and define feedback as required.

E.g. a teacher might want to provide feedback such as _"It makes no sense to use \(M\) before it is defined!"_.
