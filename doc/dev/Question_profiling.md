# Question profiling

The purpose of this document is to explain how to profile Maxima code.  This includes

1. Counting the number of calls to a particular function.
2. Timing the execution of Maxima function calls.

Primarily, this is used through the Maxima sandbox.

The following commands can be used to time Maxima commands.

Create a command to execute.

```
command()=block(print("Do something expensive"));
```

Then the following executes that command and makes sensible use of Maxima's `timer_info` command.

````
time_command() := (
    timer(all),
    command(),
    simp:true,
    T:timer_info(),
    S:sublist(rest(args(T)),lambda([a], not(is(third(a)=0)))),
    S:sort(S, lambda([a,b],third(a)>third(b))),
    S[1][4]
)$
````

`get_most_called_function()` returns `[f, number of calls, total time for all calls]` where `f` is the name of the most called function when running `command()`, all times are given in seconds.

````
get_most_called_function() := (
    timer(all),
    command(),
    simp:true,
    T:timer_info(),
    S:sublist(rest(args(T)),lambda([a], not(is(third(a)=0)))),
    S:sort(S, lambda([a,b],third(a)>third(b))),
    [S[2][1], S[2][3], S[2][4]]
)$
````


`get_highest_time_per_call()` returns `[f, time per call, number of calls, total time]` where `f` is the name of the function with highest time/call ratio in `command()`, and all times are given in seconds.

````
get_highest_time_per_call() := (
    timer(all),
    command(),
    simp:true,
    T:timer_info(),
    S:sublist(rest(args(T)),lambda([a], not(is(third(a)=0)))),
    float_time(a):= if a=0 then 0 else first(args(a)),
    S:sort(S, lambda([a,b],float_time(second(a))>float_time(second(b)))),
    [S[3][1], S[3][2], S[3][3], S[3][4]]
)$
````