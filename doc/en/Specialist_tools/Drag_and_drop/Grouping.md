# Grouping drag and drop questions

This will set up a number of columns, each of which behave similarly to the single left-hand column of the **Proof** configuration, where the student may drag and drop items starting at the top of each column. 
This is useful when we are only interesting in grouping items together, and specific row positions do not matter, or when each column may have variable length.

Example usage: `[[parsons columns="3"]] ... [[/parsons]]`.

## Troubleshooting

Please see the [troubleshooting](Troubleshooting.md) page for known issues and how to resolve them.

## Simple example

#### Question variables

```
stack_include_contrib("matchlib.mac");

steps: [
    ["f", "\\(y = x^2\\)"],
    ["g", "\\(y = x^3\\)"],
    ["quad", "Quadratic"],
    ["cubic", "Cubic"],
]
```

#### Question text
```
[[parsons columns="2"]]
{# match_encode(steps) #}
[[/parsons]]
```
## Clone mode

We emphasise that items can be re-used by setting `clone="true"` in the block header (as in `[[parsons columns="n" clone="true"]][[/parsons]]`). This is more likely to be needed for grouping and grid setups.

## Transposing on load

The re-orientation button allows the student to switch between vertical and horizontal orientation as they wish, but on load the default is for the columns to be displayed vertically. Using `transpose="true"` in the header (as in `[[parsons columns="n" transpose="true"]][[/parsons]]`) will change this so that the horizontal orientation will display on load.

## Headers

By default, answer lists in groupings and grid layouts will get default headers indexed by positive whole numbers. The available list will get a default header of "Drag from here:". These will become row indexes in **Row grouping** layout, or when the user presses the re-orient button.

Answer list headers can be changed by assigning the key `"headers"` key an an array of strings containing the new headers. The single header for the available list can be changed by assigning the `"available_header"` key to a string.

#### Question variables

```
stack_include_contrib("matchlib.mac");

steps: [
    ["f", "\\(y = x^2\\)"],
    ["g", "\\(y = x^3\\)"],
    ["quad", "Quadratic"],
    ["cubic", "Cubic"],
]
```

#### Question text 
```
[[parsons columns="2"]]
{
    "steps" : {# match_encode(steps) #},
    "headers" : ["Equation", "Type"],
    "available_header" : "Available items"
}
[[/parsons]]
```

Note that `headers.` must be a list of the same length as the number of columns and `available` must be a string. 
Beware that long headers may overflow boxes when using several columns so it is best to keep them short.

## Index

By default in **Column grouping** and **grid** layouts no index is used. In **Row grouping** mode the headers are an index, and in this case no headers exist by default.

To change this, one can pass an index to the JSON as follows:
```
[[parsons columns="1" rows="2"]]
{
    "steps": {# match_encode(steps) #},
    "headers" : ["Type"]
    "available_header" : "Available items"
    "index" : ["Equation", "\\(y = x^2\\)", "\\(y = x^3\\)"]
}
[[/parsons]]
```

Note that the length of the index must be the same as `rows + 1`. You can simply pass an empty string to the first position if no index header is required. 

## Example: Grouping example

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
    ["rec", "\\(f(x) = \\left\\{\\begin{array}{ll}1/x &, x\\neq 0 \\\\ 0&, x=0\\end{array}\\right.\\)"],
    ["sgn", "\\(f(x) = \\text{sgn}(x)\\)"]
];

steps: random_permutation(steps);

headers: [
    "Differentiable", 
    "Continuous, not differentiable", 
    "Discontinuous"
];

ta: [
    ["sq", "sin"], 
    ["abs", "sqrt"], 
    ["rec", "sgn"]
];
```

### Question text

Here we should:
- Write the question text itself.
- Open the `parsons` block with `input` and `columns` header parameters.
- Transfer the variables from _Question variables_ into a JSON inside the `parsons` block using `match_encode`.
- Close the `parsons` block.

```
<p>Recall that a function may be differentiable, continuous but 
not differentiable, or discontinuous. The following expressions define functions \(f:\mathbb{R}\rightarrow\mathbb{R}\).
Drag the functions to their appropriate category. </p>
[[parsons input="ans1" columns="3"]]
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
2. The _Model answer_ field should be a list `[ta, steps, 3]` containing the teacher answer, all possible steps and the number of columns.
3. Set the option _Student must verify_ to "no".
4. Set the option _Show the validation_ to "no".

Steps 3 and 4 are strongly recommended, otherwise the student will see unhelpful code representing the underlying state of their answer.

### Potential response tree: prt1

Define the feedback variable
```
sans: match_decode(ans1);
```
This provides the student response as a two-dimensional array of the same format as `ans`. 
At this point the author may choose to assess by comparing `sans` and `ans` as they see fit. 
In this case, the order _within_ a column really doesn't matter, but the order of the columns does of course. 
So we may convert the columns of `sans` and `ans` to sets in feedback variables using `match_column_set` from `matchlib.mac`.
```
sans: match_column_set(sans);
tans: match_column_set(ta);
```
We can then do a regular algebraic equivalence test between `sans` and `ans`. You should turn the node to `Quiet: Yes`, otherwise the student will see unhelpful code if they the answer wrong.

