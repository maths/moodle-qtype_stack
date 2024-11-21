# Systematic deployment

STACK has the option to create [random variants](../CAS/Random.md) of questions, and it is very good practice to [deploy variants](Deploying.md) in advance.  However, users may need to systematically deploy all variants of a question in a simple manner.  For example, where there is a small number of cases and all should be readily available.

Every CAS (Maxima) session contains a constant `stack_seed` which holds the integer value of the seed used by that variant of the question.

##  Deploying every variant

The constant `stack_seed` can be used to deploy every variant of a question.  As an example, consider the data below (from https://nssdc.gsfc.nasa.gov/planetary/factsheet/)

    planet:["Mercury", "Venus", "Earth", "Moon", "Mars", "Jupiter", "Saturn", "Uranus", "Neptune", "Pluto"];
    /* mass 10^(24)kg. */
    mass:[0.330, 4.87, 5.97, 0.073, 0.642, 1898.0, 568.0, 86.8, 102.0, 0.0130];
    /* Orbital Period (days). */
    period[88.0, 224.7, 365.2, 27.3, 687.0, 4331, 10747, 30589, 59800, 90560];
    /* Distance from Sun (106 km) */
    dist:[57.9, 108.2, 149.6, 0.384, 228.0, 778.5, 1432.0, 2867.0, 4515.0, 5906.4];

If you want to use this data in a question, you can use a variable to index elements in these lists.  In particular, you can deploy seeds 1,2,3,4,5,6,7,8,9,10.  Then in the question variables or castext you can use the variable `stack_seed` as an index to the data, e.g.

    The planet {@planet[stack_seed]@} has mass \({@mass[stack_seed]*10^(24)@} \mathrm{kg}\).

We recommend you add in a question variable, e.g. `n1:stack_seed;` and then use this variable (e.g. `n1`) as your index.

If you want to exclude the moon, then you can omit seed 4, and deploy only seeds 1,2,3,5,6,7,8,9,10. (Of course, alternatively you could delete the entries for the moon from the list!)

Note, in Maxima the list index starts at 1. I.e the first element of a list is `l[1]` (not zero).

It is your responsibility to make sure the index remains within range!  You can ensure this by creating an index variable such as `n1:mod(stack_seed-1,10)+1;` and then using this

    The planet {@planet[n1]@} has mass \({@mass[n1]*10^(24)@} \mathrm{kg}\).

It is sensible to always ensure your `stack_seed` does not create run-time errors.  Notice that although the `mod` function does return `0` we have avoided possible zero indexes when defining `n1`.

Of course, there are many other ways to map deployed seeds onto systematic deployment of variants.  Using consecutive integers from \(1, \ldots, n\) as the starting point is probably simplest and easiest to maintain.  For this reason there is a special option to do this on the deploy variants page.  There is also an option to use consecutive integers from \(n, \ldots, m\).

Notes

1. You can combine use of `stack_seed` with random functions.  There is nothing wrong with seeding the random number generator from a small integer!
2. STACK auto-detects random functions.  You must refer to `stack_seed`, or a random function, in the _question variables_ to trigger use of deployed variants.  Otherwise STACK will think deployed variants are not needed.  If necessary add in a variable `n1:stack_seed;` and then use `n1` as your index to make sure you have explicitly made use of `stack_seed`.
3. The variable `stack_seed` is a constant.  You cannot reassign values to this variable within the question.
4. Just as with randomisation, you must create a question note to distinguish between variants of a question.
