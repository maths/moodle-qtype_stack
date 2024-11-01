# Authoring drag and drop matching problems in STACK

The drag-and-drop functionality of the [Parson's](Question_block.md) block in STACK can also be used to write matching and other grid-based problems, which can be answered by drag and drop. 


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



## Example: Using images

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

ta: [
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
    "steps" : {#match_encode(steps)#},
    "headers" : {#headers#},
}
[[/parsons]]
<p>[[input:ans1]] [[validation:ans1]]</p>
```

### Question note, Inputs and PRT

These are exactly the same as the first [grid example](Grid.md).
