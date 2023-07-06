# Displaying mathematics for STACK in Moodle

STACK generates LaTeX code on the fly and expects to use a Moodle filter to convert this into something the user's browser will display.  For Moodle 2.7 or later, MathJax is distributed as a filter.  We strongly recommend using the Moodle MathJax filter.  One reason for adopting MathJax is that it aids [accessibility](../Students/Accessibility.md).

## Using the MathJax filter

These are notes on configuring the MathJax filter options for use with STACK.

1. Do not use "TeX filter compatibility" unless you want all equations to appear inline!
2. To add support for [actuarial notation](../Reference/Actuarial.md) you need to add the option 'enclose.js' to the extensions.  I.e. within the "MathJax configuration" text we need the following.   

        TeX: { extensions: ['enclose.js'] }

## Accessibility ##

The accessibility features supported by MathJax are given detailed [here](http://www.mathjax.org/resources/articles-and-presentations/accessible-pages-with-mathjax/).

## Adding MathJax to additional HTML

If the MathJax filter is not available then you can include it in the head of every page by adding additional HTML to every page.  We don't recommend you do this, but have retained this information in the documentation for completeness.

### Option 1: install MathJax locally ###

A local installation may be preferable, e.g., if you want your STACK-equipped Moodle to work off-line, or if you want to try to speed up LaTeX rendering.

1. Download [MathJax](http://www.mathjax.org/)
2. Unpack the archive and rename folder to "mathjax".
3. Place the mathjax folder in .../moodle/lib and set appropriate ownership, e.g., `chown -R root.root mathjax`.
4. Carry out the procedure in Option 1, editing the script above to reflect your path to MathJax. The last line can resemble the following:

       <script type="text/javascript" src="http://localhost/moodle/lib/mathjax/MathJax.js?config=TeX-AMS_HTML"></script>


### Option 2: link to external MathJax setup ###

As of August 2017, you can use an external CDN at `cdnjs.cloudflare.com`.

1. Admin -> Appearance -> Additional HTML -> Within HEAD
2. Put the following script in it and save

       <script type="text/x-mathjax-config"> MathJax.Hub.Config({
                MMLorHTML: { prefer: "HTML" },
                tex2jax: {
                    displayMath: [['\\[', '\\]']],
                    inlineMath:  [['\\(', '\\)']],
                    processEscapes: true
                },
                TeX: { 
                extensions: ['enclose.js'],
                Macros: { pounds: '{\\it\\unicode{xA3}}', euro: '\\unicode{x20AC}' }
                }
            });
       </script>
       <script type="text/javascript" async src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.1/MathJax.js?config=TeX-MML-AM_CHTML"> </script>

Please note the following.

* These settings enable only the strict LaTeX maths environments, and does not support the use of dollars;
* The `processEscapes` flag enables you to include a dollar symbol in mathematics environments with `\$`.
* The line `extensions: ['enclose.js'],` enables support for [actuarial notation](../Reference/Actuarial.md).
* The line `Macros: { pounds: '{\\it\\unicode{xA3}}', euro: '\\unicode{x20AC}' }` enables support for the UK pounds sign and the Euro symbol in LaTeX using `\pounds` and `\euro` macros respectively.  E.g. \(\pounds\) and \(\euro\).

### What about dollar mathematics delimiters? ###  {#delimiters}

Please note that we strongly discourage the use of dollar symbols for denoting LaTeX mathematics environments such as `$...$` and `$$...$$` for inline and displayed mathematics respectively.  As of STACK 4.0 use of dollars are officially unsupported.
The reasons are (1) they do not match, which makes parsing more difficult, and (2) many courses use [currency](../Authoring/CASText.md#currency) which needs this symbol and protecting it is always problematic.

If you have extensive existing materials using these delimiters then we have scripts which will automatically convert them for you.  All fields within existing questions can be converted with the automatic scripts available from

    Home > Site administration > Plugins > Question types > STACK

If you have ad-hoc text to convert then the CAS chat script *always* converts dollars into the forms `\( .... \)` and `\[ .... \]`.  You can test and edit the display of text in this window to see the conversion. 

If you really want to use dollars, MathJax can display them with the code

    displayMath: [['$$', '$$'], ['\\[', '\\]']],
    inlineMath:  [['$',  '$' ], ['\\(', '\\)']],

Before deciding to support the use of dollars for mathematics on your site, please see the notes on the use of dollars for [currency](../Authoring/CASText.md#currency).
