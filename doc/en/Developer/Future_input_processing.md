# Description of one possible future input processing pipeline

In the recent (documentation)[../Authoring/Syntax.md] there has been 
described how input processing starts to have too many options to fit 
the original "insert stars" model and how things could be expanded in 
the short term as we now are getting an overhaul of the casstring 
processing in the form of Maxima-statement parsing in 4.3. This is 
however something that should be thought of in larger scale and this 
document present one way of trying to deal with the complexity of 
the whole issue.

## The problem

Various inputs have various rules about what kind of input is being 
expected. The rules vary from forbidding floats and requiring the responses
type to match the type of the teacher's answer, to actually fixing the input
syntax in the form of insertion of missing stars and conversion of spaces
to stars. In addition to this some inputs may even declare additional 
syntax e.g. the way the line by line reasoning works. There are also some 
complex CAS-evaluation based requirements that some inputs may ask for 
like representing the answer in lowest terms when working with fractions.

Those are the kinds of things inputs declare for student input processing
and most of those are not trivial and have interactions with certain other
features we have in our validation chain.

The CAS-statement processing pipeline also includes some amount of syntactic
candy in the form logarithms of various bases being representted in 
complex ways from the statements, for example `log_x+y(z) => lg(z, x+y)`.
In addition to that kind of syntax expansions there also exists various 
rules that check for typical errors like `sin^2(x)` which will need to be
dealt in the correct place of the processing pipeline, typically before 
input level insert stars rules get applied. That latter example will break 
down quite badly if someone applies a rule that splits all multiletter 
variable names to multiplications of single letter names, before we identify 
that `sin` in this case is actually a badly written function-call that needs
to be specially pointted out to the student.

The current pre 4.3 situation is complex because most of such rules are mixed
in various processing steps and much of the logic that the inputs themselves
should handle is actually built into the complex casstring-class making it 
difficult to easily separate features and/or place new features into the mix.

## One way of looking at the casstring processing

For teacher/code sourced casstrings the logic is relatively simple:

 1. Direct parsing of the statement without any corrective pre-processing. 
    Generates an AST style representation of the statement.
 2. That AST will be piped through syntactic candy filtters to allow use
    of various features like the logarithm logic.
 3. In the end that AST will be explored for security issues.

For the pipeline of student sourced things is more complex:

 1. The parsing will go through a corrective parser that tries to add 
    missing stars into places where they would make the input syntactically 
    valid. In addition to this the parsing may apply additional syntax specific
    to the input type. Those inserted stars will be tagged in the resulting 
    AST.
 2. Syntactic candy filters will be applied to the AST.
 3. Early phase feature detection will be applied to the AST, without 
    modification of the AST, `sin^2(x)` detection and similar pattern matching.
    This detection may lead to processing marking a whole subtree of the AST
    as invalid so that insert stars of other remaining steps do not modify it
    further.
 4. Additional star insertion, based on the input's preferences, will be applied
    to the AST. Again added stars will be tagged in the AST.
     - common variable/constant identification `xpi => x*pi`
     - common function identification `xsin(x) => x*sin(x)`
     - elimination of all or just undefined function calls `sqrt(x) => sqrt*(x)` 
       and `sqrt(f(x)) => sqrt(f*(x))`
     - splitting of multi char variables `xy => x*y` and `x1 => x*1` with 
       protection of known identifiers `pialpha => pi*alpha` as opposed to
       `pi*alpha => p*i*a*l*p*h*a`
     - splitting by number/letter boundaries `x1y => x1*y`
     - float splitting `0.2e-3 => 0.2*e*-3` if wished
 5. Syntactic validity check, if the input requires that all the stars are 
    correct this step will check that the are no tagged added stars in the AST
    nor any invalid subtrees for that matter. Is such exist error messages will 
    be generated and they will mark those stars.
 6. Security check. If fails things stop here. Note that part of the security
    check rules are based on options to the input and part to the the whole 
    question.
 7. Simple feature checks, things that the input may require and that can be 
    easily checked from the AST:
     - if floats are forbidden then the AST should have none.
     - if we have some form of type constraint then the AST should fit it.
     - if significant figures or decimal places matter they should be checked 
       from the AST.
 8. Complex feature checks, things like lowest terms need to be sent to CAS to
    be checked only after these (if they exist) have been checked can we say
    wether the input is valid. These checks should not be tied to the AST they
    should happen using it but through separate CAS-statements

One way to look at this is that inputs should have means of doing much of
the low level parsing themselves, and there must be ways of them to select ready
made AST-filters to be added to the process. The other point from this is that
casstrings should not do parsing themselves they should just receive ready ASTs
and the parsing and insert stars logic are not part of validation of
the casstring the only validation the casstring does is the validation of
security, insert stars and other tricks are part of input validation.
In some sense this makes casstrings just the containers of the AST and probably
the results of its evaluation, it however makes sense for those containers to
contain the most important bit of all i.e. the security checking. It also makes
sense to use the casstring object as a wrapper of the AST and the store of
the error messages, which leads to the conclusion that casstrings get created
from the results of step 2 in those processes and need to have interfaces into
which various AST filtters get pushed into to update the AST before the final
validation.

It also makes sense to collect step 8 related statements from multiple inputs 
together for validation and that those statements also include whatever is 
needed to represent the validity to the student. Likewise step 7 probably bases
its behaviour on the teachers answer or other parameters of the input and should
also be collected from multiple inputs for single pass validation.

## Class structure

Based on the presented we need the following classes with at least the 
following methods, stack_cas_cassecurity is the very same it is in 4.3:

```
class stack_cas_casstring {

    /**
     * Executes an AST filter on the AST defining this casstring.
     */
    public function filter_ast(stack_cas_astfilter $filter) {}

    /**
     * Validation is now based only on the security settings and the current AST.
     */
    public function validate(string $security_level, stack_cas_cassecurity $rules): bool {}

}

class stack_cas_astfilter {
    
    /**
     * Does whatever it needs to the AST and may append to the errors or notes
     * might receive stack_cas_casstring directly, but better to keep these 
     * separate.
     */
    public function filter(MP_Node $ast, array &$errors, array &$answernotes): MP_Node {}

}

```

The idea here is that once the code is in this form whenever one wants to build
syntactic candy or error checks one builds a new stack_cas_astfilter and if it
is something central it will probably go to step 2 of the processing otherwise
it will be a part of the student input pipeline and under the control of
inputs. In any case one can write tests for it separate from everything else,
but one will need to also define its place in the processing order and test 
the whole chain as some other filter might appear in that chain and eat up
the things. To keep the order in order I would recommend that the files 
containing filter code are prefixed with something like '012_' and that we 
leave plenty of empty room if at all possible in that range, for future filters
to be plugged into the sequence. Ideally, those numbers should also be present
in the classnames, but obviously not as prefixes there.
