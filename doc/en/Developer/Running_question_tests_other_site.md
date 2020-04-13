# Running question tests for questions on other sites

When you upgrade to a new version of STACK, it would be reassuring to know beforehand
whether there is any change in behaviour in STACK which will affect your existing questions.

After you have done the upgrade, it is easy to run the question tests in bulk, using the
link on the STACK plugin admin screen. However, with a bit of hackery, it is possible to
run the code from the new STACK release (with a few modifications) on your development server,
but have it open a read-only connection to your live database, in order to load the questions
and their corresponding tests.

The following patch is provided as-is. It will probably require tinkering with to work in any
given situation.

```
diff --git a/bulktest.php b/bulktest.php
index 7974af2..a6614f2 100644
--- a/bulktest.php
+++ b/bulktest.php
@@ -29,13 +29,14 @@ require_once($CFG->libdir . '/questionlib.php');
 require_once(__DIR__ . '/locallib.php');
 require_once(__DIR__ . '/stack/utils.class.php');
 require_once(__DIR__ . '/stack/bulktester.class.php');
+require_once(__DIR__ . '/stack/cas/connectorhelper.class.php');


 // Get the parameters from the URL.
 $contextid = required_param('contextid', PARAM_INT);

 // Login and check permissions.
-$context = context::instance_by_id($contextid);
+$context = context_system::instance();
 require_login();
 require_capability('qtype/stack:usediagnostictools', $context);
 $PAGE->set_url('/question/type/stack/bulktest.php', array('contextid' => $context->id));
@@ -50,8 +51,8 @@ if ($context->contextlevel == CONTEXT_MODULE) {
     $PAGE->set_cm($cm, $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST));
 }

-// Create the helper class.
-$bulktester = new stack_bulk_tester();
+// Cache the connection settings.
+stack_connection_helper::make();

 // Release the session, so the user can do other things while this runs.
 \core\session\manager::write_close();
@@ -60,9 +61,25 @@ $bulktester = new stack_bulk_tester();
 echo $OUTPUT->header();
 echo $OUTPUT->heading($title);

+// Connect to other database.
+$bulktestrealdb = $DB;
+if (!$DB = moodle_database::get_driver_instance($CFG->dbtype, $CFG->dblibrary)) {
+    throw new dml_exception('dbdriverproblem', "Unknown driver $CFG->dblibrary/$CFG->dbtype");
+}
+$DB->connect('live.database.host.name', 'read_only_user', 'pa55w0rd', 'live_database_name', 'mdl_', $CFG->dboptions);
+
+$context = context::instance_by_id($contextid);
+
+// Create the helper class.
+$bulktester = new stack_bulk_tester();
+
 // Run the tests.
 list($allpassed, $failing) = $bulktester->run_all_tests_for_context($context);
 
 // Display the final summary.
 $bulktester->print_overall_result($allpassed, $failing);
+
+// Switch back to the read DB.
+$DB = $bulktestrealdb;
+
 echo $OUTPUT->footer();
diff --git a/bulktestindex.php b/bulktestindex.php
index 3d5eafa..f84b4bd 100644
--- a/bulktestindex.php
+++ b/bulktestindex.php
@@ -38,13 +38,20 @@ $PAGE->set_url('/question/type/stack/bulktestindex.php');
 $PAGE->set_context($context);
 $PAGE->set_title(stack_string('bulktestindextitle'));

-// Create the helper class.
-$bulktester = new stack_bulk_tester();
-
 // Display.
 echo $OUTPUT->header();
 echo $OUTPUT->heading(stack_string('replacedollarsindex'));

+// Connect to other database.
+$realdb = $DB;
+if (!$DB = moodle_database::get_driver_instance($CFG->dbtype, $CFG->dblibrary)) {
+    throw new dml_exception('dbdriverproblem', "Unknown driver $CFG->dblibrary/$CFG->dbtype");
+}
+$DB->connect('live.database.host.name', 'read_only_user', 'pa55w0rd', 'live_database_name', 'mdl_', $CFG->dboptions);
+
+// Create the helper class.
+$bulktester = new stack_bulk_tester();
+
 echo html_writer::start_tag('ul');
 foreach ($bulktester->get_stack_questions_by_context() as $contextid => $numstackquestions) {
     echo html_writer::tag('li', html_writer::link(
@@ -53,6 +60,9 @@ foreach ($bulktester->get_stack_questions_by_context() as $contextid => $numstac
 }
 echo html_writer::end_tag('ul');

+// Switch back to the read DB.
+$DB = $realdb;
+
 if (has_capability('moodle/site:config', context_system::instance())) {
     echo html_writer::tag('p', html_writer::link(
             new moodle_url('/question/type/stack/bulktestall.php'), stack_string('bulktestrun')));
diff --git a/stack/cas/connectorhelper.class.php b/stack/cas/connectorhelper.class.php
index 40ef26e..d8ba3e1 100644
--- a/stack/cas/connectorhelper.class.php
+++ b/stack/cas/connectorhelper.class.php
@@ -78,6 +78,11 @@ abstract class stack_connection_helper {
                 throw new stack_exception('stack_cas_connection: Unknown platform ' . self::$config->platform);
         }

+        global $bulktestrealdb;
+        if (!empty(($bulktestrealdb))) {
+            // Use the real db as the cache for performance.
+            return new stack_cas_connection_db_cache($connection, $debuglog, $bulktestrealdb);
+        }
         switch (self::$config->casresultscache) {
             case 'db':
                 global $DB;
```

As an example of the kind of tinkering that might be required, and the time I devised this patch,
the new version of STACK had a database change, which had not yet been applied to our live database.
Therefore, I had to tweak the question loading code as follows:

```
diff --git a/questiontype.php b/questiontype.php
index 6dcc96d..02c4354 100644
--- a/questiontype.php
+++ b/questiontype.php
@@ -348,7 +348,7 @@ class qtype_stack extends question_type {
         $question->inputs = $DB->get_records('qtype_stack_inputs',
                 array('questionid' => $question->id), 'name',
                 'name, id, questionid, type, tans, boxsize, strictsyntax, insertstars, ' .
-                'syntaxhint, syntaxattribute, forbidwords, allowwords, forbidfloat, requirelowestterms, ' .
+                'syntaxhint, 0 AS syntaxattribute, forbidwords, allowwords, forbidfloat, requirelowestterms, ' .
                 'checkanswertype, mustverify, showvalidation, options');

         $question->prts = $DB->get_records('qtype_stack_prts',
@@ -403,7 +403,7 @@ class qtype_stack extends question_type {
                 'strictSyntax'    => (bool) $inputdata->strictsyntax,
                 'insertStars'     => (int) $inputdata->insertstars,
                 'syntaxHint'      => $inputdata->syntaxhint,
-                'syntaxAttribute' => $inputdata->syntaxattribute,
+                'syntaxAttribute' => '0',
                 'forbidWords'     => $inputdata->forbidwords,
                 'allowWords'      => $inputdata->allowwords,
                 'forbidFloats'    => (bool) $inputdata->forbidfloat,
```
