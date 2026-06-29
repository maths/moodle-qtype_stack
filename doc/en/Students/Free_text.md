# Free-text input

This input allows you to type in free text, e.g. your complete working or mathematical proof.

1. You can type markdown text.
2. You can type AsciiMath between backticks for mathematics: <code>`...`</code>
3. You can include LaTeX between brackets, <code>\(...\)</code> for inline mathematics and <code>\[...\]</code> displayed mathematics.
4. Sometimes, but not always, you will be able to include calculations between <code>{@...@}</code>.  Rather than reaching for an external calculator, the software will replace your requested calculation with the answer.

## Mathematics

If you would like mathematical expressions/equations to be _inline_ then use AsciiMath.  For example

    This problem asks us to solve `x^2-10x+9=0`, which is a quadratic equation.

If you would like mathematical expressions/equations to be _displayed_ then also use AsciiMath, but put backticks <code>``</code> on a single line at the start and end.  For example

    To solve `x^2-10x+9` we work line by line
    `
    x^2-10x+9
    (x-5)^2-16=0
    (x-5)^2-4^2=0
    (x-5-4)(x-5+4)=0
    (x-9)(x-1)=0
    x=9 or x=1
    `

If you have text on the same line as a backtick you will get _inline_ mathematics.


## Markdown

Markdown is a lightweight markup language for creating formatted text using a plain-text editor.

Here are some example of what you need to type to format text.

```
# Heading

## Sub-heading

Paragraphs are separated by a blank line.

Two spaces at the end of a line  
produce a line break.

Text attributes _italic_, **bold**.

Horizontal rule:

---

Bullet lists nested within numbered list:

  1. fruits
     * apple
     * banana
  2. vegetables
     - carrot
     - broccoli
```

In normal markdown backticks are used for monospace

```
   `monospace`
```
However free-text input uses backticks for AsciiMath, see below.

## AsciiMath

[AsciiMath](https://asciimath.org/) is an easy-to-write markup language for mathematics.  Most AsciiMath symbols attempt to mimic in text what they look like rendered, like `oo` for \(\infty\). Many symbols can also be displayed using a LaTeX alternative (see below), but without a backslash.

Input 

    `sum_(k=1)^oo 1/k^2=pi^2/6`

is rendered as \(\sum_{k=1}^\infty \frac{1}{k^2} = \frac{\pi^2}{6}\).

**Operation symbols**

| Type     | See            | TeX alt             |
| -------- | -------------- | ------------------- |
| `+`      | \(+\)          |                     |
| `-`      | \(-\)          |                     |
| `*`      | \(\cdot\)      | cdot                |
| `**`     | \(\ast\)       | ast                 |
| `***`    | \(\star\)      | star                |
| `//`     | \(//\)         |                     |
| `\\`     | \(\backslash\) | backslash, setminus |
| `xx`     | \(\times\)     | times               |
| `-:`     | \(\div\)       | div                 |
| `|><`    | \(\ltimes\)    | ltimes              |
| `><|`    | \(\rtimes\)    | rtimes              |
| `|><|`   | \(\bowtie\)    | bowtie              |
| `@`      | \(\circ\)      | circ                |
| `o+`     | \(\oplus\)     | oplus               |
| `ox`     | \(\otimes\)    | otimes              |
| `o.`     | \(\odot\)      | odot                |
| `sum`    | \(\sum\)       |                     |
| `prod`   | \(\prod\)      |                     |
| `^^`     | \(\wedge\)     | wedge               |
| `^^^`    | \(\bigwedge\)  | bigwedge            |
| `vv`     | \(\vee\)       | vee                 |
| `vvv`    | \(\bigvee\)    | bigvee              |
| `nn`     | \(\cap\)       | cap                 |
| `nnn`    | \(\bigcap\)    | bigcap              |
| `uu`     | \(\cup\)       | cup                 |
| `uuu`    | \(\bigcup\)    | bigcup              |

<br/>

**Miscellaneous symbols**

| Type         | See             | TeX alt       |
| ------------ | --------------- | ------------- |
| `2/3`        | \(\frac{2}{3}\) | frac{2}{3}    |
| `2^3`        | \(2^3\)         |               |
| `sqrt x`     | \(\sqrt{x}\)    |               |
| `root(3)(x)` | \(\sqrt[3]{x}\) |               |
| `int`        | \(\int\)        |               |
| `oint`       | \(\oint\)       |               |
| `del`        | \(\partial\)    | partial       |
| `grad`       | \(\nabla\)      | nabla         |
| `+-`         | \(\pm\)         | pm            |
| `O/`         | \(\emptyset\)   | emptyset      |
| `oo`         | \(\infty\)      | infty         |
| `aleph`      | \(\aleph\)      |               |
| `:.`         | \(\therefore\)  | therefore     |
| `:'`         | \(\because\)    | because       |
| `|...|`      | \(\ldots\)      | |ldots|       |
| `|cdots|`    | \(\cdots\)      |               |
| `vdots`      | \(\vdots\)      |               |
| `ddots`      | \(\ddots\)      |               |
| `|\ |`       | \(\ \)          |               |
| `|quad|`     | \(\quad\)       |               |
| `/_`         | \(\angle\)      | angle         |
| `frown`      | \(\frown\)      |               |
| `/_\`        | \(\triangle\)   | triangle      |
| `diamond`    | \(\diamond\)    |               |
| `square`     | \(\square\)     |               |
| `|__`        | \(\lfloor\)     | lfloor        |
| `__|`        | \(\rfloor\)     | rfloor        |
| `|~`         | \(\lceil\)      | lceiling      |
| `~|`         | \(\rceil\)      | rceiling      |
| `CC`         | \(\mathbb{C}\)  |               |
| `NN`         | \(\mathbb{N}\)  |               |
| `QQ`         | \(\mathbb{Q}\)  |               |
| `RR`         | \(\mathbb{R}\)  |               |
| `ZZ`         | \(\mathbb{Z}\)  |               |
| `"hi"`       | \(\text{hi}\)   | `\text{hi}`   |

<br/>

**Relation symbols**

| Type   | See           | TeX alt  |
| ------ | ------------- | -------- |
| `=`    | \(=\)         |          |
| `!=`   | \(\ne\)       | ne       |
| `<`    | \(<\)         | lt       |
| `>`    | \(>\)         | gt       |
| `<=`   | \(\le\)       | le       |
| `>=`   | \(\ge\)       | ge       |
| `mlt`  | \(\ll\)       | ll       |
| `mgt`  | \(\gg\)       | gg       |
| `-<`   | \(\prec\)     | prec     |
| `-<=`  | \(\preceq\)   | preceq   |
| `>-`   | \(\succ\)     | succ     |
| `> -=` | \(\succeq\)   | succeq   |
| `in`   | \(\in\)       |          |
| `!in`  | \(\notin\)    | notin    |
| `sub`  | \(\subset\)   | subset   |
| `sup`  | \(\supset\)   | supset   |
| `sube` | \(\subseteq\) | subseteq |
| `supe` | \(\supseteq\) | supseteq |
| `-=`   | \(\equiv\)    | equiv    |
| `~=`   | \(\cong\)     | cong     |
| `~~`   | \(\approx\)   | approx   |
| `prop` | \(\propto\)   | propto   |

<br/>

**Logical symbols**

| Type   | See           | TeX alt |
| ------ | ------------- | ------- |
| `and`  | \(\land\)     |         |
| `or`   | \(\lor\)      |         |
| `not`  | \(\neg\)      | neg     |
| `=>`   | \(\implies\)  | implies |
| `if`   | \(\text{if}\) |         |
| `<=>`  | \(\iff\)      | iff     |
| `AA`   | \(\forall\)   | forall  |
| `EE`   | \(\exists\)   | exists  |
| `_|_`  | \(\bot\)      | bot     |
| `TT`   | \(\top\)      | top     |
| `|--`  | \(\vdash\)    | vdash   |
| `|==`  | \(\models\)   | models  |

<br/>

**Grouping brackets**

| Type         | See                        | TeX alt |
| ------------ | -------------------------- | ------- |
| `(`          | \((\)                      |         |
| `)`          | \()\)                      |         |
| `[`          | \([\)                      |         |
| `]`          | \(]\)                      |         |
| `(:`         | \(\langle\)                | langle  |
| `:)`         | \(\rangle\)                | rangle  |
| `<<`         | \(\langle\langle\)         |         |
| `>>`         | \(\rangle\rangle\)         |         |
| `floor(x)`   | \(\lfloor x \rfloor\)      |         |
| `ceil(x)`    | \(\lceil x \rceil\)        |         |
| `norm(vecx)` | \(\left| \vec{x} \right|\) |         |

Note, you can also use `{...}` for curly braces and `abs(x)` for \(|x|\).

**Arrows**

| Type   | See                        | TeX alt               |
| ------ | -------------------------- | --------------------- |
| `uarr` | \(\uparrow\)               | uparrow               |
| `darr` | \(\downarrow\)             | downarrow             |
| `rarr` | \(\rightarrow\)            | rightarrow            |
| `->`   | \(\to\)                    | to                    |
| `>->`  | \(\rightarrowtail\)        | rightarrowtail        |
| `->>`  | \(\twoheadrightarrow\)     | twoheadrightarrow     |
| `\|->` | \(\mapsto\)                | mapsto                |
| `larr` | \(\leftarrow\)             | leftarrow             |
| `harr` | \(\leftrightarrow\)        | leftrightarrow        |
| `rArr` | \(\Rightarrow\)            | Rightarrow            |
| `lArr` | \(\Leftarrow\)             | Leftarrow             |
| `hArr` | \(\Leftrightarrow\)        | Leftrightarrow        |

<br/>

**Accents**

| Type             | See                  | TeX alt          |
| ---------------- | -------------------- | ---------------- |
| `hat x`          | \(\hat{x}\)          |                 |
| `bar x`          | \(\overline{x}\)     | overline x      |
| `ul x`           | \(\underline{x}\)    | underline x     |
| `vec x`          | \(\vec{x}\)          |                 |
| `tilde x`        | \(\tilde{x}\)        |                 |
| `dot x`          | \(\dot{x}\)          |                 |
| `ddot x`         | \(\ddot{x}\)         |                 |
| `overset(x)(=)`  | \(\overset{x}{=}\)   | overset(x)(=)  |
| `underset(x)(=)` | \(\underset{x}{=}\)  |                 |
| `ubrace(1+2)`    | \(\underbrace{1+2}\) | underbrace(1+2) |
| `obrace(1+2)`    | \(\overbrace{1+2}\)  | overbrace(1+2)  |
| `overarc(AB)`    | \(\overparen{AB}\)   | overparen(AB)   |
| `color(red)(x)`  | \(\color{red}{x}\)   |                 |
| `cancel(x)`      | \(\cancel{x}\)       |                 |

<br/>

**Greek Letters**

| Type         | See             | Type     | See         |
| ------------ | --------------- | -------- | ----------- |
| `alpha`      | \(\alpha\)      |          |             |
| `beta`       | \(\beta\)       |          |             |
| `gamma`      | \(\gamma\)      | `Gamma`  | \(\Gamma\)  |
| `delta`      | \(\delta\)      | `Delta`  | \(\Delta\)  |
| `epsilon`    | \(\epsilon\)    |          |             |
| `varepsilon` | \(\varepsilon\) |          |             |
| `zeta`       | \(\zeta\)       |          |             |
| `eta`        | \(\eta\)        |          |             |
| `theta`      | \(\theta\)      | `Theta`  | \(\Theta\)  |
| `vartheta`   | \(\vartheta\)   |          |             |
| `iota`       | \(\iota\)       |          |             |
| `kappa`      | \(\kappa\)      |          |             |
| `lambda`     | \(\lambda\)     | `Lambda` | \(\Lambda\) |
| `mu`         | \(\mu\)         |          |             |
| `nu`         | \(\nu\)         |          |             |
| `xi`         | \(\xi\)         | `Xi`     | \(\Xi\)     |
| `pi`         | \(\pi\)         | `Pi`     | \(\Pi\)     |
| `rho`        | \(\rho\)        |          |             |
| `sigma`      | \(\sigma\)      | `Sigma`  | \(\Sigma\)  |
| `tau`        | \(\tau\)        |          |             |
| `upsilon`    | \(\upsilon\)    |          |             |
| `phi`        | \(\phi\)        | `Phi`    | \(\Phi\)    |
| `varphi`     | \(\varphi\)     |          |             |
| `chi`        | \(\chi\)        |          |             |
| `psi`        | \(\psi\)        | `Psi`    | \(\Psi\)    |
| `omega`      | \(\omega\)      | `Omega`  | \(\Omega\)  |

<br/>

**Font commands**

| Type           | See                   | TeX alt            |
| -------------- | --------------------- | ------------------ |
| `bb "AaBbCc"`  | \(\mathbf{AaBbCc}\)   | mathbf "AaBbCc"   |
| `bbb "AaBbCc"` | \(\mathbb{AaBbCc}\)   | mathbb "AaBbCc"   |
| `cc "AaBbCc"`  | \(\mathcal{AaBbCc}\)  | mathcal "AaBbCc"  |
| `tt "AaBbCc"`  | \(\mathtt{AaBbCc}\)   | mathtt "AaBbCc"   |
| `fr "AaBbCc"`  | \(\mathfrak{AaBbCc}\) | mathfrak "AaBbCc" |
| `sf "AaBbCc"`  | \(\mathsf{AaBbCc}\)   | mathsf "AaBbCc"   |

<br/>

**Standard Functions:** sin, cos, tan, sec, csc, cot, arcsin, arccos, arctan, sinh, cosh, tanh, sech, csch, coth, exp, log, ln, det, dim, mod, gcd, lcm, lub, glb, min, max, f, g.

### Special Cases

* Matrices: `[[a,b],[c,d]]` yields to \(\begin{bmatrix} a & b \\ c & d \end{bmatrix}\)

* Column vectors: `((a),(b))` yields to \(\begin{pmatrix} a \\ b \end{pmatrix}\)

* Augmented matrices: `[[a,b,|,c],[d,e,|,f]]` yields to \(\left[\begin{array}{cc|c} a & b & c \\ d & e & f \end{array}\right]\)

* Matrices can be used for layout: `{(2x,+,17y,=,23),(x,-,y,=,5):}` yields  

\[ \left\{\begin{array}{ccccc} 2x & + & 17y & = & 23 \\ x & - & y & = & 5 \end{array}\right.\]

* Complex subscripts: `lim_(N->oo) sum_(i=0)^N` yields to \(\lim_{N \to \infty} \sum_{i=0}^{N}\)

* Subscripts must come before superscripts: `int_0^1 f(x)dx` yields to \(\int_{0}^{1} f(x),dx\)

* Derivatives: `f'(x) = dy/dx` yields \(f'(x) = \frac{dy}{dx}\)

  For variables other than x, y, z, or t you will need grouping symbols: `(dq)/(dp)` yields \(\frac{dq}{dp}\)

* Overbraces and underbraces:  `ubrace(1+2+3+4)_("4 terms")` yields \(\underbrace{1+2+3+4}_{\text{4 terms}}\)

  `obrace(1+2+3+4)^("4 terms")` yields \(\overbrace{1+2+3+4}^{\text{4 terms}}\)

* Attention: Always try to surround the `>` and `<` characters with spaces so that the HTML parser does not confuse them with opening or closing tags.


[Full syntax](https://asciimath.org/) is elsewhere.

## LaTeX

LaTeX is a software system for typesetting documents.  We support the mathematics environments between brackets, <code>\(...\)</code> for inline mathematics and <code>\[...\]</code> displayed mathematics.  This leaves dollars `$` for currency.


## Calculations

When activated in a particular question, calculations between <code>{@...@}</code> will be automatically completed for you, rather than reaching for an external calculator.

1. Type simple expressions using brackets for function arguments.
2. Note angles are always in radians.  E.g. try <code>{@cos(pi)@}</code> for example.
3. Answers are given as floating point numbers, and you should choose the precision needed.  E.g. <code>{@round(cos(2),3)@}</code>


