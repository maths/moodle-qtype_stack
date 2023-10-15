# Hints

STACK contains a "formula sheet" of useful fragments which a teacher may wish to include in a consistent way.  This is achieved through the "hints" system.

Hints can be included in any [CASText](CASText.md).

To include a hint, use the syntax

    [[facts:tag]]

The "tag" is chosen from the list below.

## All supported fact sheets

### The Greek Alphabet

<code>[[facts:greek_alphabet]]</code>

||||
|--- |--- |--- |
|Upper case, \(\quad\)|lower case, \(\quad\)|name|
|\(A\)|\(\alpha\)|alpha|
|\(B\)|\(\beta\)|beta|
|\(\Gamma\)|\(\gamma\)|gamma|
|\(\Delta\)|\(\delta\)|delta|
|\(E\)|\(\epsilon\)|epsilon|
|\(Z\)|\(\zeta\)|zeta|
|\(H\)|\(\eta\)|eta|
|\(\Theta\)|\(\theta\)|theta|
|\(K\)|\(\kappa\)|kappa|
|\(M\)|\(\mu\)|mu|
|\(N\)|\( u\)|nu|
|\(\Xi\)|\(\xi\)|xi|
|\(O\)|\(o\)|omicron|
|\(\Pi\)|\(\pi\)|pi|
|\(I\)|\(\iota\)|iota|
|\(P\)|\(\rho\)|rho|
|\(\Sigma\)|\(\sigma\)|sigma|
|\(\Lambda\)|\(\lambda\)|lambda|
|\(T\)|\(\tau\)|tau|
|\(\Upsilon\)|\(\upsilon\)|upsilon|
|\(\Phi\)|\(\phi\)|phi|
|\(X\)|\(\chi\)|chi|
|\(\Psi\)|\(\psi\)|psi|
|\(\Omega\)|\(\omega\)|omega|


### Inequalities

<code>[[facts:alg_inequalities]]</code>

\[a>b \hbox{ means } a \hbox{ is greater than } b.\]
\[ a < b \hbox{ means } a \hbox{ is less than } b.\]
\[a\geq b \hbox{ means } a \hbox{ is greater than or equal to } b.\]
\[a\leq b \hbox{ means } a \hbox{ is less than or equal to } b.\]


### The Laws of Indices

<code>[[facts:alg_indices]]</code>

The following laws govern index manipulation:
\[a^ma^n = a^{m+n}\]
\[\frac{a^m}{a^n} = a^{m-n}\]
\[(a^m)^n = a^{mn}\]
\[a^0 = 1\]
\[a^{-m} = \frac{1}{a^m}\]
\[a^{\frac{1}{n}} = \sqrt[n]{a}\]
\[a^{\frac{m}{n}} = \left(\sqrt[n]{a}\right)^m\]


### The Laws of Logarithms

<code>[[facts:alg_logarithms]]</code>

For any base \(c>0\) with \(c \neq 1\):
\[\log_c(a) = b \mbox{, means } a = c^b\]
\[\log_c(a) + \log_c(b) = \log_c(ab)\]
\[\log_c(a) - \log_c(b) = \log_c\left(\frac{a}{b}\right)\]
\[n\log_c(a) = \log_c\left(a^n\right)\]
\[\log_c(1) = 0\]
\[\log_c(c) = 1\]
The formula for a change of base is:
\[\log_a(x) = \frac{\log_b(x)}{\log_b(a)}\]
Logarithms to base \(e\), denoted \(\log_e\) or alternatively \(\ln\) are called natural logarithms.  The letter \(e\) represents the exponential constant which is approximately \(2.718\).


### The Quadratic Formula

<code>[[facts:alg_quadratic_formula]]</code>

If we have a quadratic equation of the form:
\[ax^2 + bx + c = 0,\]
then the solution(s) to that equation given by the quadratic formula are:
\[x = \frac{-b \pm \sqrt{b^2 - 4ac}}{2a}.\]


### Partial Fractions

<code>[[facts:alg_partial_fractions]]</code>

Proper fractions occur with \[{\frac{P(x)}{Q(x)}}\]
when \(P\) and \(Q\) are polynomials with the degree of \(P\) less than the degree of \(Q\).  This this case, we proceed
as follows: write \(Q(x)\) in factored form,

* a <em>linear factor</em> \(ax+b\) in the denominator produces a partial fraction of the form \[{\frac{A}{ax+b}}.\]
* a <em>repeated linear factors</em> \((ax+b)^2\) in the denominator
produce partial fractions of the form \[{A\over ax+b}+{B\over (ax+b)^2}.\]
* a <em>quadratic factor</em> \(ax^2+bx+c\)
in the denominator produces a partial fraction of
the form \[{Ax+B\over ax^2+bx+c}\]
* <em>Improper fractions</em> require an additional
term which is a polynomial of degree \(n-d\) where \(n\) is
the degree of the numerator (i.e. \(P(x)\)) and \(d\) is the degree of
the denominator (i.e. \(Q(x)\)).



### Degrees and Radians

<code>[[facts:trig_degrees_radians]]</code>

\[
360^\circ= 2\pi \hbox{ radians},\quad
1^\circ={2\pi\over 360}={\pi\over 180}\hbox{ radians}
\]
\[
1 \hbox{ radian} = {180\over \pi} \hbox{ degrees}
\approx 57.3^\circ
\]


### Standard Trigonometric Values

<code>[[facts:trig_standard_values]]</code>


\[\sin(45^\circ)={1\over \sqrt{2}}, \qquad \cos(45^\circ) = {1\over \sqrt{2}},\qquad
\tan( 45^\circ)=1
\]
\[
\sin (30^\circ)={1\over 2}, \qquad \cos (30^\circ)={\sqrt{3}\over 2},\qquad
\tan (30^\circ)={1\over \sqrt{3}}
\]
\[
\sin (60^\circ)={\sqrt{3}\over 2}, \qquad \cos (60^\circ)={1\over 2},\qquad
\tan (60^\circ)={ \sqrt{3}}
\]


### Standard Trigonometric Identities

<code>[[facts:trig_standard_identities]]</code>

\[\sin(a\pm b)\ = \  \sin(a)\cos(b)\ \pm\  \cos(a)\sin(b)\]
 \[\cos(a\ \pm\ b)\ = \  \cos(a)\cos(b)\ \mp \sin(a)\sin(b)\]
 \[\tan (a\ \pm\ b)\ = \  {\tan (a)\ \pm\ \tan (b)\over1\ \mp\ \tan (a)\tan (b)}\]
 \[ 2\sin(a)\cos(b)\ = \  \sin(a+b)\ +\ \sin(a-b)\]
 \[ 2\cos(a)\cos(b)\ = \  \cos(a-b)\ +\ \cos(a+b)\]
 \[ 2\sin(a)\sin(b) \ = \  \cos(a-b)\ -\ \cos(a+b)\]
 \[ \sin^2(a)+\cos^2(a)\ = \  1\]
 \[ 1+{\rm cot}^2(a)\ = \  {\rm cosec}^2(a),\quad \tan^2(a) +1 \ = \  \sec^2(a)\]
 \[ \cos(2a)\ = \  \cos^2(a)-\sin^2(a)\ = \  2\cos^2(a)-1\ = \  1-2\sin^2(a)\]
 \[ \sin(2a)\ = \  2\sin(a)\cos(a)\]
 \[ \sin^2(a) \ = \  {1-\cos (2a)\over 2}, \qquad \cos^2(a)\ = \  {1+\cos(2a)\over 2}\]


### Hyperbolic Functions

<code>[[facts:hyp_functions]]</code>

Hyperbolic functions have similar properties to trigonometric functions but can be represented in exponential form as follows:
 \[ \cosh(x)      = \frac{e^x+e^{-x}}{2}, \qquad \sinh(x)=\frac{e^x-e^{-x}}{2} \]
 \[ \tanh(x)      = \frac{\sinh(x)}{\cosh(x)} = \frac{{e^x-e^{-x}}}{e^x+e^{-x}} \]
 \[ {\rm sech}(x) ={1\over \cosh(x)}={2\over {\rm e}^x+{\rm e}^{-x}}, \qquad  {\rm cosech}(x)= {1\over \sinh(x)}={2\over {\rm e}^x-{\rm e}^{-x}} \]
 \[ {\rm coth}(x) ={\cosh(x)\over \sinh(x)} = {1\over {\rm tanh}(x)} ={{\rm e}^x+{\rm e}^{-x}\over {\rm e}^x-{\rm e}^{-x}}\]


### Hyperbolic Identities

<code>[[facts:hyp_identities]]</code>

The similarity between the way hyperbolic and trigonometric functions behave is apparent when observing some basic hyperbolic identities:
  \[{\rm e}^x=\cosh(x)+\sinh(x), \quad {\rm e}^{-x}=\cosh(x)-\sinh(x)\]
  \[\cosh^2(x) -\sinh^2(x) = 1\]
  \[1-{\rm tanh}^2(x)={\rm sech}^2(x)\]
  \[{\rm coth}^2(x)-1={\rm cosech}^2(x)\]
  \[\sinh(x\pm y)=\sinh(x)\ \cosh(y)\ \pm\ \cosh(x)\ \sinh(y)\]
  \[\cosh(x\pm y)=\cosh(x)\ \cosh(y)\ \pm\ \sinh(x)\ \sinh(y)\]
  \[\sinh(2x)=2\,\sinh(x)\cosh(x)\]
  \[\cosh(2x)=\cosh^2(x)+\sinh^2(x)\]
  \[\cosh^2(x)={\cosh(2x)+1\over 2}\]
  \[\sinh^2(x)={\cosh(2x)-1\over 2}\]


### Inverse Hyperbolic Functions

<code>[[facts:hyp_inverse_functions]]</code>

\[\cosh^{-1}(x)=\ln\left(x+\sqrt{x^2-1}\right) \quad \mbox{ for } x\geq 1\]
 \[\sinh^{-1}(x)=\ln\left(x+\sqrt{x^2+1}\right)\]
 \[\tanh^{-1}(x) = \frac{1}{2}\ln\left({1+x\over 1-x}\right) \quad \mbox{ for } -1< x < 1\]


### Standard Derivatives

<code>[[facts:calc_diff_standard_derivatives]]</code>

The following table displays the derivatives of some standard functions.  It is useful to learn these standard derivatives as they are used frequently in calculus.

|\(f(x)\)|\(f'(x)\)|
|--- |--- |
|\(k\), constant|\(0\)|
|\(x^n\), any constant \(n\)|\(nx^{n-1}\)|
|\(e^x\)|\(e^x\)|
|\(\ln(x)=\log_{\rm e}(x)\)|\(\frac{1}{x}\)|
|\(\sin(x)\)|\(\cos(x)\)|
|\(\cos(x)\)|\(-\sin(x)\)|
|\(\tan(x) = \frac{\sin(x)}{\cos(x)}\)|\(\sec^2(x)\)|
|\(cosec(x)=\frac{1}{\sin(x)}\)|\(-cosec(x)\cot(x)\)|
|\(\sec(x)=\frac{1}{\cos(x)}\)|\(\sec(x)\tan(x)\)|
|\(\cot(x)=\frac{\cos(x)}{\sin(x)}\)|\(-cosec^2(x)\)|
|\(\cosh(x)\)|\(\sinh(x)\)|
|\(\sinh(x)\)|\(\cosh(x)\)|
|\(\tanh(x)\)|\(sech^2(x)\)|
|\(sech(x)\)|\(-sech(x)\tanh(x)\)|
|\(cosech(x)\)|\(-cosech(x)\coth(x)\)|
|\(coth(x)\)|\(-cosech^2(x)\)|

 \[ \frac{d}{dx}\left(\sin^{-1}(x)\right) =  \frac{1}{\sqrt{1-x^2}}\]
 \[ \frac{d}{dx}\left(\cos^{-1}(x)\right) =  \frac{-1}{\sqrt{1-x^2}}\]
 \[ \frac{d}{dx}\left(\tan^{-1}(x)\right) =  \frac{1}{1+x^2}\]
 \[ \frac{d}{dx}\left(\cosh^{-1}(x)\right) =  \frac{1}{\sqrt{x^2-1}}\]
 \[ \frac{d}{dx}\left(\sinh^{-1}(x)\right) =  \frac{1}{\sqrt{x^2+1}}\]
 \[ \frac{d}{dx}\left(\tanh^{-1}(x)\right) =  \frac{1}{1-x^2}\]



### The Linearity Rule for Differentiation

<code>[[facts:calc_diff_linearity_rule]]</code>

\[{{\rm d}\,\over {\rm d}x}\big(af(x)+bg(x)\big)=a{{\rm d}f(x)\over {\rm d}x}+b{{\rm d}g(x)\over {\rm d}x}\quad a,b {\rm\  constant.}\]


### The Product Rule

<code>[[facts:calc_product_rule]]</code>

The following rule allows one to differentiate functions which are
multiplied together.  Assume that we wish to differentiate \(f(x)g(x)\) with respect to \(x\).
\[ \frac{\mathrm{d}}{\mathrm{d}{x}} \big(f(x)g(x)\big) = f(x) \cdot \frac{\mathrm{d} g(x)}{\mathrm{d}{x}}  + g(x)\cdot \frac{\mathrm{d} f(x)}{\mathrm{d}{x}},\] or, using alternative notation, \[ (f(x)g(x))' = f'(x)g(x)+f(x)g'(x). \]


### The Quotient Rule

<code>[[facts:calc_quotient_rule]]</code>

The quotient rule for differentiation states that for any two differentiable functions \(f(x)\) and \(g(x)\),
 \[\frac{d}{dx}\left(\frac{f(x)}{g(x)}\right)=\frac{g(x)\cdot\frac{df(x)}{dx}\ \ - \ \ f(x)\cdot \frac{dg(x)}{dx}}{g(x)^2}. \]


### The Chain Rule

<code>[[facts:calc_chain_rule]]</code>

The following rule allows one to find the derivative of a composition of two functions.
Assume we have a function \(f(g(x))\), then defining \(u=g(x)\), the derivative with respect to \(x\) is given by:
\[\frac{df(g(x))}{dx} = \frac{dg(x)}{dx}\cdot\frac{df(u)}{du}.\]
Alternatively, we can write:
\[\frac{df(x)}{dx} = f'(g(x))\cdot g'(x).\]



### Calculus rules

<code>[[facts:calc_rules]]</code>

<b>The Product Rule</b><br />The following rule allows one to differentiate functions which are
multiplied together.  Assume that we wish to differentiate \(f(x)g(x)\) with respect to \(x\).
\[ \frac{\mathrm{d}}{\mathrm{d}{x}} \big(f(x)g(x)\big) = f(x) \cdot \frac{\mathrm{d} g(x)}{\mathrm{d}{x}}  + g(x)\cdot \frac{\mathrm{d} f(x)}{\mathrm{d}{x}},\] or, using alternative notation, \[ (f(x)g(x))' = f'(x)g(x)+f(x)g'(x). \]
<b>The Quotient Rule</b><br />The quotient rule for differentiation states that for any two differentiable functions \(f(x)\) and \(g(x)\),
\[\frac{d}{dx}\left(\frac{f(x)}{g(x)}\right)=\frac{g(x)\cdot\frac{df(x)}{dx}\ \ - \ \ f(x)\cdot \frac{dg(x)}{dx}}{g(x)^2}. \]
<b>The Chain Rule</b><br />The following rule allows one to find the derivative of a composition of two functions.
Assume we have a function \(f(g(x))\), then defining \(u=g(x)\), the derivative with respect to \(x\) is given by:
\[\frac{df(g(x))}{dx} = \frac{dg(x)}{dx}\cdot\frac{df(u)}{du}.\]
Alternatively, we can write:
\[\frac{df(x)}{dx} = f'(g(x))\cdot g'(x).\]



### Standard Integrals

<code>[[facts:calc_int_standard_integrals]]</code>



\[\int k\ dx = kx +c, \mbox{ where k is constant.}\]
\[\int x^n\ dx  = \frac{x^{n+1}}{n+1}+c, \quad (n\ne -1)\]
\[\int x^{-1}\ dx = \int {\frac{1}{x}}\ dx = \ln(|x|)+c = \ln(k|x|) = \left\{\matrix{\ln(x)+c & x>0\cr
\ln(-x)+c & x<0\cr}\right.\]

|\(f(x)\)|\(\int f(x)\ dx\)||
|--- |--- |--- |
|\(e^x\)|\(e^x+c\)||
|\(\cos(x)\)|\(\sin(x)+c\)||
|\(\sin(x)\)|\(-\cos(x)+c\)||
|\(\tan(x)\)|\(\ln(\sec(x))+c\)|\(-\frac{\pi}{2} < x < \frac{\pi}{2}\)|
|\(\sec x\)|\(\ln (\sec(x)+\tan(x))+c\)|\( -{\pi\over 2}< x < {\frac{\pi}{2}}\)|
|\(\mbox{cosec}(x)\)|\(\ln (\mbox{cose}c(x)-\cot(x))+c\quad\)   |\(0 < x < \pi\)|
|cot\(\,x\)|\(\ln(\sin(x))+c\)|\(0< x< \pi\)|
|\(\cosh(x)\)|\(\sinh(x)+c\)||
|\(\sinh(x)\)|\(\cosh(x) + c\)||
|\(\tanh(x)\)|\(\ln(\cosh(x))+c\)||
|\(\mbox{coth}(x)\)|\(\ln(\sinh(x))+c \)|\(x>0\)|
|\({1\over x^2+a^2}\)|\({1\over a}\tan^{-1}{x\over a}+c\)|\(a>0\)|
|\({1\over x^2-a^2}\)|\({1\over 2a}\ln{x-a\over x+a}+c\)|\(|x|>a>0\)|
|\({1\over a^2-x^2}\)|\({1\over 2a}\ln{a+x\over a-x}+c\)|\(|x|\)|
|\(\frac{1}{\sqrt{x^2+a^2}}\)|\(\sinh^{-1}\left(\frac{x}{a}\right) + c\)|\(a>0\)|
|\({1\over \sqrt{x^2-a^2}}\)|\(\cosh^{-1}\left(\frac{x}{a}\right) + c\)|\(x\geq a > 0\)|
|\({1\over \sqrt{x^2+k}}\)|\(\ln (x+\sqrt{x^2+k})+c\)||
|\({1\over \sqrt{a^2-x^2}}\)|\(\sin^{-1}\left(\frac{x}{a}\right)+c\)|\(-a\leq x\leq a\)|



### The Linearity Rule for Integration

<code>[[facts:calc_int_linearity_rule]]</code>

\[\int \left(af(x)+bg(x)\right){\rm d}x = a\int\!\!f(x)\,{\rm d}x
\,+\,b\int \!\!g(x)\,{\rm d}x, \quad (a,b \, \, {\rm constant.})
\]


### Integration by Substitution

<code>[[facts:calc_int_methods_substitution]]</code>

\[
\int f(u){{\rm d}u\over {\rm d}x}{\rm d}x=\int f(u){\rm d}u
\quad\hbox{and}\quad \int_a^bf(u){{\rm d}u\over {\rm d}x}\,{\rm
d}x = \int_{u(a)}^{u(b)}f(u){\rm d}u.
\]


### Integration by Parts

<code>[[facts:calc_int_methods_parts]]</code>

\[
\int_a^b u{{\rm d}v\over {\rm d}x}{\rm d}x=\left[uv\right]_a^b-
\int_a^b{{\rm d}u\over {\rm d}x}v\,{\rm d}x\]
or alternatively: \[\int_a^bf(x)g(x)\,{\rm d}x=\left[f(x)\,\int
g(x){\rm d}x\right]_a^b -\int_a^b{{\rm d}f\over {\rm
d}x}\left\{\int g(x){\rm d}x\right\}{\rm d}x.\]


### Integration by Parts

<code>[[facts:calc_int_methods_parts_indefinite]]</code>

\[
\int u{{\rm d}v\over {\rm d}x}{\rm d}x=uv- \int{{\rm d}u\over {\rm d}x}v\,{\rm d}x\]
or alternatively: \[\int f(x)g(x)\,{\rm d}x=f(x)\,\int
g(x){\rm d}x -\int {{\rm d}f\over {\rm d}x}\left\{\int g(x){\rm d}x\right\}{\rm d}x.\]


