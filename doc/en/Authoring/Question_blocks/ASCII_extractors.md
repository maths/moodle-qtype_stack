## ASCII-Extractors

The purpose of "extractors" is to identify parts of the student's text, extract this, and send it to another STACK input for automatic assessment. An extractor is specified with a self-closed `[[extractor]]` child block inside the `[[ascii]]` block. Multiple `[[extractor]]` blocks may be used to link to multiple STACK inputs.

Both client-side regular expressions and post-processing in Maxima have their merits and uses and so we support both options for teachers.

### Extractor block parameters

1. `type` (required): the extractor type. See available types below.
2. `targetinput` (required): the name of the STACK input which receives the extracted value.
5. `search`: a literal search term.  Required for the `laststringremainder` and `laststringremainderwhitespace` extractors described below.
4. `regex`: a JavaScript regular expression string. Required for the various regex extractors types. (Note: backslashes in the regex need to be escaped with an additional backslash.)

## Last expression/calculation extractors

#### `lastexpr`

Returns the trimmed content of the last inline AsciiMath expression (delimited by backticks), or the last nonempty line of the last multi-line AsciiMath block, in document order. Falls back to the final nonempty line of the raw input.

```
[[extractor type="lastexpr" targetinput="ans2" /]]
```

#### `lastblock`

Returns the raw content of the last inline AsciiMath expression, or the _full content_ of the last multi-line AsciiMath block. This option relies on having a multi-line STACK input such as `equiv` or `textarea` to receive the potential multi-line expression.

```
[[extractor type="lastblock" targetinput="ans2" /]]
```

#### `lastcalc`

Returns the trimmed content of the last `calculation` block (i.e. text enclosed in `@...@`). This needs to come directly after the `calculation` filter.

```
[[extractor type="lastcalc" targetinput="ans2" /]]
```
## String extractors

These match a line containing a text string, and are designed to reduce the need to write full regular expressions.

The `search` parameter is required, which is a simple string match.  No regex terms are supported, and there is no requirement to protect items within the string.  E.g. to search for a literal `f(x)=` you do not need to protect the brackets as you would when constructing a regular expression.

#### `laststringremainder`

* Scans the raw answer line by line from the end backwards.
* Whitespace is trimmed from the line, and any outer backticks are removed.
* The line must contain the given (modified) `search` expression exactly.
* Returns the trimmed line, without the `search`, and without any backticks (if any) around the expression.

#### `laststringremainderwhitespace`

* Scans the raw answer line by line from the end backwards.
* Whitespace is trimmed from the line.
* A final full stop is removed, if needed.
* Any outer backticks are removed (hence it matches within mathematics, or outside).
* All whitespace is removed from within the line.
* Any whitespace within `search` is replaced by regex `\s` which matches zero or more spaces.
* The line must contain the given (modified) `search` expression.
* Returns the trimmed line, without the `search`, and without any backticks (if any) around the expression.

For example, to extract `<expr>` from a line such as `f(x) = <expr>`:

```
[[extractor type="laststringremainderwhitespace" targetinput="ans2" search="f(x) =" /]]
```

Using this extractor, the following lines will all return `x^2`

    f(x) = x^2
    f(x) = `x^2`
    f(x)=x^2
    f(x)=`x^2`
    `f(x)=x^2`
    `f(x) =x^2`

Note, by design, the matching is expected to be on a line of its own. Hence this will _not_ match

    hence `f(x)=x^2`.

## Regex extractors

For regular expression extractors, the `regex` parameter is required.

For example, to extract `<expr>` from a line such as `f(x) = <expr>`:

```
[[extractor type="lastregexremainder" targetinput="ans2" regex="^f\\(x\\)\\s*=\\s*" /]]
```

Note the escaped backslashes: in the `regex` attribute value `\\(` represents the regular expression `\(` which matches a literal `(`.

#### `lastregexmatch`

* Scans the raw answer line by line from the end backwards, matching the given regular expression.
* Returns the whole trimmed line.

#### `lastregexremainder`

* Scans the raw answer line by line from the end backwards, matching the given regular expression.
* Returns the whole trimmed line with the regex removed.

Note, this means if you have capture groups within your search term any line which matches will remove the capture group.  You cannot use this extractor to capture a group and return that group!

#### `allregexmatch`

* Searches the entire raw input for all lines matching the given regular expression.
* Returns a JSON object `{"matches": [...]}` as a string. The target input should be a string or JSON input. (Choose JSON for better debugging.)

```
[[extractor type="regexallmatch" targetinput="ans2" regex="fruit" /]]
```

For example, if you have a regular expression which matches "fruit" within the student's answer (!), then the expected output will be in the form:

    "{\"matches\":[ \"Apple\", \"Banana\", \"Cherry\"]}";

If this is assigned to input `ans2`, then you can create a Maxima list of the matched strings for use in the PRT with the following in the feedback variables.

    sa1: stackmap_get(stackjson_parse(ans2), "matches");

#### `allregexremainder`

* Searches the entire raw input for all lines matching the given regular expression.
* Returns a JSON object `{"matches": [...]}` as a string. The target input should be a string or JSON input. (Choose JSON for better debugging.)
* The regex itself is removed from the matches.

## Extractor developer notes

Extractors are defined in `corsscripts/ascii/extractors`. 

In the future we may add 

* more flexible functionality, linking arbitrary extractors to multiple inputs.
* support for a `bespoke` extractor enabling users to write JS code into the extractor block for use in that question directly.

Please contact the developers for ideas of extractor use-cases.

### Naming conventions

Extractors are named using the following (informal) conventions

    <which><what><how-much><other>

Where

* `<which>` refers to which match to return
  * `last` only return the last match
  * `all` return all matches
  * `first` return the first occurrence
* `<what>` refers to the search method
  * `block` looks for asciimaths (both in-line and multiline blocks) and returns the whole thing
  * `calc` look in calculation blocks
  * `expr` looks for asciimaths (both in-line and multiline blocks) and returns the last line
  * `string` match a literal string somewhere in the text
  * `regex` use a regular expression
* `<how-much>` What do we return?
  * `match` the whole matching expression, typically the whole line, or maths block
  * `remainder` of the expression without the search string or regex
* `<other>` gives other qualifiers to the searches
  * `whitespace` search without whitespace, or with whitespace rules.
