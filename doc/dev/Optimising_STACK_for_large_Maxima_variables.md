# Optimising STACK for large Maxima variables

_Author: Salvatore Mercuri_ \
_Date: 17/04/2024_ \
_STACK version: 4.5.0_ \
_Maxima versions: (a) 5.45.1 + GCL 2.6.12; (b) 5.47.0 + SBCL 2.3.2_

This page documents efforts to optimise STACK for processing large Maxima variables, which may occur, for example, when
writing statistics questions which involve generating datasets. We describe: the specific issue as it arises in questions and in 
the STACK codebase; an approach to optimise STACK with respect to this issue; and evidence to support the use of this 
particular optimisation in future versions of STACK. 

## The issue

### In questions

One approach to writing statistics questions in STACK involves generating a random dataset in Maxima (i.e., within the `Question variables` field) as a two-dimensional array and then sending this to the `Question text` field and allowing it to be downloadable by the student as a `csv` file. On a default configuration of Maxima (compiled with GCL Lisp), this can cause CAS timeouts when the question is loaded, for fairly small datasets. One example of this question, authored by members of IDEMS International CIC, involves generating a climate dataset of shape `(360, 4)`, which causes CAS timeouts when the question is loaded.

### The root cause

To determine the root cause we created a dummy question, which generates a dataset of fixed width and variable columns, having shape `(N, 4)` with a constant entry value of `100`, via the command 
```
    makelist(makelist(100, c, 1, 4), makelist(100, r, 1, N))
```
within `Question variables`. We intercepted the CAS command called on question load from STACK (version 4.5.0) and timed this within a Maxima terminal using the approach described [here](https://docs.stack-assessment.org/en/Developer/Unit_tests/#timing-the-code). This allowed us to see the total time globally and per function, the most called function, and the average time per call for each function. This information highlighted possible inefficiencies within the function `stackjson_stringify` found [here](https://github.com/maths/moodle-qtype_stack/blob/dc19c913b6c4a8fc8b8ef20ae31ced699d23dd7b/stack/maxima/stackstrings.mac#L216). This function converts the Maxima `stack_map` representation of a JSON object into a string containing an actual JSON object, ready for parsing on the PHP side. It is a crucial component that is used [at the point of communication between Maxima and PHP](https://github.com/maths/moodle-qtype_stack/blob/dc19c913b6c4a8fc8b8ef20ae31ced699d23dd7b/stack/maxima/stackmaxima.mac#L692).

### Experiments

We performed a doubling experiment on the number of rows of the generated dataset to understand the nature of the inefficiencies in `stackjson_stringify` and record the total time taken for the CAS command used by STACK (version 4.5.0) to run within Maxima (version 5.45.1 + GCL 2.6.12). For each dataset shape ran five trials and we report here the average over trials. We did this with `stackjson_stringify` turned on (as in the version 4.5.0) and also by turning it off at the point of communication between Maxima and PHP, for comparison. We also report here the doubling ratio, defined as the total time taken by the CAS command for a dataset of shape `(2N, 4)` divided by the time taken for a dataset of shape `(2N, 4)`. For linear routines the doubling ratio should be around `2`, for quadratic around `4`. We use a dummy value of `0` if division by zero occurs in the doubling ratio. 

The table below gives the results for STACK version v4.5.0 vs. when turning the `stackjson_stringify` function **off**. We can see that using `stackjson_stringify` sees at least quadratic growth in the CAS command. By linear interpolation, we estimate a maximum dataset size of `(518, 4)` within the standard CAS timeout limit of 10 seconds. On the other hand, turning this function off seems to lead to linear growth for the size of datasets tested.

<table>
  <thead>
    <tr>
      <th>Shape</th>
      <th>Avg. time (v.4.5.0)</th>
      <th>Avg. time (Off)</th>
      <th>Doubling ratio (v4.5.0)</th>
      <th>Doubling ratio (Off)</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td style="font-weight: bold">(2, 4)</td>
      <td>0.022</td>
      <td>0.008</td>
      <td>2.272</td>
      <td>1.000</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(4, 4)</td>
      <td>0.050</td>
      <td>0.008</td>
      <td>0.880</td>
      <td>0.750</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(8, 4)</td>
      <td>0.044</td>
      <td>0.006</td>
      <td>1.500</td>
      <td>0.000</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(16, 4)</td>
      <td>0.066</td>
      <td>0.000</td>
      <td>1.545</td>
      <td>0.000</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(32, 4)</td>
      <td>0.102</td>
      <td>0.004</td>
      <td>2.059</td>
      <td>2.500</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(64, 4)</td>
      <td>0.210</td>
      <td>0.010</td>
      <td>2.390</td>
      <td>2.000</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(128, 4)</td>
      <td>0.502</td>
      <td>0.020</td>
      <td>3.470</td>
      <td>1.500</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(256, 4)</td>
      <td>1.742</td>
      <td>0.030</td>
      <td>5.597</td>
      <td>1.600</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(512, 4)</td>
      <td>9.750</td>
      <td>0.048</td>
      <td>3.173</td>
      <td>1.917</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(1024, 4)</td>
      <td>30.938</td>
      <td>0.092</td>
      <td>4.350</td>
      <td>1.348</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(2048, 4)</td>
      <td>134.592</td>
      <td>0.124</td>
      <td>8.144</td>
      <td>2.290</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(4096, 4)</td>
      <td>1096.154</td>
      <td>0.284</td>
      <td>5.450</td>
      <td>1.972</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(8192, 4)</td>
      <td>5973.766</td>
      <td>0.560</td>
      <td>N/A</td>
      <td>2.075</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(16384, 4)</td>
      <td>N/A</td>
      <td>1.162</td>
      <td>N/A</td>
      <td>2.015</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(32768, 4)</td>
      <td>N/A</td>
      <td>2.342</td>
      <td>N/A</td>
      <td>2.120</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(65536, 4)</td>
      <td>N/A</td>
      <td>4.966</td>
      <td>N/A</td>
      <td>2.193</td>
    </tr>
  </tbody>
</table>

## Proposed optimisation

Two-dimensional Maxima arrays, as generated above within `Question variables`, are evaluated and then communicated within a single string. As the number of rows of the dataset grows, the string will grow as well. Thus the problem seems to be within the [string case](https://github.com/maths/moodle-qtype_stack/blob/dc19c913b6c4a8fc8b8ef20ae31ced699d23dd7b/stack/maxima/stackstrings.mac#L222) of the `stackjson_stringify` function. Indeed, due to immutability of strings within Lisp, the implementation of the `simplode` function, which is used within `stackjson_stringify`, requires a quadratic number of traversals through a string, since it applies `sconcat` character-by-character, creating new strings at each step.

One way of avoiding `simplode` is to pass each character as an argument to `sconcat`. That is, `simplode(["h", "e", "l", "l", "o"])` is the same as `sconcat("h", "e", "l", "l", "o")`. This would avoid the quadratic traversals caused by the intermediate string creation in `simplode`. However, like many programming languages, some Lisp compilations impose constraints on the number of arguments that can be passed to a function. In GCL v2.6.12, for example, this is 64. Hence we cannot apply this directly for the dataset generation case. 

Instead we propose a _batch_ version of `stackjson_stringify` that does the following for the string case:
  1. Create character list from the string and protect escapes on each character. 
  2. Split the character list up into batches of length 64.
  3. Loop through the batches and pass the batch to `sconcat` as 64 individual character arguments to create a batch string of length 64.
  4. `sconcat` the batch strings together.

The code for this approach can be found [here](https://github.com/maths/moodle-qtype_stack/blob/2d2fc0e5fe8620163ff78644da0ce06ef5fa61df/stack/maxima/stackstrings.mac#L221).

Note also that since this is really an issue with _string length_, rather than array dimension, this means that the precision of floating point numbers in dataset entries will also have an impact. Truncating these to a small precision will help to speed up the processing time.

## Experimental results

Here, we report experimental results for the batch approach using GCL v2.6.12. We also report results for the batch approach as well as STACK v4.5.0 using Maxima version 5.47.0 along with SBCL v2.3.2 Lisp compilation.

As above, we perform a doubling experiment on the number of rows of a dataset, keeping the width and significant figures of the entry fixed at 4 and 3 respectively. Experiments are repeated 5 times for each shape, and the average across trials are reported.

### Batch approach

The table below records doubling experiments for the proposed batch optimisation of `stackjson_stringify` vs. v4.5.0 of STACK. We observe that the batch version has a significant speed-up over v4.5.0, being around **128 faster** to run the CAS command with a dataset of shape (8192, 4) than v4.5.0 (46.6 seconds vs. 5973.766). However, as we may expect, the batching only helps to delay the quadratic growth, with the batch version seeing its doubling ratio approach 4 for much larger datasets. 

<table>
  <thead>
    <tr>
      <th>Shape</th>
      <th>Avg. time (Batch)</th>
      <th>Avg. time (v4.5.0)</th>
      <th>Doubling ratio (Batch)</th>
      <th>Doubling ratio (v4.5.0)</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td style="font-weight: bold">(2, 4)</td>
      <td>0.050</td>
      <td>0.022</td>
      <td>0.880</td>
      <td>2.272</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(4, 4)</td>
      <td>0.044</td>
      <td>0.050</td>
      <td>1.182</td>
      <td>0.880</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(8, 4)</td>
      <td>0.052</td>
      <td>0.044</td>
      <td>1.500</td>
      <td>1.500</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(16, 4)</td>
      <td>0.078</td>
      <td>0.066</td>
      <td>1.538</td>
      <td>1.545</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(32, 4)</td>
      <td>0.120</td>
      <td>0.102</td>
      <td>2.017</td>
      <td>2.059</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(64, 4)</td>
      <td>0.242</td>
      <td>0.210</td>
      <td>1.950</td>
      <td>2.390</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(128, 4)</td>
      <td>0.472</td>
      <td>0.502</td>
      <td>1.881</td>
      <td>3.470</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(256, 4)</td>
      <td>0.888</td>
      <td>1.742</td>
      <td>2.130</td>
      <td>5.597</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(512, 4)</td>
      <td>1.892</td>
      <td>9.750</td>
      <td>2.016</td>
      <td>3.173</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(1024, 4)</td>
      <td>3.814</td>
      <td>30.938</td>
      <td>2.059</td>
      <td>4.350</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(2048, 4)</td>
      <td>7.854</td>
      <td>134.592</td>
      <td>2.153</td>
      <td>8.144</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(4096, 4)</td>
      <td>16.910</td>
      <td>1096.154</td>
      <td>2.756</td>
      <td>5.450</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(8192, 4)</td>
      <td>46.600</td>
      <td>5973.766</td>
      <td>2.661</td>
      <td>N/A</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(16384, 4)</td>
      <td>124.020</td>
      <td>N/A</td>
      <td>3.494</td>
      <td>N/A</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(32768, 4)</td>
      <td>433.362</td>
      <td>N/A</td>
      <td>3.741</td>
      <td>N/A</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(65536, 4)</td>
      <td>1621.212</td>
      <td>N/A</td>
      <td>N/A</td>
      <td>N/A</td>
    </tr>
  </tbody>
</table>

### Using SBCL with STACK v4.5.0

We tested STACK v4.5.0 using Maxima 5.47.0 + SBCL v2.3.2 Lisp with a doubling experiment, and compared this to STACK v4.5.0 using Maxima 5.45.1 + GCL 2.6.12. We see that SBCL scales better to larger dataset sizes, without making any changes to the `stackjson_stringify` function. Indeed, using SBCL, the CAS command for a question with a dataset of shape (8192, 4) is around **67 times faster** than when using GCL (88.86 vs. 5973.766 seconds). Of course, there is still quadratic growth eventually when using SBCL.

<table>
  <thead>
    <tr>
      <th>Shape</th>
      <th>Average time (SBCL 2.3.2)</th>
      <th>Average time (GCL 2.6.12)</th>
      <th>Doubling ratio (SBCL 2.3.2)</th>
      <th>Doubling ratio (GCL 2.6.12)</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td style="font-weight: bold">(2, 4)</td>
      <td>0.018</td>
      <td>0.022</td>
      <td>1.17</td>
      <td>2.272</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(4, 4)</td>
      <td>0.021</td>
      <td>0.050</td>
      <td>1.29</td>
      <td>0.880</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(8, 4)</td>
      <td>0.027</td>
      <td>0.044</td>
      <td>1.52</td>
      <td>1.500</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(16, 4)</td>
      <td>0.041</td>
      <td>0.066</td>
      <td>1.68</td>
      <td>1.545</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(32, 4)</td>
      <td>0.069</td>
      <td>0.102</td>
      <td>1.74</td>
      <td>2.059</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(64, 4)</td>
      <td>0.12</td>
      <td>0.210</td>
      <td>2.02</td>
      <td>2.390</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(128, 4)</td>
      <td>0.242</td>
      <td>0.502</td>
      <td>2.31</td>
      <td>3.470</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(256, 4)</td>
      <td>0.56</td>
      <td>1.742</td>
      <td>2.18</td>
      <td>5.597</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(512, 4)</td>
      <td>1.22</td>
      <td>9.750</td>
      <td>2.32</td>
      <td>3.173</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(1024, 4)</td>
      <td>2.83</td>
      <td>30.938</td>
      <td>2.93</td>
      <td>4.350</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(2048, 4)</td>
      <td>8.28</td>
      <td>134.592</td>
      <td>2.86</td>
      <td>8.144</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(4096, 4)</td>
      <td>23.68</td>
      <td>1096.154</td>
      <td>3.75</td>
      <td>5.450</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(8192, 4)</td>
      <td>88.86</td>
      <td>5973.766</td>
      <td>4.66</td>
      <td>N/A</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(16384, 4)</td>
      <td>414.53</td>
      <td>N/A</td>
      <td>4.18</td>
      <td>N/A</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(32768, 4)</td>
      <td>1729.70</td>
      <td>N/A</td>
      <td>4.34</td>
      <td>N/A</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(65536, 4)</td>
      <td>7510.99</td>
      <td>N/A</td>
      <td>N/A</td>
      <td>N/A</td>
    </tr>
  </tbody>
</table>

### Using SBCL with the batch optimisation of `stackjson_stringify` 

By combining the scalability of SBCL-compiled Lisp with the batch optimisation we obtain the following doubling experiment results. SBCL combined with the batch optimisation is able to process the CAS command containing a dataset of size (8192, 4), with entries to three significant figures, around **274 times faster** than v4.5.0 of STACK using GCL-compiled Maxima (21.790 vs. 5973.766 seconds).

<table>
  <thead>
    <tr>
      <th style="font-weight: bold">Shape</th>
      <th>Average time (SBCL + batch)</th>
      <th>Average time (GCL + batch)</th>
      <th>Average time (GCL + v4.5.0)</th>
      <th>Doubling ratio (SBCL + batch)</th>
      <th>Doubling ratio (GCL + batch)</th>
      <th>Doubling ratio (GCL + v4.5.0)</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td style="font-weight: bold">(2, 4)</td>
      <td>0.0245</td>
      <td>0.050</td>
      <td>0.0220</td>
      <td>1.34</td>
      <td>0.880</td>
      <td>2.272</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(4, 4)</td>
      <td>0.0329</td>
      <td>0.044</td>
      <td>0.0500</td>
      <td>1.21</td>
      <td>1.182</td>
      <td>0.880</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(8, 4)</td>
      <td>0.0397</td>
      <td>0.052</td>
      <td>0.0440</td>
      <td>1.41</td>
      <td>1.500</td>
      <td>1.500</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(16, 4)</td>
      <td>0.0559</td>
      <td>0.078</td>
      <td>0.0660</td>
      <td>1.79</td>
      <td>1.538</td>
      <td>1.545</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(32, 4)</td>
      <td>0.0998</td>
      <td>0.120</td>
      <td>0.102</td>
      <td>1.66</td>
      <td>2.017</td>
      <td>2.059</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(64, 4)</td>
      <td>0.166</td>
      <td>0.242</td>
      <td>0.210</td>
      <td>1.92</td>
      <td>1.950</td>
      <td>2.390</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(128, 4)</td>
      <td>0.319</td>
      <td>0.472</td>
      <td>0.502</td>
      <td>2.00</td>
      <td>1.881</td>
      <td>3.470</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(256, 4)</td>
      <td>0.638</td>
      <td>0.888</td>
      <td>1.742</td>
      <td>1.76</td>
      <td>2.130</td>
      <td>5.597</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(512, 4)</td>
      <td>1.121</td>
      <td>1.892</td>
      <td>9.750</td>
      <td>2.28</td>
      <td>2.016</td>
      <td>3.173</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(1024, 4)</td>
      <td>2.553</td>
      <td>3.814</td>
      <td>30.938</td>
      <td>2.04</td>
      <td>2.059</td>
      <td>4.350</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(2048, 4)</td>
      <td>5.196</td>
      <td>7.854</td>
      <td>134.592</td>
      <td>2.12</td>
      <td>2.153</td>
      <td>8.144</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(4096, 4)</td>
      <td>11.016</td>
      <td>16.910</td>
      <td>1096.154</td>
      <td>1.98</td>
      <td>2.756</td>
      <td>5.450</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(8192, 4)</td>
      <td>21.790</td>
      <td>46.600</td>
      <td>5973.766</td>
      <td>2.18</td>
      <td>2.661</td>
      <td>N/A</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(16384, 4)</td>
      <td>47.523</td>
      <td>124.020</td>
      <td>N/A</td>
      <td>2.17</td>
      <td>3.494</td>
      <td>N/A</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(32768, 4)</td>
      <td>103.313</td>
      <td>433.362</td>
      <td>N/A</td>
      <td>2.78</td>
      <td>3.741</td>
      <td>N/A</td>
    </tr>
    <tr>
      <td style="font-weight: bold">(65536, 4)</td>
      <td>287.431</td>
      <td>1621.212</td>
      <td>N/A</td>
      <td>N/A</td>
      <td>N/A</td>
      <td>N/A</td>
    </tr>
  </tbody>
</table>

### Answer tests

While the above experiments show that combining SBCL-compiled Maxima with the proposed batch optimisation of `stackjson_stringify` offers improved scalability with respected to dataset shape, it is important to ensure that the proposed configurations and optimisations do not lead to inflated processing times for smaller strings that one typically sees in STACK questions. 

To check this we run the answer test script (containing 2033 tests at the time of writing) on the STACK Plugin settings page on a Moodle dev environment on Linux 
Ubuntu 22.04.3, for the various STACK and Maxima compilations discussed above, with different STACK configured settings (e.g., whether to cache). These test scripts provide the total time taken, which we report in the table below. 

We observe that when running STACK using (non-optimised) Linux and no cache, SBCL seems to inflate the time taken by the answer test which is something we found to be the case previously [here](../Installation/Optimising_Maxima.md). In all other configurations, however, there does not appear to be a discernible difference among the four columns. In particular, the use of the batch optimisation of `stackjson_stringify` does not appear to significantly affect the running time of the answer tests.

<table>
  <thead>
    <tr>
      <th>Configuration</th>
      <th>v4.5.0 + GCL</th>
      <th>Batch + GCL</th>
      <th>v4.5.0 + SBCL</th>
      <th>Batch + SBCL </th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Linux + no cache</td>
      <td>2650.97342</td>
      <td>2570.31382</td>
      <td>4089.26856</td>
      <td>4285.68446</td>
    </tr>
    <tr>
      <td>Linux + cache</td>
      <td>24.65304</td>
      <td>24.94076</td>
      <td>24.79924</td>
      <td>24.78967</td>
    </tr>
    <tr>
      <td>Linux optimised + no cache</td>
      <td>152.87</td>
      <td>162.38761</td>
      <td>144.23283</td>
      <td>149.15978</td>
    </tr>
    <tr>
      <td>Linux optimised + cache</td>
      <td>14.77583</td>
      <td>14.60997</td>
      <td>14.46183</td>
      <td>14.86618</td>
    </tr>
  </tbody>
</table>

### Input tests

As above, we compare the running of the input test script (containing 439 tests at the time of writing) across the various configurations, compilations and versions to obtain the below table. Once again, we see an inflation when using SBCL for the non-optimised and non-caching configuration, however elsewhere the values remain similar across the columns. In particular, the batch optimisation of `stackjson_stringify` does not appear to significantly affect the running time of the input test script.

<table>
  <thead>
    <tr>
      <th>Configuration</th>
      <th>v4.5.0 + GCL</th>
      <th>Batch + GCL</th>
      <th>v4.5.0 + SBCL</th>
      <th>Batch + SBCL </th>
    </tr>
  </thead>
<tbody>
    <tr>
      <td>Linux + no cache</td>
      <td>441.03543</td>
      <td>434.6876</td>
      <td>679.26826</td>
      <td>785.91072</td>
    </tr>
    <tr>
      <td>Linux + cache</td>
      <td>0.69035</td>
      <td>0.58897</td>
      <td>0.63175</td>
      <td>0.76072</td>
    </tr>
    <tr>
      <td>Linux optimised + no cache</td>
      <td>13.7997</td>
      <td>13.41784</td>
      <td>14.9878</td>
      <td>14.8885</td>
    </tr>
    <tr>
      <td>Linux optimised + cache</td>
      <td>0.57989</td>
      <td>0.63715</td>
      <td>0.58733</td>
      <td>0.61798</td>
    </tr>
  </tbody>
</table>


## Conclusion

We summarise our key findings in the summary table below. The _Max dataset_ field is calculated by linearly interpolating the above tables to obtain a dataset that can be generated within the specified CAS timeout limit (note that 10 seconds is default). The _Dataset uplift_ is how many times more rows we can create using the specific configuration vs. v4.5.0 + Batch. Thus we can see that with a timeout limit of 100 seconds, Batch + SBCL can create a max dataset of shape `(31795, 4)` which is around 19 times larger than the max dataset of shape `(1706, 4)` that can be created in v4.5.0 + GCL. In all rows, we can see that Batch + SBCL outperforms the other three.

<table>
  <thead>
    <tr>
      <th style="font-weight: bold;"></th>
      <th>v4.5.0 + GCL</th>
      <th>v4.5.0 + SBCL</th>
      <th>Batch + GCL</th>
      <th>Batch + SBCL</th>
    </tr>
  </thead>
  <tbody>
      <tr>
      <td style="font-weight: bold;">Time taken to process dataset of size (8192, 4)</td>
      <td>5973.766 seconds</td>
      <td>88.86 seconds</td>
      <td>46.6 seconds</td>
      <td>21.79 seconds</td>
    </tr>
    <tr>
      <td style="font-weight: bold;">Speedup over v4.5.0 + GCL</td>
      <td>1</td>
      <td>67.23</td>
      <td>128.19</td>
      <td>274.15</td>
    </tr>
    <tr>
      <td style="font-weight: bold;">Max dataset (10s)</td>
      <td>(518, 4)</td>
      <td>(2277, 4)</td>
      <td>(2533, 4)</td>
      <td>(3738, 4)</td>
    </tr>
    <tr>
      <td style="font-weight: bold;">Max dataset (30s)</td>
      <td>(1001, 4)</td>
      <td>(4493, 4)</td>
      <td>(5902, 4)</td>
      <td>(10806, 4)</td>
    </tr>
    <tr>
      <td style="font-weight: bold;">Max dataset (100s)</td>
      <td>(1706, 4)</td>
      <td>(8472, 4)</td>
      <td>(13842, 4)</td>
      <td>(31795, 4)</td>
    </tr>
    <tr>
      <td style="font-weight: bold;">Dataset uplift (10s) over v4.5.0 + GCL</td>
      <td>1</td>
      <td>4.40</td>
      <td>4.89</td>
      <td>7.21</td>
    </tr>
    <tr>
      <td style="font-weight: bold;">Dataset uplift (30s) over v4.5.0 + GCL </td>
      <td>1</td>
      <td>4.49</td>
      <td>5.90</td>
      <td>10.80</td>
    </tr>
    <tr>
      <td style="font-weight: bold;">Dataset uplift (100s) over v4.5.0 + GCL</td>
      <td>1</td>
      <td>4.97</td>
      <td>8.11</td>
      <td>18.63</td>
    </tr>
  </tbody>
</table>


In conclusion, for authoring STACK questions in which large two-dimensional arrays are randomly generated as described, the following (possible in v4.5.0 of STACK) is recommended for current usage:
* Truncate floating point numbers which have high precision.
* Use SBCL-compiled Maxima.

For developers: it is recommended that we integrate the batch optimisation of `stackjson_stringify` in future versions of STACK, owing to the evidence showing better scalability with respect to string length alongside the lack of negative impact for typical STACK answer tests and inputs.