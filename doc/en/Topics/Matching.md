# Authoring drag and drop matching problems in STACK

The drag-and-drop functionality of the [Parson's](Parsons.md) block in STACK can also be used to write matching and other grid-based problems, which can be answered by drag and drop. 

This page provides a quick-start guide to authoring matching problems with the `parsons` block. As of STACK v4.6.0, the `parsons` block has three main configurations (each of which support further customisation) which can be achieved by setting appropriate block header parameters `columns` and `rows` as appropriate:
1. **Proof** (Example usage: `[[parsons]] ... [[/parsons]]`) : This was introduced in STACK v4.5.0, and a full guide can be found [here](Parsons.md).
2. **Grouping** (Example usage: `[[parsons columns="3"]] ... [[/parsons]]`) : This will set up a number of columns, each of which behave similarly to the single left-hand column of the **Proof** configuration, where the student may drag and drop items starting at the top of each column. This is useful when we are only interesting in grouping items together, and specific row positions do not matter, or when each column may have variable length.
3. **Grid** (Example usage: `[[parsons columns="3" rows="2"]] ... [[/parsons]]`) : This will set up a grid of shape `(columns, rows)`, where the student may drag and drop items to any position in the grid. 

## Switching orientation

Parsons blocks will display columns vertically by default. The student has the option to flip the orientation so that the columns become horizontal rows, and back again, through a button on the question page. If you wish the columns to be horizontal rows as the question display default, then simply add `transpose="true"` to the block header (e.g., `[[parsons columns="3" transpose="true"]] ... [[/parsons]]`, will load a question with 3 horizontal rows).

## Providing a model answer

_The format of the model answer is fixed and independent of the orientation discussed in the previous section_. It should be defined in `Question variables` as a two-dimensional array that is always column-grouped. For example if a model answer looks like 
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

The `clone` header parameter has been available in `parsons` since v4.5.0 of STACK. This can be set to `"true"` to allow items to be used more than once (e.g., `[[parsons columns="3" clone="true"]]...[[/parsons]]`).

## Headers and index

Column headers default to `0`, `1`, ..., `columns` when using **Grouping** or **Grid** configurations of the Parson's block, so we recommend to always set custom headers for these cases. See the examples below for how to do this.

There is also the option of setting the index, which is possible in both **Grouping** and **Grid** configurations, but is more likely to be useful for the latter. This will appear as a specially styled left-most column and fixes labels for the rows. The index does not count as a column, so you should  _decrease columns by one in the header parameters_. The item that appears top-left in both the index and header should be included in the _index only_. See the examples below for more details.

## Item style

In **Grouping** and **Grid** configurations, the height and width of individual items can be changed via the `item-height` and `item-width` header parameters. Parameter values should be a string containing a number, and this will set the pixels of the height/width. The default is `50px` for height, and the width is automatically deduced from the page layout and number of columns for the vertical orientation, or it is `100px` for the horizontal configuration. This may be needed if you have images or other large overflowing items (including header titles).

## The matching library

The `matchlib.mac` Maxima library contains a small number of functions that are required for basic functionality of assessing matching problems in STACK. Essentially they translate the author answers to and back from JSON format expected by the drag-and-drop engine. Be sure to include this and make use of it as detailed in the examples below.

## Example 1 : Grouping example

In our first example, the student is asked to place functions, given as equations, into columns with the categories "Differentiable", "Continuous, not differentiable", "Discontinuous".

### Question variables 

As a minimum it is recommended to include:
- Load the matching library.
- Define all items in the available list as a two-dimensional array, where each item is an array of the form `["<ID>", "<actual item contents>"]`. You will use the `"<ID>"` string to write solutions and assess student inputs; the second item is what is displayed to the student.
- Randomly permute the available items.
- The headers that will appear on top of the answer columns.
- The correct answer as a two-dimensional array. This should be column grouped.

For our example, the `Question variables` field looks as follows.
```
stack_include("contribl://matchlib.mac");

steps : [
    ["sq", "\\(f(x) = x^2\\)"],
    ["sin", "\\(f(x) = \\sin(x)\\)"],
    ["abs", "\\(f(x) = |x|\\)"],
    ["sqrt", "\\(f(x) = \\sqrt(x)\\)"],
    ["rec", "\\(f(x) = 1/x\\)"],
    ["sgn", "\\(f(x) = \\text{sgn}(x)\\)"]
];

headers: [
    "Differentiable", 
    "Continuous, not differentiable", 
    "Discontinuous"
];

steps: random_permutation(steps);

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
- Transfer the variables from `Question variables` into a JSON inside the `parsons` block as appropriate.
- Close the `parsons` block.
- Set `style="display:none"` in the input div to hide the messy state from the student.

```
<p>Recall that a function may be differentiable, continuous but 
not differentiable, or discontinuous. Drag the functions 
to their appropriate category. </p>
[[parsons input="ans1" columns="3"]]
{
    "steps" : {#stackjson_stringify(steps)#},
    "headers" : {#headers#}
}
[[/parsons]]
<p style="display:none">[[input:ans1]] [[validation:ans1]]</p>
```

### Input: ans1

1. The _Input type_ field should be **String**.
2. The _Model answer_ field should construct a JSON object from the teacher's answer `ta` using `match_correct(ans, steps)`.
3. Set the option "Student must verify" to "no".
4. Set the option "Show the validation" to "no".

Steps 3 and 4 are recommended, otherwise the student will see unhelpful code representing the underlying state of their answer.

### Potential response tree: prt1

Define the feedback variable
```
sans: match_interpret(ans1);
```
This provides the student response as a two-dimensional array of the same format as `ans`. At this point the author may choose to assess by comparing `sans` and `ans` as they see fit. In this case, the order _within_ a column really doesn't matter, but the order of the columns does of course. So we may convert the columns of `sans` and `ans` to sets in feedback variables using `match_column_setify` from `matchlib.mac`.
```
sans: match_column_setify(sans);
ans: match_column_setify(ans);
```
We can then do a regular algebraic equivalence test between `sans` and `ans`. You should turn the node to `Quiet: Yes`, otherwise the student will see unhelpful code if they the answer wrong.

## Example 2 : Grid example using an index

## Example 4 : Grid example without an index

## Example 3 : Using images


