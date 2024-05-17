# Authoring drag-and-drop matching and grid problems

The drag-and-drop functionality developed for [Parson's problems for proof](Parsons.md) has been extended to be used for general matching and grid problems. To author these, use the `[[parsons]] ... [[/parsons]]` block as one would for Parson's problems, but specify either `columns = "n"` or `columns="n"` _and_ `rows="m"`, which will set up a drag-and-drop grouping or grid layout with the number columns and rows as specified. You cannot specify `rows` without specifying `columns`.

The combinations of these two parameters define three possible layout configurations as follows:
1. **Proof** (`[[parsons]] [[/parsons]]`) : if both `columns` and `rows` are unspecified then this will give the traditional Parson's proof layout with an answer list that users must drag to and an available list that users must drag from. Refer to [the guide](Parsons.md) for writing Parson's proof questions.
2. **Column grouping** (`[[parsons columns="n"]] [[/parsons]]`) : If `columns="n"` is specified and `rows` is unspecified, this will lay out `n` _vertically arranged_ answer lists that items must be dragged to and an additional vertical available list that items must be dragged from. In this case, the lists can be arbitrary length and must be grown from the top downwards just as in the **Proof** layout. Via the reorientation button, the student is able to switch orientation between this and a row grouping setting (where answer lists are arranged as rows that are arbitrary length and grow from the left rightwards).
4. **Grid** (`[[parsons columns="n" rows="m"]] [[/parsons]]`) : If both `columns="n"` and `rows="m"` are specified, then this will lay out an `m` by `n` answer grid that items must be dragged to and a vertical available list that items must be dragged from. In this case, individual items can be passed to any position in the grid. The user also has the option to re-orient the grid to have `m` columns and `n` rows via the reorientation button.

The basic usage of all four modes are the exact same as [the Proof case](Parsons.md#authoring-json-within-the-question-text-itself), one can just modify the block parameters as specified. For example
```
[[parsons columns="2"]]
{
    "f" : "\\(y = x^2\\)",
    "g" : "\\(y = x^3\\)",
    "quad" : "Quadratic",
    "cubic" : "Cubic",
}
[[/parsons]]
```
## Clone mode

We emphasise that items can be re-used by setting `clone="true"` in the block header (as in `[[parsons columns="n" clone="true"]][[/parsons]]`). This is more likely to be needed for grouping and grid setups.

## Transposing on load

The re-orientation button allows the student to switch between vertical and horizontal orientation as they wish, but on load the default is for the columns to be displayed vertically. Using `transpose="true"` in the header (as in `[[parsons columns="n" transpose="true"]][[/parsons]]`) will change this so that the horizontal orientation will display on load.

## Headers

By default, answer lists in groupings and grid layouts will get default headers indexed by positive whole numbers. The available list will get a default header of "Drag from here:". These will become row indexes in **Row grouping** layout, or when the user presses the re-orient button.

Answer list headers can be changed by assigning the key `"headers"` key an an array of strings containing the new headers. The single header for the available list can be changed by assigning the `"available_header"` key to a string.
```
[[parsons columns="2"]]
{
    "steps": {
        "f" : "\\(y = x^2\\)",
        "g" : "\\(y = x^3\\)",
        "quad" : "Quadratic",
        "cubic" : "Cubic",
    },
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
    "steps": {
        "quad" : "Quadratic",
        "cubic" : "Cubic",
    },
    "headers" : ["Type"]
    "available_header" : "Available items"
    "index" : ["Equation", "\\(y = x^2\\)", "\\(y = x^3\\)"]
}
[[/parsons]]
```

Note that the length of the index must be the same as `rows + 1`. You can simply pass an empty string to the first position if no index header is required. 

## Sortable options

The final JSON key allowed inside the `parsons` block is `"options"` whose value can be a JSON containing options that can be used to customise the functionality of the drag-and-drop list. See [the Parsons guide](Parsons.md) for how to include these, and [the Sortable library](https://github.com/SortableJS/Sortable#options) for further details on possible customisations.

## Full list of block parameters

See [the Parsons authoring guide](Parsons.md#block-parameters) for a full list of supported block parameters.

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

## State

The state of the problem at any given point in time during question answer takes on the following format:
``` 
{used: usedState, available: availableState}
```
where `usedState` and `availableState` are arrays containing the keys specified in `steps` of the JSON in the answer. In all cases, `availableState` is a flat array of variable length. The shape of `usedState`` depends on which of the four layouts is being used. We give examples below.

1. **Proof**: In this case `usedState` will have shape `(1, 1, ?)`, where `?` indicates the variable dimension. For example:
````
[[parsons input="ans1"]]
{
  "1":"Assume that \\(n\\) is odd.",
  "2":"Then there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n=2m+1\\).",
  "3":"\\[ n^2 = (2m+1)^2 = 2(2m^2+2m)+1.\\]",
  "4":"Define \\(M=2m^2+2m\\in\\mathbb{Z}\\) then \\(n^2=2M+1\\).",
}
[[/parsons]]
````
might have, at a given time, a state that looks like:
```
{
    used : [
        [
            ["1", "3"]
        ]
    ]
    available : 
        ["2", "4"]
}
```
2. **Column grouping**: In this case `usedState` will have shape `(n, 1, ?)`, where `n` is the number of columns and `?` indicates the variable dimension. For example:
```
[[parsons columns="2"]]
{
    "f" : "\\(y = x^2\\)",
    "g" : "\\(y = x^3\\)",
    "quad" : "Quadratic",
    "cubic" : "Cubic",
}
[[/parsons]]
```
might have, at a given time, a state that looks like:
```
{
    used : [
        [
            ["f"]
        ],
        [
            ["quad", "cubic"]
        ]
    ],
    available : ["g"]
}
```
3. **Row grouping** : In this case `usedState` will have shape `(m, 1, ?)`, where `m` is the number of rows and `?` indicates the variable dimension. The state of **Row grouping** is just the same as **Column grouping** if `m` and `n` are the same.
4. **Grid** : In this case `usedState` will have shape `(n, m, 1)`, where `n` is the number of columns and `m` is the number of rows. For example:
```
[[parsons columns="2" rows="3"]]
{
    "f" : "\\(y = x^2\\)",
    "g" : "\\(y = x^3\\)",
    "h" : "\\(y = x^4\\)",
    "quad" : "Quadratic",
    "cubic" : "Cubic",
    "quart" : "Quartic"
}
[[/parsons]]
```
might have, at a given time, a state that looks like:
```
{
    used : [
        [
            ["f"],
            ["g"],
            [],
        ],
        [
            ["quad"],
            [],
            ["quart"]
        ]
    ],
    available : ["h", "cubic"]
}
```