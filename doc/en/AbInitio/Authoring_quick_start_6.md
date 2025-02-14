# Authoring quick start 6: STACK question libaray

1 - [First question](Authoring_quick_start_1.md) | [2 - Question variables](Authoring_quick_start_2.md) | [3 - Feedback](Authoring_quick_start_3.md) | [4 - Randomisation](Authoring_quick_start_4.md) | [5 - Question tests](Authoring_quick_start_5.md) | 6 - question library | [7 - Multipart questions](Authoring_quick_start_7.md) | [8 - Simplification](Authoring_quick_start_8.md) | [9 - Quizzes](Authoring_quick_start_9.md)

### Finding questions

The primary goal of STACK is to allow teachers to write their own materials.  That said, STACK is technical and it is often sensible to start with an existing question as a template and modify it to meet your needs.   STACK materials are available in the following ways.

1. The STACK question library: available to any teacher to use immediately.
2. Material banks (Moodle .xml) which can be imported.
3. Material banks distributed via the [gitsync](https://github.com/maths/moodle-qbank_gitsync) plugin.

If you have been following the quick start guide so far, you should also know how to write your own question from scratch.

### 1. The STACK question library

The STACK question library is available to any teacher to use immediately.

1. Create a new STACK question.
2. From the blank STACK question, follow the link to the "STACK question library".
3. Choose a question, and check the preview.
4. Import the question direct to your current question bank. (e.g. the category you created the new question in).
5. Go back to the question bank.  Your imported question is now available to review/edit/use.

For example, try importing `Doc-Examples\Reveal_block_example.xml`.  This question provides a demonstration of how to use multiple inputs, and the "reveal" block.

### 2. Importing questions from an existing server

Let us look at how you import questions from an existing server into your server.

First, you must export the existing questions:

1. log into the module on the Moodle server from which you wish to export questions, and click on `Question bank` in the Administration block. Then click on `Export`,  
2. Click on `Moodle XML format`, then choose the category you want to export.  Moodle only lets you export individual categories. 
3. Click on `Export questions to file`. This will download a file with the all the questions that category.

To import these questions into your course:

1. Log into your module on the Moodle server and click on `Question bank` in the Administration block,
2. Click on `Import`,
3. Click on `Moodle XML format` then drag and drop the `?.xml` file from your Downloads folder on your desktop, and click `Import` and then `Continue`. A copy of the questions should then appear in the question bank for your module and you can modify them as you want.

### 3. The gitsync module

The purpose of the [gitsync](https://github.com/maths/moodle-qbank_gitsync) plugin is to synchronise questions from part of a moodle question bank to an external file system.  This is an advanced feature, requiring additional server setup.

The motivating use-case is to share questions either (i) between multiple courses on a single moodle site or (ii) between multiple sites. Normally the external file system is part of a version control repository (e.g. git), so that version control tools can be used on the external file system to track differences between versions, merge changes, use branches to maintain different versions, and so on.

##### The next part of the authoring quick start guide looks at [multipart questions](Authoring_quick_start_7.md).
