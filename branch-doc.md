## The parser branch

**NOT FOR PRODUCTION USE, maybe in 2024-2025**

In this branch the old PEG.js parser for Maxima input has been replaced with 
a custom self built one. The primary reason for this is to have a proper
separate lexer for the parser, some performance improvements and fine tuning
of certain error handling are the other reasons.

### Key features

 1. The parser is modular, you can pick and match the lexer and the parser
    itself according to your needs. Basically, you select if you do the "Root"
    parsing or the "Equivline" and in addition to that pick a lexer to decide
    what exactly is an integer or what separators one uses for various roles.

 2. There are plenty of parameters related to parsing, especially locale
    related parameters. Use the `stack_parser_options` class to pass those
    options around. Use it for all things tied to representation of the parsed
    content, also make it the place where the lexer gets chosen based on
    the options.

 3. Lexers can tie into unicode mappings if need be. For CAS-communication it
    is however recommended that one uses a lexer that does not map anything
    and has minimal amount of options, as the lexer is the primary expense
    in our parsing. Basically, don't use too fancy lexers if possible.

 4. Insert-stars and fix-spaces now happen in the parser and in some cases in
    the lexers, one can control if it happens and whether we are insertting
    `*` or `;` but there is no need to have a separate corrective parser.    

### New things

 1. The parser can now parse the whole Maxima code of STACK (not the lisp 
    files though), this is used to allow compilation of such code. The aim
    here is to allow use of compiled features like inline-CASText and any
    similar ones that may come up, also the possibility of including 
    unit-tests within our Maxima side code and extracting them during such
    a compilation step is a feature that we might want to explore.
    Naturally, such a compilation could also collect documentation from
    comments and build an indexed doc for our own functions.

    For this some functions in `stack/maxima` have been moved as samples
    to single function files under `stack/maximasrc` where there are 
    additional thematic subdirectories. The idea is that by running
    `cli/stack_maxima_compiler.php` one would then generate some products.

    The primary product being `stack/maxima/compiled.mac` which is being
    imported though normal `stackmaxima.mac` importting and brings those
    moved samples back. The secondary products are the `compiled_tests.mac`
    which can be executed to run our own Maxima-side tests, and 
    the documentation under `doc/en/CAS/Library`.
  
 2. The option to localise syntax, by setting certain options of the parser
    one can start using continental commas and digit grouping (thousand
    separators). Note that these things are converted back to CAS-syntax
    during localisation, and one needs to apply certain `toString`
    parameters to represent the parsed content as it was back to the student.
    Those paramters can be extracted from the `stack_parser_options`.

 3. When a lexer decides what an integer is it could just as well decide that
    a hexadecimal string is an integer, thus allowing base-n to flow
    everywhere should it do that then there needs to be AST-filter level
    post-processing and CAS level logic to deal with those base-n literals.
    The current plan is that if a lexer allows those i.e. there has been
    a setting active in `stack_parser_options` and a base-n lexer has been
    selected then one needs to trigger a conversion that would do something
    like `0xBEEF` -> `stack_basen("0xBEEF")`. Possibly, with PHP side
    conversion and error reportting, leading to 
    `stack_basen(48879, 16, "C-style")`.


### TODO

 1. Needs to match error messages, or improve them. Can improve as we can get
    more context from the parser. Can also receive partial parsing results,
    should one wish to validate them. Might be intersting, but unlikely to be
    beneficial to warn about things present in partial results.

 2. Clean up various old usages of the old. Map all parsing to be based on
    `stack_parser_options` based logic and interfaces. No parser selection,
    calling or tuning at the point of parsing, just describe what you want
    with the options-class and ask for a parse result with it.

 3. Build the localised parsing option hierarchy, i.e. where one sets
    the parser locale and where one can override it. Also needs user facing
    notifications to tell the user that unlike previously we now speak their
    own locale.

 4. Fine tune the parser code-generation logic to produce code that pleses
    the Moodle code-style checker.


### Next steps
 
 1. Now that inline-CASText becomes usable for our libraries we really should
    start converting answertests and other CAS side sources of localisable 
    output to use it


### Notes

 1. The CASText2 parser is still a PEG.js parser. At some point it might be
    replaced as well.
 
 2. The parser generator can generate multiple language versions, for now it
    only produces PHP parsers and there are some broken fragments of a TS
    parser present in the grammars, assume that at some point those TS
    fragments will change or maybe get dropped.


### Development guidance

If trying to match error messages, the key tool for this is to run the tester
with the `--exception` argument you can then see all the details, you will 
probably also want to limit to a particular lexer:
```
> php cli/parser_tester.php --string="fail...3;" --only=CAS --exception
```
Without that argument you would only see the translated exception, to tune
the translation look at `maxima_parser_utils::translate_exception`.


The `parser_tester` is also the primary tool for testing lexers, if building
a new one or wanting to test a particular configuration feel free to add
the config to the tool, there is a simple array there. By default it will run
all configs, so for now that `--only` argument is somewhat overused.



If you want to adjust the generated documentation, work on the compiler script
at `cli/stack_maxima_compiler.php`.



Tuning the parser itself can be done by:

 1. Tuning the grammars `stack/maximaparser/autogen/bottom-up-grammar...`
    and then regenerating the LALR-tables with 
    `stack/maximaparser/autogen/lalr-....js` and finally regenerating the PHP
    parser with `stack/maximaparser/autogen/php-generator-....js`.
    If the table generation gives errors, you grammar is probably unclear.

 2. Simply tuning the parser generator is also an option if one wants to for
    example add soem extra information to the exceptions, just make sure that
    all the parsers (Root and Equivline for now) behave the same. Note that 
    the rule specific code is in the grammar, and in that you need to signal 
    indentation with the magical char `>` at the start of a line.
