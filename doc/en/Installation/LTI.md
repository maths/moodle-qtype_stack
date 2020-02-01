# About LTI

Learning Tools Interoperability (LTI) is a web services protocol which allows online learning systems to exchange information and interconnect.  It can be used to provide quizzes (containing STACK questions or not) to other learning systems, or to include quizzes within a Moodle system on which STACK is not installed.

LTI is entirely separate from STACK, and the details of the protocol are available from the [IMS Global](https://www.imsglobal.org/activity/learning-tools-interoperability) website.  LTI can be used to provide/consume a wide range of functionality beyond STACK quizzes.

# Installing LTI

There are two sides to LTI: the provider and consumer.

1. If you haven't installed STACK on your server, or use another learning management system, you will need to *consume* a service with STACK.  Moodle has an LTI consumer by default.  You don't need to add anything to Moodle to consume LTI.
2. The LTI provider `local_ltiprovider` is an [optional plugin](https://moodle.org/plugins/local_ltiprovider).  If you intend to provide STACK questions as an LTI service you will need to install this.

# Providing STACK quizzes

This section describes how to use LTI to provide Moodle quizzes (e.g. containing STACK questions).  We assume you have written your quizzes as part of a Moodle course.

## Setting up a Moodle course as a service for Learn

The Moodle LTI provider enables a wide range of possibilities.

* A single course is provided which contains a number of quizzes.
* The provided course is (normally) consumed by a single course in Learn.
* A single grade is provided to Learn.  All marks are recorded in the Moodle gradebook separately, and students should be able to access these.
* Students and teachers follow a single link from Learn.  The LTI protocol ensures this connection respects their respective level of permissions.

If you would like more than one grade in Learn, then consider having more than one course on Moodle.  Students will be able to see their individual grades in Moodle (unless you disable access to the gradebook!), so it is likely to be sufficient for most purposes to return a single grade to Learn.  The difficulty with this is the lack of shared question banks.

1. Login to the provider (STACK) directly with Moodle admin privileges and create a course as follows.

        Site administration -> Courses -> Manage courses and categories.

   Then choose "Create new course".
2. Create the course with appropriate Course full name and Course short name.  These should be identical to those on Learn.

        Course format -> Format -> Topics format
        Number of sections -> 0

   This creates the simplest possible course, with no section breakup.  All the quizzes will then appear as a list in this.  Other options are, of course, possible.
   (If you just really want a single quiz, consider the "Single activity format").
3. Delete all unnecessary elements, e.g. News.
4. Add quizzes, etc., as required.  This does not need to be complete before the next step, but the course should be non-empty when it is exposed to avoid confusion.
5. Now expose the course using the LTI provider.

       Course administration -> LTI Provider -> Add

       Tool to be provided -> Course
       
       Layout and CSS -> Hide page header, footer, left, right blocks.

   Save changes.
6. The "List of tools provided" will give the 'Tool name', 'Shared secret' and 'Launch URL'.  These will be needed by the LTI consumer.

It is possible to expose an individual quiz, but then you need to consume the quiz.  It is easier to make one connection at the course level, than many LTI connections at the quiz level.


# Providing clones of Moodle courses

The University of Edinburgh uses a learning management system called "Learn".  We need to connect Learn up to Moodle to allow a wide variety of courses (on Learn) to use quizzes containing STACK (and other) questions.  We want each (Learn) course to have a private area on the Moodle server parallel to the course on Learn.

We have developed an [enrolement plugin](https://github.com/maths/enrol_lticoursetemplate), [`enrol_lticoursetemplate`](https://github.com/maths/enrol_lticoursetemplate), which provides an LTI service which enables users to _clone a course template_ from a remote connection, automatically giving each teacher the ability to create a new clean course tied to their consumer content system.  This plugin removes the need to set up parallel courses in Moodle by hand.

[Documention](https://github.com/maths/enrol_lticoursetemplate) on how to set up this plugin is available.

