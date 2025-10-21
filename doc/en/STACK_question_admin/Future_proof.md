# Guidelines for ensuring that a question works in the future

We are committed to long-term support for STACK questions.  The most important things are listed here.

1. All questions must have ["question tests"](Testing.md), which allow unit testing of each question variant.  You can test individual question variables with the `s_assert` function.  Testing helps build questions that detect changes in the platform.
2. Use simple correct HTML with all closing tags, avoiding explicit style of your own.
3. Use only simple core LaTeX Maths environments/commands, with only `\(...\)` and `\[...\]` as the maths delimiters.  *In many projects `$`-delimiters for LaTeX are strictly forbidden*.  (There is a simplistic auto-convert to help with a one-time conversion.)
4. Avoid linking to externally hosted content, such as pictures and applets.
5. End Maxima commands with a semi-colon `;`.
6. Add comments to your Maxima code.
7. **Do not use arbitrary Javascript!**  Future versions of STACK will not support arbitrary Javascript.  Please work with developers to create supported features, e.g. the `[[reveal]]` block is a prime example of this approach working.
8. A STACK question stores the version of STACK with which it was last edited. If we need to make changes the bulk test will use this version number, and search within your question, to identify questions that might need attention.

## Writing CASText

CASText is an HTML-document fragment with inline LaTeX equations, and special blocks.  E.g. special blocks indicate where the input boxes go.  CASText is the definition of the visible parts of the question.

You should ensure that you are writing valid (X)HTML.  Make sure that all the tags get closed and that you are not placing block level elements in places where they do not belong. `<span><div>...</div></span>` is bad while `<div><span>...</span></div>` might be less bad. 

Avoid using inline CSS styles in your CASText which may change and cause conflicts.  In general all styles are bad with the sole exception being `text-align` in the context of table cells. Otherwise:

* If at all possible remove all styles and the `<span>` tags related to them from the CASText. Most WYSIWYG editors have some **clear formatting** feature to do just that. You cannot have block level HTML elements inside a span tag, which is an inline element.
* Do not define `font-size` anywhere. If you need a heading with big text then use the `<hX>...</hX>` tags, but not with too small or large values of X. *In Abacus `<h3>` and `<h4>` are recommended especially in model solutions.*
* Other font style should be left to the surrounding system but feel free to use `<i>`, `<b>`, `<em>`, and `<strong>` if you need and assume that the surrounding system handles them correctly.
* You can use the `<code>` tag to explain how to type in answers.
* The top level of your CASText document should consist of `<p>`, `<table>`, `<div>`, or CASText block elements not of raw text. For the PRT be careful about using `<p>` tags (which editors hide).  If you use the compact PRT style then STACK then puts this into span tags, and you cannot have block level HTML elements such as `<p>` inside a span tag.
* In the case of tables and images you may use a bit of styling, e.g. borders, paddings, margins, and sizes, but you should always use relative units when describing those sizes. Scale to match the current font-size or maybe to the width of the viewport or even to the maximum dimension of the display not to pixels as pixels have very different actual sizes on different screens and we cannot assume that the software displaying things will make the same scaling assumptions for them in the future.
* STACK now SVG, and this is preferred to embed images within the CASText.
* Only use supported JavaScript libraries, like JSXGraph.
* Avoid complex LaTeX constructions where you inject values into multiple places, instead try to tune Maxima LaTeX generation to meet your needs, e.g. [showing working](../CAS/Matrix.md#showing-working-showing_working), using `texput`, or changing other features of Maxima constructs.

If your CASText document contains scripts like JSXGraph content definitions you should ensure that if they require some external files to be evaluated you include those files with the question as links to external files will be broken at some point or the external files themselves change. In the case of JSXGraph use the `jsxgraph` CASText block which will handle the scripts at STACK level.

__All external files/links are bad!__ If you have images or other documents related to the question they should be included in the question. Avoid embedded frames, applets and other interactive content. To test inclusion you should be able to export the question and import it to a freshly installed raw system on a computer not connected to the internet and those questions should work. *There is nothing wrong with internal files of any type, as long as they come with the question.*

STACK does provide the option for [inclusions](../Authoring/Inclusions.md) within questions.  If you regularly use `stack_include` in your questions please consider contributing your libraries to the STACK core code.  Contributing tested libraries is the best way to ensure longer term maintenance.  Use of the [include](../Authoring/Inclusions.md) feature with external source is also a bad thing, but you can make it less bad if the included file is present on a public server and link to a version that never changes.


## Writing CAS code

The CAS code (internally keyvals and CASStrings) consists of Maxima assignment statements. Occasionally, Maxima's behaviour changes, and occasionally there are changes in the STACK provided Maxima functions. Changes cannot be avoided, but you can try to develop your questions based on as new as possible Maxima version to give them longer life. Otherwise there are some details that should be noted:

* End your statements with semicolons (`;`) this will ease your life if you ever need to copy code to Maxima for testing. In the future semicolons may become mandatory, automatic conversion will however handle that change.
* Avoid interesting chars in your variable names e.g. `_` has meaning and it is reasonable to assume that other chars might have new meanings in the future.
* Never write to a variable sharing a name with an input!

You should also heed to these general guidelines:

* Correct answers are generally stored in variables named `ta1`, which match the corresponding input name.
* Should there be need to process the student's answer it might make sense to store it to `sans1`, etc., which match the corresponding input name.
* `texput` instead of manual string construction when generating LaTeX representations will keep your code readable.
* Write comments! More complex code is more likely to break at some point and someone, probably you, will need to understand it and provide a replacement logic for it.


## Question tests & teacher's answers

To detect broken questions we need to be able to test them and the only reasonable way to that is through the question tests. You must define some tests: at least one for each PRT in your question. Preferably provide one correct answer test for each PRT, tests for each branch in the PRTs, and some nonsense inputs to check for 'false positive' style failures.

For manual testing it is important to provide each input with the correct teacher's answer and that teacher's answer must be in a form that matches input requirements.  For example, if you require a floating-point number with a specific number of significant figures then do not specify the teacher's answer as a rational number!  Note that you will need to do the same output formatting for the question tests if your question cares about the formatting of the answer.

Please note that the system administrator has access to a "bulk test" script which runs all the tests on a category. You can use this when you upgrade your site.


# Things that have broken or will break

There have been some changes, which have created broken questions between versions.

* List instantiation in Maxima is now required by Maxima and writing to an un-instantiated list gives errors like 'ARRSTORE: use_fast_arrays=false; allocate a new property hash table'. Basically, this means that you cannot say `TAns[1]:x;` without first saying `TAns:[];`. So do not assume that Maxima knows you want to create a list by simply assigning to an index on an undefined variable.

# Abacus guidelines

The Abacus project is a STACK material sharing organisation that seeks to develop high-quality materials to be implemented following these guidelines. This document ends with additional guidelines adopted by Abacus. This is both good practice, and will be relevant if you ever intend to join Abacus.

Abacus materials are assumed to take into account the comments above. In addition to this there are some other rules:

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