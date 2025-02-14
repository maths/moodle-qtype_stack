# CAS libraries for representing text-based proofs

STACK provides libraries to represent and manage lines of a text-based proof in a tree structure.  This page is reference documentation for these CAS libraries.  For examples of how to use these see the topics page on using [Parson's problems](../../Specialist_tools/Drag_and_drop/index.md).

To use these functions you have to [load an optional library](../../Authoring/Inclusions.md) into each question.

E.g. `stack_include_contrib("prooflib.mac")` will include the library published in the master branch on github, which will be at or just ahead of an official release.

## Proof construction functions, and their manipulation

Proofs are represented as "proof construction functions" with arguments.  For example, an if and only if proof would be represented as `proof_iff(A,B)`, where both `A` and `B` are sub-proofs. Proof construction functions don't typically modify their arguments, but some proof construction functions have simplification properties.  For example `proof_iff(A,B)` is normally equivalent to `proof_iff(B,A)`.

STACK supports the following types of proof construction functions.  The following are general proofs

* `proof()`: general, unspecified proof.
* `proof_c()`: general proof, with commutative arguments.  Typically each argument will be another proof block type.
* `proof_opt()`: steps in a proof which are optional.  It assumes a single step.  Wrap each optional step individually.

The following represent particular types of proof.

* `proof_iff()`.  This proof must have two arguments.  These arguments are assumed to commute.
* `proof_cases()`. In proof by exhaustive cases, the first element is fixed, and it typically used to describe/justify the cases.  E.g. "\(n\) is either odd or even".  The remaining cases commute, and typically each argument will be another proof block type.
* `proof_goal()`. This proof seeks to establish the goal.  The last element is fixed, and it typically used to describe/justify the goal.  E.g. "\(f\) is continuously differentiable".  The remaining cases commute, and typically each argument will be another proof block type.
* `proof_ind()`.  A proof by induction must have four arguments.  The 1st and 4th are fixed position (defining the statement and conclusion), whereas the 2nd and 3rd commute (for the base case, and induction step).

It is relatively easy to add in new functions to represent a particular type of proof.

The variable `proof_types` is a list holding the names of all proof construction functions.   E.g. this list is used by the predicate `proofp` to decide if the operator represents a proof.

`proof_validatep` is a validation function: the argument must be a tree built from proof construction functions as operators (with the right number of arguments in some cases) or atoms which are integer or strings.

`proof_flatten` turns a proof-tree into a list of steps.  E.g. `proof_flatten(proof_iff(A,B))` is just `[A, B]`.  This function is useful when the teacher creates a structured tree, but a student's proof is a flat list.

`proof_normal` returns a normalised proof-tree, e.g. sorting arguments of commutative proof construction functions.  Note, this function does not change keys, so will not match proofs using integer keys with a proof using string keys.

`proof_alternatives` returns a list of all proof trees which are alternatives to its argument.  E.g. `proof_iff(A,B))` has two alternatives:  `[proof_iff(A,B),proof_iff(B,A)]`.  This function recurses over all sub-proofs to generate any tree which might be equivalent.  Note, if a student is correct then using `proof_normal` will match their answer with the teacher's.  However, when trying to identify possible mistakes we need to find the tree which is _closest_ to one of the acceptable proof trees.  Use this function sparingly, as it is computationally expensive on deeply nested trees with many commutative elements.

## Using a `proof_steps` list

The design relies on a list of `proof_steps`.   Consider this example:"\(n\) is odd if and only if \(n^2\) is odd.";

````
proof_steps: [
    ["assodd",     "Assume that \\(n\\) is odd."],
    ["defn_odd",   "Then there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n=2m+1\\)."],
    ["alg_odd",    "\\[ n^2 = (2m+1)^2 = 2(2m^2+2m)+1.\\]"],
    ["def_M_odd",  "Define \\(M=2m^2+2m\\in\\mathbb{Z}\\) then \\(n^2=2M+1\\)."],
    ["conc_odd",   "Hence \\(n^2\\) is odd."],

    ["contrapos",  "We reformulate \"\\(n^2\\) is odd \\(\\Rightarrow \\) \\(n\\) is odd \" as the contrapositive."],
    ["assnotodd",  "Assume that \\(n\\) is not odd."],
    ["even",       "Then \\(n\\) is even, and so there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n=2m\\)."],
    ["alg_even",   "\\[ n^2 = (2m)^2 = 2(2m^2).\\]"],
    ["def_M_even", "Define \\(M=2m^2\\in\\mathbb{Z}\\) then \\(n^2=2M\\)."],
    ["conc_even",  "Hence \\(n^2\\) is even."]
];

P1:proof_iff(proof("assodd","defn_odd","alg_odd","def_M_odd","conc_odd"),proof("contrapos","assnotodd","even","alg_even","def_M_even","conc_even"));
````

Note that the variable `proof_steps` is a _list_ of lists:  `[ ["key", "step", ("comment")], ...`

1. The first element is a `key`. This is a string, or integer, which is used to refer to the `step`.
2. The `key` can be an integer position in the proof_steps, a string `key`, or a string.
  * Using integers: `proof_iff(proof(1,2,3,4,5),proof(6,7,8,9,10,11))`;
  * Using keys: `proof_iff(proof("assodd","defn_odd","alg_odd","def_M_odd","conc_odd"),proof("contrapos","assnotodd","even","alg_even","def_M_even","conc_even"))`
3. The `proof_steps` list can contain an optional string argument `"comment"`.  This string can be used to store justification, explanation and narrative.  Some display functions use this argument, when it exists.  To prune out the comments use `proof_steps_prune(proof_steps)` as an argument to the display function.
4. Note that the backslash must be protected when defining these strings.
5. The strings can contain HTML, including `<img>` tags for including images within draggable elements.

`proof_keys_sub(ex, proof_steps)` takes a proof built from numbered indexes, and translate this into string keys.  In the above example, it might be easier to author a proof as `proof(1,2,3,4,5)` rather than type in `proof("assodd","defn_odd","alg_odd","def_M_odd","conc_odd")`. The whole design could be built on numbered keys (and these are valid), but string keys are easier to remember and easier to interpret when looking at students' attempts.  Also, string keys can be inserted later without the need to re-number existing numerical keys. Do not use numerical keys if you intend to randomly permute the strings in the proof!

`proof_keys_int(ex, proof_steps)` takes a proof built from string keys, and translate this into numbered indexes.

`proof_getstep(key, proof_steps)` looks for the key `key` in the `proof_steps` list.  They key can be either a numerical, or string key.  If found then the function returns the full string, otherwise the key is returned without an error.  One advantage of this approach is that the teacher can define abbreviated, alternative proofs using some of the strings in `proof_steps`, e.g. using the above example the following omits all the detail in the sub-proofs, focusing on the structure and hypothesis/conclusion of each block.

````
P2:proof_iff(proof("assodd","\\(\\cdots\\)","conc_odd"), proof("contrapos","assnotodd","\\(\\cdots\\)","conc_even"));
````

When displayed, the keys `"\\(\\cdots\\)"` do not occur in `proof_steps`, so are returned unchanged.  (Alteratively a teacher could add keys to `proof_steps` for such situations.)

`proof_parsons_key_json(ta, proof_steps)` is used to construct the _model answer_ in an input.  This function separates the keys in `proof_steps` into those used in `ta` and those which are unused.  This enables the _model_answer_ to construct a Parson's block as the teacher might leave it, with both the model answer and some strings unused.  Use `proof_parsons_key_json(ta, [])` to omit displaying any unused steps when showing the model answer.

## Displaying whole proof and proof-step pairs

To display a whole proof whole proof using proof-step lists use `proof_display(P1, proof_steps)`.  E.g. add `{@proof_display(P1, proof_steps)@}` to the appropriate castext.  This will (1) replace all keys in the proof `P1` with the corresponding strings in `proof_steps` (if they exist) and (2) display the structure using the nested `<div class="proof-block">` from the [CSS Styles for displaying proof](Proof_styles.md), (3) display any narrative.

`proof_display_para(P1, proof_steps)` displays a complete proof, but using HTML paragraphs to split blocks.  This is a more traditional presentation of proof.

To prume out the optional comments use `proof_steps_prune(proof_steps)` as an argument to the display function.  E.g. use the following castext `{@proof_display(P1, proof_steps_prune(proof_steps))@}`

## Example proofs

Example proofs are distributed with STACK in a `proofsamples` sub-folder of the maxima "contrib" folder: [https://github.com/maths/moodle-qtype_stack/tree/master/stack/maxima/contrib](https://github.com/maths/moodle-qtype_stack/tree/master/stack/maxima/contrib).  These proofs can be loaded with `stack_include` in the normal way.  Each file contains

1. A string variable `thm` showing the Theorem to be proved.
2. A proof steps list `proof_steps`
3. A variable `proof_ans` which contains one proof the teacher considers correct.

