# Statistics support in STACK.


The following optional packages provide statistics support in Maxima:

    load("stats");
    load("distrib");
    load("descriptive");

Please see Maxima's documentation for information on the functions these packages contain.

These packages are included by default. The Debian package manager currently has a release of Maxima (as of Nov 2015) without these packages and attempting to load them renders STACK unusable. For this reason, they may have been disabled by your system administrator and your server may not support inclusion of these packages.

## Package: descriptive

Note that the "descriptive" package includes a number of functions to plotting graphs, such as boxplots and scatterplot.  These are not supported by STACK.
