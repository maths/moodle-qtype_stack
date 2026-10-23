# STACK essentials for AI-assisted question authoring

**Audience: the assistant. This is authoritative for the essential technical features of any STACK question.**

This document defines the essential technical requirements for STACK questions.
It is written for AI coding agents and LLMs assisting human question authors.
A project or authoring kit may add its own conventions on top of these.

### Write the question as Moodle XML

STACK questions are imported and exported as Moodle XML. Moodle's importer
(`question/format/xml/format.php`, `import_headers()`) reads the fields every
question type shares; STACK's `import_from_xml()` in `questiontype.php` reads
the rest. Nearly every element is optional and takes a default when omitted.
This is the smallest question that is complete in practice: randomised, marked
and tested.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<quiz>
  <question type="stack">
    <name><text>Differentiate a power</text></name>
    <questiontext>
      <text><![CDATA[<p>Differentiate \(x^{{@n@}}\) with respect to \(x\).</p>
<p>[[input:ans1]] [[validation:ans1]]</p>]]></text>
    </questiontext>
    <generalfeedback>
      <text><![CDATA[<p>\(\frac{d}{dx} x^{{@n@}} = {@ta1@}\).</p>]]></text>
    </generalfeedback>
    <stackversion><text>2026080600</text></stackversion>
    <questionvariables>
      <text>n: 2 + rand(5);
ta1: n*x^(n-1);</text>
    </questionvariables>
    <specificfeedback><text>[[feedback:prt1]]</text></specificfeedback>
    <questionnote><text>{@n@}</text></questionnote>
    <input>
      <name>ans1</name>
      <type>algebraic</type>
      <tans>ta1</tans>
    </input>
    <prt>
      <name>prt1</name>
      <node>
        <name>0</name>
        <answertest>AlgEquiv</answertest>
        <sans>ans1</sans>
        <tans>ta1</tans>
        <trueanswernote>prt1-1-T</trueanswernote>
        <falseanswernote>prt1-1-F</falseanswernote>
      </node>
    </prt>
    <qtest>
      <testcase>1</testcase>
      <testinput><name>ans1</name><value>ta1</value></testinput>
      <expected><name>prt1</name><expectedscore>1</expectedscore><expectedpenalty>0</expectedpenalty><expectedanswernote>prt1-1-T</expectedanswernote></expected>
    </qtest>
    <qtest>
      <testcase>2</testcase>
      <testinput><name>ans1</name><value>x^n</value></testinput>
      <expected><name>prt1</name><expectedscore>0</expectedscore><expectedpenalty>0.1</expectedpenalty><expectedanswernote>prt1-1-F</expectedanswernote></expected>
    </qtest>
  </question>
</quiz>
```

**The file.** UTF-8, with root element `<quiz>` and one
`<question type="stack">` for each question. The order of elements inside a
question does not matter.

**Elements the importer requires.** The question's `<name><text>`, and the
`<name>` of every `<input>`, `<prt>` and `<node>`, and the `<testcase>` number
of every `<qtest>`. Without them the import fails.

**Text fields and plain fields.** These fields wrap their content in `<text>`:
`name`, `questiontext`, `generalfeedback`, `stackversion`, `questionvariables`,
`specificfeedback`, `questionnote`, `questiondescription`, `prtcorrect`,
`prtpartiallycorrect`, `prtincorrect`, `feedbackvariables`, `truefeedback` and
`falsefeedback`. Every other field holds its value directly, as in
`<tans>ta1</tans>`. A text field may carry a `format` attribute; it defaults to
`html`.

**Escaping.** Wrap any `<text>` that contains HTML in `<![CDATA[ ... ]]>`.
Without CDATA the XML parser reads the HTML tags as XML elements, the field is
not a string, and the question does not import correctly. CDATA cannot contain
`]]>`. Outside CDATA, write `<` as `&lt;` and `&` as `&amp;`; this matters in
Maxima code, such as `x<1` in `<tans>`, `<sans>` or a test `<value>`. XML
defines only `&lt; &gt; &amp; &apos; &quot;`: an HTML entity such as `&nbsp;`
outside CDATA is a parse error, so use the character itself or a numeric
reference such as `&#160;`.

**Inputs.** Each `[[input:ans1]]` in the question text needs an `<input>` named
`ans1` and a `[[validation:ans1]]` tag. `<tans>` is the teacher's answer, an
expression in the question variables.

**PRTs.** A potential response tree is a graph of `<node>`s named `0`, `1`, ….
`<truenextnode>` and `<falsenextnode>` name the next node; `-1`, the default,
ends the tree. The first node is the one no other node points to: there must be
exactly one, and no cycles. Give every branch an answer note, distinct within
its PRT. STACK's convention is `prt1-1-T`: the PRT's name, the node's name plus
one, and the branch. Do not omit a note: Moodle's import stores an omitted note
as empty, while the API fills in `prt1-1-T` and `prt1-1-F` whichever node or
PRT it is, so tests cannot tell the branches apart.

**The feedback tag.** Each PRT's `[[feedback:prt1]]` appears exactly once, in
the question text or in `<specificfeedback>`; a tag that appears twice marks the
question broken. If `<specificfeedback>` is omitted and the question text
contains `[[input:ans1]]`, the importer fills it with `[[feedback:prt1]]`. So
when the feedback tags are in the question text, write an empty
`<specificfeedback><text></text></specificfeedback>`.

**The question note.** If the question variables call a random function such
as `rand()`, the note must not be empty (`questionnotempty`). It names the
variant, so it must show every random value: two deployed variants with the
same note are reported as duplicates. An omitted note becomes `{@ta1@}`.

**Tests.** Each `<qtest>` gives a `<value>` for every input, a Maxima
expression which may use the question variables, and for every PRT an
`<expected>` score, penalty and answer note. The expected note is, at its
simplest, the note of the last branch taken. Give all three: an omitted note defaults to `1-0-T`,
which never matches. An empty score and penalty with the note `NULL` expect the
PRT not to be evaluated, for example because an input is invalid. A wrong
answer's expected penalty is the question's `<penalty>`.

**Defaults.** An omitted element takes a default from one of two places. Some
are fixed in the code: `<defaultgrade>` 1 (0 for a question with no inputs),
`<penalty>` 0.1, input `<type>` algebraic, node `<answertest>` AlgEquiv,
`<truescore>` 1, `<falsescore>` 0, PRT `<value>` 1. The rest come from the
site's STACK settings (`get_config('qtype_stack', ...)`, defaults in
`settings.php`), for example `<boxsize>` 15, `<forbidfloat>` 1, `<mustverify>`
1, `<showvalidation>` 1 and `<multiplicationsign>` dot. A site administrator
can change these, so the same file may behave differently on two sites. Write
an element whenever the question depends on its value. A question exported
from Moodle lists every element.

### Stamp the target STACK version

Every STACK question's `<stackversion>` is the version number of STACK on the
system the question is written for. It is the plugin's version number, a date of
the form `YYYYMMDDXX` (for example `2026080600`), not the release name
(`4.13.1`). The number on a running site is `$plugin->version` in
`question/type/stack/version.php`.

    <stackversion><text>2026080600</text></stackversion>

Take the number from the project's own instructions, or ask the human. Never
guess it, never default to the latest release, and never copy it from an
example, a template or an older question.

Why: STACK reads this field as the version the question was last edited with.
Saving the question in Moodle's edit form stamps the site's current version, but
an XML import keeps whatever the file says, and a missing field is stored empty.
A question with no stamp, or one that is not a number, has an upgrade error: the
question tests are not run by the bulk tester or the API's `/test` route. The
question dashboard shows the stamp beside the site's version, and
`validate_against_stackversion()` uses it to decide which upgrade checks apply to
older questions. A stamp newer than the site's STACK gives a warning, not an
error.

The stamp does not change how the question runs: STACK always uses the code
installed on the site. Stamping an older version does not restore old
behaviour, and stamping a newer one does not make newer features available.
The stamp is a record for the people who maintain the question later.
