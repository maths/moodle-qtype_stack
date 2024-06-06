# Authoring drag and drop matching problems in STACK

The drag-and-drop functionality of the [Parson's](Parsons.md) block in STACK can also be used to write matching and other grid-based problems, which can be answered by drag and drop. 

This page provides a quick-start guide to authoring matching problems with the `parsons` block. 
As of STACK v4.6.0, the `parsons` block has three main configurations (each of which support further customisation) which can be achieved by setting appropriate block header parameters `columns` and `rows` as appropriate:
1. **Proof** (Example usage: `[[parsons]] ... [[/parsons]]`) : This was introduced in STACK v4.5.0, and a full guide can be found [here](Parsons.md).
2. **Grouping** (Example usage: `[[parsons columns="3"]] ... [[/parsons]]`) : 
This will set up a number of columns, each of which behave similarly to the single left-hand column of the **Proof** configuration, where the student may drag and drop items starting at the top of each column. 
This is useful when we are only interesting in grouping items together, and specific row positions do not matter, or when each column may have variable length.
3. **Grid** (Example usage: `[[parsons columns="3" rows="2"]] ... [[/parsons]]`) : 
This will set up a grid of shape `(columns, rows)`, where the student may drag and drop items to any position in the grid. 

Note that many **Grid** style questions can also be written using the **Grouping** setup. 
The main difference between them is that **Grid** allows the student to drag any item to any position in the grid, regardless
of whether the item above it has been filled; **Grouping** on the other hand only allows students to drag items to the 
end of the list within a column.

## Troubleshooting

If your matching problem is not displaying properly, in particular if the all the items are displayed in a single yellow block, then
double-check that you have spelled the keys of the JSON inside the Parsons block correctly as described below. They should be a subset of 
```
{"steps", "options", "headers", "available_header", "index"}
```
and a superset of 
```
{"steps"}
```
For technical reasons this is one error that we are unable to validate currently.

## Switching orientation

Parsons blocks will display columns vertically by default. 
The student has the option to flip the orientation so that the columns become horizontal rows, and back again, through a button on the question page. 
If you wish the columns to be horizontal rows as the question display default, then simply add `transpose="true"` to the block header (e.g., `[[parsons columns="3" transpose="true"]] ... [[/parsons]]`, will load a question with 3 horizontal rows).

## Providing a model answer

_The format of the model answer is fixed and independent of the orientation discussed in the previous section_. 
It should be defined in _Question variables_ as a two-dimensional array that is always column-grouped. 
For example if a model answer looks like 
```
 a | b | c 
---|---|---
 d |   | e 
---|---|---
 f |   |   
```
Then this should be defined as
```
ans: [["A", "D", "F"], ["B"], ["C", "E"]];
```
where, e.g., `"A"` is the key identifier for string `"a"`.

## Cloning items

The `clone` header parameter has been available in `parsons` since v4.5.0 of STACK. 
This can be set to `"true"` to allow items to be used more than once (e.g., `[[parsons columns="3" clone="true"]]...[[/parsons]]`).

## Headers and index

Column headers default to `0`, `1`, ..., `columns` when using **Grouping** or **Grid** configurations of the Parson's block, so we recommend to always set custom headers for these cases. 
See the examples below for how to do this.

There is also the option of setting the index, which is possible in both **Grouping** and **Grid** configurations, but is more likely to be useful for the latter. 
This will appear as a specially styled left-most column and fixes labels for the rows. 
The index does not count as a column, so you should  _decrease columns by one in the header parameters_. 
The item that appears top-left in both the index and header should be included in the _index only_. 
See the examples below for more details.

## Item style

In **Grouping** and **Grid** configurations, the height and width of individual items will by default auto-resize so that all their heights and widths are set to contain the largest item content. 
This will also apply to headers and index items as well.

This can be overriden via the `item-height` and `item-width` header parameters. Parameter values should be a string containing a number, and this will set the pixels of the height/width. 
The default is `50px` (`item-height="50"`) for height, and the width is automatically deduced from the page layout and number of columns for the vertical orientation, or it is `100px` (`item-width="100"`) for the horizontal configuration. 
This may be needed if autosizing does not quite work as expected.

## The matching library

The `matchlib.mac` Maxima library contains a small number of functions that are required for basic functionality of assessing matching problems in STACK. 
Essentially they translate the author answers to and back from JSON format expected by the drag-and-drop engine. 
Be sure to include this and make use of it as detailed in the examples below.

We include some basic helper functions that can allow the author to specify whether they care or not about the order within and between rows or columns as follows.
- `ans: [["A", "B"], ["C", "D"], ["E", "F"]]`
- `match_column_set` : I don't care about the order within a column -> 
```
match_column_set(ans) = [{"A", "B"}, {"C", "D"}, {"E", "F"}]
```
- `match_row_set` : I don't care about the order within a row (unlikely to be required but included for completeness) -> 
```
match_row_set(ans) = [{"A", "C", "E"}, {"B", "D", "F"}]
```
- `match_set_column` : I don't care about the order of the columns (unlikely to be required but included for completeness) -> 
```
match_set_column(ans) = {["A", "B"], ["C", "D"], ["E", "F"]}
```
- `match_set_row` : I don't care about the order of the rows -> 
```
match_set_rows(ans) = {["A", "C", "E"], ["B", "D", "F"]}
```
- `match_transpose` :  I would like to turn my answer into a row-grouped array -> 
```
match_transpose(ans) = [["A", "C", "E"], ["B", "D", "F"]]
```

## Example 1 : Grouping example

In our first example, the student is asked to place functions, given as equations, into columns with the categories "Differentiable", "Continuous, not differentiable", "Discontinuous".

### Question variables 

As a minimum it is recommended to include:
- Load the matching library.
- Define all items in the available list as a two-dimensional array, where each item is an array of the form `["<ID>", "<actual item contents>"]`. 
You will use the `"<ID>"` string to write solutions and assess student inputs; the second item is what is displayed to the student.
- Randomly permute the available items.
- The headers that will appear on top of the answer columns.
- The correct answer as a two-dimensional array. 
This should be column grouped.

For our example, the _Question variables_ field looks as follows.
```
stack_include("contribl://matchlib.mac");

steps : [
    ["sq", "\\(f(x) = x^2\\)"],
    ["sin", "\\(f(x) = \\sin(x)\\)"],
    ["abs", "\\(f(x) = |x|\\)"],
    ["sqrt", "\\(f(x) = \\sqrt{|x|}\\)"],
    ["rec", "\\(f(x) = \\left\{\\begin{array}{ll}1/x &, x\\neq 0 \\\\ 0&, x=0\\end{array}\\right.\\)"],
    ["sgn", "\\(f(x) = \\text{sgn}(x)\\)"]
];

steps: random_permutation(steps);

headers: [
    "Differentiable", 
    "Continuous, not differentiable", 
    "Discontinuous"
];

ans: [
    ["sq", "sin"], 
    ["abs", "sqrt"], 
    ["rec", "sgn"]
];
```

### Question text

Here we should:
- Write the question text itself.
- Open the `parsons` block with `input` and `columns` header parameters.
- Transfer the variables from _Question variables_ into a JSON inside the `parsons` block as appropriate.
- Close the `parsons` block.
- Set `style="display:none"` in the input div to hide the messy state from the student.

```
<p>Recall that a function may be differentiable, continuous but 
not differentiable, or discontinuous. The following expressions define functions \(f:\mathbb{R}\rightarrow\mathbb{R}\).
Drag the functions to their appropriate category. </p>
[[parsons input="ans1" columns="3"]]
{
    "steps" : {#stackjson_stringify(steps)#},
    "headers" : {#headers#}
}
[[/parsons]]
<p style="display:none">[[input:ans1]] [[validation:ans1]]</p>
```

### Question note

A question note is required due to the random permutation of `steps`. We use:
```
{@map(first, steps)@}
```

### Input: ans1

1. The _Input type_ field should be **String**.
2. The _Model answer_ field should construct a JSON object from the teacher's answer `ans` using `match_correct(ans, steps)`.
3. Set the option _Student must verify_ to "no".
4. Set the option _Show the validation_ to "no".
5. Add `hideanswer` to _Extra options_.

Steps 3, 4 and 5 are strongly recommended, otherwise the student will see unhelpful code representing the underlying state of their answer.

### Potential response tree: prt1

Define the feedback variable
```
sans: match_interpret(ans1);
```
This provides the student response as a two-dimensional array of the same format as `ans`. 
At this point the author may choose to assess by comparing `sans` and `ans` as they see fit. 
In this case, the order _within_ a column really doesn't matter, but the order of the columns does of course. 
So we may convert the columns of `sans` and `ans` to sets in feedback variables using `match_column_set` from `matchlib.mac`.
```
sans: match_column_set(sans);
ans: match_column_set(ans);
```
We can then do a regular algebraic equivalence test between `sans` and `ans`. You should turn the node to `Quiet: Yes`, otherwise the student will see unhelpful code if they the answer wrong.

## Example 2 : Grid example

Here, the student is asked to drag functions and their derivatives to relevant columns and rows. 
This particular example could work as a grouping example in the vein of Example 1 above, however the key difference 
here is that the student can drag an item to any position in the grid, whereas in grouping items can only be added 
to the end of a growing column list.

Much of this example is very similar to Example 1 above, with the following key differences:
- The `parsons` block should include a specified `rows` parameter.
- The `match_correct` function should use `true` as a third parameter inside _Model answer_.
- The `match_interpret` function should use `true` as a third parameter inside the PRT.
- We also define our PRT answer test differently, since we care only about the order within a row being preserved.
However this difference is not _required_ and is due only to the nature of the question (i.e., what we want to assess from this question is 
different from the one in Example 1), rather than from any system requirements.

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

ans: [
  ["f", "g"], 
  ["dfdx", "dgdx"], 
  ["df2d2x", "dg2d2x"]
];
```

### Question text

Here we should:
- Write the question text itself.
- Open the `parsons` block with `input`, `columns` and `rows` header parameters.
- Transfer the variables from _Question variables_ into a JSON inside the `parsons` block as appropriate.
- Close the `parsons` block.
- Set `style="display:none"` in the input div to hide the messy state from the student.

```
<p>Drag the items to match up the functions with their derivatives. </p>
[[parsons input="ans1" columns="3" rows="2"]]
{
    "steps" : {#stackjson_stringify(steps)#},
    "headers" : {#headers#}
}
[[/parsons]]
<p style="display:none">[[input:ans1]] [[validation:ans1]]</p>
```

### Question note

A question note is required due to the random permutation of `steps`. We use:
```
{@map(first, steps)@}
```

### Input: ans1

1. The _Input type_ field should be **String**.
2. The _Model answer_ field should construct a JSON object from the teacher's answer `ta` using `match_correct(ans, steps, true)`.
3. Set the option _Student must verify_ to "no".
4. Set the option _Show the validation_ to "no".
5. Add `hideanswer` to _Extra options_.

Steps 3, 4 and 5 are strongly recommended, otherwise the student will see unhelpful code representing the underlying state 
of their answer.

### Potential response tree: prt1

Define the feedback variable
```
sans: match_interpret(ans1, true);
```
This provides the student response as a two-dimensional array of the same format as `ans`. 
At this point the author may choose to assess by comparing `sans` and `ans` as they see fit. In this case, the _order of the rows themselves_ really doesn't matter, but the order of the rows does of course. So we may convert the list of rows of `sans` and `ans` to a set in feedback variables using `match_set_row` from `matchlib.mac`.
```
sans: match_set_row(sans);
ans: match_set_row(ans);
```
We can then do a regular algebraic equivalence test between `sans` and `ans`. 
You should turn the node to `Quiet: Yes`, otherwise the student will see unhelpful code if they the answer wrong.

## Example 3 : Grid example with an index

One can add a left-hand index to the grid in Example 2 by defining an `index` array in _Question variables_ and passing this through in the JSON inside the `parsons` block. 
This will fix the row order and simplify assessment.

Important points:
- An item that appears in both the header and the index is **required**. 
This item should appear in the index and not in the header.
- Reduce the value of the `columns` parameter in the `parsons` block by one: this corresponds only to the number of answer columns.
- Pass the index as the value of key `"index"` inside the JSON within the `parsons` block.

### Question variables 

The question variables for Example 2 with an index is as follows.
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

ans: [
  ["dfdx", "dgdx"], 
  ["df2d2x", "dg2d2x"]
];
```

### Question text

```
<p>Drag the items to match up the functions with their derivatives. </p>
[[parsons input="ans1" columns="2" rows="2"]]
{
    "steps" : {#stackjson_stringify(steps)#},
    "headers" : {#headers#},
    "index" : {#index#}
}
[[/parsons]]
<p style="display:none">[[input:ans1]] [[validation:ans1]]</p>
```

### Question note

A question note is required due to the random permutation of `steps`. We use:
```
{@map(first, steps)@}
```

### Input

This is exactly the same as Example 2. 

1. The _Input type_ field should be **String**.
2. The _Model answer_ field should construct a JSON object from the teacher's answer `ta` using `match_correct(ans, steps, true)`.
3. Set the option _Student must verify_ to "no".
4. Set the option _Show the validation_ to "no".
5. Add `hideanswer` to _Extra options_.

### PRT

As in Example 2, we first extract the two-dimensional array of used items from the students input.
```
sans: match_interpret(ans1, true);
```
At this point the author may choose to assess by comparing `sans` and `ans` as they see fit. 
Since we have fixed the order of both dimensions, there is only one correct answer which is given by `ans`. 
Hence we have a basic PRT which tests only algebraic equivalence between `sans` and `ans`. 
As always, turn the node to `Quiet: Yes`, otherwise the student will see unhelpful code if they the answer wrong.

## Example 4 : Using images

Through the use of STACK's `plot` function, which wraps Maxima's `plot2d`, static images can also be included within items. 
Apart from modifying the content of the steps of Example 2, the key difference here is that the width and height of the items must also be specified in the block parameter, to make sure that plots fit inside.
This can be done by specifying the `item-width` and `item-height` parameters within the block header of `parsons`.
Because of this, it is recommended to always specify the `[size, x, y]` option within `plot`, and add some padding to `x` and `y` to define the values of `item-width` and `item-height`.
Example 2 with plots rather than equations is given below.

### Question variables

```
stack_include("contribl://matchlib.mac");

steps: [
  ["f", plot(x^2,[x,-1,1], [size, 200, 200])],
  ["g", plot(x^3,[x,-1,1], [size, 200, 200])],
  ["dfdx", plot(2*x,[x,-1,1], [size, 200, 200])],
  ["dgdx", plot(3*x^2,[x,-1,1], [size, 200, 200])],
  ["df2d2x", plot(2,[x,-1,1], [size, 200, 200])],
  ["dg2d2x", plot(6*x,[x,-1,1], [size, 200, 200])]
];

steps: random_permutation(steps);

headers: ["Function", "\\(d/dx\\)", "\\(d^2/d^2x\\)"];

ans: [
  ["f", "g"], 
  ["dfdx", "dgdx"], 
  ["df2d2x", "dg2d2x"]
];
```

### Question text

```
<p>Drag the items to match up the functions with their derivatives. </p>
[[parsons input="ans1" columns="3" rows="2" item-height="250" item-width="250"]]
{
    "steps" : {#stackjson_stringify(steps)#},
    "headers" : {#headers#},
}
[[/parsons]]
<p style="display:none">[[input:ans1]] [[validation:ans1]]</p>
```

### Question note, Inputs and PRT

These are exactly the same as Example 2.



