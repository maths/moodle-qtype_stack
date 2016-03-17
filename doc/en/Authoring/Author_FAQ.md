# Author FAQ

## How can I report a bug or make a suggestion? ##

Contributions are very welcome.  Please see the [community](../About/Community.md) page for more specific details.

## What is LaTeX? Where can I get help learning LaTeX? ##

LaTeX is a document preparation system. For STACK questions we only need some simple LaTeX, so please do not be put off.
In particular STACK only really makes use of LaTeX for mathematical markup, and does not use the document structure tags.

* An introduction for those totally new to LaTeX is found [here](http://www.andy-roberts.net/misc/latex/index.html)
* The mathematics environment is describe [here](http://www.andy-roberts.net/writing/latex/mathematics_1)
* Details about LaTeX are available from <http://www.latex-project.org/guides/>.

Note that some of the more complex examples will not work on STACK. Just keep things simple.

## Can I add HTML to CAS-enabled text? ##

Yes.  You can use HTML tags as usual.  For example, you can use these tags to insert references to images etc.
It's even possible to embed question values within image tags to allow calls to third-party dynamic graph generators.

The Simple Venn sample question demonstrates using the [Google charts](http://code.google.com/apis/chart/) API:

![](http://chart.apis.google.com/chart?cht=v&chs=200x100&chd=t:100,100,0,50&chdl=A|B)

## Why does a Maxima function not work in STACK? ##

Not all Maxima functions are enabled by STACK, for obvious security reasons.
It may be that your function belongs to a library which STACK does not load by default.
Do you need to use Maxima's load command to use it? If so, you will need to ask your system administrator or the developers to add a load command so that this library becomes available.

Some libraries are optional and may not be included by your local installation.

You should also be aware that there are also a number of functions defined by STACK which are not standard Maxima functions.
The command you need may well not be enabled since you should use one STACK provides instead.

## How can I test out STACK specific functions in a Maxima session? ##

Details of how to load STACK functions into a command line Maxima session are given in the [STACK-Maxima sandbox](../CAS/STACK-Maxima_sandbox.md).

## How can I confirm my student's answer is fully simplified? ##

The philosophy of STACK is to establish properties of the student's answer.  "Simplify" is an ambiguous notion.
For example, \(1\) is simpler than \(2^0\) but \(2^{2^{10}}\) is probably simpler than writing the integer it represents in decimals.  Everyone would agree that \(x+2\) is simpler than \(\frac{x^2-4}{x-2}\), but we might argue that the first expression below is simpler.

\[ \frac{x^{12}-1}{x-1} =  x^{11}+x^{10}+x^9+x^8+x^7+x^6+x^5+x^4+x^3+x^2+x+1.\]

Simplify is often taken implicitly to mean "the shortest equivalent expression", but this issue is often not discussed.

To avoid these problems, STACK expects teachers to specify the properties they want.  For example, if you want the factored form you should test for this, not describe it as "simplified".

In STACK a very useful test is equivalence up to [associativity and commutativity](Answer_tests.md#EqualComAss) of the basic arithmetic operations of addition and multiplication.  This is often what teachers need in this case.

## How can I change which Maxima functions STACK allows? ##

This is a job for a developer.  Details of this are in the code with the casstring class.  [See the latest code on github](https://github.com/maths/moodle-qtype_stack/blob/master/stack/cas/casstring.class.php).  

## Why doesn't Maxima give `int(1/x,x)=log(abs(x))`?

Because \( \int \frac{1}{x}dx = \log(|x|) \) is OK on either the negative or positive real axis, but it is not OK in the complex plane. There is a switch that controls this, however.

    (%i199) integrate(1/x,x);
    (%o199) log(x)

    (%i200) integrate(1/x,x), logabs : true;
    (%o200) log(abs(x))

## Why don't I get anything back from the CAS?

Debugging questions can be difficult.  We have not written a full parser, so we cannot trap all the errors.  If all else fails, you may need to examine exactly the expression which is being sent to Maxima.

To do this go to

    Site administration -> Plugins -> Question types -> STACK

Ensure that `CAS debugging` is checked.

Then, you should get error reporting.  As an example navigate to

    Site administration -> Plugins -> Question types -> STACK -> Healthcheck

There you can see an example of an expression sent to Maxima.  Namely:

    cab:block([ RANDOM_SEED, OPT_NoFloats, sqrtdispflag, simp, assume_pos, caschat0, caschat1], stack_randseed(0), make_multsgn(dot), make_complexJ(i), OPT_NoFloats:true, sqrtdispflag:true, simp:true, assume_pos:false, print("[TimeStamp= [ 0 ], Locals= [ ") , print("0=[ error= ["), cte("caschat0",errcatch(caschat0:plot([x^4/(1+x^4),diff(x^4/(1+x^4),x)],[x,-3,3]))) , print("1=[ error= ["), cte("caschat1",errcatch(caschat1:plot([sin(x),x,x^2,x^3],[x,-3,3],[y,-3,3]))) , print("] ]") , return(true) );

Expressions such as this can be copied into the [STACK-Maxima sandbox](../CAS/STACK-Maxima_sandbox.md) and evaluated.  The errors returned here might help track down the problem.

The issue is normally that you have tried to create a _syntactically invalid_ maxima command.  For example `[a,,b]` will crash Maxima.  Since we have not created a full parser, all syntax errors like this are not yet trapped.
