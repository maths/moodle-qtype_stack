# Guidelines for ensuring that a question works in the future

Creating a great STACK question takes effort and often people worry about how updating STACK or Moodle or other components running those questions might affect their questions. While there are no guarantees you can increase the ongoing reliability of your questions by taking the following things into account. 

In some parts of this document we mention "Abacus". The Abacus project is a STACK material sharing organisation that seeks to develop high-quality materials to be implemented following these guidelines. This document ends with additional guidelines adopted by Abacus.  This is both good practice, and will be relevant if you ever intend to join Abacus.

The most important things are listed here. More detail is below.

1. Build questions so that it is possible to detect changes in the platform. All questions must have "question tests", which allow unit testing of each question variant.
2. Use simple correct HTML with all closing tags, avoiding explicit style of your own.
3. Use only simple core LaTeX Maths environments/commands, with only `\(...\)` and `\[...\]` as the maths delimiters.
4. Avoid linking to externally hosted content, such as pictures and applets.
5. End Maxima commands with a semi-colon `;`.
6. Add comments to your Maxima code.

## Writing CASText

CASText is the definition of the visible parts of the question, the bit where you pose the problem to the student lay out the equations and inputs and possibly draw fancy graphics. Technically, CASText is an HTML-document fragment with inline LaTeX equations and possible logic in the form of CASText blocks.

To ease parsing of CASText you should ensure that you are writing valid (X)HTML i.e. make sure that all the tags get closed and that you are not placing block level elements in places where they do not belong. `<span><div>...</div></span>` is bad while `<div><span>...</span></div>` might be less bad. In the case of LaTeX using `\(...\)` instead of `$...$` and `\[...\]` instead of `$$...$$`, *in Abacus `$`-delimiters for LaTeX are strictly forbidden*.  (There is a simplistic auto-convert to help with a one-time conversion.)

To ensure consistent presentation you should avoid using inline CSS styles in your CASText, as the styles of the surrounding system may change and cause conflicts. In general all styles are bad with the sole exception being `text-align` in the context of table cells. Otherwise:

* If at all possible remove all styles and the `<span>` tags related to them from the CASText. Most WYSIWYG editors have some **clear formatting** feature to do just that. You cannot have block level HTML elements inside a span tag, which is an inline element.
* You should not define `font-size` anywhere. If you need a heading with big text then use the `<hX>...</hX>` tags, but not with too small or large values of X. *In Abacus `<h3>` and `<h4>` are recommended especially in model solutions.*
* Likewise all other font stylings should be left to the surrounding system but feel free to use `<i>`, `<b>`, `<em>`, and `<strong>` if you need and assume that the surrounding system handles them correctly.
* You can use the `<code>` tag to explain how to type in answers.
* The top level of your CASText document should consist of `<p>`, `<table>`, `<div>`, or CASText block elements not of raw text. For the PRT be careful about using `<p>` tags (which editors hide).  If you use the compact PRT style then STACK then puts this into span tags, and you cannot have block level HTML elements such as `<p>` inside a span tag.
* In the case of tables and images you may use a bit of styling, e.g. borders, paddings, margins, and sizes, but you should always use relative units when describing those sizes. Scale to match the current font-size or maybe to the width of the viewport or even to the maximum dimension of the display not to pixels as pixels have very different actual sizes on different screens and we cannot assume that the software displaying things will make the same scaling assumptions for them in the future.
* STACK now uses SVG, and this is preferred if you can embed this within the CASText.
* Only use supported JavaScript libraries, like JSXGraph.
* Avoid complex LaTeX constructions where you inject values into multiple places, instead try to tune Maxima LaTeX generation to meet your needs, e.g. [showing working](../CAS/Matrix.md#showing-working-showing_working), using `texput`, or changing other features of Maxima constructs.

If your CASText document contains scripts like JSXGraph content definitions you should ensure that if they require some external files to be evaluated you include those files with the question as links to external files will be broken at some point or the external files themselves change. In the case of JSXGraph use the `jsxgraph` CASText block which will handle the scripts at STACK level.

__All external files/links are bad!__ If you have images or other documents related to the question they should be included in the question. Avoid embedded frames, applets and other interactive content. To test inclusion you should be able to export the question and import it to a freshly installed raw system on a computer not connected to the internet and those questions should work. *There is nothing wrong with internal files of any type, as long as they come with the question.* Use of the [include](Inclusions.md) feature from 4.4 is also a bad thing, but you can make it less bad if the included file is present on a public server and if it is versioned so that one can link to a version that never changes.

## Writing CAS code

The CAS code (internally keyvals and CASStrings) consists of Maxima assignment statements. Occasionally, Maxima's behaviour changes, and occasionally there are changes in the STACK provided Maxima functions. Changes cannot be avoided, but you can try to develop your questions based on as new as possible Maxima version to give them longer life. Otherwise there are some details that should be noted:

* End your statements with semicolons (`;`) this will ease your life if you ever need to copy code to Maxima for testing. In the future semicolons may become mandatory, automatic conversion will however handle that change.
* Avoid interesting chars in your variable names e.g. `_` has meaning and it is reasonable to assume that other chars might have new meanings in the future.
* Never write to a variable sharing a name with an input!

You should also heed to these general guidelines:

* Teachers answers are generally stored in variables named `tAns`, `tans1`, or maybe `TAns3` this is a pretty widely spread convention and should be followed, in general the question variables should end with a listing of assignments defining the teachers answers for each input.
* Should there be need to process the students answer it might make sense to store it to `sAns...`.
* `texput` instead of manual string construction when generating LaTeX representations will keep your code readable.
* Write comments! More complex code is more likely to break at some point and someone, probably you, will need to understand it and provide a replacement logic for it. Especially, long oneliners have tendency to mystically break.


## Question tests & teacher's answers

To detect broken questions we need to be able to test them and the only reasonable way to that is through the question tests. You must define some tests: at least one for each PRT in your question. Preferably provide one correct answer test for each PRT, tests for each branch in the PRTs, and some nonsense inputs to check for 'false positive' style failures.

For manual testing it is important to provide each input with the correct teacher's answer and that teacher's answer must be in a form that matches input requirements.  For example, if you require a floating-point number with a specific number of significant figures then do not specify the teacher's answer as a rational number!  Note that you will need to do the same output formatting for the question tests if your question cares about the formatting of the answer.

Please note that the system administrator has access to a "bulk test" script which runs all the tests on a category. You can use this when you upgrade your site.


# Things that have broken or will break

There have been some changes, which have created broken questions between versions.

* List instantiation in Maxima is now required by Maxima and writing to an uninstantiated list gives errors like 'ARRSTORE: use_fast_arrays=false; allocate a new property hash table'. Basically, this means that you cannot say `TAns[1]:x;` without first saying `TAns:[];`. So do not assume that Maxima knows you want to create a list by simply assigning to an index on an undefined variable.
* Maxima has its own `addrow` command, that differs from STACKs version. Please check any questions using `addrow`. Eventually, a conversion will be made that will switch the arguments of function calls in old questions using the old STACK version of that function but the change process will be long.


# Abacus guidelines

Abacus materials are assumed to take into account the future proofing related comments above. In addition to this there are some other rules:

### Naming of questions

The question names are to be in English and the question name is then suffixed with the language codes of the localisations present in the question e.g. 'Eigenvalues and eigenvectors [FI,SV]'. While uniqueness of the naming of questions might be desirable it makes more sense to name the collection (category for those dealing with Moodle) of questions uniquely and keep the individual question names short.

As a special rule if your question contains scripting inside CASText section that would suffer from being opened and saved with a helpful WYSIWYG editor do note that in the name. Maybe a suffix of '[NO WYSIWYG]' or a prefix if you fear that it would not be displayed in the question list due to a long question name.

Use lexicographical ordering to your advantage, so questions are listed in an order which might be helpful when using them in a quiz or a course.

### Keywords & author attribution

'Tags' i.e. keywords can be connected to questions and you are encouraged to use them. You may try to gain access to a listing of most common ones and see if your ideas about them might have been typed slightly differently over there but worry not about creating a new one.

Author attribution in Abacus happens through keys/tags. Your questions should have a tag referencing your home organisation and one for your name. As some platforms do not support spaces in their keywords you may want to play it safe by writing the author tag as 'LastnameFirstname'. If you are significantly modifying an existing question feel free to add your author tag there but do not remove the existing ones.

### HTML

All HTML needs to be valid XHTML. Empty `<p></p>`-tags and `<br/>`-tags are to be eliminated on sight. All content should go through **clear formatting**. Editing using the plain text area editor is strongly recommended, at the minimum authors should take a look at the source code view of their WYSIWYG editor and prune every now and then.

### Model solution

Abacus materials are expected to contain model solutions in the 'general feedback'-field and that solution is expected to apply to the random variables present in the student's question. Feel free to use conditional rendering available through CASText blocks to handle differences in the solution process if the random variables require it. However, in general questions should aim to have the same solution process for all values of the parameters to avoid differences in difficulty. Naturally, questions with wildly different solutions are also useful.

### Question note

The question note must be filled correctly, i.e. all random variables must be present in it. It is also recommended that the note provides a summary of the question and answer, if multiple differing solution processes are possible the note should tell which of those is required in the variant. Question notes are used if one selects specific variants for use and all that information will help then.

### Localisation

Questions are to be localised within the question i.e. there must not exist more than one instance of the same question only differing by the language of the CASText portions. Currently, the localisation is to be done with the multilang filter of Moodle; once better or higher-level solutions are made possible we will automatically convert to them.

### PRT feedback

It is perfectly acceptable to place the PRT-feedback marker within the question text, as opposed to the specific feedback field in Moodle, and it is recommended when dealing with questions with multiple PRTs and inputs. Try to place the feedback near to the things it applies to e.g. feedback for part A at the end of part A before part B starts.

### CAS Code & input and PRT naming

Abacus materials should aim to use English names for the CAS variables, inputs and PRTs to ease debugging by other members. Realistically, when generating new localisations for an existing question the variables should be renamed at the same time unless they are already in English. 

If a variable name could cause confusion you should describe the variable in inline comments. You should use verbose internal variable names but not too verbose, try to aim for less than 10 characters. PascalCase/camelCase is the recommended way of dealing with multi word verbose names.

Naming of PRTs is highly recommended, even `partA` and `partB` will be more descriptive names than `prt1` and `prt2`, naming of PRTs happens when you create them i.e. when you create that PRT-feedback marker.