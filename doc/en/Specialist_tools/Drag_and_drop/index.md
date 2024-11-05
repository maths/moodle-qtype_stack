# Drag and drop questions in STACK

Drag and drop problems are often referred to as "Parson's problems".

You can add in drag and drop functionality using the `[[parsons ..]]` [question block](Question_block.md). 
As of STACK v4.6.0, the `parsons` block has three main configurations (each of which support further customisation) which can be achieved by setting block header parameters `columns` and `rows` as appropriate:

1. **Proof** (Example usage: `[[parsons]] ... [[/parsons]]`) : This was introduced in STACK v4.5.0, and a full guide can be found under [Parsons problems](Parsons.md).
2. **Grouping** (Example usage: `[[parsons columns="3"]] ... [[/parsons]]`) : 
This will set up a number of columns, each of which behave similarly to the single left-hand column of the **Proof** configuration, where the student may drag and drop items starting at the top of each column. 
This is useful when we are only interesting in grouping items together, and specific row positions do not matter, or when each column may have variable length. Example problems are given in the [grouping](Grouping.md) page.
3. **Grid** (Example usage: `[[parsons columns="3" rows="2"]] ... [[/parsons]]`) : 
This will set up a grid of shape `(columns, rows)`, where the student may drag and drop items to any position in the grid. Example problems are given in the [grid](Grid.md) page.

Note that many **Grid** style questions can also be written using the **Grouping** setup. 
The main difference between them is that **Grid** allows the student to drag any item to any position in the grid, regardless
of whether the item above it has been filled; **Grouping** on the other hand only allows students to drag items to the 
end of the list within a column.

There is separate documentation on [troubleshooting](Troubleshooting.md) Parsons questions.