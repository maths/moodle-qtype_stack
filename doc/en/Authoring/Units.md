# Units

_Planned for a future STACK release._

It is quite common in science subjects to need to accept an answer which has _units_,
for example using \(m/s\).  Fortunately, Maxima already has a units package.

## The differences between `unit` and `units` packages  ##

Note that in Maxima there are two packages which enable a user to manipulate physical units.

1. `load(unit);` the code is in `Maxima-5.21.1\share\maxima\5.21.1\share\contrib\unit\unit.mac`
2. `load(units);` the code is in `Maxima-5.21.1\share\maxima\5.21.1\share\physics\units.mac`  See also [Maxima documentation](http://maxima.sourceforge.net/docs/manual/en/maxima_76.html#SEC319)

The differences between these are discussed in Maxima's
[online manual](http://maxima.sourceforge.net/docs/manual/en/maxima_76.html#SEC321).

**WE NEED TO USE THE UNIT PACKAGE** not the units package for STACK since it is much more comprehensive.

### Unit package ###

There are no mm units.

## Examples  ##

### Example 1  ###

Let us assume that the correct answer is `12.1*m/s^2`.

1. This value is inserted to STACK exactly as it is above. Note the multiplication sign between
   the number and units. Thus only one answer field is needed. We think this is the best solution (see below).
2. The teacher may want to use their own units. For example, the unit package does not
   include mm (millimetre), it is defined there as a word "millimetre". First of all, in my codes I
   substitute all mms by m/1000. If we use two answer fields, then we need to move this "/1000" to the number part. Now this happens automatically.
3. Then STACK converts the student answer such that it include only meters and seconds.
   Unit package includes the suitable "convert" function. This function also handles the number
   coefficients automatically (e.g. 1*km = 1000*m etc.).
4. STACK picks the number from this converted code (the command "coeff").
5. Finally STACK compares this number to the respective model answer. In this comparison it
   uses `NumAbsolute` or something like that.

So, the following code is needed in the [feedback variables](KeyVals.md#Feedback_variables) (`ans1` is the student's answer).

    temp1 = subst(m/1000,mm,ans1)
    temp2 = convert(temp1,[m,s])
    temp3 = coeff(coeff(temp2,m),1/s^2)

The last command strips out the numbers so we can use a floating point comparison test to gauge the correct level of accuracy.

Here, the respective model answer is 12.1 without any unit.

## Answer tests  ##

Once we are confident with how this all works, we will create a _units_ [answer tests](Answer_tests.md).
This will provide feedback such as

* correct units, wrong number
* wrong units, but number is equivalent on conversion
* wrong class of units, i.e. Imperial not metric is a different problem from using \(m\) vs \(km\).
* dimensional problems

This answer test will then be similar to Algebraic Equivalence, but will automatically provide built in feedback.

## Current problems/issues  ##

1. 30/7/10 Jarno Ruokokoski reports  `load(unit);` this is very slow (10 seconds) to load.
Adding "load(unit)" to Maxima automatically under these circumstances would not work...
2. If we automatically `load` a library every time, then variable names such as \(m\) will
   acquire a meaning.  Perhaps it might be better to have a global question option "load unit(s)
   package" which will only load units for questions which make use of them.
   More complex for question authors, of course, and may not actually speed things up when compiled...
