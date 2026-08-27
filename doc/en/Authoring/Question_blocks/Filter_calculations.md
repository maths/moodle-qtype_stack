# Filter blocks: calculations

STACK provides two filters for the [Ascii block](ASCII.md) to perform calculations.

Both these filters find text enclosed between `{@...@}` tags on a single line and evaluates the contents using [https://mathjs.org/](https://mathjs.org/).  There are two versions, a basic scientific calculator and the full version.

    [[filter type="calculation" /]]
    [[filter type="cas" /]]

Note, the order of filters is important, and it is essential that the calculation or cas filter is applied before any markdown filter you are using.  That way the results of any calculation are inserted into text which is then processed by the markdown filter.  The _results_ of the calculation can then be captured by the `lastexpr` and `lastblock` extractors (see below).  Hence, you will typically need to use

```
[[ascii input="ans1"]]
  [[filter type="calculation" /]]
  [[filter type="markdown-math" transforms="aligneq" /]]
  [[extractor type="lastexpr" targetinput="ans2" /]]
[[/ascii]]
```

Note, the calculation filter really does a search and replace on the text. It's therefore important for users (including students) to embed a calculation within a mathematics environment.  If you would like the result of a numerical calculation within a LaTeX inline expression then use

    \( {@3*31@} \)

or, if using the AsciiMath filter

    `{@3*31@}`

While just `{@3*31@}` will be replaced within the text (and probably looks OK as numbers within text), it won't be within a mathematics environment.  (Experienced castext users might not have noticed that when rendering castext server-side we auto-detect if a calculation is within a mathematics environment, and if not we ensure it's inline mathematics. This auto-detection does not happen here!)

### `calculation` filter

The `calculation` filter provides only basic calculator functions, including support for statistics.  This is the intended filter for student use.

Angles are in radians only.

For example, `The answer to \(1+1={@1+1@}\).` displays the answer as 2. The enclosed text is also collected as a block and available to the `lastcalc` extractor.

### `cas` filter

The maths.js library is extensive, and includes many CAS functions.  The `cas` filter provides full access to this library, e.g. try the following calculation.

    `{@derivative("sin(2*x^3)", "x")@}`

(Note the use of backticks to indicate the result of the calculation should be processed by AsciiMath.)

This filter is not intended to be used by students. 