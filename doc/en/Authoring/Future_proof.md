# Guidelines for ensuring that a question works in the future

Creating a great STACK question takes effort and often people worry about how updating STACK or Moodle or other components running those questions might affect their questions. In general no guarantees can be made about questions surviving every update but while authoring questions you can increase the chances of your guestions by taking the following things in account. The primary thing however is to build guestions so that detection of possible breakdown due to changing platform is easy and that the question is easy to parse and thus possible to automatically fix.

In some parts of this document we mention Abacus it is a STACK material sharing organisation that prefers its materials to be implemented in a certain way. This document ends with a version of Abacus guidelines as they are currently understood, might be relevant if you ever intend to join Abacus.


## Writing CASText

CASText is the definition of the visible parts of the question, the bit where you pose the problem to the student lay out the equations and inputs and possibly draw fancy graphics. Technically, CASText is an HTML-document fragment with inline LaTeX equations and possible logic in the form of CASText blocks.

To ease parsing of CASText you should ensure that you are writing valid (X)HTML i.e. make sure that all the tags get closed and that you are not placing block level elements in places where they do not belong. `<span><div>...</div></span>` is bad while `<div><span>...</span></div>` might be less bad. In the case of LaTeX using `\(...\)` instead of `$...$` and `\[...\]` instead of `$$...$$`, *in Abacus `$`-delimiters for LaTeX are strictly forbidden*.

To ensure consistent presentation you should avoid using styles in your CASText, as the styles of the surrounding system may change and cause conflicts. In general all styles are bad with the sole exception being `text-align` in the context of table cells. Otherwise:

* If at all possible remove all styles and the `<span>` tags related to them from the CASText. Most WYSIWYG editors have some **clear formatting** feature to do just this.
* You should not define `font-size` anywhere. If you need an heading with big text then use the `<hX>...</hX>` tags, but not with too small or large values of X.
* Likewise all other font stylings should be left to the surrounding system but feel free to use `<i>`, `<b>`, `<em>`, and `<strong>` if you need and assume that the surrounding system handles them correctly.
* Paragraphs are your friends use `<p>...</p>` tags and do not leave your content as raw text. The top level of your CASText document should consist of `<p>`, `<table>`, `<div>`, or CASText block elements not of raw text.
* In the case of tables and images you may use a bit styling e.g. borders, paddings, margings, and sizes but you should always use relative units when describing those sizes. Scale to match the current font-size or maybe to the width of the viewport or even to the maximum dimension of the display not to pixels as pixels have very different actual sizes on different screens and we cannot assume that the software displaying things will make the same scaling assumptions for them in the future.

If your CASText document contains scripts like JSXGraph content definitions you should ensure that if they require some external files to be evaluated you include those files with the question as links to external files will be broken at some point or the external files themselves change. In the case of JSXGraph use the `jsxgraph` CASText block which will handle the scripts at STACK level.

All external files are bad! If you have images or other documents related to the question they should be included in the question. To test inclusion you should be able to export the question and import it to a freshly installed raw system on an computer not connected to the internet and those questions should work.



## Writing CAS code

The CAS code (internally keyvals and CASStrings) consists of Maxima assignment statements and can suffer from Maximas behaviour changing as well as changes in the STACK provided Maxima functions. There is little you can do about that other than trying to develop your questions on as new as possible Maxima version to give them longer life. Otherwise there are some details that should be noted:

* End your statements with semicolons (`;`) this will ease your life if you ever need to copy code to Maxima for testing. In the future semicolons may become mandatory, automatic conversion will however handle that change.
* Avoid interesting chars in your variable names e.g. `_` has meaning and it is reasonable to assume that new chars might have new meanings in the future.
* Newer write to a variable sharing a name with an input!

You should also heed to these general guidelines:

* Teachers answers are generally stored in variables named `tAns`, `tans1`, or maybe `TAns3` this is a pretty widely spread convention and should be followed, in general the question variables should end with a listing of assignments defining the teachers answers for each input.
* Should there be need to process the students answer it might make sense to store it to `sAns...`.
* ´texput´ instead of manual string construction when generating LaTeX representations will keep your code readable.
* Write comments! More complex code is more likely to break at some point and someone, probably you, will need to understand it and provide a replacement logic for it. Especially, long oneliners have tendency to mystically break.


## Question tests & teachers answers

To detect broken questions we need to be able to test them and the only reasonable way to that is through the question tests. So do define some of them atleast one for each PRT in your question. Preferably provide one correct answer test for each PRT, tests for each branch end in the PRTs, and some nonsense inputs to check for 'false positive' style failures.

For manual testing it is important to provide each input with the correct teachers answer and that teachers answer must be output formatted so that it would work as raw input e.g. if you require specific number of significant figures and float form input providing a fraction as the teachers answer is not the correct way to go. Note that you will need to do the same output formatting for the question texts if your question cares about the formatting of the answer.


# Things that have broken or will break

Some things have been seen to break during the last years due to various components updating, here are some of those:

* List instanttiation in Maxima is now required by Maxima and writing to an uninstanttiated list gives errors like 'ARRSTORE: use_fast_arrays=false; allocate a new property hash table'. Basically, this means that you cannot say `TAns[1]:x;` without first saying `TAns:[];`. So do not assume that Maxima knows you want to create a list by simply assigning to an index on an undefined variable.



# Abacus guidelines

Abacus materials are assumed to take into account the future prooffing related comments above. In addition to this there are some other rules:

### Naming of questions

The question names are to be in English and the question name is then suffixed with the language codes of the localisations present in the question e.g. 'Eigenvalues and eigenvectors [FI,SV]'. While uniquenes of the naming of questions might be desirable it makes more sense to name the collection (category for those dealing with Moodle) of questions uniquely and keep the individual question names short.

As a special rule if your question contains scripting inside CASText section that would suffer from being opened and saved with a helpful WYSIWYG editor do note that in the name. Maybe a suffix of '[NO WYSIWYG]' or a prefix if you fear that it would not be displayed in the question list due to a long question name.

### Keywords & author attribution

'Tags' i.e. keywords can be connected to questions and you are encouraged to use them. You may try to gain access to a listing of most common ones and see if your ideas about them might have been slightly differently typoed over there but worry not about creating a new one.

Author attribution in Abacus happens through keys/tags. Your questions should have a tag referencing your home organisation and one for your name. As some platforms do not support spaces in their keywords you may want to play it safe by writing the author tag as 'LastnameFirstname'. If you are significantly modifying an existing question feel free to add your author tag there but do not remove the existing ones.

### HTML

All HTML needs to be valid XHTML. Empty `<p></p>`-tags and `<br/>`-tags are to be eliminated on sight. All content should go through **clear formatting**. Editing using the plain text area editor is strongly recommended, at the minimum authors should take a look at the source code view of their WYSIWYG editor and prune every now and then.

### Model solution

Abacus materials are expected to contain model solutions in the 'general feedback'-field and that solution is expected to apply to the random variables present in the students question. Feel free to use conditional rendering available through CASText blocks to handle differences in the solution process if the random variables require it. However, in general questions should aim to have the same solution process for all values of the parameters to avoid differences in difficulty. Naturally, questions with wildly different solutions are also useful.

### Question note

The question note must be filled correctly, i.e. all random variables must be present in it. It is also recommended that the note provides a summary of the question and solution.

### Localisation

Questions are to be localised whitin the question i.e. there must not exist more than one instance of the same question only differing by the language of the CASText portions. Currently, the localisation is to be done with the multilang filter of Moodle, once better or higher level solutions are made possible we will automatically convert to them.

### PRT feedback

It is perfectly acceptable to place the PRT-feedback markker in the question text and it is recommended when dealing with questions with multiple PRTs and inputs. Try to place the feedback near to the things it applies to e.g. feedback for part A at the end of part A before part B starts.

