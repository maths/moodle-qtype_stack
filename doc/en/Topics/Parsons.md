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

## Question variables

In the current basic implementation _Question variables_ should be left blank, as they are not used. Shortly, one will be able to define proof steps using Maxima arrays within the _Question variables_ field and include these in the Parsons block as defined in the Question text section.

## Question text

Here is where one should write the text of the proof and also where the Parson's block should be defined, containing the structure of the Parson's problem. Within the header of the Parson's block, ensure that `input="inputvar"` is included, where `inputvar` is the identifier of the input block, for example `input="ans1"` as below. A minimal working example for the proof that _n is odd if and only if n^2 is odd_ is achieved by placing the following in the _Question text_ field:
````
<p> Let \(n\) be an integer. Show that \(n\) is odd if and only if \(n^2\) is odd. </p>
[[ parsons input="ans1" ]]
{ 
  "1":"Assume that \\(n\\) is odd.",
  "2":"Then there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n=2m+1\\).",
  "3":"\\[ n^2 = (2m+1)^2 = 2(2m^2+2m)+1.\\]",
  "4":"Define \\(M=2m^2+2m\\in\\mathbb{Z}\\) then \\(n^2=2M+1\\).",
  "5": "Assume that \\(n\\) is even.",
  "6": "Then there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n = 2m\\)."
};
[[/parsons ]]
<p>[[input:ans1]] [[validation:ans1]]</p>
````

Additional display options including `height` and `width` may also be passed to the header, as in 
````
[[ parsons input="ans1" height="360px" width="100%"]]
{ 
  "1":"Assume that \\(n\\) is odd.",
  "2":"Then there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n=2m+1\\).",
  "3":"\\[ n^2 = (2m+1)^2 = 2(2m^2+2m)+1.\\]",
  "4":"Define \\(M=2m^2+2m\\in\\mathbb{Z}\\) then \\(n^2=2M+1\\).",
  "5": "Assume that \\(n\\) is even.",
  "6": "Then there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n = 2m\\)."
};
[[/parsons]]
````

The Parson's drag and drop lists are created using the Sortable JavaScript library. These lists come with their own set of [options](https://github.com/SortableJS/Sortable#options), currently these are set at the default option for `"animation"` which controls the animation speed.
````
{
    "animation": 50,
}
````
Most Sortable options can be toggled by passing a JSON that is structured as follows in the Parson's block:
````
[[ parsons input="ans1" height="360px" width="100%"]]
{
    "steps": { 
        "1":"Assume that \\(n\\) is odd.",
        "2":"Then there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n=2m+1\\).",
        "3":"\\[ n^2 = (2m+1)^2 = 2(2m^2+2m)+1.\\]",
        "4":"Define \\(M=2m^2+2m\\in\\mathbb{Z}\\) then \\(n^2=2M+1\\).",
        "5": "Assume that \\(n\\) is even.",
        "6": "Then there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n = 2m\\)."
    },
    "options": {
        "animation" : 150,
    }
}
[[/parsons]]
````
However, note that some options cannot be toggled as they are required for the proper functioning of the Sortable lists. Hence, any user-defined options for `ghostClass` and `group` are overwritten.

## Input: ans1

The _Input type_ field should be **String**. The _Model answer_ field should be the Maxima representation of the proof solution. 
For the above example this should be `proof(1, 2, 3, 4)`.

## Potential response tree: prt1

TODO

