# Changelog

The STACK project release notes are stored in the [development history](doc/en/Developer/Development_history.md) file within the documentation.

Proposed future changes are kept in the [development track](doc/en/Developer/Development_track.md) file.
## Pending import validation changes

* Count STACK authoring failures as import errors, so the default Stop on error policy aborts initial
  XML uploads before writing questions. Disabling Stop on error explicitly retains repairable failures
  as draft, broken questions with diagnostics.
