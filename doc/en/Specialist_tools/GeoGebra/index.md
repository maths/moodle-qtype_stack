# GeoGebra in STACK

Author Tim Lutz - University of Edinburgh and University of Education Heidelberg, 2022-23.

STACK supports inclusion of dynamic graphics using GeoGebra: [https://geogebra.org](https://geogebra.org).  This page is reference documentation when using GeoGebra applets both to display GeoGebra worksheets as part of a STACK question, and how to connect GeoGebra worksheets to a STACK input.

To help with assessment, STACK provides a number of [geometry related maxima functions](../../CAS/Geometry.md).

Please note that significant computation and calculation can be done within GeoGebra itself.  In many cases it might be much better to establish mathematical properties within the GeoGebra applet, and link the _results_ to STACK inputs.  These results could be the distance between relevant objects, or boolean results.

A current restriction of the STACK design is that you cannot have a variable name in question variables which also matches the name of an input.
For example, you cannot randomly generate the initial position of a point \(A\) with the "set" instruction, and also link this GeoGebra object to the input `input:A` with a "watch" instruction.
In this situation you will need to have _dependent_ objects (probably hidden) in GeoGebra which match to inputs.
(This is hard-wired into the design of STACK and cannot be changed, sorry.)

__A note on licenses:__ Please note that [GeoGebra's license](https://www.geogebra.org/license) does not match the [STACK licence](https://github.com/maths/moodle-qtype_stack/blob/master/COPYING.txt).  Users of STACK remain entirely responsible for complying with license for materials, and media embedded inside STACK questions.

### Disclaimer

The creation of these resources has been (partially) funded by the ERASMUS+ grant program of the European Union under grant No. 2021-1-DE01-KA220-HED-000032031. Neither the European Commission nor the project's national funding agency DAAD are responsible for the content or liable for any losses or damage resulting of the use of these resources.

