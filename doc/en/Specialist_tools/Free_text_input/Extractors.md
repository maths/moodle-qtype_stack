# Authoring free-text questions with extractors

This page discusses a free-text question with extractors.

## Questions with a single extractor and multiple standard STACK inputs

This is a complex question which can be loaded from 

    Doc-Examples/Specialist-Tools-Docs/Free-text-input/Free-text_with_extractors.xml

This page contains only notes, and we recommend you load this question and work through it.

### Design

This question adds free-text working to a classic STACK question.

* one input, the characteristic polynomial, is extracted automatically from the student's text.
* students still have to fill in the final answer, as would be the case with a classic STACK question.

This combination is a pragmatic middle ground between trying to extract all answers with regex, and having students fill in forms.

### Syntax hint as castext

To pre-fill in the "syntax hint" in the free-text input you need to do two things.

First create a maxima variable as a `castext` string

```
sh1:castext("
`
M = {#args(M)#}
`

-----------

Characteristic polynomial =

-----------
");
```

This contains

1. the matrix in the question, but by using `args(M)` we get AsciiMath form `M = [[8,-6,9],[12,-10,12],[0,0,-1]]` rather than maxima syntax `M = matrix([8,-6,9],[12,-10,12],[0,0,-1])`.  Note how we use castext within Maxima here, and we embed variables with the `{#...#}` form, so they are without LaTeX symbols.
2. the syntax hint contains the prompt for students to ensure the regex has a reasonable target: `Characteristic polynomial = `
3. the input `ans1` has syntax hint as castext `{#sh1#}` which displays the castext without `"`s, but maintaining the linebreaks.

### Extractor

Note the ASCII block has an extractor 

```
[[input:ans1]] [[validation:ans1]]
[[ascii input="ans1"]]
  [[extractor type="regexmatch" targetinput="scp" regex="^\\s*Characteristic polynomial\\s*=\\s*" /]]
[[/ascii]]
```

This fills in one of the inputs, `scp` which is the student's characteristic polynomial.  This input has very relaxed stars (basically insert `*`s etc).

This input is not displayed, note

```
<p style="display:none">[[input:scp]]</p>
```
but the validation tag will be visible if students make a syntax error.

### Teacher's answer

The teacher's answer for `ans1` is also written as castext within Maxima, in a very similar style to the syntax hint. 

## Questions with a multiple extractors and multiple standard STACK inputs

This is a complex question which can be loaded from 

    Doc-Examples/Specialist-Tools-Docs/Free-text-input/Free-text_with_multiple_extractors.xml

This page contains only notes, and we recommend you load this question and work through it.

### Design

This question extracts two independent answers, \(a=?\) and \(b=?\) from the student's working.  Students still have to fill in the final answer, as would be the case with a classic STACK question.

This combination is a pragmatic middle ground between trying to extract all answers with regex, and having students fill in forms.

### Extractors

This is the complete question text:

```
<p>You have a parabola of the form \(f(x)=a\,x^2+b\) which satisfies \(f({@x1@})={@f1@}\) and \(f'({@x2@})={@f2@}\).</p>
<p>Work line by line below, find the values of \(a\) and \(b\) and hence the formula for \(f\).  Justifying your answer fully.</p>
<div class="free-text-container">
[[input:ans1]] [[validation:ans1]]
[[ascii input="ans1"]]
  [[extractor type="laststringremainderwhitespace" targetinput="saa" search="a =" /]]
  [[extractor type="laststringremainderwhitespace" targetinput="sab" search="b =" /]]
[[/ascii]]
</div>
<p>Make sure your answer contains lines <code>a=?</code> and <code>b=?</code> for your values of the coefficients.</p>
<p style="display:none">[[input:saa]]</p>
<p>\(a=\)[[validation:saa]]</p>
<p style="display:none">[[input:sab]]</p>
<p>\(b=\)[[validation:sab]]</p>
<p>\(f(x)=\) [[input:saf]][[validation:saf]]</p>
```

Notes.

1. The inputs `saa` and `sab` are hidden with `<p style="display:none">`, however validation information is available via compact validation so students can see their answers are correctly extracted.
2. Note the use of the `laststringremainderwhitespace` extractor, with a simpler search term, rather than a regular expression.
3. Students still have to fill in the final answer, as would be the case with a classic STACK question.  This _could_ be extracted automatically with a further `extractor` block within the `ascii` block in the question text.
4. The input and `[[ascii]]` block are contained in the `<div class="free-text-container">` so they appear side by side.

In this example the PRT is minimal, and could be improved for partial credit. 