# Units

It is quite common in science subjects to need to accept an answer which has _units_,
for example using \(m/s\).

## The differences between `unit` and `units` packages  ##

Note that in Maxima are a number of separate packages which enable a user to manipulate physical units.

1. `load(unit);` the code is in `...\share\contrib\unit\unit.mac`
2. `load(units);` the code is in `...\share\physics\units.mac`
3. `load(ezunits);` the code is in `...\share\ezunits\ezunits.mac`

**WE DO NOT USE THESE PACKAGES** as they are too slow to load.  Instead we have a lightweight package of our own.

## Examples  ##

### Example 1  ###

Let us assume that the correct answer is `12.1*m/s^2`.

1. This value is inserted to STACK exactly as it is above. Note the multiplication sign between
   the number and units. Thus only one answer field is needed. We think this is the best solution (see below).
2. The teacher may want to use their own units. For example, the unit package does not
   include `mm` (millimetre), it is defined there as a word `millimetre`.
3. STACK converts the student's answer to SI base units only.
   This function also handles the number coefficients automatically (e.g. `1*km = 1000*m` etc.).
4. STACK sepaates the number from the units.
5. Finally STACK compares this number to the respective model answer. In this comparison it uses `NumSigFigs`.

## Answer test  ##

A units answer test is provided.  This is designed to accept a single answer with units such as `12.3*m/s`.  This will not work with sets, lists, matrices, equations etc.  Both the teacher and student must answer in this form.

The answer test splits up the answer into two parts.
  * The numerical part, which is tested with ATNumSigFigs.  Use the appropriate options for this test.
  * The units.

This answer test provides feedback such as:

* correct units, wrong number
* wrong units, but number is equivalent on conversion
* wrong class of units, i.e. Imperial not metric is a different problem from using \(m\) vs \(km\).
* dimensional problems

See the examples to see how far we have got with the development of this feature. Comments and suggestions are welcome.

__Notes__

1. The student may not include any variables in their answer.  All variables are considered to be units.
2. The numerical part is compared using the `NumSigFigs` test.  This *requires* various options, i.e. the number of significant figures.  Hence this answer test also requires identical options.

## Tips for dealing with units in STACK ##

CAS vaiables are tricky.

*  Try `12.3m*s^(-1)` not `12.3m/s`.  The display is better.  The answer test accepts either.
