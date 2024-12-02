# Statistics in STACK

STACK was not designed with Statistics in mind. To assess statistics, we can use the algebraic tools, and standard support for [proofs/derivation](Proof\index.md). However this is not the main concern in statistics education. This is intended as a guide to writing Statistics questions in STACK.
## Assessment approaches

### Code

Most statistics courses use programming in R or python. It may be tempting to assess code snippets by asking students to input a string, but this is very hard to assess due to different interpretations of what is required, and different variable names. 

If you wish to assess code using a Moodle quiz, this can be done using the [Coderunner question type](https://docs.moodle.org/405/en/CodeRunner_question_type). This question type allows students to write code which is assessed on its effectiveness on specified examples.

### Numerics
However, we can assess the _results_ of statistical annalysis (such as in \(R\)). 




Most teachers using STACK make use of randomisation, but this relies on Maxima's ability to calculate the correct answer. This section is intended to aid the translation of questions relying on commands in the statistical computing software \(R\) into STACK questions using Maxima. You do not need an in depth knowledge of statistics to use this.  

#### Distribution functions

STACK loads the "distrib" package from maxima by default. Check that your server in the plugin 'STACK' settings has `distrib` in the box `Load optional Maxima libraries`.

######  Key points:

Mostly, it is simple to figure out the format of the maxima equivalent, however there are some points that may cause issues. (This is especially true if you are less familiar with statistics.)

- For the function r* in \(R\), or random* in Maxima, the order of the inputs is different. 
- Maxima and R have different default settings for the Gamma distribution. Maxima uses the shape and scale parameters, while R uses the shape and rate parameters. As such, be careful to translate.
- Data is expressed in a list in Maxima, thus `c(1,2,3)` translates to `[1,2,3]`.


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

 We denote with a *, where there would be the distribition e.g. normal. 

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
<div class="divTableCell"> </div>
</div>
<div class="divTableRow">
<div class="divTableCell"><code>q*</code></div>
<div class="divTableCell"><code>quantile_*</code></div>
<div class="divTableCell">Inverse of CDF. By inputting \(y\), we calculate the value of \(x\) for which the \(P(X \leq x)=y\). Value at a specified percentile.</div>
<div class="divTableCell">Useful for confidence intervals.</div>
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

From this, we can generally estimate what the translation will be, however let us consider a full list of distributions, including the inputs they take. **The <code>random_</code> version of these will be reversed.** 

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
<div class="divTableCell">**<code>*gamma(x,m,1/s)</code>**</div>
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

<br>
For detailed informaion on this see the [distrib package documentation](https://maths.cnam.fr/Membres/wilk/MathMax/help/Maxima/maxima_47.html). This also provides information on calculating skewness and kurtosis.

##### Mean variance and standard deviation

###### Key points 
- R and maxima have different default settings for Variance (and by extension, standard deviation). `var(x)` in R will calculate the sample variance while maxima calculates the population variance. In maxima, `var1(x)` would be the equivalent to `var(x)` in R.
- In R, if no mean and standard deviation is provided, mean = 0 and standard deviation = 1. 
<div class="divTable">
<div class="divTableBody">
<div class="divTableRow">
<div class="divTableHead">R-Code</div>
<div class="divTableHead">Maxima</div>
<div class="divTableHead">What is calculated</div>
</div>
<div class="divTableRow">
<div class="divTableCell"><code>var(x)</code></div>
<div class="divTableCell"><code>var1(x)</code></div>
<div class="divTableCell">Sample variance of a dataset \(s^2=\frac{\sum(x_i-\bar{x})^2}{n-1}\)</div>
</div>
<div class="divTableRow">
<div class="divTableCell"><code>var(x)*(n-1)/n</code></div>
<div class="divTableCell"><code>var1(x)</code></div>
<div class="divTableCell">Population variance of dataset \(\sigma^2=\frac{\sum(x_i-\bar{x})^2}{N}\)</div>
</div>
<div class="divTableRow">
<div class="divTableCell"><code>sd(x)</code></div>
<div class="divTableCell"><code>std1(x)</code></div>
<div class="divTableCell">Sample standard deviation \(\sqrt(s^2)\)</div>
</div>
<div class="divTableRow">
<div class="divTableCell"><code>sd(x)*sqrt((n-1)/n)</code></div>
<div class="divTableCell"><code>std(x)</code></div>
<div class="divTableCell">Population standard deviation \(\sqrt(\sigma^2)\)</div>
</div>
</div>
<div class="divTableRow">
<div class="divTableCell"><code>mean(x)</code></div>
<div class="divTableCell"><code>mean(x)</code></div>
<div class="divTableCell">Mean of the dataset \(\frac{\sum x_i}{n} \)</div>
</div>
</div>
</div>

#### Useful other functions

- <code>binomial(n,k)</code> := \( \frac{n!}{k!(n-k)!} \)
- <code>makelist(f(x),x,a,b)</code>:= list of f(x) from a to b.

Variatations of make list are detailed in the [Maxima documentation for lists](https://maxima.sourceforge.io/docs/manual/maxima_21.html).




## Presenting information
In statistics education, we may want to display R code or data. It can be tempting to take a screenshot and upload this, but it is better to use the `<pre>` environment to display this code as formatted in a code editor. Alternatively, you can upload a file of data using the Moodle link feature or [serving out data](..\Authoring\Serving_out_data.md).


It may be difficult for students to input some statistical notation so try to keep things simple or use an input type other than algebraic expression. STACK uses `\var<LETTER>{}` for greek letters, as such it is logical to use these versions in your questions, to ensure the validation a student view matches the question. 
## General tips

STACK may simplify when not appropriate for statistics. For instance, while \(\sigma^2\) is in fact the standard deviation squared, it is often not desired to simplify this in an expression. Consider this when writing algebraic questions or using question variables with `simp:true` on.

It is worth being careful with using `i` as a sum index. Maxima may interpret this as the imaginary number `i`.  



## Example 

In this example, the student is asked find a confidence interval. We will randomly generate a data set for the student. This also includes [serving out data](..\Authoring\Serving_out_data.md).
### Question variables 



For our example, the _Question variables_ field looks as follows.
```
/*function to round to a number of decimal points*/
rnd(x,dp):=float((round(x*10^dp)/10^dp));
/* Randomise a mean and standard deviation*/
me:rand([48,49,50,51]);
sd:1+rand(2);
/* Randomise a mean and standard deviation*/
me:rand([48,49,50,51]);
sd:1+rand(2);
nn:50;
/*randomise dataset*/
AA:rnd(random_normal(me,sd,nn),2);
BB:rnd(random_normal(50,2,nn),2);
CC:rnd(random_normal(51,5,nn),2);
/*format data*/
lab: ["A","B","C"];
data: makelist([AA[i],BB[i],CC[i]],i,1,nn);
/*find confidence interval 95%*/
tme: mean(AA);
tsd: std1(AA);
tse: float(quantile_normal(0.975,0,1)*tsd/sqrt(nn));
LB:tme-tse;
UB:tme+tse;
ta:[LB,UB];
```

### Question text


```
<p>Download <a href="[[textdownload name="data.csv"]]
{@stack_csv_formatter(data,lab)@}[[/textdownload]]"> 
this normal dataset</a> from lab work, and calculate 
the 95% confidence interval of dataset A.
Give your answer to an appropriate number of significant figures.</p>
<p>\([\)[[input:ans1]]\(,\) [[input:ans2]] \(]\)[[validation:ans1]][[validation:ans2]]</p>
```

### Question note


```
{@dispsf(ta,4)@}
```

### Input: ans1

1. The _Input type_ field should be **Numerical**.
2. The _Model answer_ field should be "dispsf(LB,4)".
3. Set the option _Forbid float_ to "no".
4. Set the option _Show the validation_ to "Yes, compact".(Optional)

### Input: ans2

1. The _Input type_ field should be **Numerical**.
2. The _Model answer_ field should be "dispsf(UB,4)".
3. Set the option _Forbid float_ to "no".
4. Set the option _Show the validation_ to "Yes, compact".(Optional)

### Potential response tree: prt1

###### Node1
**Answer test:** NumAbsolute
**SAns:** ans1
**TAns:** LB
**Test options:** 0.01
**Node 1 when true:** **Mod** = **Score** 0.5
**Node 1 false feedback:** Your lower bound is incorrect.

###### Node2
**Answer test:** NumAbsolute
**SAns:** ans2
**TAns:** UB
**Test options:** 0.01
**Node 1 when true:** **Mod** + **Score** 0.5
**Node 1 false feedback:** Your upper bound is incorrect.