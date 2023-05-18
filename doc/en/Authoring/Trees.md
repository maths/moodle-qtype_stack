# Trees

It is sometime very useful to display the tree structure of an algebraic expression to a student.

For example, the HTML code for the tree of \(1+2x^3\) is given below.

```
<ul class='tree'>
  <li><code>+</code>
  <ul>
    <li><span class='atom'>\(1\)</span></li>
    <li><code>*</code>
      <ul><li><span class='atom'>\(2\)</span></li>
      <li><code>^</code>
      <ul>
        <li><span class='atom'>\(x\)</span></li>
        <li><span class='atom'>\(3\)</span></li>
      </ul>
      </li>
    </ul>
    </li>
  </ul>
  </li>
</ul>
```
This is displayed as follows.

<p>
<figure>
<ul class='tree'>
  <li><code>+</code>
  <ul>
    <li><span class='atom'>\(1\)</span></li>
    <li><code>*</code>
      <ul><li><span class='atom'>\(2\)</span></li>
      <li><code>^</code>
      <ul>
        <li><span class='atom'>\(x\)</span></li>
        <li><span class='atom'>\(3\)</span></li>
      </ul>
      </li>
    </ul>
    </li>
  </ul>
  </li>
</ul>
</figure>
</p>

The tree is displayed in pure HTML using unordered lists `<ul>` and styled with CSS via the `<ul class='tree'>`.  Therefore, such trees could be written in HTML by hand.

STACK provides a function `disptree` to generate the above tree diagram from a Maxima expression.  For example, use `{@disptree(1+2+pi*x^3)@}` in castext.  This function generates a string representing the tree of that expression, and is not an inert function.

STACK provides a function `treestop` to stop traversing the tree, and use the LaTeX of the subexpression instead.  For example in `disptree(1/treestop(1+x^2)=4)` STACK produces a tree but one node has \(1+x^2\), rather than also showing the subtree of this expression as well.  This gives the user some control over the complexity of tree and what to display.

<p>
<figure>
<ul class='tree'>
  <li><code>=</code>
  <ul>
    <li><code>/</code>
    <ul>
      <li><span class='atom'>\(1\)</span></li>
      <li><span class='atom'>\(1+x^2\)</span></li>
    </ul>
    </li>
    <li><span class='atom'>\(4\)</span></li>
  </ul>
  </li>
</ul>
</figure>
</p>

Note, because of the HTML generated, and the LaTeX inside the tree HTML, you cannot embed these trees inside displayed LaTeX using `\[ ... \]`.  The only way to display a tree is using `{@disptree(....)@}` as an isolated mathematical expression.

## Examples

To see the tree structure of the binomial theorem (with `simp:false`)

`{@disptree(apply("+",map(lambda([ex],binomial(n,ex)*x^ex), ev(makelist(k,k,0,5),simp))))@}`

## Educational value of trees

Seeing the explicit tree structure of an expression has significant educational value at certain moments.  E.g. students want to type `x=a or b` as an answer. The following illustrates why they need to write `x=(a or b)` or `x=a or x=b` instead!

```
p1:x=a nounor b;
p2:x=(a nounor b);
```
with the following castext: `{@p1@}: {@disptree(p1)@}  <br/> {@p2@}: {@disptree(p2)@}` (with `simp:false`).

## Styles

In order to correctly display list items within the `<ul class='tree'>` list, additional styling is needed.  All list items must be styled with one of the following tags.  The Maxima code ensures that operator nodes are styled slightly differently from atoms/terminal nodes. Some operators, such as integrals and sums, have special style rules applied.

1. `<code>` is used to display operators as html code.
1. `<span class='op'>` is used to display operators as LaTeX.
2. `<span class='atom'>` is used to display atoms and terminal nodes.
3. `<span class='cell'>` has minimal style, and is not used by the Maxima code.  This is intended for general use.

The code does its best to respest the LaTeX output.  If you create special tex rules using `texput` you also have to tell the tree generation code to look for this rule.  STACK has a set `tree_texlist` of operators to which special rules apply.  To add a rule use the following.

     texput(boo, "\\diamond");
     tree_texlist:union(tree_texlist,{"boo"});

Then, the operator `boo` will be typeset as \(\diamond\) in tree output, as well as in tex output.  E.g. try the following castext: `{@disptree(boo(a,b))@}`.
