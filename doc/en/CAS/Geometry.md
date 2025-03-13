# Geometry related Maxima functions

STACK adds a number of geometry related functions to maxima to help teachers establish mathematical properties, particularly when using the [Geogebra input](../Specialist_tools/GeoGebra/index.md).

These functions are defined in `stack/maxima/geometry.mac`.

___Note that unless already defined in Maxima, function names should match function names in Geogebra___


### `Length`

`Length(v)` returns the Euclidean length of the vector (represented as a list) from the origin to the point.

### `Distance`

`Distance(A, B)` returns the Euclidean distance between points represented as lists.  Works in any dimension.

### `Angle`

`Angle(A, B, C)` returns the angle between three points \(A\), \(B\), \(C\).  The function returns radians.
Note angles are given between \(-\pi\) and \(\pi\) (not between \(0\) and \(2\pi\)).
