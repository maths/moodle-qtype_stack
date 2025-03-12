<h1> Hints </h1>

STACK contains a "formula sheet" of useful fragments which a teacher may wish to include in a consistent way.  This is achieved through the "hints" system.

Hints can be included in any [CASText](../../Authoring/CASText.md).

To include a hint, use the syntax

    [[facts:tag]]

The "tag" is chosen from the list below.  Note, these hints are basic HTML strings and are stored in the language files.

<h2> All supported fact sheets </h2>

### The Greek Alphabet

<code>[[facts:greek_alphabet]]</code>

<table>
<thead>
<tr>
<th>Upper case</th>
<th>lower case</th>
<th>name</th>
</tr>
</thead>
<tbody>
<tr>
<td>\(A\)</td>
<td>\(\alpha\)</td>
<td>alpha</td>
</tr>
<tr>
<td>\(B\)</td>
<td>\(\beta\)</td>
<td>beta</td>
</tr>
<tr>
<td>\(\Gamma\)</td>
<td>\(\gamma\)</td>
<td>gamma</td>
</tr>
<tr>
<td>\(\Delta\)</td>
<td>\(\delta\)</td>
<td>delta</td>
</tr>
<tr>
<td>\(E\)</td>
<td>\(\epsilon\)</td>
<td>epsilon</td>
</tr>
<tr>
<td>\(Z\)</td>
<td>\(\zeta\)</td>
<td>zeta</td>
</tr>
<tr>
<td>\(H\)</td>
<td>\(\eta\)</td>
<td>eta</td>
</tr>
<tr>
<td>\(\Theta\)</td>
<td>\(\theta\)</td>
<td>theta</td>
</tr>
<tr>
<td>\(K\)</td>
<td>\(\kappa\)</td>
<td>kappa</td>
</tr>
<tr>
<td>\(M\)</td>
<td>\(\mu\)</td>
<td>mu</td>
</tr>
<tr>
<td>\(N\)</td>
<td>\( u\)</td>
<td>nu</td>
</tr>
<tr>
<td>\(\Xi\)</td>
<td>\(\xi\)</td>
<td>xi</td>
</tr>
<tr>
<td>\(O\)</td>
<td>\(o\)</td>
<td>omicron</td>
</tr>
<tr>
<td>\(\Pi\)</td>
<td>\(\pi\)</td>
<td>pi</td>
</tr>
<tr>
<td>\(I\)</td>
<td>\(\iota\)</td>
<td>iota</td>
</tr>
<tr>
<td>\(P\)</td>
<td>\(\rho\)</td>
<td>rho</td>
</tr>
<tr>
<td>\(\Sigma\)</td>
<td>\(\sigma\)</td>
<td>sigma</td>
</tr>
<tr>
<td>\(\Lambda\)</td>
<td>\(\lambda\)</td>
<td>lambda</td>
</tr>
<tr>
<td>\(T\)</td>
<td>\(\tau\)</td>
<td>tau</td>
</tr>
<tr>
<td>\(\Upsilon\)</td>
<td>\(\upsilon\)</td>
<td>upsilon</td>
</tr>
<tr>
<td>\(\Phi\)</td>
<td>\(\phi\)</td>
<td>phi</td>
</tr>
<tr>
<td>\(X\)</td>
<td>\(\chi\)</td>
<td>chi</td>
</tr>
<tr>
<td>\(\Psi\)</td>
<td>\(\psi\)</td>
<td>psi</td>
</tr>
<tr>
<td>\(\Omega\)</td>
<td>\(\omega\)</td>
<td>omega</td>
</tr>
</tbody>
</table>



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
\[\log_c(a) = b \text{, means } a = c^b\]
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

Fractions \[{\frac{P(x)}{Q(x)}}\]
when \(P\) and \(Q\) are polynomials with the degree of \(P\) less than the degree of \(Q\) are called <em>proper algebraic fractions</em>.
To re-write this as <em>partial fractions</em> write \(Q(x)\) in factored form,
<ul>
<li>a <em>linear factor</em> \(ax+b\) in the denominator produces a partial fraction of the form \[{\frac{A}{ax+b}}.\]</li>
<li>a <em>repeated linear factors</em> \((ax+b)^2\) in the denominator
produce partial fractions of the form \[{A\over ax+b}+{B\over (ax+b)^2}.\]</li>
<li>a <em>quadratic factor</em> \(ax^2+bx+c\) in the denominator produces a partial fraction of the form \[{Ax+B\over ax^2+bx+c}\]</li>
<li><em>Improper fractions</em> require an additional term which is a polynomial of degree \(n-d\) where \(n\) is the degree of the numerator (i.e. \(P(x)\)) and \(d\) is the degree of the denominator (i.e. \(Q(x)\)).</li>
</ul>


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

\[\cosh^{-1}(x)=\ln\left(x+\sqrt{x^2-1}\right) \quad \text{ for } x\geq 1\]
 \[\sinh^{-1}(x)=\ln\left(x+\sqrt{x^2+1}\right)\]
 \[\tanh^{-1}(x) = \frac{1}{2}\ln\left({1+x\over 1-x}\right) \quad \text{ for } -1< x < 1\]


### Standard Derivatives

<code>[[facts:calc_diff_standard_derivatives]]</code>

<p>The following table displays the derivatives of some standard functions.  It is useful to learn these standard derivatives as they are used frequently in calculus.</p>
<table style="padding-right:5%;width: 60%;">
<thead>
<tr>
<th>\(f(x)\)</th>
<th>\(f'(x)\)</th>
</tr>
</thead>
<tbody>
<tr>
<td>\(k\), constant</td>
<td>\(0\)</td>
</tr>
<tr>
<td>\(x^n\), any constant \(n\)</td>
<td>\(nx^{n-1}\)</td>
</tr>
<tr>
<td>\(e^x\)</td>
<td>\(e^x\)</td>
</tr>
<tr>
<td>\(\ln(x)=\log_{\rm e}(x)\)</td>
<td>\(\frac{1}{x}\)</td>
</tr>
<tr>
<td>\(\sin(x)\)</td>
<td>\(\cos(x)\)</td>
</tr>
<tr>
<td>\(\cos(x)\)</td>
<td>\(-\sin(x)\)</td>
</tr>
<tr>
<td>\(\tan(x) = \frac{\sin(x)}{\cos(x)}\)</td>
<td>\(\sec^2(x)\)</td>
</tr>
<tr>
<td>\(cosec(x)=\frac{1}{\sin(x)}\)</td>
<td>\(-cosec(x)\cot(x)\)</td>
</tr>
<tr>
<td>\(\sec(x)=\frac{1}{\cos(x)}\)</td>
<td>\(\sec(x)\tan(x)\)</td>
</tr>
<tr>
<td>\(\cot(x)=\frac{\cos(x)}{\sin(x)}\)</td>
<td>\(-cosec^2(x)\)</td>
</tr>
<tr>
<td>\(\cosh(x)\)</td>
<td>\(\sinh(x)\)</td>
</tr>
<tr>
<td>\(\sinh(x)\)</td>
<td>\(\cosh(x)\)</td>
</tr>
<tr>
<td>\(\tanh(x)\)</td>
<td>\(sech^2(x)\)</td>
</tr>
<tr>
<td>\(sech(x)\)</td>
<td>\(-sech(x)\tanh(x)\)</td>
</tr>
<tr>
<td>\(cosech(x)\)</td>
<td>\(-cosech(x)\coth(x)\)</td>
</tr>
<tr>
<td>\(coth(x)\)</td>
<td>\(-cosech^2(x)\)</td>
</tr>
</tbody>
</table>
<p> \[ \frac{\mathrm{d}}{\mathrm{d}x}\left(\sin^{-1}(x)\right) =  \frac{1}{\sqrt{1-x^2}}\]
 \[ \frac{\mathrm{d}}{\mathrm{d}x}\left(\cos^{-1}(x)\right) =  \frac{-1}{\sqrt{1-x^2}}\]
 \[ \frac{\mathrm{d}}{\mathrm{d}x}\left(\tan^{-1}(x)\right) =  \frac{1}{1+x^2}\]
 \[ \frac{\mathrm{d}}{\mathrm{d}x}\left(\cosh^{-1}(x)\right) =  \frac{1}{\sqrt{x^2-1}}\]
 \[ \frac{\mathrm{d}}{\mathrm{d}x}\left(\sinh^{-1}(x)\right) =  \frac{1}{\sqrt{x^2+1}}\]
 \[ \frac{\mathrm{d}}{\mathrm{d}x}\left(\tanh^{-1}(x)\right) =  \frac{1}{1-x^2}\]</p>



### The Linearity Rule for Differentiation

<code>[[facts:calc_diff_linearity_rule]]</code>

\[{\mathrm{d}\,\over \mathrm{d}x}\big(af(x)+bg(x)\big)=a{\mathrm{d}f(x)\over \mathrm{d}x}+b{\mathrm{d}g(x)\over \mathrm{d}x}\quad a,b {\rm\  constant.}\]


### The Product Rule

<code>[[facts:calc_product_rule]]</code>

The following rule allows one to differentiate functions which are
multiplied together.  Assume that we wish to differentiate \(f(x)g(x)\) with respect to \(x\).
\[ \frac{\mathrm{d}}{\mathrm{d}{x}} \big(f(x)g(x)\big) = f(x) \cdot \frac{\mathrm{d} g(x)}{\mathrm{d}{x}}  + g(x)\cdot \frac{\mathrm{d} f(x)}{\mathrm{d}{x}},\] or, using alternative notation, \[ (f(x)g(x))' = f'(x)g(x)+f(x)g'(x). \]


### The Quotient Rule

<code>[[facts:calc_quotient_rule]]</code>

The quotient rule for differentiation states that for any two differentiable functions \(f(x)\) and \(g(x)\),
 \[\frac{\mathrm{d}}{\mathrm{d}x}\left(\frac{f(x)}{g(x)}\right)=\frac{g(x)\cdot\frac{\mathrm{d}f(x)}{\mathrm{d}x}\ \ - \ \ f(x)\cdot \frac{\mathrm{d}g(x)}{\mathrm{d}x}}{g(x)^2}. \]


### The Chain Rule

<code>[[facts:calc_chain_rule]]</code>

The following rule allows one to find the derivative of a composition of two functions.
Assume we have a function \(f(g(x))\), then defining \(u=g(x)\), the derivative with respect to \(x\) is given by:
\[\frac{\mathrm{d}f(g(x))}{\mathrm{d}x} = \frac{\mathrm{d}g(x)}{\mathrm{d}x}\cdot\frac{\mathrm{d}f(u)}{\mathrm{d}u}.\]
Alternatively, we can write:
\[\frac{\mathrm{d}f(x)}{\mathrm{d}x} = f'(g(x))\cdot g'(x).\]



### Calculus rules

<code>[[facts:calc_rules]]</code>

<b>The Product Rule</b><br />The following rule allows one to differentiate functions which are
multiplied together.  Assume that we wish to differentiate \(f(x)g(x)\) with respect to \(x\).
\[ \frac{\mathrm{d}}{\mathrm{d}{x}} \big(f(x)g(x)\big) = f(x) \cdot \frac{\mathrm{d} g(x)}{\mathrm{d}{x}}  + g(x)\cdot \frac{\mathrm{d} f(x)}{\mathrm{d}{x}},\] or, using alternative notation, \[ (f(x)g(x))' = f'(x)g(x)+f(x)g'(x). \]
<b>The Quotient Rule</b><br />The quotient rule for differentiation states that for any two differentiable functions \(f(x)\) and \(g(x)\),
\[\frac{\mathrm{d}}{\mathrm{d}x}\left(\frac{f(x)}{g(x)}\right)=\frac{g(x)\cdot\frac{\mathrm{d}f(x)}{\mathrm{d}x}\ \ - \ \ f(x)\cdot \frac{\mathrm{d}g(x)}{\mathrm{d}x}}{g(x)^2}. \]
<b>The Chain Rule</b><br />The following rule allows one to find the derivative of a composition of two functions.
Assume we have a function \(f(g(x))\), then defining \(u=g(x)\), the derivative with respect to \(x\) is given by:
\[\frac{\mathrm{d}f(g(x))}{\mathrm{d}x} = \frac{\mathrm{d}g(x)}{\mathrm{d}x}\cdot\frac{\mathrm{d}f(u)}{\mathrm{d}u}.\]
Alternatively, we can write:
\[\frac{\mathrm{d}f(x)}{\mathrm{d}x} = f'(g(x))\cdot g'(x).\]



### Standard Integrals

<code>[[facts:calc_int_standard_integrals]]</code>


<p>\[\int k\ \mathrm{d}x = kx +c, \text{ where } k \text{ is constant.}\]
\[\int x^n\ \mathrm{d}x  = \frac{x^{n+1}}{n+1}+c, \quad (n\ne -1)\]
\[\int x^{-1}\ \mathrm{d}x = \int {\frac{1}{x}}\ \mathrm{d}x = \ln(|x|)+c = \ln(k|x|)\]</p>
<table style="padding-right:5%;width: 60%;">
<thead>
<tr>
<th>\(f(x)\)</th>
<th>\(\int f(x)\ \mathrm{d}x\)</th>
<th></th>
</tr>
</thead>
<tbody>
<tr>
<td>\(e^x\)</td>
<td>\(e^x+c\)</td>
<td></td>
</tr>
<tr>
<td>\(\cos(x)\)</td>
<td>\(\sin(x)+c\)</td>
<td></td>
</tr>
<tr>
<td>\(\sin(x)\)</td>
<td>\(-\cos(x)+c\)</td>
<td></td>
</tr>
<tr>
<td>\(\tan(x)\)</td>
<td>\(\ln(\sec(x))+c\)</td>
<td>\(-\frac{\pi}{2} &lt; x &lt; \frac{\pi}{2}\)</td>
</tr>
<tr>
<td>\(\sec x\)</td>
<td>\(\ln (\sec(x)+\tan(x))+c\)</td>
<td>\( -{\pi\over 2}&lt; x &lt; {\frac{\pi}{2}}\)</td>
</tr>
<tr>
<td>\(\text{cosec}(x)\)</td>
<td>\(\ln (\text{cose}c(x)-\cot(x))+c\quad\)</td>
<td>\(0 &lt; x &lt; \pi\)</td>
</tr>
<tr>
<td>cot(\x\)</td>
<td>\(\ln(\sin(x))+c\)</td>
<td>\(0&lt; x&lt; \pi\)</td>
</tr>
<tr>
<td>\(\cosh(x)\)</td>
<td>\(\sinh(x)+c\)</td>
<td></td>
</tr>
<tr>
<td>\(\sinh(x)\)</td>
<td>\(\cosh(x) + c\)</td>
<td></td>
</tr>
<tr>
<td>\(\tanh(x)\)</td>
<td>\(\ln(\cosh(x))+c\)</td>
<td></td>
</tr>
<tr>
<td>\(\text{coth}(x)\)</td>
<td>\(\ln(\sinh(x))+c \)</td>
<td>\(x&gt;0\)</td>
</tr>
<tr>
<td>\({1\over x^2+a^2}\)</td>
<td>\({1\over a}\tan^{-1}{x\over a}+c\)</td>
<td>\(a&gt;0\)</td>
</tr>
<tr>
<td>\({1\over x^2-a^2}\)</td>
<td>\({1\over 2a}\ln{x-a\over x+a}+c\)</td>
<td>\(x > a >0\)</td>
</tr>
<tr>
<td>\({1\over a^2-x^2}\)</td>
<td>\({1\over 2a}\ln{a+x\over a-x}+c\)</td>
<td>\(a > x >0\)</td>
</tr>
<tr>
<td>\(\frac{1}{\sqrt{x^2+a^2}}\)</td>
<td>\(\sinh^{-1}\left(\frac{x}{a}\right) + c\)</td>
<td>\(a&gt;0\)</td>
</tr>
<tr>
<td>\({1\over \sqrt{x^2-a^2}}\)</td>
<td>\(\cosh^{-1}\left(\frac{x}{a}\right) + c\)</td>
<td>\(x\geq a &gt; 0\)</td>
</tr>
<tr>
<td>\({1\over \sqrt{x^2+k}}\)</td>
<td>\(\ln (x+\sqrt{x^2+k})+c\)</td>
<td></td>
</tr>
<tr>
<td>\({1\over \sqrt{a^2-x^2}}\)</td>
<td>\(\sin^{-1}\left(\frac{x}{a}\right)+c\)</td>
<td>\(-a\leq x\leq a\)</td>
</tr>
</tbody>
</table>



### The Linearity Rule for Integration

<code>[[facts:calc_int_linearity_rule]]</code>

\[\int \left(af(x)+bg(x)\right)\mathrm{d}x = a\int\!\!f(x)\,\mathrm{d}x
\,+\,b\int \!\!g(x)\,\mathrm{d}x, \quad (a,b \, \, {\rm constant.})
\]


### Integration by Substitution

<code>[[facts:calc_int_methods_substitution]]</code>

\[
\int f(u){\mathrm{d}u\over \mathrm{d}x}\mathrm{d}x=\int f(u)\mathrm{d}u
\quad\hbox{and}\quad \int_a^bf(u){\mathrm{d}u\over \mathrm{d}x}\,{\rm
d}x = \int_{u(a)}^{u(b)}f(u)\mathrm{d}u.
\]


### Integration by Parts

<code>[[facts:calc_int_methods_parts]]</code>

\[
\int_a^b u{\mathrm{d}v\over \mathrm{d}x}\mathrm{d}x=\left[uv\right]_a^b-
\int_a^b{\mathrm{d}u\over \mathrm{d}x}v\,\mathrm{d}x\]
or alternatively: \[\int_a^bf(x)g(x)\,\mathrm{d}x=\left[f(x)\,\int
g(x)\mathrm{d}x\right]_a^b -\int_a^b{\mathrm{d}f\over {\rm
d}x}\left\{\int g(x)\mathrm{d}x\right\}\mathrm{d}x.\]


### Integration by Parts

<code>[[facts:calc_int_methods_parts_indefinite]]</code>

\[
\int u{\mathrm{d}v\over \mathrm{d}x}\mathrm{d}x=uv- \int{\mathrm{d}u\over \mathrm{d}x}v\,\mathrm{d}x\]
or alternatively: \[\int f(x)g(x)\,\mathrm{d}x=f(x)\,\int
g(x)\mathrm{d}x -\int {\mathrm{d}f\over \mathrm{d}x}\left\{\int g(x)\mathrm{d}x\right\}\mathrm{d}x.\]


