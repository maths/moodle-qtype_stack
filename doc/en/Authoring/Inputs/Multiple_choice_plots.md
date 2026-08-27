# Dealing with plots in MCQ

It is possible to use plots as the options in a [STACK MCQ](Multiple_choice_input.md), either Maxima-generated plots or external images.

By design, the value of an MCQ selection are limited to legitimate CAS objects.
The `plot` command returns a string which is the URL of the dynamically generated image on the server.
The "value" of this can't be assessed by the potential response trees.
For this reason you must use the display option with plots and must only put the plot command in the display option.

For example, to create a correct answer consisting of three plots consider the following in the question variables.

    p1:plot(x,[x,-2,2],[y,-3,3])
    p2:plot(x^2,[x,-2,2],[y,-3,3])
    p3:plot(x^3,[x,-2,2],[y,-3,3])
    ta:[[1,true,p1],[2,false,p2],[3,false,p3]]

The actual CAS value of the answer returned will be the respective integer selected (radio or dropdown) or list of integers (checkbox).  The PRT can then be used to check the value of the integer (or list) as normal.

For this reason you will probably want to switch off the validation feedback ``your last answer was...".

## Dealing with external images in MCQ ##

It is also possible to embed the URL of an externally hosted image as the "display" field of an MCQ.
The string is not checked, and is also passed through the CAS.
This feature is fragile to being rejected as an invalid CAS object, and so is not recommended.  (This could also be improved...)

For example, the question variables could be something like

    i1:"<img src='http://www.maths.ed.ac.uk/~csangwin/Pics/z1.jpg' />"
    i2:"<img src='http://www.maths.ed.ac.uk/~csangwin/Pics/z2.jpg' />"
    i3:"<img src='http://www.maths.ed.ac.uk/~csangwin/Pics/z3.jpg' />"
    ta:[[1,true,i1],[2,false,i2],[3,false,i3]]
