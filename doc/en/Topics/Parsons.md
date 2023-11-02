# Authoring Parson's problems (drag and drop) in STACK

Parson’s problems require students to assemble pre-written text into a correct order by dragging blocks into a tree structure.

This page provides a quick-start to authoring whole questions.  There are a number of parts to authoring a Parson's problem:

1. Defining strings and creating drag and drop area using the [Parson's block](../Authoring/Parsons.md).
2. Linking the Parson's block to a STACK input.
3. Assessing the student's answer.

While drag and drop is certainly not new in automatic assessment, these problems have become popular in computer science, e.g. Parsons (2006).

Parson’s problems enable students to focus on the structure and meaning, by providing all the right words but just not initially in the right order. Learning mathematical proof is well-known to be very difficult for most students. The scaffolding provided by Parson's problems has been found to be very useful to students in discrete mathematics. This may well be due to the _expert reversal effect_. The expert reversal effect is a well-established phenomenon that what is useful for a beginner is quite different, perhaps the opposite, of what is useful for an expert. Even when independent proof writing is the final goal of instruction, it is very likely that Parson's problems have an important place in formative learning towards that goal.

Traditionally Parson’s problems require the student to create an ordered list. For mathematical proofs, we believe it is more natural to ask students to create a _tree_. Parson's problems in mathematics, especially proofs, do not always have a unique answer which is considered correct.
Hence, there are challenges in deciding what a correct answer is in particular cases, and how to develop authoring tools to robustly create problems with reliable marking algorithms.
For example, in a proof of
\[ A\mbox{ if and only if } B\]
We might expect/require two conscious and separate blocks

1.  Assume \(A\), \(\cdots\), hence \(B\).
2.  Assume \(B\), \(\cdots\), hence \(A\).

The order in which these two sub-proofs are presented is (normally) irrelevant.  That is the _if and only if_ proof construct allows its two sub-proofs to commute.  This is precisely the same sense in which \(A=B\) and \(B=A\) are equivalent. There are _blocks_ within the proof which can change order. Furthermore, since proofs are often nested blocks these sub-proofs may themselves have acceptable options for correctness. If the student has an opportunity to indicate more structure, then the assessment logic becomes considerably simpler, more reliable and transparent. Actually, we think there is significant educational merit in making this structure explicit and consciously separating proof-structure from the finer grained details.
It is true that professional mathematicians often omit indicating explicit structure, they abbreviate and omit whole blocks ("the other cases are similar") but these are all further examples of expert reversal.

Notes

* Lists are a special case of a tree with one root (the list creation function) and an arbitrary number of nodes in order.  Hence our design explicitly includes traditional Parson's problems as a special case.
* Teachers who do not want to scaffold explicit block structures (e.g. signal types of proof blocks) can choose to restrict students to (i) flat lists, or (ii) lists of lists.

# Minimal Parson's question

The following is a minimal Parson's question where there student is expected to create a list in one and only one order.

## Question text

The example question text below contains a Parson's block. Within the header of the Parson's block, ensure that `input="inputvar"` is included, where `inputvar` is the identifier of the input, for example `input="ans1"` as below. A minimal working example for the proof that _\(\log_2(3)\) is irrational_ is achieved by placing the following in the _Question text_ field:

````
<p>Show that \(\log_2(3)\) is irrational. </p>
[[parsons input="ans1"]]
{
    "assume" : "Assume, for a contradiction, that \\(\\log_2(3)\\) is rational.",
    "defn_rat" : "Then \\(\\log_2(3) = \\frac{p}{q}>0\\) where ",
    "defn_rat2" : "\\(p\\) and \\(q\\neq 0\\) are positive integers.",
    "defn_log" : "Using the definition of logarithm:",
    "defn_log2" : "\\[ 3 = 2^{\\frac{p}{q}}\\]",
    "alg" : "\\[ 3^q = 2^p\\]",
    "alg_int" : "The left hand side is always odd and the right hand side is always even.",
    "contra" : "This is a contradiction.",
    "conc" : "Hence \\(\\log_2(3)\\) is irrational."
};
[[/parsons ]]
<p>[[input:ans1]] [[validation:ans1]]</p>
````

Notes:

1. The Parson's block requires a JSON object containins `"key":"string"` pairs. The `string` will be shown to the student.  The student's answer will be returned in terms of the `key` tags.  Numbers can be used as keys, but names keys above will be more specific.
2. The `\` character in the string must be protected!  Notice that `\(...\)` needs to be typed as `\\(...\\)`.
3. The [Parson's block](../Authoring/Parsons.md) has a wide range of options such as `height` and `width` which are documented elsewhere.

## Input: ans1

1. The _Input type_ field should be **String**. 
2. The _Model answer_ field should be initially empty, i.e. `""`
3. Set the option "Student must verify" to "no".

## Potential response tree: prt1

Define the following question variables:

````
stack_include("contribl://prooflib.mac");
ta:proof("assume","defn_rat","defn_rat2","defn_log","defn_log2","alg","alg_int","contra","conc");
````

The optional library `prooflib.mac` contain many useful functions for dealing with student's answers which represent proofs.

Define the feedback variables:

````
sa:proof_parsons_interpret(ans1);
````

The student's answer will be a _JSON string_, but we need to interpret which of the strings have been used and in what order.  The `proof_parsons_interpret` takes a JSON string and  builds a proof representation object.

Then you can set up the potential response tree to be `ATAlgEquiv(sa,ta)` to confirm the student's answer is the same as the teacher's answer.

# Parson's question with block order options

TODO!  To get this following example to work we need to sort out `{# stackjson_stringify(proof_steps) #}` in the Parson's block.

The following Parson's question is an _if and only if_ proof, containing two blocks in order.

The input is unchanged from the above example. For the question variables use

````
stack_include("contribl://prooflib.mac");
dispproof_para([ex]) := block([ex1],apply(sconcat, flatten(append(["<p>"], [simplode(ex, " ")], ["</p>"]))));

ta:proof_iff(proof("assodd","defn_odd","alg_odd","def_M_odd","conc_odd"), proof("contrapos","assnotodd","even","alg_even","def_M_even","conc_even"));

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

tal:proof_alternatives(ta);
tas:setify(map(proof_flatten, tal));
tad:map(lambda([ex], proof_disp_replacesteps(ex, proof_steps)), tal);
````

The complete question text is

````
<p>Let \(n\in\mathbb{N}\).  Show that \(n\) is odd if and only if \(n^2\) is odd. </p>
[[parsons input="ans1"]]
{# stackjson_stringify(proof_steps) #}
[[/parsons ]]
<p>[[input:ans1]] [[validation:ans1]]</p>
````

FOR NOW

````
<p>Let \(n\in\mathbb{N}\).  Show that \(n\) is odd if and only if \(n^2\) is odd. </p>
[[parsons input="ans1"]]
{
    "assodd":     "Assume that \\(n\\) is odd.",
    "defn_odd":   "Then there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n=2m+1\\).",
    "alg_odd":    "\\[ n^2 = (2m+1)^2 = 2(2m^2+2m)+1.\\]",
    "def_M_odd":  "Define \\(M=2m^2+2m\\in\\mathbb{Z}\\) then \\(n^2=2M+1\\).",
    "conc_odd":   "Hence \\(n^2\\) is odd.",

    "contrapos":  "We reformulate \"\\(n^2\\) is odd \\(\\Rightarrow \\) \\(n\\) is odd \" as the contrapositive.",
    "assnotodd":  "Assume that \\(n\\) is not odd.",
    "even":       "Then \\(n\\) is even, and so there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n=2m\\).",
    "alg_even":   "\\[ n^2 = (2m)^2 = 2(2m^2).\\]",
    "def_M_even": "Define \\(M=2m^2\\in\\mathbb{Z}\\) then \\(n^2=2M\\).",
    "conc_even":  "Hence \\(n^2\\) is even."
};
[[/parsons ]]
<p>[[input:ans1]] [[validation:ans1]]</p>
````


Notice the function `stackjson_stringify` turns the variable `proof_steps` into a JSON object.

Notice in this example the teacher's proof is nested.  This can be seen if we use numerical keys, not string keys and define 

````
ta:proof_iff(proof(1,2,3,4,5),proof(6,7,8,9,10,11));
````

The two blocks can be in either order.  Prooflib provides a function to automatically create both options.  Notice the command `tal:proof_alternatives(ta);` in the question variables.  The variable `tal` will be a list of both options for the if and only if proof.  Note that `proof_alternatives` will recurse over all sub-proofs.  Types of supported proof structure are documented within the prooflib file.  Then we have to "flatten" each of these proofs to a set of list-based proofs: `tas:setify(map(proof_flatten, tal));`

As before, define the feedback variables to interpret the JSON as a proof:

````
sa:proof_parsons_interpret(ans1);
````

Set up the potential response tree to check if the student's proof `sa` is in the set of possible teacher's proofs.
The simplest way is `ATAlgEquiv(elementp(sa,tas), true)` to confirm the student's answer is in the set of answers equivalent to teacher's answer.

To see this in action, try the following in the general feedback to display both proof options.

````
This is the proof, written with some structure
{@ev(tad[1], map(lambda([ex], ex=dispproof), proof_types))@}
Notice this proof has two sub-proofs, which can occur in any order.  Therefore we have two correct versions of this proof.
<table><tr>
<td><div class="proof">{@ev(tad[1], map(lambda([ex], ex=dispproof_para), proof_types))@}</div></td>
<td><div class="proof">{@ev(tad[2], map(lambda([ex], ex=dispproof_para), proof_types))@}</div></td>
</tr></table>
Can you see the differences between these proofs?
````