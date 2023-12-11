# Authoring Parson's problem blocks

Parson’s problems require students to assemble pre-written text into a correct order by dragging blocks into a tree structure.  This block is needed to hold the javascript and strings which students can drag.

STACK provides a `[[parsons]] ... [[/ parsons]]` [question block](Question_blocks/index.md).  The question author pre-defines strings to drag within the block.  These strings can be defined as Maxima variables in the question text, or just defined within the question block itself.

Users interact with the strings, dragging them into a tree structure.  Note, a list is a special case of a more structured tree.  The block can be linked to a STACK input so that the student's configuration can be stored and/or assessed.  This page is reference documentation for the `[[parsons]]` block.  Documentation on how to use this block in a complete question is given under topics: [Authoring Parson's problems](../Topics/Parsons.md).

## Authoring JSON within the question text itself.

Here is a basic example of use:

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

Assume the question author writes a list `proof_steps` of pairs `["key", "string"]` in Maxima (as in the examples), in the question variables with both the correct and incorrect strings.

````
[parsons input="ans1" ]]
{# stackjson_stringify(proof_steps) #}
[[/parsons]]
````

or they can avoid strings going via Maxima at all by writing JSON directly

Both these approaches can be combined, assuming `proof_steps` is a list of pairs `["key", "string"]` as in previous examples.

````
[[parsons input="ans1"]]
{
  "1":{#proof_steps[1][2]#},
  "2":"Then there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n=2m+1\\).",
  "3":"\\[ n^2 = (2m+1)^2 = 2(2m^2+2m)+1.\\]",
  "4":"Define \\(M=2m^2+2m\\in\\mathbb{Z}\\) then \\(n^2=2M+1\\).",
}
[[/parsons]]
````

Note the `\` character needs to be protected within strings, so for example we have to type `\\(n=2m+1\\)` rather than just `\(n=2m+1\)`.

## Customising the `[[parsons]]` block

### JSON "options"

The `[[parsons]]` block is a wrapper for the javascript library "Sortable.js", optimised and with default options for Parson's problems. Moreover, there are default settings we add in, such as the headers for each of the two lists. These may be customised by structuring the JSON in the block contents as follows:

````
[[parsons input="ans1"]]
{ "steps": {# stackjson_stringify(proof_steps) #},
  "options": {"header" : ["Custom header for the answer list", "Custom header for the available steps"],
              "sortable option 1" : value,
              ...
              "sortable option n" : value}
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

The only non-Sortable option that may currently be customised is the `header` option. The default for these are:
````
{
    "header": ["Construct your solution here:", "Drag from here:"]
}
````
To modify these pass an array of length two, with first entry corresponding to the header for the answer list and the second entry corresponding to the header for the list of available steps.

Note that if you enter an unknown sortable option or if an attempt to pass `ghostClass`, `group`, or `onSort` is made, then these will simply be ignored. A warning will be displayed on the question page to signify this situation.

### Block parameters

Functionality and styling can be customised through the use of block parameters.

1. `input`: string. The name of the STACK input variable (e.g., `"ans1"`), this links to an internal `state` parameter that updates the input as a Maxima expression so that it can be stored and evaluated by a PRT.
2. `height`: string containing a positive float + a valid CSS unit (e.g.`"480px"`, `"100%"`, ...). Default is to create a window of automatic height to fit all the content upon load. Entering a value for the `height` parameter in the block header fixes the height of the window containing the drag-and-drop lists and will disable automatic resizing of the window containing the lists. Students will still be able to automatically resize the window with the expand button.
3. `width`: string containing a positive float + a valid CSS unit (e.g.`"480px"`, `"100%"`, ...).  Default is `"100%"`. This fixes the width of the window containing the drag-and-drop lists.
4. `aspect-ratio`: string, containing a float between 0 and 1. This can be used with `height`/`length` _or_ `width` (not both) and automatically determines the value of the un-used parameter in accordance with the value of `aspect-ratio`; unset by default. An error will occur if one sets values for `aspect-ratio`, `width`, `height` _or_ `aspect-ratio`, `width`, `length`.
5. `clone`: string of the form `"true"` or `"false"`. It is `"false"` by default. When `"false"` there are two lists and each proof step is "single-use", here the author must write all necessary proof steps even if they repeat; when `"true"` all proof steps are re-usable with no restrictions on how many times they are used, steps can only be dragged from the available list into the answer list, and there is a bin to tidy up steps.
6. `orientation`: string of the form `"horizontal"` or `"vertical"`. This can be used to fix the initial orientation shown to the user, `"horizontal"` will show lists side-by-side and `"vertical"` will show lists on top of each other. Note that there is a button on the page in which the user may switch the orientation to their preference while answering the question, so the `"orientation"` block parameter only determines the initial layout. It is `"horizontal"` by default.
7. `override-css`: string containing the location of a local CSS file contained in `question/type/stack/corsscripts/` directory in the format `cors://file-name` or a href to an external CSS stylesheet. This will override all CSS styling of the drag-and-drop listing, so it should be used with care. However, it can be used to customise the styling of the lists by writing one's own custom CSS file and passing in the location of that file to this parameter. This parameter is unset by default.
8. `override-js`: string containing a local JavaScript library or a href to a cdn of a JavaScript library. This will overwrite the Sortable library used with the library identified by the string. This should be used if one wishes to use an updated version of the Sortable library, or adding functionality with a custom library. Note that the custom library will need to either extend or import the base Sortable functionality. Unset by default.
9. `version`: string of the form `"local"` or `"cdn"`. Whether to use STACK's local copy of the Sortable library or whether to pull version 1.15.0 of Sortable from cdn. This is `"local"` by default.

## Random generation of `proof_step` order

To track which random variants of a question a student sees, and make sure they return to the same variant, we need to perform all randomisation at the Maxima level.

To create a random order, you must define steps as Maxima objects using a `proof_steps` list (see the documentation of for [CAS libraries for representing text-based proofs](../Proof/Proof_CAS_library.md)) then you can randomly order the `proof_steps` as follows.

1. Define a variable `proof_steps` as normal.
2. Add in `proof_steps:random_permutation(proof_steps);` to the question variables.
3. Add in a question note such as `{@map(first, proof_steps)@}` to create a meaningful, minimal, question note giving the order of steps.

Note, if you randomly generate random variants it is _strongly recommended_ you use text-based keys.  Keeping track of permuted numerical keys will be very difficult!

## Block connection with Maxima

All communication to and from the Parsons block uses the JSON format.  However, internally STACK uses maxima objets.  We therefore need to convert between Maxima syntax and JSON format.

1. The maxima function `stackjson_stringify(proof_steps)` will convert a list of `proof_steps` into a JSON string.
2. The maxima function `proof_parsons_interpret(ans1)` will convert a JSON string into a [proof construction function](../Proof/Proof_CAS_library.md).
3. The maxima function `proof_parsons_key_json(ta, proof_steps)` takes the teacher's answer `ta` and a list of proof steps `proof_steps` and creates a JSON string which represents `ta` and lists any available (unused) strings from the `proof_steps` list.  This function is needed to set up the "model answer" field in the inputs from a maxima representation of the proof.

### Block parameters: `height` and `width`

Additional display options including `height` and `width` may also be passed to the header, as in

````
[[parsons input="ans1" height="360px" width="100%"]]
{
  "1":"Assume that \\(n\\) is odd.",
  "2":"Then there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n=2m+1\\).",
  "3":"\\[ n^2 = (2m+1)^2 = 2(2m^2+2m)+1.\\]",
  "4":"Define \\(M=2m^2+2m\\in\\mathbb{Z}\\) then \\(n^2=2M+1\\).",
  "5":"Assume that \\(n\\) is even.",
  "6":"Then there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n = 2m\\)."
};
[[/parsons]]
````

## Adding plots to a Parson's block

Since HTML can be embedded into strings dragged within a Parson's block, images can be included with the HTML `<img>` tags as normal.

STACK-generated [plots](../Plots/index.md) can also be included just using `{@plot(x^2,[x,-1,1])@}` as might be expected.  This is because of the _order_ of evaluation.  The full URL of the image is only created in the (complex) chain of events after the value has been substituted into the javascript code.

````
[[parsons input="ans1"]]
{
  "A":"The inverse function of \\(f(x)=x^2\\) has graph",
  "B":{#plot(x^2,[x,-1,1],[size,250,250])#},
};
[[/parsons]]
````

Notice that since the value of `plot(...)` is a Maxima string of `<img>` tag, there is no need to add in string quotes when defining the JSON above.  The `{#...#}` will print `"` as part of the output.  However, for convenience string quotes are removed from the display form `{@...@}` (as typically you just want the plot without quotes).  Hence this is an alternative.

````
[[parsons input="ans1"]]
{
  "A":"The inverse function of \\(f(x)=x^2\\) has graph",
  "B":"{@plot(sqrt(x),[x,-1,1],[size,250,250])@}",
};
[[/parsons]]
````

An alternative is to use the Maxima `castext` function, e.g.

    s1:castext("Consider this graph {@plot(x^2,[x,-1,1],[size,250,250])@}");

and then use the value of `s1`, in the Parson's block within the question text

````
[[parsons input="ans1"]]
{
  "A":"The inverse function of \\(f(x)=x^2\\) has graph",
  "B":"{@s1@}",
};
[[/parsons]]
````

A last direct example of question variables

````
proof_steps:[
  [ "A", plot(sqrt(x),[x,-1,1],[size,180,180],[margin,1.7],[yx_ratio, 1],[plottags,false])],
  [ "B", plot(x,[x,-1,1],[size,180,180],[margin,1.7],[yx_ratio, 1],[plottags,false])],
  [ "C", plot(x^2,[x,-1,1],[size,180,180],[margin,1.7],[yx_ratio, 1],[plottags,false])],
  [ "D", plot(x^3,[x,-1,1],[size,180,180],[margin,1.7],[yx_ratio, 1],[plottags,false])]
];
````

## Adding trees to a Parson's block

STACK enables question authors to display the tree structure of an algebraic expression using castext `{@disptree(1+2+pi*x^3)@}`.
