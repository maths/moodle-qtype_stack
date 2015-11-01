# Statistics support in STACK.


The following optional packages provide statistics support in Maxima:

    load("stats");
    load("distrib");
    load("descriptive");

Please see Maxima's documentation for information on the functions these packages contain.

For a short period of time, STACK loaded these optional packages by default to provide statistics support.  However, the Debian package manager currently has a release of Maxima without these packages and attempting to load them renders STACK unusable. Currently, by default STACK does NOT load these packages.

## Package: descriptive

Note that the "descriptive" package includes a number of functions to plotting graphs, such as boxplots and scatterplot.  These are not supported by STACK.
