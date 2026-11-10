# `stack_require` style library construction example

This directory contains source code for imaginary libraries `lib1` and `lib2` that have been annotated in such a way that they could be used by `stack_require`, however, they are **not usable** as their corresponding `.stacklib` files are not in the correct place. Those and their corresponding documentation files have been placed here as examples.

You can look at these minimal source files to figure out how to write your own libraries. You do not need to split your libraries to as many files as these have been split to. These have intentionally been constructed to demonstrate as many features as possible and thus have a need for some extra splitting.

Note that while those `.stacklib` files are not in the correct place for normal use they are however used by the unit-tests of the system, you might gain even slightly more insight by reading the `tests/stack_require_test.php`.

## Cyclic dependencies

Both `lib1` and `lib2` have parts that depend on each other. To construct such things one needs to use the comment based annotations and place a comment immediately before the relevant definition or inside it, in the case of tests you must place the comment inside the test. As an example see `lib1a.mac` and the function `lib1a_fun1` and trace the requirements. Obviously, defining a dependency is the first step, you must also construct the relevant library that contains it and that library needs to be present (either in the system or through explicit remote reference) when `stack_require` is used.

## Selective preamble

Library preamble, i.e., code that executes on library load can be made to only apply on specific items in the library, as an example requiring (directly or through dependecies) any item named `lib1b*` defined in the `lib1b.mac` will trigger certain `texput` rules defined in that file to execute. Items in `lib1a.mac` that do not depend on `lib1b.mac` items do not trigger those rules.

All top-level statements that are not definitions using `:` or `:=` operators, is part of the preamble of that file and will be part of the requirements of all items in that file.

## Documentation

Any top-level definitions, by `:` or `:=` operators, can have comment based documentation with certain specific annotations. Those comments will have to start with `/**` and each line of them needs to have that `*` at the start, the content will be interpreted as Markdown in the same way as other STACK documentation. Note that any identifiers, that have no such documentation will not appear in the documentation, nor will any preamble content.

The generated documentation can also include a intro-block which comes from a `.md` file given to the library building tool. In this example see `lib1intro.md` and how it appears in `lib1.md` which is the generated documentation.

## Tests

You may include tests for any library local items in any of the included files. Should you use items that are not locally defined in that library inside that test you must also have a `@require` annotation for them, see `lib2.mac` for such tests. A test is always a definition of the function `s_test_case(simp)` and returns `true` if all is well, the test will be executed with both `simp:false` and `simp:true` so you should define the expected behaviour for both cases. Never define anything affecting the test outside the test and if you have to modify any global variables in the test make sure you only modify them locally in the block of that test function.

Note that the tests will be executed in a context where only the requirements of the test are present and nothing more. As an example of this explore the tests in `lib1b.mac`. Note that it may make sense to have versions of ones tests that bring in extra items to see if there are any unexpected side effects (you might construct things where the effects are intended, but that may require some interesting steps to work around the implicit requirement detection).