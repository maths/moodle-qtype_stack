# Workflow for authoring Parsons Problems

This page suggests a workflow for authoring Parsons problems effectively.  In particular, to author the `proof_steps` lists you have to protect LaTeX backslashes in Maxima strings.  This is tedious, tricky and error prone!

For example, you have to define Maxima strings such as `"\\( f(n)=\\sin(n\\pi) \\)"`.

This suggested workflow provides a tool to automatically add in these extra slashes as a one-off process.

1. Load a basic working Parsons problem from a template from the library page as a starting point.  This guarantees a simple working Parsons problem.
2. From the question dashboard, choose the "Send general feedback to the CAS" link, which takes you to the caschat page.
3. Write the full proof, in simple LaTeX, as the worked solution and confirm the LaTeX works within the CAStext (without extra slashes).
4. Move your lines from the worked solution to the question variables and form the `proof_steps` list.  Do not add extra slashes!
5. At the bottom of the page, choose the "Protect slashes within Maxima string variables" option. This is a one-off conversion and it will add slashes.
6. Now, define the teacher's answer using your `proof_steps` and the `proof()` functions which STACK provides.  Make sure your worked solution displays properly, e.g. by using the `{@proof_display(ta, proof_steps)@}` command.  Once you are happy, save these values (question variables and general feedback) back to your question.

The "Protect slashes within Maxima string variables" option will add slashes _every time_ the option is selected, so this is effectively a one-off process.  However, you can write the full proof in normal LaTeX before converting to Maxima strings.

Clearly, you can also use this option on fragments and copy/paste back to a question (without "save back to question" which replaces the question variables etc.).
