# Inclusions

This is an expert level topic, please avoid this feature unless you feel that you 
understand the implications and concecuences of it. Note that using this feature
goes against the self-contained material principle present in 
[the guidelines](Future_proof.md).

That being said, a common request on the wish-list has been to provide some means
of sharing code between questions. The inclusion-feature is the first step towards
such a feature, however, it is not perfect nor do we expect it to be the solution 
that will finally be selected. But we provide it now that the backend has the tools
to provide it, for those adventurous enough.

Technical note: Currently, inclusions within inclusions are not supportted, due to
loop detection and security validation reasons.

## Inclusions life-cycle

For all current types of inclusions the inclusion happens at the time of compilation,
which means that the source will be fetched when the question gets compiled and will
stay in the cached compilation product. Compilation happens during the first use of
the question after it has been saved or the cache has been purged.

The logic will not track changes to the source material, if one wants to fetch it
again one must re-compile the question, either by purging the caches or by editing
the question.

Note that in the current solution during export we export only the source address 
and the exported material will only work if the address is accessible at the end 
that imports it. This is the primary reason why the current solution is not 
considered as good enough for a general solution.


## Inclusions within text

The simpler inclusion type is the CASText2 include-block, which will simply place
CASText2-code from a given address at the blocks location. Note that one may need
to be careful with the format of the context and included code, the include logic
assumes that the code is of the same format as the context, so if your included 
content is in Markdown-format including it directly to HTML-context may cause 
trouble.

Typical use case would be to have a JSXGraph plotting logic that expect that
certain variables contain parameters for plotting and one would then simply
make sure that one would populate those parameters before inclusion of the ploting 
logic for example like this:

```
First plot the first plot with the values defined earlier.
[[include src="http://example.com/fragments/myplot.txt"/]]

Then include the same plotting logic but change the parameters to plot something else.
[[define plot_fun="theotherfun" plot_param1="something"/]]
[[include src="http://example.com/fragments/myplot.txt"/]]

```

As the included content can be of a different format (Markdown etc.) than 
the context into which it gets included it is recommended that the included
content defines its own format. For example, wrapping itself in one of 
the new format controlling blocks `[[moodleformat]]`, `[[htmlformat]]` or
`[[markdownformat]]`, thus allowing whatever format content to be included
within another formats content. Note, that at this time identification of 
math-mode is not quite ready to understand all of these context switches.


## Inclusions within CAS-logic

The more complex inclusion type is the includion into keyvals, i.e. question-variables
or into feedback-variables. This type of an inclusion will again act just like any
other bit of logic as if written directly at that place in the code. If one writes
this type of an inclusion within an if-statement it will simply get written open within 
an if-statement, the if will only decide if it executes, but it will still take that 
space and bandwidth.

Also note that the included material must follow all the rules of normal STACK 
keyvals and if one for example plugs in large amounts of code into the question-variables
then all the identifiers bound in that code will be markked as forbidden-words for
the students. So particular care should be taken when choosing identifiers in such
shared logic.

Note that we do not do tree-shaking at this point, so if you include a massive library
of functions that you do not use the question will still have to load those functions
into the CAS and that may take some time.

THe way to do an inclusion is to call the `stack_include()`-function with a string 
argument, the argument must be a raw string and not a reference to a variable 
containing such a string. Like this:

```
a: rand(3)+2;
/* Load logic to tune the presentation. */
stack_include("http://example.com/fragments/mytexputrules.txt");
/* Load some special randomisation functions. */
stack_include("http://example.com/fragments/mymatrixrand.txt");
m: mymatrix_rand_integer_invertible(a);
```

Note, that `stack_include()` has no Maxima side equivalent so you cannot simply
copy-paste your question-vars into Maxima to debug things. You will need to
manually do that inclusion in those cases.

You may not use evaluation flags with `stack_include()` while the code included may
have them the inclusion call cannot be used to apply flags to all the included content.