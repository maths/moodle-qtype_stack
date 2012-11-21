# Plots and graphics in STACK questions.

Plots and graphics can be placed into any of the [CAStext](../Authoring/CASText.md) fields.

The main way to create plots is using Maxima.

## plot() {#plot}

In STACK, the `plot` command has been defined to be a wrapper for Maxima's `plot2d` command. Try, for example, the following in the question stem.

    @plot(x^2,[x,-1,1])@

You can add a second variable to control the axes.

    plot(x^2,[x,-1,1],[y,0,2])

However, Maxima will not always allow you to get the axes you want (this is a bug in Maxima).
To get many plots in one window, we need to define a list of functions.

    plot([x^2,sin(x)],[x,-1,1])

This can be done with Maxima's `makelist` command

    (p(k):=x^k,pl:makelist(p(k),k,1,5),plot(pl,[x,-1,1]))

## implicit_plot()  {#implicit}

In Maxima

    load("implicit_plot");
    implicit_plot(x^2+y^2=x^2*y^2+1,[x,-2,2],[y,-2,2]);

generates a plot of an implicit function.

Maxima's `implicit_plot()` function does not respect the plot options, and we cannot place the resulting plot files in the correct places.
Hence, STACK does not currenltly support implicit plots.


## HTML

Note also that images can be included as HTML.  It is easiest to place your image somewhere on the internet and include a URL in the body of your STACK question.

## Google charts  {#google}

__NOT YET REINSTATED IN STACK 3.__

In particular, you can dynamically generate a URL for
[Google charts](http://code.google.com/apis/chart/) and in this way include randomly generated diagrams.

An example question is included as

    test-venn.xml

This includes the code in the question variables to create [random objects](Random.md#rand).

    a : 30 + rand(20);
    b : 40 + rand(50);
    anb : 5 + rand(20);
    aub : a+b-anb;

Then, in the question stem we have the HTML which uses this.  Note the way valus of variables are inserted here.

    <img src="http://chart.apis.google.com/chart?cht=v&chs=200x100&chd=t:@a@,@b@,0,@anb@,0,0&chdl=A|B">

This should look like the following, with in this case \(a=33\), \(b=65\), \(a\cap b=18\).

<img src="http://chart.apis.google.com/chart?cht=v&chs=200x100&chd=t:33,65,0,18,0,0&chdl=A|B">


## See also

[Maxima reference topics](index.md#reference).
