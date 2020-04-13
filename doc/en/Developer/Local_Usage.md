# Local Usage

There are a number of [reporting options](../Authoring/Reporting.md) which are built-in and supported.  To use the features on this page requires moodle admin permission.

It is often useful to know numbers about STACK usage on your local server.  To do this we need to work in the Moodle database.  E.g.

1. How many STACK questions were initialised for students during the last time interval?
2. How many attempts those students made to those question during that time on average?
3. Which courses had most attempts and which courses had the most STACK questions in use?
4. Question bank status by course, how many questions were in use, which questions have not been used for a long time?
5. Quiz/activity usage, i.e. in what context are the questions being used and for example how many questions are present in a quiz by average?
6. Duplicate questions, how many copies of the same questions are present in the question banks?

Do get this kind of data you will need the [custom sql report](https://moodle.org/plugins/report_customsql).

### The number of questions that students have interacted with ###

That is the number of questions that students have interacted with.

    SELECT COUNT(1)
    FROM {question_attempts} qa
    JOIN {question} q ON q.id = qa.questionid
    JOIN {quiz_attempts} quiza ON quiza.uniqueid = qa.questionusageid
    WHERE quiza.preview = 0
    AND q.qtype = 'stack'
    AND qa.timemodified BETWEEN %%STARTTIME%% AND %%ENDTIME%%
    And, here is a query that does number of questions attempted (ever) by course

    SELECT c.shortname AS Website, COUNT(1) AS Number_of_STACK_questions_attempted

    FROM {question_attempts} qa
    JOIN {question} q ON q.id = qa.questionid
    JOIN {quiz_attempts} quiza ON quiza.uniqueid = qa.questionusageid
    JOIN {quiz} quiz ON quiz.id = quiza.quiz
    JOIN {course} c ON c.id = quiz.course

    WHERE q.qtype = 'stack'
    AND quiza.preview = 0

    GROUP BY c.shortname

    ORDER BY COUNT(1) DESC, c.shortname

Moodle has a built-in report that will count the number of questions of any type in the question bank (report/questioninstances) but I think this is a less useful statistic.

### Closing date of quizzes which use STACK ###

Another useful report, which we use for planning the time of upgrades, is this one, which lists the close date of quizzes uses STACK (including ones that use STACK questions via Moodle's 'Random question' feature:

    SELECT DISTINCT
        c.shortname AS Website,
        quiz.name AS iCMA,
        quiz.timeopen AS Open_date,
        quiz.timeclose AS Close_date

    FROM {quiz} quiz
    JOIN {course} c ON c.id = quiz.course

    WHERE to_timestamp(quiz.timeclose) > 'now'::timestamp - '1 month'::interval
    AND EXISTS (
        SELECT 1
        FROM {quiz_slots} slot
        JOIN {question} q ON q.id = slot.questionid
        WHERE slot.quizid = quiz.id AND (
            q.qtype = 'stack' OR q.qtype = 'random' AND EXISTS (
                SELECT 1
                FROM {question_categories} qc
                JOIN {question} rq ON rq.category = qc.id
                WHERE qc.id = q.category AND rq.qtype = 'stack'
            )
        )
    )
    ORDER BY Close_date, Website, iCMA

### List all the people who have created STACK questions in your site ###

One final query, which lists all the people who have created STACK questions in your site (useful if you need to email them about something:

    SELECT u.firstname, u.lastname, u.username, u.email, number_created, number_modified

    FROM (
        SELECT COALESCE(createdby, modifiedby) AS userid,
               COALESCE(number_created, 0) AS number_created,
            COALESCE(number_modified, 0) AS number_modified
        FROM (

            SELECT createdby, COUNT(1) AS number_created
            FROM {question} WHERE qtype = 'stack'
            GROUP BY createdby

        ) qscreated
        FULL JOIN (

            SELECT modifiedby, COUNT(1) AS number_modified
            FROM {question} WHERE qtype = 'stack'
            GROUP BY modifiedby

        ) qsmodified ON createdby = modifiedby

    ) comined_counts

    JOIN {user} u ON u.id = userid

    ORDER BY number_created DESC, number_modified DESC

### Further queries ###

These queries were last updated in April 2020 (and are not actively maintained as a core part of STACK).  Please contribute related queries to the developers.
