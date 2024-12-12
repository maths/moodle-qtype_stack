# Input options

This is reference documentation for the input options.

## Options summary table ##

This table lists all options, and which inputs use/respect them.  The `.` means the option is ignored.

Options           | Alg | Num | Units | Matrix | Check | Radio | Drop | T/F | TextArea | Equiv | String | Notes
------------------|-----|-----|-------|--------|-------|-------|------|-----|----------|-------|--------|------
Box size          |  Y  |  Y  |  Y    |   Y    |   .   |   .   |   .  |  .  |    Y     |   Y   |   Y    |   Y
Strict Syn        |  Y  | (1) |  (1)  |   Y    |   .   |   .   |   .  |  .  |    Y     |   Y   |   .    |   .
Insert stars      |  Y  |  Y  |  Y    |   Y    |   .   |   .   |   .  |  .  |    Y     |   Y   |   .    |   .
Syntax hint       |  Y  |  Y  |  Y    |   Y    |   .   |   .   |   .  |  .  |    Y     |   Y   |   Y    |   Y
Hint att          |  Y  |  Y  |  Y    |   Y    |   .   |   .   |   .  |  .  |    Y     |   Y   |   Y    |   Y
Forbidden words   |  Y  |  Y  |  Y    |   Y    |   .   |   .   |   .  |  .  |    Y     |   Y   |   .    |   .
Allowed words     |  Y  |  Y  |  Y    |   Y    |   .   |   .   |   .  |  .  |    Y     |   Y   |   .    |   .
Forbid float      |  Y  |  Y  |  Y    |   Y    |   .   |   .   |   .  |  .  |    Y     |   Y   |   .    |   .
Lowest terms      |  Y  |  Y  |  Y    |   Y    |   .   |   .   |   .  |  .  |    Y     |   Y   |   .    |   .
Check type        |  Y  |  Y  |  Y    |   Y    |   .   |   .   |   .  |  .  |    .     |   .   |   .    |   .
Must verify       |  Y  |  Y  |  Y    |   Y    |   Y   |   Y   |   Y  |  Y  |    Y     |   Y   |   Y    |   .
Show validation   |  Y  |  Y  |  Y    |   Y    |   Y   |   Y   |   Y  |  Y  |    Y     |   Y   |   Y    |   .
**Extra options:**|     |     |       |        |       |       |      |     |          |       |        |
`rationalize`   |  Y  |  Y  |  .    |   .    |   .   |   .   |   .  |  .  |    .     |   .   |   .    |   .
min/max sf/dp     |  .  |  Y  |  Y    |   .    |   .   |   .   |   .  |  .  |    .     |   .   |   .    |   .
`floatnum`      |  .  |  Y  |  .    |   .    |   .   |   .   |   .  |  .  |    .     |   .   |   .    |   .
`intnum`        |  .  |  Y  |  .    |   .    |   .   |   .   |   .  |  .  |    .     |   .   |   .    |   .
`rationalnum`   |  .  |  Y  |  .    |   .    |   .   |   .   |   .  |  .  |    .     |   .   |   .    |   .
`consolidatesubscripts` |  Y  |  .  |  Y    |   Y    |   .   |   .   |   .  |  .  |    .     |   .   |   .    |   .
`negpow`        |  .  |  .  |  Y    |   .    |   .   |   .   |   .  |  .  |    .     |   .   |   .    |   .
`simp`            |  Y  |  Y  |  Y    |   Y    |   .   |   .   |   .  |  .  |    Y     |   .   |   .    |   .
`align`        |  Y  |  Y  |  Y    |   .    |   .   |   .   |   .  |  .  |    .     |   .   |   .    |   .
`nounits`      |  Y  |  Y  |  Y    |   Y    |   Y   |   Y   |   Y  |  .  |    .     |   Y   |   .    |   .
`checkvars`    |  Y  |  .  |  .    |   Y    |   .   |   .   |   .  |  .  |    .     |   Y   |   .    |   .
`validator`    |  Y  |  Y  |  Y    |   Y    |   .   |   .   |   .  |  .  |    .     |   .   |   Y    |   .
`feedback`    |  Y  |  .  |  Y    |   Y    |   .   |   .   |   .  |  .  |    .     |   .   |   .    |   .

For documentation about the various options not documented on this page look at the pages for the specific inputs in which each option is used.

Notes:

1. The numerical and units input type ignore the strict syntax option and always assume strict syntax is "true".
Otherwise patterns for scientific numbers such as `2.23e4` will have multiplication characters inserted.

### Extra option: hideanswer ### {#extra_option_hideanswer}

Users are increasingly using inputs to store _state_, which makes no sense for a user to see.  For example, when using [JSXGraph](../../Specialist_tools/JSXGraph/index.md) or [GeoGebra](../../Specialist_tools/GeoGebra/index.md) users transfer the configuration of the diagram into an input via JavaScript.  In many situations, it makes no sense for the student to see anything about this input.  The validation can be switched off with the regular "show validation" option, the input box itself can be hidden with JavaScript/CSS.  Putting `hideanswer` in the extra options stops displaying the "teacher's answer", e.g. at the end of the process.

All input types should support this extra option.

Do not use this option in questions in place of the normal quiz settings.

### Extra option: allowempty ###

Normally a _blank_, i.e. empty, answer has a special status and are not considered "valid".  Hence, a PRT relying on an input left blank will not be traversed.  Answers consisting only of whitespace are also considered as empty.  The extra option `allowempty` allows the input to be empty.  Internally it is essential that the variable name of the input, (e.g. `ans1`) is really assigned a specific value.

* Most inputs, including the algebraic input, an empty answer will be replaced by the Maxima atom `EMPTYANSWER`.  The teacher will need to deal with `EMPTYANSWER` tags in the PRT.
* String inputs will return the empty string `""` as an empty answer (to avoid a type-mismatch).
* Textarea inputs will return `[EMPTYANSWER]` to make sure the answer is always a list (to avoid a type-mismatch).
* Matrix inputs will return the correct size matrix filled with `null` atoms, e.g. `matrix([null,null],[null,null])`.

We strongly recommend (with many years of experience) that teachers do not use this option without very careful thought!

For example, if you don't want to give away how many answers you expect, then ask the student to provide a _set_ of answers.  Another option is to use the "TextArea" input type.  Each line of the TextArea is validated separately, and the resulting mathematical expression is a list.  The student is therefore free to choose how many expressions to type in, as the circumstances require, without a pre-defined number of input boxes.  By design, it is better to use these methods than trying to combine separate inputs, some of which are empty, in the PRT later.

Our experience strongly suggests this option should only be used for edge cases, and not for routine use.

If you use this option when students navigate away from a page the system will "validate" the inputs, and hence any empty boxes will be considered an active empty choice by the student and will be assessed.  If you use this option there is no way to distinguish between an active empty answer choice, and a student who deletes their answer.  (The same problem occurs with radio buttons....)

There are (unfortunately) some edge cases where it is useful to permit the execution of a PRT without all the inputs containing significant content.

Assume you have three inputs `ans1`, `ans2`, `ans3` contributing to a PRT, all of which have the `allowempty` option set because you don't want to tell the student which might be empty.  Assume the correct answer has at least one entry non-empty.  Then, make the first node of the PRT check

    ATAlgEquiv({ans1,ans2,ans3},{EMPTYANSWER})

This checks if all inputs are empty, so if true set the score and the penalty to be zero and stop.  This prevents the student accruing a penalty if they navigate away with all the boxes empty, but the PRT will still execute an "attempt".

If a teacher has three inputs `ans1`, `ans2`, `ans3`, then they can define a set in the feedback variables as follows

    sa:setdifference({ans1,ans2,ans3},{EMPTYANSWER})

The variable `sa` will be a set containing the non-empty answers (if any).

The teacher can use the `EMPTYANSWER` tag as a "correct answer".

### Extra option: simp ###

Actually simplify the student's answer during the validation process.  This will allow students to type in something like

    makelist(k^2,k,1,8)

If teacher's want this kind of thing, then a syntax hint is probably in order as well.

You may need to `ev(ans1,simp)` explicitly in any potential response tree.

It makes no sense to simplify the equivalence reasoning input type, so this has been omitted.

Note, STACK does it's best to preserve the number of significant figures in a student's answer.  For example, if a student types in `0.200*0.00500` then it should be displayed at \( 0.200\cdot 0.00500 \) in validation (depending on the symbol chosen in the question for multiplication).  However, if this extra option is chosen then the calculation will be performed.  If the result is a single floating point number, then the number of decimal places displated will be the _maximum_ number of decimal places entered in a float used by a stuent.  Otherwise Maxima's default way of displaying results used.  In particular, for floats, trailing zeros are removed.  More significant figures may be displayed than entered.

### Extra option: align ###

Controls if the student's answer is aligned 'left' or 'right' within the input box.

### Extra option: nounits ###

As of STACK 4.3, if units are declared in a question then the whole question will use a units context for parsing inputs.  For example, in a multi-part question you may use a matrix input.  If you do so, and use variable names, then these will be parsed expecting them to be usits.  To prevent this in a particular input, use the `nounits` option

### Extra option: consolidatesubscripts ###

As of STACK 4.3.10, there is an option to "consolidate subscripts".

There is a subtle (and perhaps confusing) difference between atoms in Maxima.  The strings `a1` and `a_1` are both atoms in Maxima, and are different.  Hence, the atoms `a1` and `a_1` are not considered to be algebraically equivalent. To avoid penalising students on a technicality, if you include the extra option `consolidatesubscripts` or `consolidatesubscripts:true` then students' input will be converted to the form without the underscore.

1. In students' input `M_1` is converted to `M1`.
2. Teachers are expected to use the correct pattern `M1` in the correct answer and in PRTs.
3. We only filter a very limited pattern, namely `^[a-zA-Z]+_[0-9]+$` which is an atom starting with one or more letters, then an underscore `_` then one or more digits.  This is the only pattern currently replaced.  Specifically double subscripts or non-numeric subscripts are ignored.

(If you have genuine use for more patterns please contact the developers with examples!)

More information on subscripts is given in the atoms and subscripts section of the more general [Maxima](../../CAS/Subscripts.md) documentation.

### Extra option: checkvars ###

As of STACK 4.4.0, there is an option to check, or allow comparison between, variables which occur in the student's answer and the teacher's answer.

This option takes the form of `checkvars:n`, where `n` is an integer. Omitting this option is equivalent to setting `n=0`.

The binary bits are used to set this options.

1. If the 1st binary bit of `n` is 1 (i.e. `n` is odd) then we flag up spurious variables.
2. If the 2nd binary bit of `n` is 1 then we flag up missing variables.

So, to check both set `checkvars:3`.

The numerical argument provides potential for future-proofing features (e.g. case sensitivity).

### Extra option: validator/feedback ###

This allows an input to add additional bespoke validation, based on a function defined by the question author.  For example, you can define a function which checks if the student's answer is a _list of exactly three floating point numbers_.  See the [validator documentation](../../CAS/Validator.md) for more details.

Writing bespoke validators is an advanced feature, but offers two significant benefits.

1. Students are less likely to be penalised on a technicality, especially in high-stakes situations;
2. Potential response tree authoring becomes much easier and more reliable because the validation acts as a "guard clause" only allowing correctly structured information through to the PRT.  This means type-checking need not be done in the PRT before assessment.
3. The extra option `validator` is designed to allow you to choose extra expressions to be invalid.  The extra option `feedback` will simply print an additional message to students in the validation feedback.

### Extra option: monospace ###

This option is available for algebraic, numerical, units and varmatrix inputs. It controls if the student's answer is displayed using monospace font. `monospace` and `monospace:true` will force the input to use monospace. `monospace:false` will force proportional font.

If `monospace` is not specified, then the CURRENT system default for the given input type will be used when the question is displayed. 

## Future extra options ##

In the future we are likely to add additional functionality via the _extra options_ fields.  This is because the form-based support becomes ever more complex, intimidating and difficult to navigate.
