# Authoring Parson's problem blocks

Parson’s problems require students to assemble pre-written text into a correct order by dragging blocks into a tree structure.  This block is needed to hold the javascript and strings which students can drag.

STACK provides a `[[parsons]] ... [[/ parsons]]` [question block](Question_blocks/index.md).  The question author pre-defines strings.  These strings can be defined as Maxima variables in the question text, or just defined within the question block itself.

Users interact with the strings, dragging them into a tree structure.  Note, a list is a special case of a more structured tree.  The block can be linked to a STACK input so that the student's configuration can be stored and/or assessed.  This page is reference documentation for the `[[parsons]]` block.  Documentation on how to use this block in a complete question is given under topics: [Authoring Parson's problems](../Topics/Parsons.md).

## Authoring JSON within the question text itself.

Assume the question author writes a list of strings in Maxima, `proof_steps` with both the correct and incorrect strings.

````
[[ parsons state="maxima_value1" ]]
{# stackjson_stringify(proof_steps) #}
[[/ parsons ]]
````

or they can avoid strings going via Maimxa at all by writing JSON directly

````
[[ parsons state="maxima_value1" ]]
{ 
  "1":"Assume that \\(n\\) is odd.",
  "2":"Then there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n=2m+1\\).",
  "3":"\\[ n^2 = (2m+1)^2 = 2(2m^2+2m)+1.\\]\",
  "4":"Define \\(M=2m^2+2m\\in\\mathbb{Z}\\) then \\(n^2=2M+1\\).",
}
[[/ parsons ]]
````

Both these approaches can be combined

````
[[ parsons state="maxima_value1" ]]
{ 
  "1":{#proof_steps[1]#},
  "2":"Then there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n=2m+1\\).",
  "3":"\\[ n^2 = (2m+1)^2 = 2(2m^2+2m)+1.\\]\",
  "4":"Define \\(M=2m^2+2m\\in\\mathbb{Z}\\) then \\(n^2=2M+1\\).",
}
[[/ parsons ]]
````

## Adding `Sortable.js` options to the `[[parsons]]` block

The `[[parsons]]` block is a wrapper for the Javascript library "Sortable.js", optimised and with default options for Parson's problems.  As such, there are a very wide range of options for this javascript library.  These options are all passed into the block as a JSON string.   To do this we separate out the arguments to the block into separate "steps" and "options" fields. 

````
[[ parsons state="maxima_value1" ]]
{ "steps": {{# stackjson_stringify(proof_steps) #}},
  "options": {....}
}
[[/ parsons ]]
````

The default options are TODO: confirm the above syntax and the default options!

## Block paramaters

1. Parameter `state` gives the tree built up from the keys from which the applet should be initialised.
2. The applet returns up updated state (indentical format: maxima expression) for evaluation by a PRT.  This is linked to an input with parameter `input-ref-???`.
3. `height` and `width` paramaters exist.  TODO: examples/specs.
