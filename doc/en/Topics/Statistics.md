# Statistics in STACK

STACK was originally designed with algebra and calculus in mind, rather than statistics. To assess statistics, we can use the algebraic tools, and standard support for [proofs/derivation](Proof/index.md). However this is not the main concern in statistics education. This is intended as a guide to writing statistics questions in STACK.

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
  border:1px solid black;}
</style>

 We denote with a *, where there would be the distribition e.g. normal. 

|R-Code|Maxima|What is calculated|Notes|
|---|---|---|---|
|`d*`|`pdf_*`|Probability density function for a distribution. \(P(X=x)\)|&nbsp;|
|`p*`|`cdf_*`|Cumulative distribution function a distribution. \(P(X\leq x) = \int_{-\infty}^x P(X=i)\; \mathrm{di} \)|
|`q*`|`quantile_*`|Inverse of CDF. By inputting \(y\), we calculate the value of \(x\) for which the \(P(X \leq x)=y\). Value at a specified percentile.|Useful for confidence intervals.|
|`r*`|`random_*`|A list of normally numbers with the specified distribution.|in R, inputs will be (n,[vars]), in maxima inputs are ([vars], n). See normal for example.|


    
Let us now see an example; the normal distribution.

|R-Code|Maxima|
|---|---|
|`dnorm(x,m,s)`|`pdf_normal(x,m,s)`|
|`pnorm(x,m,s)`|`cdf_normal(x,m,s)`|
|`qnorm(x,m,s)`|`quantile_normal(x,m,s)`|
|`rnorm(n,m,s)`|`random_normal(m,s,n)`|


From this, we can generally estimate what the translation will be, however let us consider a full list of distributions, including the inputs they take. **The `random_` version of these will be reversed.**

|R-Code|Maxima|
|---|---|
|`*norm(x,m,s)`|`*normal(x,m,s)`|
|`*t(x,n)`|`*student_t(x,n)`|
|`*chisq(x,n)`|`*chi2(x,n)`|
|`*f(x,m,n)`|`*f(x,m,n)`|
|`*exp(x,m)`|`*exp(x,m)`|
|`*lnorm(x,m,s)`|`*lognormal(x,m,s)`|
|`*gamma(x,m,s)`|**`*gamma(x,m,1/s)`**|
|`*beta(x,m,n)`|`*beta(x,m,n)`|
|`*unif(x,m,s)`|`*continuous_uniform(x,m,s)`|
|`*logis(x,m,s)`|`*logistic(x,m,s)`|
|`*weibull(x,m,s)`|`*weibull(x,m,s)`|
|`*cauchy(x,m,s)`|`*cauchy(x,m,s)`|
|`*geom(x,m)`|`*geometric(x,m)`|
|`*binom(x,m,s)`|`*binomial(x,m,s)`|
|`*pois(x,m)`|`*poisson(x,m)`|
|`*geom(x,m)`|`*geometric(x,m)`|
|`*nbinom(x,m,s)`|`*negative_binomial(x,m,s)`|

For detailed informaion on this see the [distrib package documentation](https://maths.cnam.fr/Membres/wilk/MathMax/help/Maxima/maxima_47.html). This also provides information on calculating skewness and kurtosis.

##### Mean variance and standard deviation

###### Key points

- R and maxima have different default settings for Variance (and by extension, standard deviation). `var(x)` in R will calculate the sample variance while maxima calculates the population variance. In maxima, `var1(x)` would be the equivalent to `var(x)` in R.
- In R, if no mean and standard deviation is provided, mean = 0 and standard deviation = 1.
- In Maxima, the default mean is 0 and standard deviation is 1.

|R-Code|Maxima|What is calculated|
|---|---|---|
|`var(x)`|`var1(x)`|Sample variance of a dataset \(s^2=\frac{\sum(x_i-\bar{x})^2}{n-1}\)|
|`var(x)*(n-1)/n`|`var1(x)`|Population variance of dataset \(\sigma^2=\frac{\sum(x_i-\bar{x})^2}{N}\)|
|`sd(x)`|`std1(x)`|Sample standard deviation \(\sqrt(s^2)\)|
|`sd(x)*sqrt((n-1)/n)`|`std(x)`|Population standard deviation \(\sqrt(\sigma^2)\)|
|`mean(x)`|`mean(x)`|Mean of the dataset \(\frac{\sum x_i}{n} \)|

#### Linear regression

Maxima can calculate linear regressions. The function `linear_regression(x)` takes an argument `x`, a two column matrix or a list of pairs, and will return a summary of results. The following is a list of the results that can be extracted.

- 'b_estimation: regression coefficients estimates.
- 'b_covariances: covariance matrix of the regression coefficients estimates.
- b_conf_int: confidence intervals of the regression coefficients.
- b_statistics: statistics for testing coefficient.
- b_p_values: p-values for coefficient tests.
- b_distribution: probability distribution for coefficient tests.
- v_estimation: unbiased variance estimator.
- v_conf_int: variance confidence interval.
- v_distribution: probability distribution for variance test.
- residuals: residuals.
- adc: adjusted determination coefficient.
- aic: Akaike’s information criterion.
- bic: Bayes’s information criterion. 

Results can be used using the function `take_inference(prop, res)`. Where prop is the property you want to extract and res is the variable the linear regression is saved to.
For example,

	XY: addcol(matrix(), x, y);
	results: linear_regression(XY);
	coeffs: take_inference('b_estimation, results);	

More information can be found in the [Maxima documentation for Functions and Variables](https://maxima.sourceforge.io/docs/manual/maxima_370.html)
#### Useful other functions

- <code>binomial(n,k)</code> := \( \frac{n!}{k!(n-k)!} \)
- <code>makelist(f(x),x,a,b)</code>:= list of f(x) from a to b.

Variatations of make list are detailed in the [Maxima documentation for lists](https://maxima.sourceforge.io/docs/manual/maxima_21.html).

## Presenting information

In statistics education, we may want to display R code or data. It can be tempting to take a screenshot and upload this, but it is better to use the `<pre>` environment to display this code as formatted in a code editor. Alternatively, you can upload a file of data using the Moodle link feature or [serving out data](../Authoring/Serving_out_data.md).

It may be difficult for students to input some statistical notation, so try to keep things simple or use an input type other than algebraic expression. STACK uses `\var<LETTER>{}` for Greek letters, as such it is logical to use these versions in your question to ensure the validation a student view matches the question. 

## General tips

STACK may simplify when not appropriate for statistics. For instance, while \(\sigma^2\) is in fact the standard deviation squared, it is often not desired to simplify this in an expression. Consider this when writing algebraic questions or using question variables with `simp:true` on.

If you feel that you cannot randomise questions using Maxima, consider writing the questions in your chosen programming language, then copying the question and changing the values, and then make use of the [Moodle random question](https://docs.moodle.org/405/en/Random_question_type) function. 

It is worth being careful with using `i` as a sum index. Maxima may interpret this as the imaginary number `i`.  

## Example 

In this example, the student is asked find a confidence interval. We will randomly generate a data set for the student. This also includes [serving out data](../Authoring/Serving_out_data.md).

### Question variables 

For our example, the _Question variables_ field looks as follows.
```
/*function to round to a number of decimal points*/
rnd(x,dp):=float((round(x*10^dp)/10^dp));
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
