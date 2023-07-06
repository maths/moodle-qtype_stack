# Static blocks

Static blocks are mostly used when a random version is created, rather than relating to managing dynamic content such as Javascript.

## Lang block ##

A new feature in 4.4 is a STACK specific localisation mechanism that allows one to output differing text based on the language the student has chosen in their VLE.

    [[lang code='fi']]...Text in Finnish...[[/lang]]

Read more about this in the [languages](../Languages.md) documentation.

## Comment blocks ##

Comment blocks allow you to put content into CASText which will not be seen by students.

    [[ comment ]] Place comments here which will not appear to students. [[/ comment ]]

Before 4.4 the contents of the block needed to be syntactically correct CASText. That is no longer the case and you can much more easily use this block to comment our unfinished stuff.

## Todo blocks ##

"todo" blocks allow you to put items into CASText which indicate future work needed.  This will not be seen by students.

    [[ todo ]] Place requests to collaborators here. This will not appear to students. [[/ todo ]]

Any question with a todo will flag an error in the bulk tester.  This will _not_ throw an error in the editing form.  These blocks can also be found by the dependency checker.

The todo block is similar to the comments block.  A different block is provided to facilitate searching for questions with specific "todo" items remaining.  The contents must be valid castext (unlike the comments block which can be anything) because in the future we may extend the functionality to display todo items in a teacher preview.  If you need to include invalid content either use the comment block, or escape block inside the todo, e.g.

    [[todo]][[escape]]...[[/escape]][[/todo]]

The contents of this block are replaced by the static

    <!--- stack_todo --->

to provide a searchable tag in instantiated text which is not visible in regular html, e.g. in the dependency checker.

## The debug block ##

The special "debug" block allows question authors to see all the values of variables created during a session in a table.  Do not leave this block in a live question!

    [[ debug /]]

## Format blocks ##

In general CASText is assumed to be written in the format (Markdown, raw HTML, Moodle auto-format) that Moodle defines and which can be selected in the editor if one uses the plain text area editor. However, there are cases where one might need to mix formats withing the CASText itself, one of those cases is the inclusion of content written in another format. In these cases one can wrap the differing part in blocks that declare the format to use for that portion. The blocks used for this are named `[[moodleformat]]`, `[[markdownformat]]`, and `[[htmlformat]]`. In the end all CASText evaluates down to HTML, even if it were written in Markdown-format it will be rendered down to HTML.

## Textdownload block ##

STACK can construct a text-file using CASText and provide a link to it for download. This is obviously a way for serving out randomised data to the student. Do note that you can generate whatever you want as the content of that file, one could even generate a LaTeX template with question specific values for the student to fill things in. Read more about [serving data out](../Serving_out_data.md).

## Include block ##

A new feature in 4.4 is the ability to include content from an URL. The include block allows one to do that. However, it is not a recommended tool for novices and all users choosing to use it should consider what it means for the future maintenance and shareability of your questions. See the specific documentation on [include logic](../Inclusions.md).

## Template block ##

Since 4.4.2 it has been possible to use templates to handle repetitive content or to override content deeper in libraries. Templates are essentially a way for handling repetition when `[[foreach]]` does not easily work or when inline CASText based function solutions are inconvenient. While inline CASText is often better it might not work as well as overridable templates when working with libraries.

The template block has two parameters, the first being a name which should be a valid name for a function and the second being the mode parameter that controls the blocks behaviour and is of use especially for library builders. There are three different ways for using this block:

1. To define a templates value for a given name one simply wraps that value in this block with that name. `[[template name="foobar"]]Whatever is {@whatever@}[[/template]]`. This will not output anything and can also be done in inline CASText either in the question-variables to effect the whole question or in feedback-variables to effect PRTs.
2. To output that template, one simply uses the empty block form `[[template name="foobar"/]]` which will output whatever has been defined as that templates value or a warning about the template not been defined. One can add a mode parameter `mode="ignore missing"` to not see that warning. Typically, one will use the `[[define/]]` block to change the values used within the template.
3. For library makers the most common operation mode is the `mode="default"` where the contents of the block are used if no overriding definition can be found. The default value will not define a default template and this intentional, if a template is to be shared then it needs to be defined at a global level where it always gets evaluated while default templates tend to be sensible to use even in conditionally evaluated contexts. Basically, if your library has any CASText that could benefit from being overridable you simply give it a name and wrap it with `[[template name="libarary_xyz" mode="default"]]...[[/template]]` and then maybe document somewhere that this name has this default where these injectable variables have these roles so that people may replace the wording and structure and still use the same values.

Note in the background templates are just functions with CASText values. You can do the same with inline CASText and more importantly building your own functions allows you to use arguments for them and thus makes repetition with varying parameters simpler. For templates no arguments exist, for them the values come from the context where they get placed in, and must therefore be controlled though other means.

