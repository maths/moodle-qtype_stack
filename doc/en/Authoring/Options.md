# Options

Options affect the behaviour of each question.  

### Question Level Simplify  ###

See the entry on [simplification](../CAS/Maxima.md#Simplification).  Default is TRUE.

### Assume Positive  ###

This option sets the value of [Maxima](../CAS/Maxima.md)'s
	
	assume_pos
 
variable.

If true and the sign of a parameter \(x\) cannot be determined from the current context or
other considerations, `sign` and `asksign(x)` return true. This may forestall some automatically-generated
asksign queries, such as may arise from integrate or other computations

Default is False

### Mark Modification  ###

This option sets how a score is assigned for multiple attempts at the question.

1. Penalty scheme (default)
2. First Answer is taken
3. Last Answer is taken

### Question Penalty ###

This is the percentage of the marks deducted from each different and valid attempt which is not
completely correct, when the penalty mark modification scheme is in use. 
The default is \(10\%\) of the marks available for this question, entered at \(0.1\).

### Feedback used 			{#Feedback_used}

The [feedback](Feedback.md) available can be varied as follows:

1. Full (default)
2. Text + generic (no score)
3. Generic + score (no detailed text)
4. Text only
5. Generic only (no detailed text or score)
6. Score only (no text)
7. No feedback

The generic [feedback](Feedback.md) is set using the three strings

* Feedback Correct
* Feedback Partially Correct
* Feedback Incorrect

The default values for these are

	<span class='correct'>Correct answer, well done.</span>
	<span class='partially'>Your answer is partially correct.</span>
	<span class='incorrect'>Incorrect answer.</span>

The classes enable some colour to enhance the feedback.

### Worked solution on demand  ###

[Worked solution](CASText.md#Worked_solution) should be available after a due date set in the quiz.
Currently the lack of information exchange between Moodle and STACK makes this difficult.
Hence we have an option Worked solution on demand, which allows the teacher to decide if the student
can choose to see the worked solution.  

The default value is true.

## Output  ##

The following options affect how mathematics is displayed.

### Multiplication Sign ###

* (none), e.g. \(x(x+1)\)
* Dot, e.g. \(x\cdot(x+1)\)
* Cross, e.g. \(x\times (x+1)\)

### Surd for Square Root ### {#surd}

This option sets the value of [Maxima](../CAS/Maxima.md)'s
	
	sqrtdispflag

When false the prefix function `sqrt(x)` will be displayed as \(x^{1/2}\).
Please note that Maxima (by default) does not like to use the \(\sqrt{}\) symbol.
The internal representation favours fractional powers, for very good reasons.
In  Maxima 5.19.1 we get:

	(%i1) 4*sqrt(2);
	(%o1) 2^(5/2)
	(%i2) 6*sqrt(2);
	(%o2) 3*2^(3/2)

The discussion of this issue can be followed on the
[Maxima mailing list](http://www.math.utexas.edu/pipermail/maxima/2009/018460.html).
Do you really want to continue using \(\sqrt{}\) in your teaching?

### sqrt(-1)			{#sqrt_minus_one}

In Maxima `%i` is the complex unit satisfying `%i^2=-1`.  However, students would
like to type `i` and physicists and engineers `j`.  
We also sometimes need to use symbols `i` and `j` for vectors.  
To accommodate these needs we have an option `ComplexNo` which provides a context for these symbols
and affects the way they are displayed.

| Option   | Interpretation   | Display   | ~     | ~    | ~     | ~      
| -------- | ---------------- | --------- | ----- | ---- | ----- | ----- 
|          | %i^2             | i^2       | j^2   | %i   | i     | j     
| -------- | ---------------- | --------- | ----- | ---- | ----- | ----- 
| i        | -1               | -1        | j^2   | i    | i     | _j_   
| j        | -1               | i^2       | -1    | j    | _i_   | j     
| symi     | -1               | i^2       | j^2   | i    | _i_   | _j_   
| symj     | -1               | i^2       | j^2   | j    | _i_   | _j_   

Note the use of both Roman and italic symbols in this table.
