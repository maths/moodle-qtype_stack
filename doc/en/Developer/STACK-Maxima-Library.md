# New guidelines for the STACK-Maxima-Libary development

In the post STACK 4.12 world the new parser can be used to "compile" parts
of the Maxima side logic of STACK. This compilation also separates Maxima
side unit tests from the code and uses code comments for generation of
documentation of the Maxima side functionalities.

When transferring old logic or adding new logic follow the following simple
guidelines.

- Try to store logically simillar functionality in the same file or place
  every function in separate files if need.
- Naming of the `.mac`-files under `stack/maximasrc/` does not matter so
  be as verbose as you wish, the naming of the diorectory structure however
  is visible and groupping things with it is recommended.
- Include tests for each new function and try to also include tests for
  options that might affect those functions.
- Pay attenttion to the warnings of the compiler.
- And do remember to run those tests.


## How-to

When working on the content of the `stack/maximasrc/` directory you must do
the following steps:

1. If you have changed the directory structure, delete the old generated
   documentation. `rm -rf doc/en/CAS/Library` this is to avoid accumulation of
   orphaned pages referencing old paths.
2. Run the compiler `php cli/stack_maxima_compiler.php`.
3. Does the output from that make sense?
4. Remember to regenerate any images before trying to run the tests in your
   STACK-Maxima, to run the tests simply say the following assuming normal path
   settings `load("maximasrccompiled_tests.mac")`.
5. Remember to commit the logic in `stack/maxima/maximasrccompiled.mac`,
   the tests in `stack/maxima/maximasrccompiled_tests.mac`, and the docs
   under `doc/en/CAS/Library`. 


## Directory docs

Each subdirectory of `stack/maximasrc/` may contain a `description.md` file
describing the tools present in that directory or subtree. Please comment
on common features and functions intended to be used together in this.


## Function and option-variable docs

In the `.mac`-files containing the logic you may place a comment block just
before any top level item in the code. Here are two examples:

```
/**
 * The option `sbasen_construct_default_case` controls the case of the digits
 * `A-Z` (or 10-35) when creating new `stackbasen` objects in logic.
 *
 * Note that this is separate from the option `sbasen_output_force_case`,
 * which controls the presentation of existing, possibly student sourced
 * values.
 *
 * By default this is `"upper"` and the generation will use uppercase letters
 * when need be.
 * The value `"lower"` will naturally use lowercase instead.
 */
sbasen_construct_default_case: "upper"$
```

The first one is for an option-variable, note the use of the comment block
where the first line has that extra `*` and the following lines are likewise
prepended with such a star. Use backticks to wrap code fragments, if
the contents of those backticks is a function-name or option-variable defined
elsewhere in the `stack/maximasrc/` a link to it will be automatically
constructed.

For the second one lets look at a function, where things get a bit more
complicated.

```
/**
 * A function to convert singular C hex format `stackbasen` object to
 * a LaTeX string presentation. By default `\texttt{0xAbc1}` is the output
 * format.
 *
 * Feel free to override this function in the preamble, if the style does not
 * match your needs.
 *
 * This function respects the option `sbasen_output_force_case`.
 *
 * @param[stackbasen] sbasen_num, a single base-N object in the `"C"`-format.
 * @return[string] the matching LaTeX presentation
 */
sbasen_texput_C_hex(sbasen_num) := block([tmp],
	/* @ignore[global=sbasen_output_force_case] */
	tmp: if sbasen_output_force_case = "upper" then supcase(first(sbasen_num), 3)
	elseif sbasen_output_force_case = "lower" then sdowncase(first(sbasen_num), 3)
	else first(sbasen_num),
	tmp: sconcat("{\\texttt{", tmp, "}}"),
	return(tmp)
)$
```

Here, we have the same style of a comment block, now with two special
"annotations" at the end. Firstly, we have
the `@param[datatype] argame, description` annotation describing what sort of
an item we expect as the argument to this function. Note that the description
can be multiple lines long and will continue to the next `@param` or any other
annotation, or the end of the comment block. The second annotation simply
describes the return value of this function and its datatype, it also continues
to the next annotation or the end of the comment.

Inside the function definition we also have one more comment worth noting.
This function behaves differently based on the global option-variable
`sbasen_output_force_case`, however the compilation process check functions
against outside effects and thus needs to know that in this case this is what
we want. When compiling the compiler will give errors and may suggest adding
such annotations into the code or to modify the code otherwise, should you
choose the annotation route place a normal comment with that suggested
annotation inside the statement.


## Compile time checks and conversions

The compilation process not only separates tests from the logic and generates
the documentation it also rewrites some of the logic. You should be aware
of this rewriting as it might make your life easier or cause unexpected
behaviour.

One of the more benign things the rewriting does is the compilation of inline
`castext()`, which may ease writing validators with localised responses.

The more significant thing done is the checking of all variable names used in
functions, to find out any usage of variables that are not local to that
function this is done to avoid unexpected interaction with other parts of
the logic and author/student content. You may need to annotate expected cases
of interaction, and when doing so you should also remember to note them in
the documentation.

As variable names may shadow variables used elsewhere and possibly in
the expressions received as arguments, the compilation process will rename
all arguments and local variables to identifiers that are globally forbidden
and thus cannot conflict with input. If, for whatever reason, this is a problem
add an annotation of the form `/* @ignore[rename=your_variable_name] */` to
protect that variable from renaming.

The intention of the renaming logic is to allow the logic to be written without
worrying about shadowing and for the original code to be easily readable and
verbose.