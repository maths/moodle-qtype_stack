# Drag and drop questions in STACK

Drag and drop problems are often referred to as "Parson's problems".

You can add in drag and drop functionality using the `[[parsons ..]]` [question block](Question_block.md).  This can be used in three ways in STACK.  How to author example questions in major areas:

As of STACK v4.6.0, the `parsons` block has three main configurations (each of which support further customisation) which can be achieved by setting appropriate block header parameters `columns` and `rows` as appropriate:

1. **Proof** (Example usage: `[[parsons]] ... [[/parsons]]`) : This was introduced in STACK v4.5.0, and a full guide can be found under [Parsons problems](Parsons.md).
2. **Grouping** (Example usage: `[[parsons columns="3"]] ... [[/parsons]]`) : 
This will set up a number of columns, each of which behave similarly to the single left-hand column of the **Proof** configuration, where the student may drag and drop items starting at the top of each column. 
This is useful when we are only interesting in grouping items together, and specific row positions do not matter, or when each column may have variable length. Example problems are given in the [grouping](Grouping.md) page.
3. **Grid** (Example usage: `[[parsons columns="3" rows="2"]] ... [[/parsons]]`) : 
This will set up a grid of shape `(columns, rows)`, where the student may drag and drop items to any position in the grid. Example problems are given in the [grid](Grid.md) page.

Note that many **Grid** style questions can also be written using the **Grouping** setup. 
The main difference between them is that **Grid** allows the student to drag any item to any position in the grid, regardless
of whether the item above it has been filled; **Grouping** on the other hand only allows students to drag items to the 
end of the list within a column.

## Troubleshooting

If your matching problem is not displaying properly, in particular if the all the items are displayed in a single yellow block, then double-check that you have spelled the keys of the JSON inside the Parsons block correctly as described below. They should be a subset of 

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
`