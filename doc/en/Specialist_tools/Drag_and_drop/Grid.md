# Grid drag and drop questions

This will set up a grid of shape `(columns, rows)`, where the student may drag and drop items to any position in the grid. 

Example usage: `[[parsons columns="3" rows="2"]] ... [[/parsons]]`

## Troubleshooting

Please see the [troubleshooting](Troubleshooting.md) page for known issues and how to resolve them.

## Example: Grid example

Here, the student is asked to drag functions and their derivatives to relevant columns and rows. 
This particular example could work as a grouping example in the vein of the [grouping example](Grouping.md), however the key difference 
here is that the student can drag an item to any position in the grid, whereas in grouping items can only be added 
to the end of a growing column list.

Much of this example is very similar to the [grouping example](Grouping.md), with the following key differences:
- The `parsons` block should include a specified `rows` parameter.
- The `match_answer` function should use `true` as a third parameter inside _Model answer_.
- The `match_decode` function should use `true` as a third parameter inside the PRT.
- We also define our PRT answer test differently, since we care only about the order within a row being preserved.
However this difference is not _required_ and is due only to the nature of the question (i.e., what we want to assess from this question is 
different from the one in the [grouping example](Grouping.md)), rather than from any system requirements.

### Question variables 

As a minimum it is recommended to include:
- Load the matching library.
- Define all items in the available list as a two-dimensional array, where each item is an array of the form `["<ID>", "<actual item contents>"]`. 
You will use the `"<ID>"` string to write solutions and assess student inputs; the second item is what is displayed to the student.
- Randomly permute the available items.
- The headers that will appear on top of the answer columns.
- The correct answer as a two-dimensional array. This should always be column grouped.

For our example, the _Question variables_ field looks as follows.

```
stack_include("contribl://matchlib.mac");

steps : [
  ["f", "\\(y = x^2\\)"],
  ["g", "\\(y = x^3\\)"],
  ["dfdx", "\\(y' = 2x\\)"],
  ["dgdx", "\\(y' = 3x^2\\)"],
  ["df2d2x", "\\(y'' = 2\\)"],
  ["dg2d2x", "\\(y'' = 6x\\)"]
];

steps: random_permutation(steps);

headers: [
  "Function", 
  "\\(d/dx\\)", 
  "\\(d^2/d^2x\\)"
];

ta: [
  ["f", "g"], 
  ["dfdx", "dgdx"], 
  ["df2d2x", "dg2d2x"]
];
```

### Question text

Here we should:
- Write the question text itself.
- Open the `parsons` block with `input`, `columns` and `rows` header parameters.
- Transfer the variables from _Question variables_ into a JSON inside the `parsons` block using `match_encode`.
- Close the `parsons` block.

```
<p>Drag the items to match up the functions with their derivatives. </p>
[[parsons input="ans1" columns="3" rows="2"]]
{
    "steps" : {#match_encode(steps)#},
    "headers" : {#headers#}
}
[[/parsons]]
<p>[[input:ans1]] [[validation:ans1]]</p>
```

### Question note

A question note is required due to the random permutation of `steps`. We use:
```
{@map(first, steps)@}
```

### Input: ans1

1. The _Input type_ field should be **Parsons**.
2. The _Model answer_ field should be a list `[ta, steps, 3, 2]` containing the teacher answer, all possible steps, the number of columns, 
and the number of rows.
3. Set the option _Student must verify_ to "no".
4. Set the option _Show the validation_ to "no".

Steps 3 and 4 are strongly recommended, otherwise the student will see unhelpful code representing the underlying state 
of their answer.

### Potential response tree: prt1

Define the feedback variable
```
sans: match_decode(ans1, true);
```
This provides the student response as a two-dimensional array of the same format as `ans`. 
At this point the author may choose to assess by comparing `sans` and `ans` as they see fit. In this case, the _order of the rows themselves_ really doesn't matter, but the order of the rows does of course. So we may convert the list of rows of `sans` and `ans` to a set in feedback variables using `match_set_row` from `matchlib.mac`.
```
sans: match_set_row(sans);
tans: match_set_row(ta);
```
We can then do a regular algebraic equivalence test between `sans` and `ans`. 
You should turn the node to `Quiet: Yes`, otherwise the student will see unhelpful code if they the answer wrong.

## Example: Grid example with an index

One can add a left-hand index to the grid in first grid example by defining an `index` array in _Question variables_ and passing this through in the JSON inside the `parsons` block. 
This will fix the row order and simplify assessment.

Important points:
- An item that appears in both the header and the index is **required**. 
This item should appear in the index and not in the header.
- Reduce the value of the `columns` parameter in the `parsons` block by one: this corresponds only to the number of answer columns.
- Pass the index as the value of key `"index"` inside the JSON within the `parsons` block.

### Question variables 

The question variables with an index is as follows.
```
stack_include("contribl://matchlib.mac");

steps : [
  ["dfdx", "\\(y' = 2x\\)"],
  ["dgdx", "\\(y' = 3x^2\\)"],
  ["df2d2x", "\\(y'' = 2\\)"],
  ["dg2d2x", "\\(y'' = 6x\\)"]
];

steps: random_permutation(steps);

headers: [
  "\\(d/dx\\)", 
  "\\(d^2/d^2x\\)"
];

index: [
  "Function",
  "\\(y = x^2\\)",
  "\\(y = x^3\\)"
]

ta: [
  ["dfdx", "dgdx"], 
  ["df2d2x", "dg2d2x"]
];
```

### Question text

```
<p>Drag the items to match up the functions with their derivatives. </p>
[[parsons input="ans1" columns="2" rows="2"]]
{
    "steps" : {#match_encode(steps)#},
    "headers" : {#headers#},
    "index" : {#index#}
}
[[/parsons]]
<p>[[input:ans1]] [[validation:ans1]]</p>
```

### Question note

A question note is required due to the random permutation of `steps`. We use:
```
{@map(first, steps)@}
```

### Input

This is exactly the same as the previous. 

1. The _Input type_ field should be **Parsons**.
2. The _Model answer_ field should be a list `[ta, steps, 2, 2]` containing the teacher answer, all possible steps, the number of columns, 
and the number of rows.
3. Set the option _Student must verify_ to "no".
4. Set the option _Show the validation_ to "no".

### PRT

As in with the previous , we first extract the two-dimensional array of used items from the students input.
```
sans: match_decode(ans1, true);
```
At this point the author may choose to assess by comparing `sans` and `ta` as they see fit. 
Since we have fixed the order of both dimensions, there is only one correct answer which is given by `ta`. 
Hence we have a basic PRT which tests only algebraic equivalence between `sans` and `ta`. 
As always, turn the node to `Quiet: Yes`, otherwise the student will see unhelpful code if they the answer wrong.
