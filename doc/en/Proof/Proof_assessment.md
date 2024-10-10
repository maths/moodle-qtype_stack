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
sa:parsons_decode(ans1);
[pd, saa]:proof_assessment(sa, proof_alternatives(ta));
```

The variable `pd` now contains the edit distance from the student's proof to the closest teacher's.  The teacher can decide if this is close enough (zero, of course means exact match) and whether to display the feedback.  For example, use `ATAlgEquiv(pd,0)` to check if the student's proof matches one of the teacher's proofs.  If the number of edits required is at least the length of the teacher's proof then everything needs editing and there is little point displaying feedback!

To display feedback use `{@proof_assessment_display(saa, proof_steps)@}` in a PRT feedback (or other castext).

## Proof assessment when steps within separate sub-hypotheses can be interleaved

Consider the following proof.

<div style="color: #2f6473; background-color: #def2f8; border-color: #d1edf6;">
<div class="proof">
<p>H1. Assume that \(3 \cdot 2^{172} + 1\) is a perfect square.</p>
<p>S2. There is a positive integer \(k\) such that \(3 \cdot 2^{172} + 1 = k^2\).</p>
<p>S3. Since \(3 \cdot 2^{172} + 1 > 2^{172} = (2^{86})^2 > 172^2\), we have \(k > 172\).</p>
<p>S4. We have \(k^2 = 3 \cdot 2^{172} + 1 < 3 \cdot 2^{172} + 173\).</p>
<p>S5. Also, \(3 \cdot 2^{172} + 173 = (3 \cdot 2^{172} + 1) + 172 < k^2 + k\). Further, \(k^2 + k < (k + 1)^2\).</p>
<p>C6. Since \(3 \cdot 2^{172} + 173\) is strictly between two successive squares \(k^2\) and \((k + 1)^2\), it cannot be a perfect square.</p>
</div>
</div>

In this proof has the following dependency structure.

<pre>
     S1
      |
     S2
    / |
  S3  |
  |   S4
  S5  |
  |  / 
  C6
</pre>

In particular, we have the following options for "correct" answers.

1. `[S1, S2, S3, S4, S5, C6]`
2. `[S1, S2, S4, S3, S5, C6]`
3. `[S1, S2, S3, S5, S4, C6]`

This graph dependency cannot be inferred from a proof tree type structure.  While it is considerably simpler for a teacher to write a proof as a tree, interleaved steps in a student's proof may not be wrong.

Therefore we provide a function which takes (a) the student's list (of Parsons keys), and (b) a representation of the directed graph and uses the teacher's graph to conduct assessment.

To author the graph we create lists of key-lists in Maxima as follows.  This is a list of lists, representing the edges of the graph..

<pre>
ta: [
      ["S1", "S2"],
      ["S2", "S3", "S5"],
      ["S2", "S4"],
      ["S4", "C6],
      ["S5", "C6]
     ];
</pre>

1. Each key used in the teacher's proof must occur in the student's list.  Missing keys (i.e. steps) will be flagged.
2. Only keys used in the teacher's proof should occur in the student's list.  Extra keys (i.e. steps) will be flagged.
3. For each list, we check that the keys occur in the specified order in the student's proof.  E.g. 
  * in the first list `["S1", "S2"]` we check that `"S1"` comes before `"S2"` in the student's proof.
  * in the second list `["S2", "S3", "S5"]` we check that `"S2"` comes before `"S3"`, and `"S3"` comes before `"S4"`.  Note, by allowing lists with more than two keys we reduce the complexity of expressing long chains of steps.
4. We do _not_ specify that nothing can be between steps.  That's a separate property which this test does not establish.  (Separate tools are needed to establish, e.g. "No other steps should occur between X and Y".)

Writing a graph is considerably more complex for a teacher than using the `proof()` functions, but of course it gives the teacher more flexibility with what to accept.  The two approaches can be combined.  If a student's answer is found to be incorrect, then we can still establish the closest distance to a proof the teacher considers to be correct to give automatic feedback on how a student should change their proof.

## Bespoke feedback

In addition to the automatic feedback, or as an alternative, a teacher can check other properties and define feedback as required.

E.g. a teacher might want to provide feedback such as _"It makes no sense to use \(M\) before it is defined!"_.
