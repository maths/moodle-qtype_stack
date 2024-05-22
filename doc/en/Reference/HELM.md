# HELM

During the summer of 2020, we started to convert the [HELM workbooks](https://learn.lboro.ac.uk/archive/olmp/olmp_resources/pages/wbooks_fulllist.html) into STACK quizzes.  The ultimate goal is to create and release one quiz for each of the .pdf workbooks.

To help question authors work in parallel and yet achieve a consistent style we introduced the following style conventions which are now available to any STACK question.

### Key points 

* These appear in boxes in the original HELM workbooks.
* Do not include the numbers that appear in the PDFs (i.e. use "Key Point" rather than "Key Point 1")

<table cellpadding="10" border="1">
<caption></caption>
<thead>
<tr>
<th scope="col">Example</th>
<th scope="col">Code</th>
</tr>
</thead>
<tbody>
<tr>
<td>
<p>A generalisation of the third law of indices states:</p>
<div class="HELM_keypoint">
<h4>Key Point</h4>
<p> For all ... .</p>
</div>
<td><pre>&lt;p&gt;A generalisation of the third law of indices states:&lt;/p&gt;
&lt;div class="HELM_keypoint"&gt;
&lt;h4&gt;Key Point&lt;/h4&gt;
&lt;p&gt;For all ... .&lt;/p&gt;
&lt;/div&gt;<br></pre></td>
</tr>
</tbody>
</table>


### Examples 

* Have a horizontal rule before them. They also have a horizontal rule after them, unless they are last in the text.
* Do not include the numbers that appear in the PDFs (i.e. use "Example" rather than "Example 1")
* The headings are &lt;h4&gt; with a special class applied to them for styling.
* Use the HELM_parts and HELM_parts_inline styles to label lists of parts (a), (b), (c), etc.


<table cellpadding="10" border="1">
<caption></caption>
<thead>
<tr>
<th scope="col">Example</th>
<th scope="col">Code</th>
</tr>
</thead>
<tbody>
<tr>
<td>
<hr>
<h4 class="HELM_example">Example</h4>
<p>Use a calculator to evaluate ... .</p>
<h4 class="HELM_solution">Solution</h4>
<p>Using the ... button on the calculator check that you obtain ... .</p>
</td>
<td><pre>&lt;hr&gt;
&lt;h4 class="HELM_example"&gt;Example&lt;/h4&gt;
&lt;p&gt;Use a calculator to evaluate ... .&lt;/p&gt;
&lt;h4 class="HELM_solution"&gt;Solution&lt;/h4&gt;
&lt;p&gt;Using the ... button on the calculator check that you obtain ... .&lt;/p&gt;<br></pre></td>
</tr><tr>
<td>
<hr>
<h4 class="HELM_example">Example</h4>
<p>Identify the index and base in the following expressions. </p>
<ol class="HELM_parts_inline">
<li> Ex 1. </li>
<li> Ex 2. </li>
<li> Ex 3. </li>
</ol>

<h4 class="HELM_solution">Solution</h4>
<ol class="HELM_parts">
  <li>In the expression ..., 8 is the base and 11 is the index.</li>
  <li>In the expression ..., -2 is the base and 5 is the index.</li>
  <li>In the expression ..., p is the base and -q is the index.<p>
  </p><p>The interpretation of a negative index will be given
  in sub-section 4 which starts on page 31.</p></li>
</ol>
</td>
<td><pre>&lt;hr&gt;
&lt;h4 class="HELM_example"&gt;Example&lt;/h4&gt;
&lt;p&gt;Identify the index and base in the following expressions. &lt;/p&gt;
&lt;ol class="HELM_parts_inline"&gt;
&lt;li&gt; Ex 1. &lt;/li&gt;
&lt;li&gt; Ex 2. &lt;/li&gt;
&lt;li&gt; Ex 3. &lt;/li&gt;
&lt;/ol&gt;

&lt;h4 class="HELM_solution"&gt;Solution&lt;/h4&gt;
&lt;ol class="HELM_parts"&gt;
  &lt;li&gt;In the expression ..., 8 is the base and 11 is the index.&lt;/li&gt;
  &lt;li&gt;In the expression ..., -2 is the base and 5 is the index.&lt;/li&gt;
  &lt;li&gt;In the expression ..., p is the base and -q is the index.&lt;p&gt;
  &lt;/p&gt;&lt;p&gt;The interpretation of a negative index will be given
  in sub-section 4 which starts on page 31.&lt;/p&gt;&lt;/li&gt;
&lt;/ol&gt;</pre></td>
</tr>
</tbody>
</table>

Hint: If &lt;hr&gt; doesn't work try preceding it with &lt;p&gt;&amp;nbsp;&lt;/p&gt;.


### Questions

* Start with heading "Exercise" - see below for styling. Note that these are numbered in the PDFs but do not copy the numbers. The Moodle quiz will take care of numbering the questions.

<table cellpadding="10" border="1">
<caption></caption>
<thead>
<tr>
<th scope="col">Example</th>
<th scope="col">Code</th>
</tr>
</thead>
<tbody>
<tr>
<td><p class="HELM_exercise">Exercise</p></td>
<td><pre>&nbsp;&lt;p class="HELM_exercise"&gt;Exercise&lt;/p&gt;</pre></td>
</tr>
</tbody></table>

