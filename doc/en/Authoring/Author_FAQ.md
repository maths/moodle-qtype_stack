# Author FAQ

## Which version of STACK do I have? ##

If you navigate to the front page of the STACK documentation _on your server_ then you can find the version number of the STACK plugin at the bottom of the page.  If your site is `https://maths.assessment/site` then the URL of the STACK documentation is probably `https://maths.assessment/site/question/type/stack/doc/doc.php`.  We distribute the documentation with the source code so you can check the STACK features you have available on your site by referring to this version of the documentation (rather than the docs on [https://docs.stack-assessment.org/](https://docs.stack-assessment.org/) which documents the latest release.).

The version number is given in the form used by all Moodle plugins, e.g. `2022052300` which is basically a release date of the plugin you are using.

## How can I report a bug or make a suggestion? ##

General community discussion takes place on [https://stack-assessment.zulipchat.com/](https://stack-assessment.zulipchat.com/)

The source code, and development discussion, is on [github](http://github.com/maths/moodle-qtype_stack/issues), with an additional [ILIAS](https://github.com/ilifau/assStackQuestion/) site.

## Can I write questions in multiple languages?

Yes, see support for [multiple languages](Languages.md).

## What is LaTeX? Where can I get help learning LaTeX? ##

LaTeX is a document preparation system. For STACK questions we only need some simple LaTeX, so please do not be put off.
In particular STACK only really makes use of LaTeX for mathematical markup, and does not use the document structure tags.

* An introduction for those totally new to LaTeX is found [here](http://www.andy-roberts.net/misc/latex/index.html).
* The mathematics environment is described [here](http://www.andy-roberts.net/writing/latex/mathematics_1).
* Details about LaTeX are available from <http://www.latex-project.org/guides/>.

Note that some of the more complex examples will not work on STACK. Just keep things simple.

## Can I add HTML to CAS-enabled text? ##

Yes.  You can use HTML tags as usual.  For example, you can use these tags to insert references to images etc.
It's even possible to embed question values within image tags to allow calls to third-party dynamic graph generators.

The Simple Venn sample question demonstrates using the [Google charts](http://code.google.com/apis/chart/) API:

![](http://chart.apis.google.com/chart?cht=v&chs=200x100&chd=t:100,100,0,50&chdl=A|B)

## How can I test out STACK specific functions in a Maxima session? ##

Details of how to load STACK functions into a command line Maxima session are given in the [STACK-Maxima sandbox](../CAS/STACK-Maxima_sandbox.md).

## Why does a Maxima function not work in STACK? ##

Not all Maxima functions are enabled by STACK, for obvious security reasons.
It may be that your function belongs to a library which STACK does not load by default.
Do you need to use Maxima's load command to use it? If so, you will need to ask your system administrator or the developers to add a load command so that this library becomes available.

Some libraries are optional and may not be included by your local installation.

You should also be aware that there are also a number of functions defined by STACK which are not standard Maxima functions.
The command you need may well not be enabled since you should use one STACK provides instead.

## How can I change which Maxima functions STACK allows? ##

This is a job for a developer.  Please contact us.

## How can I use subscripts in STACK ##

More information on subscripts is given in the atoms and subscripts section of the more general [Maxima](../CAS/Maxima.md) documentation.  Also see the inputs extra option [consolidatesubscripts](Inputs.md).

## How can I confirm my student's answer is fully simplified? ##

The philosophy of STACK is to establish properties of the student's answer.  "Simplify" is an ambiguous notion.
For example, \(1\) is simpler than \(2^0\) but \(2^{2^{10}}\) is probably simpler than writing the integer it represents in decimals.  Everyone would agree that \(x+2\) is simpler than \(\frac{x^2-4}{x-2}\), but we might argue that the first expression below is simpler.

\[ \frac{x^{12}-1}{x-1} =  x^{11}+x^{10}+x^9+x^8+x^7+x^6+x^5+x^4+x^3+x^2+x+1.\]

Simplify is often taken implicitly to mean "the shortest equivalent expression", but this issue is often not discussed.

To avoid these problems, STACK expects teachers to specify the properties they want.  For example, if you want the factored form you should test for this, not describe it as "simplified".

In STACK a very useful test is equivalence up to [associativity and commutativity](Answer_Tests/index.md#EqualComAss) of the basic arithmetic operations of addition and multiplication.  This is often what teachers need in this case.

## Why doesn't Maxima give `int(1/x,x)=log(abs(x))`?

Because \( \int \frac{1}{x}dx = \log(|x|) \) is OK on either the negative or positive real axis, but it is not OK in the complex plane. There is a switch that controls this, however.

    (%i199) integrate(1/x,x);
    (%o199) log(x)

    (%i200) integrate(1/x,x), logabs : true;
    (%o200) log(abs(x))

Furthermore, the [integration answer test](Answer_Tests/index.md#Int) will allow teachers to accept either `log(x)` or `log(abs(x))` (or both) from a student.

## Why don't I get anything back from the CAS?

Debugging questions can be difficult.  We have not written a full parser, so we cannot trap all the errors.  If all else fails, you may need to examine exactly the expression which is being sent to Maxima.

To do this go to

    Site administration -> Plugins -> Question types -> STACK

Ensure that `CAS debugging` is checked.

Then, you should get error reporting.  As an example navigate to

    Site administration -> Plugins -> Question types -> STACK -> Healthcheck

There you can see an example of an expression sent to Maxima.  Expressions such as this can be copied into the [STACK-Maxima sandbox](../CAS/STACK-Maxima_sandbox.md) and evaluated.  The errors returned here might help track down the problem.

The issue is normally that you have tried to create a _syntactically invalid_ maxima command.  For example `[a,,b]` will crash Maxima.  Since we have not created a full parser, all syntax errors like this are not yet trapped.
