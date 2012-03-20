# Author FAQ

## What is LaTeX? Where can I get help learning LaTeX? ##

LaTeX is a document preparation system. For STACK questions we only need some simple LaTeX, so please do not be put off.
Details about LaTeX are available from <http://www.latex-project.org/guides/>.

Perhaps a better introduction for those totally new to LaTeX is found [here](http://www.andy-roberts.net/misc/latex/index.html)
with more specific information about [mathematics](http://www.andy-roberts.net/misc/latex//latextutorial9.html) with examples for you to follow.
Note that some of the more complex examples will not work on STACK. Just keep things simple.

## Can I add HTML to CAS-enabled text? ##

Yes.  You can use HTML tags as usual.  For example, you can use these tags to insert references to images etc.
It's even possible to embed question values within image tags to allow calls to third-party dynamic graph generators.
 
The Simple Venn sample question demonstrates using the [Google charts](http://code.google.com/apis/chart/) API:

![](http://chart.apis.google.com/chart?cht=v&chs=200x100&chd=t:100,100,0,50&chdl=A|B)

## Why does a Maxima function not work in STACK? ##

Not all Maxima functions are enabled by STACK, for obvious security reasons.
It may be that your function belongs to a library which STACK does not load by default.
Do you need to use Maxima's load command to use it? If so, you will need to ask your system administrator
or the developers to add a load command so that this library becomes available.

You should also be aware that there are also a number of functions defined by STACK which are not standard Maxima functions.
The command you need may well not be enabled since you should use one STACK provides instead.

## How can I test out STACK specific functions in a Maxima session? ##

Details of how to load STACK functions into a command line Maxima session are given in the [STACK-Maxima sandbox](../CAS/STACK-Maxima_sandbox.md).

## How can I change which Maxima functions STACK allows? ##

This is a job for a developer.

**It is not enough to just change this file!** You will also need to copy and re-run the install script.
This should not re-install the databases or wipe data, but at the end of the file will generate a number of temporary files to reflect your new settings.
The most important of these for this purpose is

## Why doesn't Maxima give `int(1/x,x)=log(abs(x))`?

Because \( \int \frac{1}{x}dx = \log(|x|) \) is OK on either the negative or
positive real axis, but it is not OK in the complex plane. There is a switch that
controls this, however.

	(%i199) integrate(1/x,x);
	(%o199) log(x)

	(%i200) integrate(1/x,x), logabs : true;
	(%o200) log(abs(x))
