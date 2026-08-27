# STACK-Maxima library Documentation

In addition to the [normal documentation](./Documentation.md) found and managed
under the `/doc/` folders there is a part of documentation generated from other
sources. The structure under `/doc/en/CAS/Library/` is built from
`/stack/maximasrc/` by the `cli/stack_maxima_compiler.php`. Should you wish to
modify or expand that documentation follow these instructions.

## Documenting STACK-Maxima sources

The directory tree under `/stack/maximasrc/` can be as deep as one wishes and
can be modified with no effect to the actual systems behaviour it will however
change the documentation addressess and thus affect any links someone might
have to the documentation.

*TODO: we should have a method for linking directly to identifiers no matter
where they currently are. Would need some sort of routing, possibly JS on
the index page?*

The general idea in that directory tree is to place items related to each other
under the same directory to ease browsing by theme, e.g., if it is related to
"base-N" it is probably under the directory hierarchy with a name like
"/basen/", there may also be subdirectories to finetune the topic, e.g., tools
for "binary" operations might be in a subdirectory "/basen/binary/". Each
directory can then have a MarkDown file called `description.md` describing
the common themes of that directory.

### Functions, variables, inert and unbound

The system currently supports documenting functions and variables by placing
a comment-block just before their definition. For inert functions and unbound
variables that do not have definitions in the form of code you may add extra
comment blocks somewhere in the `.mac` files in the directories under 
`/stack/maximasrc/`. Note that those files may also contain test-cases 
(`s_test_case(simp) := ...`) for logic and that one does not need to document
test cases, but if one wishes one can comment in a style that does not lead to documentation. Here is an example of a typical function documentation:

```
/**
 * A functions documentation, is a comment starting with `/**` the second
 * asterisk being important and each line of this comment must start with
 * an asterisk. These asterisks are not part of the actual content.
 *
 * The content can contain arbitrary MarkDown and the only real difference
 * to normal MD is related to code syntax backticks used for identifiers
 * defined elsewhere in the maximasrc, basically, `some_function` will also
 * link to the documentation related to that identifier.
 * 
 * There are also annotations used to describe certain common features of
 * the object in question. In the case of an function the parameters and
 * return value have to be documented. The content of an annotation can be 
 * arbitrarily long and will end at the start of the next annotation or at 
 * the end of the comment block.
 *
 * @param[positive integer] something, a parameter annotation starts with 
 *    the "@" sign and the keyword "param" followed by a description of
 *    the type of an expected value inside brackets, then comes the name
 *    of the parameter which must match the name in the actual code of
 *    the function definition, if it does not the compiler will complain.
 *    That name must be followed by a comma.
 *
 *    Finally one can write as much content as one wishes, it will all be 
 *    considered as a single paragraph unless you add empty lines into 
 *    the content. There are no requirements to indent but it might ease
 *    spotting where we shift to the next annotation.
 *
 * @param[expression] other, in general one should define the overall
 *    behaviour with various types of combinations of parameters in 
 *    the documentation before the annotations and focus on the specific
 *    requirements of the parameters in the annotations.
 *
 * @return[expression] finally a function will return something, again
 *    the type of that returned value should be defined in brackets.
 */
a_function(something, other) := ...$


/* If you need to add comments to the test cases of that function just omit
 * the second asterisk from the opening of that comment. For now commenting
 * tests is not a thing we actively process, and as they are all named
 * `s_test_case` them being defined multiple times might cause some issues,
 * for the other processing.
 */
s_test_case(simp) := is(a_function(2,x)=...)$
s_test_case(simp) := is(a_function(2,x*y)=...)$
```

In the case of a global variable the documentation block starts with the same
extra asterisk but has no need for annotations. Annotations, will however be
needed if the variable is unbound. Here is an example:

```
/**
 * The variable `default_foobar` controls...
 *
 * By default it has the value `fooo`, but you can set it to...
 */
default_foobar: fooo;

/**
 * In the above one `fooo` is obviously somewhat special, and possibly it is
 * an unbound variable with no definition and should not be bound globaly
 * we should document that. We can do it by adding a documentation block
 * somewhere where it is not immediately being followed by some definition
 * of an relevant object. And adding a special annotation to that block to name
 * the item it targets to. Currently, the typical type brackets in
 * the annotation have no role, but that might change in the future so we will
 * include them in the annotation syntax.
 *
 * @unboundidentifier[] fooo
 */
```

Finally, inert-functions require the same kind of a documentation block as
normal functions, but as with unbound variables that block must not be placed
immediately before defintion of something else and must contain an annotation
to to the documentation to the identifier. It also needs to define
the parameters at same time, e.g, `@inertfunction[] some_function(x,y,z)`.
See the source of the documentation for `stackunits` or `stackbasen` for
examples.