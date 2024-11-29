# Upgrading question defaults

A number of default settings have been changed in recent versions of STACK.  There is no Moodle-based mechanism to change these settings in all current questions.

One user-level way to change all the questions is to export and edit the resulting .xml.  For example, the following are useful find/replace pairs.

    <text>Correct answer, well done.</text>
    <text><![CDATA[<span style="font-size: 1.5em; color:green;"><i class="fa fa-check"></i></span> Correct answer, well done.]]></text>

    <text>Your answer is partially correct.</text>
    <text><![CDATA[<span style="font-size: 1.5em; color:orange;"><i class="fa fa-adjust"></i></span> Your answer is partially correct.]]></text>

    <text>Incorrect answer.</text>
    <text><![CDATA[<span style="font-size: 1.5em; color:red;"><i class="fa fa-times"></i></span> Incorrect answer.]]></text>

The API/YAML format will make this kind of maintenance much easier.
