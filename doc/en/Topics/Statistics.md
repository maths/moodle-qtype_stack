# Statistics in STACK

STACK was not designed with Statistics in mind. To assess statistics, we can of course use the algebraic tools, and standard support for [proofs/derivation](../Proof). However this is not the main concern in statistics education. So what can we do?


## Coderunner

## Numerics

STACK loads the "distrib" package from maxima by defult. Check that your server in the plugin 'STACK' settings has <code>distrib</code> in the box <code>Load optional Maxima libraries</code>.


This guide is intended to aid the translation of questions relying on comands in the statistical computing software \(R\) into STACK questions using Maxima. You do not need an in depth knowlegde of statistics to use this.  


### Density functions

Key points:

Mostly, it is simple to figure out the format of the maxima equivelent, 
however there are some points that may cause issues. (This is especially true if you are less familiar with statistics.)

- For the function r* in R, or random* in Maxima, the order of the inputs is different. 
- Maxima and R have different defualt settings for the Gamma distribution. Maxima uses the shape and scale parameters, while R uses the shape and rate parameters. 
As such, be careful to translate.

<style>
table, th, td {
  border:1px solid black;
}
.divTable{
	display: table;
	width: 100%;
}
.divTableRow {
	display: table-row;
}
.divTableHeading {
	background-color: #EEE;
	display: table-header-group;
}
.divTableCell, .divTableHead {
	border: 1px solid #999999;
	display: table-cell;
	padding: 3px 10px;
}
.divTableHeading {
	background-color: #EEE;
	display: table-header-group;
	font-weight: bold;
}
.divTableFoot {
	background-color: #EEE;
	display: table-footer-group;
	font-weight: bold;
}
.divTableBody {
	display: table-row-group;
}
</style>


Firstly, it is useful to know the translations for common distribution functions calculations. We denote with a *, where there would be the distribition e.g. normal. 

<p>
<div class="divTable">
<div class="divTableBody">
<div class="divTableRow">
<div class="divTableHead">R-Code</div>
<div class="divTableHead">Maxima</div>
<div class="divTableHead">What is calculated</div>
<div class="divTableHead">Notes</div>
</div>
<div class="divTableRow">
<div class="divTableCell"><code>d*</code></div>
<div class="divTableCell"><code>pdf_*</code></div>
<div class="divTableCell">Probability density function for a distribution. \(P(X=x)\) </div>
<div class="divTableCell">&nbsp;</div>
</div>
<div class="divTableRow">
<div class="divTableCell"><code>p*</code></div>
<div class="divTableCell"><code>cdf_*</code></div>
<div class="divTableCell">Cumulative distribution function a distribution. \(P(X\leq x) = \int_{-\infty}^x P(X=i)\; \mathrm{di} \)</div>
<div class="divTableCell">&nbsp;</div>
</div>
<div class="divTableRow">
<div class="divTableCell"><code>q*</code></div>
<div class="divTableCell"><code>quantile_*</code></div>
<div class="divTableCell">Inverse of CDF. By inputting \(y\), we calculate the value of \(x\) for which the \(P(X \leq x)=y\). Value at a specified percentile.</div>
<div class="divTableCell">&nbsp;</div>
</div>
<div class="divTableRow">
<div class="divTableCell"><code>r*</code></div>
<div class="divTableCell"><code>random_*</code></div>
<div class="divTableCell">A list of normally numbers with the specified distribition.</div>
<div class="divTableCell">in R, inputs will be (n,[vars]), in maxima inputs are ([vars], n). See normal for example.</div>
</div>
</div>
</div>
</p>

    
<p> Let us now see an example using the normal distribution</p> 

<p>
<div class="divTable">
<div class="divTableBody">
<div class="divTableRow">
<div class="divTableHead">R-Code</div>
<div class="divTableHead">Maxima</div>
</div>
<div class="divTableRow">
<div class="divTableCell"><code>dnorm(x,m,s)</code></div>
<div class="divTableCell"><code>pdf_normal(x,m,s)</code></div>
</div>
<div class="divTableRow">
<div class="divTableCell"><code>pnorm(x,m,s)</code></div>
<div class="divTableCell"><code>cdf_normal(x,m,s)</code></div>
</div>
<div class="divTableRow">
<div class="divTableCell"><code>qnorm(x,m,s)</code></div>
<div class="divTableCell"><code>quantile_normal(x,m,s)</code></div>
</div>
<div class="divTableRow">
<div class="divTableCell"><code>rnorm(n,m,s)</code></div>
<div class="divTableCell"><code>random_normal(m,s,n)</code></div>
</div>
</div>
</div>
</p>
<br>
<br>

From this, we can generally estimate what the translation will be, however let us consider a full list of distributions, including the inputs they take. **The <code>random_</code> version of these will of course be reversed.** 

<div class="divTable">
<div class="divTableBody">
<div class="divTableRow">
<div class="divTableHead">R-Code</div>
<div class="divTableHead">Maxima</div>
</div>
<div class="divTableRow">
<div class="divTableCell"><code>*norm(x,m,s)</code></div>
<div class="divTableCell"><code>*normal(x,m,s)</code></div>
</div>
<div class="divTableRow">
<div class="divTableCell"><code>*t(x,n)</code></div>
<div class="divTableCell"><code>*student_t(x,n)</code></div>
</div>
<div class="divTableRow">
<div class="divTableCell"><code>*chisq(x,n)</code></div>
<div class="divTableCell"><code>*chi2(x,n)</code></div>
</div>
<div class="divTableRow">
<div class="divTableCell"><code>*f(x,m,n)</code></div>
<div class="divTableCell"><code>*f(x,m,n)</code></div>
</div>
<div class="divTableRow">
<div class="divTableCell"><code>*exp(x,m)</code></div>
<div class="divTableCell"><code>*exp(x,m)</code></div>
</div>
<div class="divTableRow">
<div class="divTableCell"><code>*lnorm(x,m,s)</code></div>
<div class="divTableCell"><code>*lognormal(x,m,s)</code></div>
</div>
<div class="divTableRow">
<div class="divTableCell"><code>*gamma(x,m,s)</code></div>
<div class="divTableCell"><code>*gamma(x,m,1/s)</code><strong> note the difference</strong></div>
</div>
<div class="divTableRow">
<div class="divTableCell"><code>*beta(x,m,n)</code></div>
<div class="divTableCell"><code>*beta(x,m,n)</code></div>
</div>
<div class="divTableRow">
<div class="divTableCell"><code>*unif(x,m,s)</code></div>
<div class="divTableCell"><code>*continuous_uniform(x,m,s)</code></div>
</div>
<div class="divTableRow">
<div class="divTableCell"><code>*logis(x,m,s)</code></div>
<div class="divTableCell"><code>*logistic(x,m,s)</code></div>
</div>
<div class="divTableRow">
<div class="divTableCell"><code>*weibull(x,m,s)</code></div>
<div class="divTableCell"><code>*weibull(x,m,s)</code></div>
</div>
<div class="divTableRow">
<div class="divTableCell"><code>*cauchy(x,m,s)</code></div>
<div class="divTableCell"><code>*cauchy(x,m,s)</code></div>
</div>
<div class="divTableRow">
<div class="divTableCell"><code>*geom(x,m)</code></div>
<div class="divTableCell"><code>*geometric(x,m)</code></div>
</div>
<div class="divTableRow">
<div class="divTableCell"><code>*binom(x,m,s)</code></div>
<div class="divTableCell"><code>*binomial(x,m,s)</code></div>
</div>
<div class="divTableRow">
<div class="divTableCell"><code>*pois(x,m)</code></div>
<div class="divTableCell"><code>*poisson(x,m)</code></div>
</div>
<div class="divTableRow">
<div class="divTableCell"><code>*geom(x,m)</code></div>
<div class="divTableCell"><code>*geometric(x,m)</code></div>
</div>
<div class="divTableRow">
<div class="divTableCell"><code>*nbinom(x,m,s)</code></div>
<div class="divTableCell"><code>*negative_binomial(x,m,s)</code></div>
</div>
</div>
</div>


For detailed informaion on this see the [distrib package documentation](https://maths.cnam.fr/Membres/wilk/MathMax/help/Maxima/maxima_47.html). 

### Useful other functions

<code>binomial(n,k)</code> = \(\frac{n!}{k!(n-k)!}\)
<code>makelist([j,f(j,[vars])],j,n,m)</code>

