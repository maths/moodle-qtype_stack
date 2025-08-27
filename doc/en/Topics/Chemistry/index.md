# Support for chemistry

STACK provides a comprehensive chemical data sheet for use in numerical problems.

To include chemical data use the following within your STACK question (question variables).

    stack_include_contrib("chemistry.mac");

Developer notes: 

* Before offical release, testing can use `stack_include("https://raw.githubusercontent.com/maths/moodle-qtype_stack/iss1504/stack/maxima/contrib/chemistry.mac");`.  (This will not work after release!)
* During development to load the code local to your development server use `stack_include("contribl://chemistry.mac");`

## Using chemical data

Chemical data is stored in a (large) associative array `%_STACK_CHEM_ELEMENTS` using the standard chemical symbols as keys in the array. (See [https://en.wikipedia.org/wiki/Chemical_symbol](https://en.wikipedia.org/wiki/Chemical_symbol).)

For example, the entry for `"H"`, hydrogen, is

````
["H", [
        ["Name", [ ["en", "hydrogen"], ["fi", "vety"] ]],
        ["AtomicNumber", 1],
        ["AtomicMass", 1.008]
 ]]
````

There are convenience functions which access this data.

* `chem_units(dp)` Returns the units addociated with `dp`. E.g. `chem_units("AtomicMass")` gives `g*mol^(-1)`.
* `chem_data_all(element)` returns all the data associated with `element`.  E.g. `chem_data_all("H")`.
* `chem_data(element, dp)` returns the data `dp` associated with `element`.  E.g. `chem_data("H", "AtomicMass")` gives `1.008`.
* `chem_data_units(element, dp)` returns the data `dp` associated with `element` using the `stackunits` function.  E.g. `chem_data_units("H", "AtomicMass")` gives `stackunits(1.008,g*mol^(-1))`.

Notes.

1. Names of elements are always given as strings.  E.g. to access data for hydrogen use `"H"`.
2. Field names are always given as strings, e.g. `"AtomicMass"` is a string (not an atom `AtomicMass`).
3. The utility functions filter the `"Name"` field to give the name of the element with the local language selection.  STACK uses the global `%_STACK_LANG` variable.  If no local name is defined the `"Name"` in English is returned.   If you really want _all_ the data, just use `assoc(element, %_STACK_CHEM_ELEMENTS)` rather than ` chem_data_all(element)`.

TODO: write a maxima function which gives an annotated atomic symbol in LaTeX, based on the chemical data e.g. \(^{25}_{12}\mbox{Mg}^{2+}\).


## Display of chemical formula in LaTeX

For pure display of chemical formula in LaTeX, the [`mhcem` package](https://mhchem.github.io/MathJax-mhchem/) is already available in MathJaX.  Here is a minial example:

```
\(\require{mhchem}\)
\(\ce{C6H5-CHO}\),
\(\ce{SO4^2- + Ba^2+ -> BaSO4 v}\)
```


