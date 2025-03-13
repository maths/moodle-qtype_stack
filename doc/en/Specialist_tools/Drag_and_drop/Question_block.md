# Parson's problem (drag and drop) question blocks

Parson’s problems require students to assemble pre-written text into a correct order by dragging blocks into a tree structure.  

This page provides reference documentation for the `[[parsons]] ... [[/ parsons]]` question block.  

The question author pre-defines strings to drag within the block.  These strings can be defined as Maxima variables in the question text, or just defined within the question block itself.

Users interact with the strings, dragging them into a tree structure.  Note, a list is a special case of a more structured tree.  The block can be linked to a STACK input so that the student's configuration can be stored and/or assessed.  This page is reference documentation for the `[[parsons]]` block.  Documentation on how to use this block in a complete question is given under topics: [Authoring Parson's problems](Parsons.md).

## Basic usage

Here is a basic example of use:

#### Question variables

The question author should write all steps to be shown to the student as a list of pairs of the form `["key", "string"]`, where 
`"string"` is what is shown to the student on the question page. Throughout the question the author uses `"key"` to reference 
the steps. Note the `\` character needs to be protected within strings, so for example we have to type `\\(n=2m+1\\)` rather than just `\(n=2m+1\)`.

```
stack_include("contribl://prooflib.mac");

proof_steps: [
    ["assume", "Assume that \\(n\\) is odd."],
    ["ex", "Then there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n=2m+1\\)."],
    ["expand", "\\[ n^2 = (2m+1)^2 = 2(2m^2+2m)+1.\\]"],
    ["def", "Define \\(M=2m^2+2m\\in\\mathbb{Z}\\) then \\(n^2=2M+1\\)."],
];
```

#### Question text 

````
[[parsons input="ans1"]]
{# parsons_encode(proof_steps) #}
[[/parsons]]
````

## Customising the `[[parsons]]` block

### JSON "options"

The `[[parsons]]` block is a wrapper for the javascript library "Sortable.js", optimised and with default options for Parson's problems. Moreover, there are default settings we add in, such as the headers for each of the two lists. These may be customised by structuring the JSON in the block contents as follows:

````
[[parsons input="ans1"]]
{ "steps": {# parsons_encode(proof_steps) #},
  "options": {"sortable option 1" : value, ..., "sortable option n" : value},
  "headers" : ["Custom header for the answer list"], 
}
[[/parsons]]
````

A list of all Sortable.js options can be found [here](https://github.com/SortableJS/Sortable#options), currently these are set at the default option for `"animation"` which controls the animation speed.

````
{
    "animation": 50,
}
````
Most other Sortable options can be modified, except for `ghostClass`, `group` and `onSort` as these are required to be set for basic functionality.

Note that if you enter an unknown sortable option or if an attempt to pass `ghostClass`, `group`, or `onSort` is made, then these will simply be ignored. A warning will be displayed on the question page to signify this situation.

The default for "headers" and "available_header" are:
````
{
    "headers": ["Construct your solution here:"], 
    "available_header": ["Drag from here:"]
}
````

#### Troubleshooting

If your Parson's problem is not displaying properly, in particular if the all the items are displayed in a single yellow block, then
double-check that you have spelled the keys of the JSON inside the Parsons block correctly as described above. They should be a subset of 
```
{"steps", "options", "headers", "available_header"}
```
and a superset of 
```
{"steps"}
```
For technical reasons this is one error that we are unable to validate currently.

### Block parameters

Functionality and styling can be customised through the use of block parameters.

1. `input`: string. The name of the STACK input variable (e.g., `"ans1"`), this links to an internal `state` parameter that updates the input as a Maxima expression so that it can be stored and evaluated by a PRT.
2. `height`: string containing a positive float + a valid CSS unit (e.g.`"480px"`, `"100%"`, ...). Default is to create a window of automatic height to fit all the content upon load. Entering a value for the `height` parameter in the block header fixes the height of the window containing the drag-and-drop lists and will disable automatic resizing of the window containing the lists. Students will still be able to automatically resize the window with the expand button.
3. `width`: string containing a positive float + a valid CSS unit (e.g.`"480px"`, `"100%"`, ...).  Default is `"100%"`. This fixes the width of the window containing the drag-and-drop lists.
4. `aspect-ratio`: string, containing a float between 0 and 1. This can be used with `height`/`length` _or_ `width` (not both) and automatically determines the value of the un-used parameter in accordance with the value of `aspect-ratio`; unset by default. An error will occur if one sets values for `aspect-ratio`, `width`, `height` _or_ `aspect-ratio`, `width`, `length`.
5. `clone`: string of the form `"true"` or `"false"`. It is `"false"` by default. When `"false"` there are two lists and each proof step is "single-use", here the author must write all necessary proof steps even if they repeat; when `"true"` all proof steps are re-usable with no restrictions on how many times they are used, steps can only be dragged from the available list into the answer list, and there is a bin to tidy up steps.
6. `override-css`: string containing the location of a local CSS file contained in `question/type/stack/corsscripts/` directory in the format `cors://file-name` or a href to an external CSS stylesheet. This will override all CSS styling of the drag-and-drop listing, so it should be used with care. However, it can be used to customise the styling of the lists by writing one's own custom CSS file and passing in the location of that file to this parameter. This parameter is unset by default.
7. `override-js`: string containing a local JavaScript library or a href to a cdn of a JavaScript library. This will overwrite the Sortable library used with the library identified by the string. This should be used if one wishes to use an updated version of the Sortable library, or adding functionality with a custom library. Note that the custom library will need to either extend or import the base Sortable functionality. Unset by default.
8. `version`: string of the form `"local"` or `"cdn"`. Whether to use STACK's local copy of the Sortable library or whether to pull version 1.15.0 of Sortable from cdn. This is `"local"` by default.
9. `columns` : string containing an integer `"n"`. How many vertical answer lists to display. By default, this is not used. If it is specified, then the styling will change to a grid-format with multiple vertical answer lists of unspecified length.
10. `rows` : string containing an integer `"m"`. How many horizontal answer lists to display. By default, this is not used. If it is specified and `columns` is _not_ specified, this will change to a grid-format with multiple horizontal answer lists of unspecified width. If both `columns` and `rows` are specified then this will provide a fixed length and width grid format, where items can be dragged to any position in the grid in any order. You cannot specify `rows` without specifying `columns`.
11. `transpose` : `"true"` or `"false"`; `"false"` by default. While the student is able to re-orient between vertical and horizontal as they wish, the default on load is for columns to be vertical. If you wish them to default to being horizontal, then pass `transpose="true"`.
12. `log` : `"true"` or `"false"`; `"false"` by default. When set to `"true"` the student's will contain their entire drag-and-drop move history for that attempt, along with the timestamp (number of seconds since 00:00 GMT 01/01/1970) of that move.

## Sortable options

The final JSON key allowed inside the `parsons` block is `"options"` whose value can be a JSON containing options that can be used to customise the functionality of the drag-and-drop list. See [the Parsons guide](Parsons.md) for how to include these, and [the Sortable library](https://github.com/SortableJS/Sortable#options) for further details on possible customisations.

## Random generation of `proof_step` order

To track which random variants of a question a student sees, and make sure they return to the same variant, we need to perform all randomisation at the Maxima level.

To create a random order, you must define steps as Maxima objects using a `proof_steps` list (see the documentation of for [CAS libraries for representing text-based proofs](../../Topics/Proof/Proof_CAS_library.md)) then you can randomly order the `proof_steps` as follows.

1. Define a variable `proof_steps` as normal.
2. Add in `proof_steps:random_permutation(proof_steps);` to the question variables.
3. Add in a question note such as `{@map(first, proof_steps)@}` to create a meaningful, minimal, question note giving the order of steps.

## Block connection with Maxima

All communication to and from the Parsons block uses the JSON format.  However, internally STACK uses maxima objets.  We therefore need to convert between Maxima syntax and JSON format.

1. The maxima function `parsons_encode(proof_steps)` will convert a list of `proof_steps` into a JSON string with hashed keys.
2. The maxima function `parsons_decode(ans1)` will convert a JSON string into a [proof construction function](../../Topics/Proof/Proof_CAS_library.md).

### Block parameters: `height` and `width`

Additional display options including `height` and `width` may also be passed to the header, as in

````
[[parsons input="ans1" height="360px" width="100%"]]
{# parsons_encode(proof_steps) #}
[[/parsons]]
````

## Adding plots to a Parson's block

Since HTML can be embedded into strings dragged within a Parson's block, images can be included with the HTML `<img>` tags as normal.

STACK-generated [plots](../../CAS/Maxima_plot.md) can also be included just using `{@plot(x^2,[x,-1,1])@}` as might be expected.  This is because of the _order_ of evaluation.  The full URL of the image is only created in the (complex) chain of events after the value has been substituted into the javascript code.

````
proof_steps: [
    ["A", "The inverse function of \\(f(x)=x^2\\) has graph"],
    ["B", plot(x^2,[x,-1,1],[size,250,250])]
];
````

## Legacy versions

In (2024072500) we changed the way we deal with Parsons problems, adding in a special input type to support them.
These will hash they keys of the `proof_steps` variable so that they are hidden even when the web page is inspected. 
This also fixes a randomisation bug that occurred when numerical keys are used (see Issue [#1237](https://github.com/maths/moodle-qtype_stack/issues/1237)).

| Use   | Old versions              | New versions          |             |
|-------|---------------------------|-----------------------|-------------|
| Block | `stackjson_stringify`      | `parsons_encode`      |             |
| Input | `proof_parsons_key_json`   | `parsons_answer`      | Used in test-case construction |
| PRT   | `proof_parsons_interpret`  | `parsons_decode`      |             |

E.g. in test-case construction use `parsons_answer([ta, proof_steps])` where `ta` is a list of tags and `proof_steps` is the proof steps array.

Legacy versions of questions are still supported and should function as previously. However it is strongly recommended to update questions to use the new functions.  You will be prompted to upgrade by the bulk test.

## Obtaining attempt histories

When `log = "true"` is used in the block header, the final input value submitted will contain an array containing the internal 
representations of the attempt's move history, along with the timestamp at which each move occurs. Timestamps are measured as 
number of seconds elapsed since 00:00 GMT 01/01/1970. 

Given the following `proof_steps` variable within Question Variables:
```
proof_steps: [  
  ["assume_odd", "Assume that \\(n\\) is odd."],
  ["ex_odd", "Then there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n=2m+1\\)."],
  ["expand", "\\[ n^2 = (2m+1)^2 = 2(2m^2+2m)+1.\\]"],
  ["def", "Define \\(M=2m^2+2m\\in\\mathbb{Z}\\) then \\(n^2=2M+1\\)."],
  ["assume_even", "Assume that \\(n\\) is even."],
  ["ex_even", "Then there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n = 2m\\)."]
];
```
the move history takes the following format
```
[
    [
        {"used" : ["assume_odd", ...], "available" : []}, 
        1723723269679
    ],
    [
        {"used" : ["assume_odd", ...], "available" : [...]}, 
        1723723269675
    ],
    ...
    [
        {"used" : [], "available" : ["assume_odd", ...]},
        1723723269667
    ]
]
```
