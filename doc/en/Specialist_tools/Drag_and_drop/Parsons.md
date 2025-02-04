# Authoring Parson's problems (drag and drop) in STACK

Parson’s problems require students to assemble pre-written blocks (typically containing text) into a correct order by dragging blocks into a tree structure.  E.g. to assemble a correct mathematical proof.

This page provides a quick-start to authoring whole questions.  There are a number of parts to authoring a Parson's problem:

1. [Defining strings](../../Topics/Proof/Proof_CAS_library.md) and creating drag and drop area using the [Parson's block](Question_block.md).
2. Linking the Parson's block to a STACK input (string input).
3. [Assessing the student's answer](../../Topics/Proof/Proof_assessment.md) and providing meaningful feedback automatically.

While drag and drop is certainly not new in automatic assessment, these problems have become popular in computer science, e.g. Parsons (2006).

Parson’s problems enable students to focus on the structure and meaning, by providing all the right words but just not initially in the right order. Learning mathematical proof is well-known to be very difficult for most students. The scaffolding provided by Parson's problems has been found to be very useful to students in discrete mathematics. This may well be due to the _expert reversal effect_. The expert reversal effect is a well-established phenomenon that what is useful for a beginner is quite different, perhaps the opposite, of what is useful for an expert. Even when independent proof writing is the final goal of instruction, it is very likely that Parson's problems have an important place in formative learning towards that goal.

Traditionally Parson’s problems require the student to create an ordered list. For mathematical proofs, we believe it is more natural to ask students to create a _tree_. Parson's problems in mathematics, especially proofs, do not always have a unique answer which is considered correct.
Hence, there are challenges in deciding what a correct answer is in particular cases, and how to develop authoring tools to robustly create problems with reliable marking algorithms.
For example, in a proof of
\[ A\text{ if and only if } B\]
We might expect/require two conscious and separate blocks

1.  Assume \(A\), \(\cdots\), hence \(B\).
2.  Assume \(B\), \(\cdots\), hence \(A\).

The order in which these two sub-proofs are presented is (normally) irrelevant.  That is the _if and only if_ proof construct allows its two sub-proofs to commute.  This is precisely the same sense in which \(A=B\) and \(B=A\) are equivalent. There are _blocks_ within the proof which can change order. Furthermore, since proofs are often nested blocks these sub-proofs may themselves have acceptable options for correctness.
Proofs often contain local variables.  Use of explicit block-structures clarify the local scope of variables, and the local scope of assumptions.

STACK provides ["proof construction functions"](../../Topics/Proof/Proof_CAS_library.md) with arguments. For example, an if and only if proof would be represented as `proof_iff(A,B)`.  Here `A` and `B` are either sub-proofs or strings to be shown to the student.

If the student has an opportunity to indicate more structure, then the assessment logic becomes considerably simpler, more reliable and transparent. Actually, we think there is significant educational merit in making this structure explicit and consciously separating proof-structure from the finer grained details. It is true that professional mathematicians often omit indicating explicit structure, they abbreviate and omit whole blocks ("the other cases are similar") but these are all further examples of expert reversal.

Notes

* Lists are a special case of a tree with one root (the list creation function) and an arbitrary number of nodes in order.  Hence our design explicitly includes traditional Parson's problems as a special case.
* Teachers who do not want to scaffold explicit block structures (e.g. signal types of proof blocks) can choose to restrict students to (i) flat lists, or (ii) lists of lists.

## Troubleshooting

Please see the [troubleshooting](Troubleshooting.md) page for known issues and how to resolve them.

## Examples 

Please follow the examples below to understand how the components work.  Then we provide two tools.

1. Advice on workflow for [authoring Parson's problems](../../Topics/Proof/Proof_Parsons_workflow.md).
2. Template questions in the [STACK question library](../../STACK_question_admin/Sample_questions.md#STACK_library), under `Topics\Parsons-proof-template.xml` which you can load and use immediately.

## Example 1: a minimal Parson's question

The following is a minimal Parson's question where there student is expected to create a list in one and only one order.
It shows the proof that _\(\log_2(3)\) is irrational_.

### Question variables

Define the following question variables:

````
stack_include("contribl://prooflib.mac");

proof_steps: [
    ["assume", "Assume, for a contradiction, that \\(\\log_2(3)\\) is rational."],
    ["defn_rat", "Then \\(\\log_2(3) = \\frac{p}{q}>0\\) where "],
    ["defn_rat2", "\\(p\\) and \\(q\\neq 0\\) are positive integers."],
    ["defn_log", "Using the definition of logarithm:"],
    ["defn_log2", "\\[ 3 = 2^{\\frac{p}{q}}\\]"],
    ["alg", "\\[ 3^q = 2^p\\]"],
    ["alg_int", "The left hand side is always odd and the right hand side is always even."],
    ["contra", "This is a contradiction."],
    ["conc", "Hence \\(\\log_2(3)\\) is irrational."]
];

proof_steps: random_permutation(proof_steps);

ta: proof("assume", "defn_rat", "defn_rat2", "defn_log", "defn_log2", "alg", "alg_int", "contra", "conc");
````

The optional library `prooflib.mac` contain many useful functions for dealing with student's answers which represent proofs.

The variable `proof_steps` holds all the keys that will be displayed on the string. 
Each step is indexed by a key identifier, which are used to refer to the steps throughout the question (for example, when constructing the model answer).

We can randomise the order the strings will be shown on the mpage through `random_permutation`.

The variable `ta` holds the teacher's answer which is a proof construction function `proof`.  The arguments to this function are string keys, e.g. `"alg"` which refer to lines in the proof.  The teacher expects these lines in this order.

### Question text

The example question text below contains a Parson's block. Within the header of the Parson's block, ensure that `input="ans1"` is included, where `ans1` is the identifier of the input. Place the following in the _Question text_ field:

````
<p>Show that \(\log_2(3)\) is irrational. </p>
[[parsons input="ans1"]]
{# parsons_encode(proof_steps) #}
[[/parsons ]]
<p>[[input:ans1]] [[validation:ans1]]</p>
````

Notes:

1. The function `parsons_encode` turns the variable `proof_steps` into a JSON object with hashed keys, as expected by the `parsons` block.
2. The [Parson's block](Question_block.md) has a wide range of options such as `height` and `width` which are documented elsewhere.

### Question note

Due to the randomisation of the proof steps, we need to add a question note. One that simply gives the order of the keys is as follows.

```
{@ map(first, proof_steps) @}
```

### Input: ans1

1. The _Input type_ field should be **Parsons**.
2. The _Model answer_ field should be a list `[ta, proof_steps]`.
3. Set the option "Student must verify" to "No".
4. Set the option "Show the validation" to "No".

### Potential response tree

Define the feedback variables:

````
sa: parsons_decode(ans1);
````

The student's answer will be a _JSON string_, but we need to interpret which of the strings have been used and in what order.  The `parsons_decode` function takes a JSON string and  builds a proof representation object.

Then you can set up the potential response tree to be `ATAlgEquiv(sa,ta)` to confirm the student's answer is the same as the teacher's answer.

### Adding a test case

On the STACK question dashboard, add in a test case.  Test case construction requires use of a special function `parsons_answer` to mock up the input from a Parsons block (with hashing) and send to the testing code what a correct response from the user would look like.  The test case should look like

````
parsons_answer(ta, proof_steps)
````

In general, you need to provide a list of tags in place of `ta`, and the `proof_steps` array.

## Example question 2: a proof with interchangeable block order

The following Parson's question is an _if and only if_ proof, containing two blocks in order.

### Question variables

````
stack_include("contribl://prooflib.mac");

ta: proof_iff(proof("assodd","defn_odd","alg_odd","def_M_odd","conc_odd"), proof("contrapos","assnotodd","even","alg_even","def_M_even","conc_even"));

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

/* Permute the steps randomly. */ 
proof_steps: random_permutation(proof_steps);

/* Generate the alternative proofs. */
tal: proof_alternatives(ta);

/* Create a set of flattened proofs. */
tas: setify(map(proof_flatten, tal));
````

### Question text 

The complete question text is

````
<p>Let \(n\in\mathbb{N}\).  Show that \(n\) is odd if and only if \(n^2\) is odd. </p>
[[parsons input="ans1"]]
{# parsons_encode(proof_steps) #}
[[/parsons ]]
<p>[[input:ans1]] [[validation:ans1]]</p>
````

Notice in this example the teacher's proof is nested. This can be seen if we use numerical keys, not string keys and define

````
ta:proof_iff(proof(1,2,3,4,5),proof(6,7,8,9,10,11));
````

The two blocks can be in either order. 
Prooflib provides a function to automatically create both options. 
Notice the command `tal:proof_alternatives(ta);` in the question variables. 
The variable `tal` will be a list of both options for the if and only if proof. 
Note that `proof_alternatives` will recurse over all sub-proofs. 
Types of supported proof structure are documented within the prooflib file. 
Then we have to "flatten" each of these proofs to a set of list-based proofs: `tas:setify(map(proof_flatten, tal));`

### Question note

Due to the randomisation of the proof steps, we need to add a question note. One that simply gives the order of the keys is as follows.

```
{@ map(first, proof_steps) @}
```

### Input

The "Input" should be set exactly as in the previous example.

1. The _Input type_ field should be **Parsons**.
2. The _Model answer_ field should be a list `[ta, proof_steps]`.
3. Set the option "Student must verify" to "No".
4. Set the option "Show the validation" to "No".

### Potential reponse tree

As before, define the feedback variables to interpret the JSON as a proof:

````
sa:parsons_decode(ans1);
````

Set up the potential response tree to check if the student's proof `sa` is in the set of possible teacher's proofs.
The simplest way is `ATAlgEquiv(elementp(sa,tas), true)` to confirm the student's answer is in the set of answers equivalent to teacher's answer.

To see this in action, try the following in the general feedback to display both proof options.

````
This is the proof, written with some structure
{@proof_display(tal[2], proof_steps)@}
Notice this proof has two sub-proofs, which can occur in any order.  Therefore we have two correct versions of this proof.
<table><tr>
<td><div class="proof">{@proof_display_para(tal[1], proof_steps)@}</div></td>
<td><div class="proof">{@proof_display_para(tal[2], proof_steps)@}</div></td>
</tr></table>
Can you see the differences between these proofs?
````

We have much more sophisticated [general assessment tools](../../Topics/Proof/Proof_assessment.md) for establishing the edit distance between the student's and teacher's proof and providing feedback on how to correct a partially correct proof.  These are documented elsewhere.

### Adding a test case

On the STACK question dashboard, add in a test case.  Test case construction requires use of a special function `parsons_answer` to mock up the input from a Parsons block (with hashing) and send to the testing code what a correct response from the user would look like.  The test case should look like

````
parsons_answer(ta, proof_steps)
````

In general, you need to provide a list of tags in place of `ta`, and the `proof_steps` array.

## Legacy versions

Old versions of the parsons block (before 2024072500) used `stackjson_stringify` in place of `parsons_encode`, `proof_parsons_key_json` in place of 
`parsons_answer`, and `proof_parsons_interpret` in place of `parsons_decode`, and used the `String` input type. Legacy versions of questions are still 
supported and should function as previously, so long as the `String` input type is used. However it is strongly recommended to update questions to use the new functions.
These will hash they keys of the `proof_steps` variable so that they are hidden even when the web page is inspected. This 
also fixes a randomisation bug that occurred when numerical keys are used (see Issue [#1237](https://github.com/maths/moodle-qtype_stack/issues/1237)).