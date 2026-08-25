# Granular inclusion based contrib libraries

The [inclusion logic provided by `stack_include`](../Authoring/Inclusions.md) is meant for small blocks that are expected to be fully active. This presents a problem when one might have a large toolbox of related tools and does not want to bring the whole thing into the question to slow things down. Granular inclusions are an option for selectively bringing in items, typically functions, from such larger toolboxes with only the dependencies those items might need.

To effectively use this functionality one needs to have pre-processed libraries, from which one then loads items into the question. This page tries to go over the tools provided for managing such libraries.

## How it works?

Firstly, we take all the source code of the library, possibly multiple files of it and parse it with a tool that identifies any local dependencies in that set of files. That mapping is then stored as a `.stacklib` file. When one then requests something to be included we simply fetch that part and all things it refers from such a file or multiple such files if there are separately declared external dependencies. For an example, if we have the following "library":

```
/**
 * A function
 * @param[integer] a param
 */@return[boolean] returns something
predicatex(x) := if ev(is(x < 1),simp) then true else foobar(x);

/**
 * A function
 * @param[integer] a param
 */@return[boolean] returns something
foobar(x) := block([simp:true],
	/* @require lib2/fun1 lib2/fun2 */
	return is(fun1(x) < fun2(x))
);

/* The source can also contain test-cases, those are naturally not included in the included content. 
 * However, if you intend to support the use of `stack_include` as well you will probably keep
 * the tests in a separate file.
 */
s_test_case(simp) := is(foobar(3)=false);
```
Then we would get a requirement mapping like this, where the reference for `foobar` is automatically identified, but the `lib2` references need to be declared with an annotation:
```
'predicatex' -> ['local/foobar']
'foobar' -> ['lib2/fun1', 'lib2/fun2']
```

With such a mapping, existing as a `.stacklib` file in the expected place, one can then use the `stack_require` macro to load in a function and all its requirements, lets call the upper sample `lib1`:
```
stack_require("lib1", ["predicatex"]);
``` 

You can require multiple identifiers at the same time or separately, in singular or multiple `stack_require` calls. The preferred way is to write a singular `stack_require` statement unless you are requiring things into the preamble (i.e., when using `%_stack_preamble_end`), if so only bring in the relevant bits into it and load the rest after that.

```
stack_require("validator_lib", ["validator1"]);
...
%_stack_preamble_end;
...
stack_require(
	"heavy_stuff", ["bigtool1","bigtool2"],
	"other_stuff", ["that_other_thing"],
	"validator_lib", ["extra_tools"]
);
...
```
That latter `stack_require` will be aware of things loaded by the previous one, and won't reload them or their shared dependencies, as long as they exists in the same block of code, e.g., in question variables. In general, if you are going to need something later in the questions, e.g., in feedback variables do load it together, to the question scope, with everything else, only in the case of very rarely activating PRTs does it make some limited sense to keep whatever they need as a separate load inside them, however, that is not a sensible optimisation.

### Preamble in libraries

In addition to STACKs own preamble, used to separate the part of question variables that needs to be present during validation, libraries can also have preamble logic, which executes during the load of that library. Anything, that is present in the code of that library that is not a definition of a function (using the `:=` operator) or a variable (using the ':' operator), will be considered part of the preamble. That preamble will be considered as a requirement for all function and variable definitions in that specific file, using `stack_require` to bring in any identifier that was defined in a file with such content will also bring in that content. This feature can be useful to trigger definition of specific `texput` rules or other rules needed by the library.

As an example of this in action, loading parts of the linear algebra libraries will trigger the loading of the defintion of `stack_linear_algebra_declare` and following that the execution of that specific command, leading to certain rules to come into play.

Note that any `@require` annotations in the preamble are naturally also requirements for all the items defined in the library. Do note that the `@require` annotations are only extracted from comments inside statements and from the comment immediately before the statement, and in the case of tests only from internal comments.

### Bulk loading identifiers

There is no `*` identifier to automatically load everything from a library, i.e., you can't say `stack_require("libary",["*"])`, that would be silly for a tool that aims to minimise extra things to load. If the library developer has a real need to load subsets of the library, i.e., multiple items that don't require each other and thus automatically load together, they can use the `@require` annotation logic to declare dependencies and connect multiple items to an identifier.
``` 
/**
 * Loads the common toolbox for X.
 * @require local/A local/E local/G lib3/Y
 */
toolboxX: true;
```
After which requiring the identifier `toolboxX` would bring in all the referenced items. Hopefully, those items represent only a small subset of the library and are all used when loaded.



## The command line tool

If you are creating a library, you will need to use the command line tool to process your code into the manifest. This means that you need to have a functioning STACK installation and access to the command line in it. If you have such a thing then the general procedure is as follows:

 1. Place the raw code files of your library and any possible primary documentation fragment into a directory under the STACK source code tree, e.g., under `/stack/maxima/contrib/`. Prefereably, name all files with your library name as a prefix.
 2. Then execute the command lien tool by giving it as arguments, your librarys name and after that all the files it is constructed from. The code files being `.mac` files with content that could be placed directly to question variables and the documentation fragment being a `.md` file with a section of text to be included in the generated documentation. The tool, `cli/contriblibrarybuilder.php`, may require you to provide full paths to the input files.
 ```
cd .../stack/
php cli/contriblibrarybuilder.php libraryname /.../libraryname.mac /.../libraryname_tests.mac /.../libraryname.md
 ```
 3. The result will be written to `stack/maxima/contrib/libraryname.stacklib` and `doc/en/CAS/ContribLibraries/libraryname.md`. And is immediately usable in that STACK installation.

## Prototyping without the tool

If you are just developing a library and are able to serve the code files though an http(s) address you can use a special URL to compile the library manifest during question compilation. This is a method that can be useful when quickly iterating on your library code. However, that compilation will take time and does not produce persistent manifests nor documentation. It is also impossible to refer to such a runtime compiled library from other libraries.
```
stack_require("genmanifest: https://example.com/code1.mac https://example.com/code2.mac", ["fun1","fun2"]);
```
Note that you could use this to load older non granular libraries and try to extract parts from them, but there is no guarantee that that would work, i.e., some requirements might not be detectable by the automatic detection logic, and it would be slow during compile time.

## Remote library loading

The `stack_require` tool is primarily focused on fetching libraries from the local STACK installation, however, it can be made to fetch a `.stacklib` file from a remote http(s) address, just like `stack_include`. However, this requires that the address matches the following regexp pattern and that it only matches it once.
```
http.*/([a-zA-Z0-9_]+)\.stacklib
```
Basically, the name of the library must be obvious. The following addresses represent styles that might be in use:
```
https://example.com/stacklibs/lib1.stacklib
https://example.com/stacklibs/lib1.stacklib?version=2.2
https://example.com/stacklibs/lib1.stacklib/v2.2
``` 
The name is required to allow overriding of a local version of the same name for requirements of other libraries. Do note that you may not load the same libraryname from multiple sources (into the same keyval block) the following is an immediate compile time error.
```
/* This will be a compile time error due to library name collision. */
stack_require(
	"https://example.com/stacklibs/lib1.stacklib/v2.2", ["fun2"]
	"lib1", ["fun1"]
);
```