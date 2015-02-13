# Development track for STACK 3.4

This page describes the major tasks we still need to complete in order to be able to release the next version: STACK 3.4. Plans looking further into the future are described  on [Future plans](Future_plans.md). 
The past development history is documented on [Development history](Development_history.md).

How to report bugs and make suggestions is described on the [community](../About/Community.md) page.

**Important changes in STACK version 3.4:**  CAS commands within CASText are now required to be enclosed as `{@..@}`.  The old syntax `@..@` will not work.  Old questions can be converted with the fix maths delimiters script.  

    Administration -> Site administration -> Question types -> STACK
    
Then choose the link to "fix maths delimiters script".

Tasks completed:
1. Change in the behaviour of the CASEqual answer test.  Now we always assume `simp:false`.
2. Add support for more AMS mathematics environments, including `\begin{align}...\end{align}`, `\begin{align*}...\end{align*}` etc.
3. STACK tried to automatically write an optimised image for linux.  This should help installs where unix access is difficult.
4. Changes to the CASText format 
  * Require matching `{@..@}` for CAS variables. Note the old syntax `@..@` will not work. 
  * Expand the CASText format to enable us to embed the _value_ of a variable in CASText, with `{#..#}`, not just the displayed form.
  * Auto update script to change existing questions (part of the fix maths delimiters script).
5. Conditionals in CASText adaptive blocks. (Aalto) 


## STACK custom reports

Basic reports now work.

* *done* Add titles and explanations to the page, and document with examples.
* Really ensure "attempts" list those with meaningful histories.  I.e. if possible filter out navigation to and from the page etc.
* Add better maxima support functions for off-line analysis.
 * A fully maxima-based representation of the PRT?




 
