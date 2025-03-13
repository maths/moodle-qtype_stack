# Plots and graphics in STACK

Plots and graphics can be placed into any of the [CASText](../Authoring/CASText.md) fields, e.g. question, worked solution, feedback.

There are a number of ways to embed plots, images, diagrams etc into STACK.

1. Embed an external image, using the HTML `<img>` tag.
2. [Maxima plots](../CAS/Maxima_plot.md) can be used in STACK questions using the `plot` command which provides access to some of the functionality of `plot2d`.
3. Create an image with [JSXGraph](../Specialist_tools/JSXGraph/index.md).
4. For graph theory [discrete graphs](../Topics/Discrete_mathematics.md) can be created directly using STACK's [`plot` command](Plots.md) command by building a combination of discrete and line plots.

Notes.

* The `draw` package is currently not supported.
* Maxima's `implicit_plot()` function does not respect the plot options, and we cannot place the resulting plot files in the correct places. Hence, STACK does not currently support implicit plots.  For reference try `load("implicit_plot");implicit_plot(x^2+y^2=x^2*y^2+1,[x,-2,2],[y,-2,2]);` in Maxima.
* As of version 4.0, the tags `{#...#}` provide the possibility to interact with 3rd party scripts.  If you have examples of this, please contact the developers.
