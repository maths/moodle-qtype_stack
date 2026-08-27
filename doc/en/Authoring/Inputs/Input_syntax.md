# Input syntax

The whole point of STACK is to have the student enter an algebraic expression.  These input options related to choices for input syntax.

### Insert Stars ### {#Insert_Stars}

Insert Stars affect the way STACK treats the validation of CAS strings.

There are the following options.

* Don't insert stars:  This does not insert `*` characters automatically.  If there are any pattern identified the result will be an invalid expression.  Note, students can type in unknown functions such as `x(t+1)` and this will be valid.  If you need to _forbid_ this use a bespoke validator.
* Insert `*`s for implied multiplication.  If any patterns are identified as needing `*`s as a pattern like `)(` then they will automatically be inserted into the expression quietly.
* Insert `*`s assuming single character variable names.  In many situations we know that a student will only have single character variable names.  Identify variables in the student's answer made up of more than one character then replace these with the product of the letters.
* Insert `*`s assuming no user-functions. If there is no function with name `x`, a pattern like `x(t+1)` will interpreted as `x*(t+1)`. Note, the student's formula is interpreted and variables identified, so \(\sin(ax)\) will not end up as `s*i*n*(a*x)` but as `sin(a*x)`.
  
It is also possible to select multiple options at the same time. The following options are currently available:

* Don't insert stars
* Insert stars for implied multiplication only
* Insert stars assuming single-character variable names
* Insert stars for spaces only
* Insert stars for implied multiplication and for spaces
* Insert stars assuming single-character variable names, implied multiplication and for spaces
* Insert stars for implied multiplication, for spaces, and for no user-functions.
* Insert stars for implied multiplication, for spaces, no user-functions and assuming single-character variable names.

Clearly all the possible combinations give \(2^n\) options.  Hence, we only provide a few options.  If you are willing to insert stars for unknown functions such as `x(t+1)` (forbid unknown functions) then you would also insert stars for simpler concepts such as `2x`.

If a space is taken for multiplication what should we do with \(\sin\ x\)?  Currently this is transformed to \(\sin \times x\) and then rejected as invalid as you can't multiply the function name by its argument.  Use these latter options with caution: in the long run students are likely to need to use a strict syntax with machines, and letting them use spaces now might be a disservice.

It is often very important to have some on-screen representation of multiplication, e.g. as a dot, so the student can see at the validation that `xe^x` is interpreted

1. as \( (x\cdot e)^x\) if we assume single character variable names, and
2. as \( xe^x\) if we just "Insert `*`s for implied multiplication".  The absence of the dot here is key.
 