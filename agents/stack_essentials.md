# STACK essentials for AI-assisted question authoring

**Audience: the assistant. This is authoritative for essential technical features of any STACK question.

This document defines the essential technical requirements for STACK questions. It is written for AI coding agents and LLMs assisting human question authors.

### Stamp the target STACK version

Every STACK question must have a `<stackversion>` number for the STACK version of 
the target system. Look upstream in your project for the default value. Never copy 
the value from a template, the showcase or an older question.

Why: The version records what features are available during authoring and testing,
and ensures the question will work on the target system. STACK reads this field
as the version the question was last edited with. Saving in Moodle's edit form
stamps the current version, but an XML import keeps whatever the file says. STACK
uses it in `validate_against_stackversion()` (the question dashboard, the bulk
tester and the API's `/test` route) to flag features removed/changed after a given
version, and the dashboard reports it. A copied old value misstates what the question
needs.
