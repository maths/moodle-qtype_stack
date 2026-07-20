# Finite fields

[Finite Fields Computations in Maxima](https://sourceforge.net/p/maxima/code/ci/master/tree/share/contrib/gf/) can be done with the contributed `gf` package.  

Fields are of the form \(\mathbb{F}_{p}[x]/{m(x)}\), where \(p\) is a prime number and \(m(x)\) is an polynomial irreducible over \(\mathbb{F}_{p}\).  If the degree of \(m(x)\) is \(n\), the the finite field will contain \(p^n\) elements, each element being a polynomial of degree strictly less than \(n\), and all coefficients being in \(\{0,1,\ldots,p-1\}\). Such a field is called a _finite field_ or_Galois field_ of order \(p^n\), and is denoted \(\mathbb{F}_{p^n}\).  Note that although there are many different irreducible polynomials to choose from, if \(m(x)\) and \(n(x)\) are different polynomials irreducible over \(\mathbb{F}_p\)  and of the same degree, then the fields  \[ \mathbb{F}_{p}[x]/{m(x)} \] and \[ \mathbb{F}_{p}[x]/{n(x)} \] are isomorphic.

Given a prime number \(p\) and a polynomial \(m(x)\) you can create a field by using the command `gf_set_data(p, m(x))`, for example.

    gf_set_data(2,x^4+x+1);

returns

    "Structure [GF-DATA]"

As of July 2026, the STACK developers have not investigated what a "Structure [GF-DATA]" is or how that could be communicated between PHP and Maxima!  However, that communication is not always needed and so _some_ functionality of `gf` can be used in STACK questions.

One solution is to use the preamble (before `%_stack_preamble_end`) to ensure Maxima is setup wit use a particular finite filed.

For example,

```
m:x^4+x+1;
p:2;
/* The gf_set_data() function tried to return a LISP structure, which we can't capture.  Return true instead. */
f:(gf_set_data(p,m), true);
%_stack_preamble_end

p1 : x^3+x;
p2 : x^3+x^2+1;

ta1:gf_add(p1, p2);
```

In the above, the `gf_set_data()` function tries to return a LISP structure, which we can't capture.  By enclosing this in a block, we return true instead.  The function has done it's job in settup up Maxima for finite field use.  You can then use the following question text.


    <p>Add \({@p1@}\) to \({@p2@}\) in \(\mathbb{F}_{@2@}[x]/m(x)\) where \(m(x)={@m@}\).</p><p>[[input:ans1]] [[validation:ans1]]</p>

Notice the use of `gf_add()`, rather than regular use of `+`.  This means normal addition is still availble so that feedback can be provided.

An example question is available in

    stacklibrary/Topics/FiniteFields/gf_add.xml