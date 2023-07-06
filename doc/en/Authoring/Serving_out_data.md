# Serving out data

In some contexts one wants to generate random data for the students to act on, depending on the ammount of data and whether one expects the students to transfer it to other tools for processing the methods used for serving that data data out vary. Here are some examples.

## HTML-tables

If you only have a small amount of data you may simply print out a table of it and let the students copy-paste it from there. When doing this one will want to avoid LaTeX representations as those may case trouble for students try to copy-paste things. Here is an example of using CASText `foreach`-blocks to iterate and output an aray of data:

```
[[comment]]This just defines the data for this sample, you may use one coming from your question-vars.[[/comment]]
[[define data="(rand(zeromatrix(4,3)+100)-50)/10.0"/]]

<table>
[[foreach row="args(data)"]]
	<tr>[[foreach cell="row"]]<td>{#cell#}</td>[[/foreach]]</tr>
[[/foreach]]
</table>

<p>More control is available. You could tune the style of the values without affecting copy paste too much.</p>
<table>
[[foreach row="args(data)"]]
	<tr>[[foreach cell="row"]]
		<td>
		[[if test="cell < 0"]]<span style="color:red;">[[/if]]
		{#cell#}
		[[if test="cell < 0"]]</span>[[/if]]
		</td>
		[[/foreach]]</tr>
[[/foreach]]
</table>
```

## Raw Maxima code

Should you want to just give out Maxima code for use in Maxima or similar enough syntax you may simply use the `{#...#}` injection instead of `{@...@}` and it will output the raw form which is again copy-pastable. However, this does not work well with large amounts of data as the line may become quite long and even selecting it may prove problematic.

## File transfer

Since 4.4 STACK has allowed the author to construct text-files that can be downloaded and that contain freeform CASText constructed in the questions session with the questions seed, thus allowing unique random output for the students. This functionality will server everything out as plain text unless the defined name of the file ends with `.csv`. The primary limitation, for this feature is that it only works in the question-text, it may not be used anywhere else and it will never support adaptation to current student inputs.

How it works is that you wrap whatever you want the file to contain in a `[[textdownload]]` block, for that block a single parameter `name` must be defined and that will define the name suggested when saving this file. Whatever is inside that block will be rendered as normal CASText, however nothing defined in the CASText outside that block can affect it, which may cause some confusion, just repeat those defines inside the block if you use such things. The block will then get replaced with an URL from which the file can be downloaded, you will probably want to place that URL into a link or some such construct.

As an example, using the new function that generates CSV-strings from data we can write as follows:

```
/* Define these in question variables: */
lab: ["A","B","C"];
data: makelist([rand(322)/100.0,rand(600)/100.0,rand(300)/100.0], i, 50);
```

```
[[comment]]Use them like this in the question-text.[[/comment]]
Load the data from 
<a href="[[textdownload name="data.csv"]]{@stack_csv_formatter(data,lab)@}[[/textdownload]]">this file</a> and ...
```
That function returns a string and it takes in the data as a matrix or a list of lists and labels as a list. If no labels are necessary use `false`. Also you can use `stackfltfmt` to control the representation of pure floats in the data. Just in case you meet trouble `labels` is a keyword that cannot be used which is a shame when defining labels.
