# STACK essentials for AI-assisted question authoring

**Audience: the assistant. This is authoritative for the essential technical features of any STACK question.**

This document defines the essential technical requirements for STACK questions.
It is written for AI coding agents and LLMs assisting human question authors.
A project or authoring kit may add its own conventions on top of these.

### Stamp the target STACK version

Every STACK question's `<stackversion>` is the version number of STACK on the
system the question is written for. It is the plugin's version number, a date of
the form `YYYYMMDDXX` (for example `2026080600`), not the release name
(`4.13.1`). The release-to-number map is in `doc/en/Installation/STACK_versions.md`;
the number on a running site is `$plugin->version` in
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
