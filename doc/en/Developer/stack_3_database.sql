-- ----------------------------------------------------------
-- All tables will have a prefix stack_   ?
-- ----------------------------------------------------------


-- ----------------------------------------------------------
-- `question` the table teachers actually use to author questions.
-- Students interact with the `deployed_question` table to answer versions.
--
-- Probably need quite a few of the options to be explict columns here for direct later use.

CREATE TABLE IF NOT EXISTS `question` (
  `id`      bigint(10) unsigned NOT NULL auto_increment,
  `parent`  bigint(10) unsigned NOT NULL DEFAULT '0',

  `timecreated`     timestamp NOT NULL,
  `timemodified`    timestamp NOT NULL,
  `createdby`       bigint(10) unsigned default NULL,
  `modifiedby`      bigint(10) unsigned default NULL,

  `name`            varchar(255) DEFAULT '',
  `description`     text,

  `variables`    text,
  `questiontext` text,
  `solution`     text,
  `note`         text,
  `options`      text,	-- JSON array?
  `tests`        text,	-- JSON array?

  `valid`       tinyint(1) unsigned NOT NULL DEFAULT '0',	
  
  `meta_publisher`          text,
  `meta_format`             text,
  `meta_language`           text,
  `meta_rights`             text,
  `meta_learning_context`   text,
  `meta_difficulty`         text,
  `meta_competency`         text,
  `meta_competency_level`   text,
  `meta__time_allocated`    time default NULL,
  `meta_exercise_type`      text,

  PRIMARY KEY (`id`)
); 

--  These fields have been dropped from question.
-- 	`questionGUID` char(22) default NULL,
-- 	`status` text NOT NULL,
--	`published` varchar(20) NOT NULL,

-- --------------------------------------------------------

--
-- Table structure for table `response_tree`
--

CREATE TABLE IF NOT EXISTS `response_tree` (
  `id` 		int(11) NOT NULL auto_increment,
  `q_id` 	int(11) NOT NULL COMMENT 'question id',
  `name` 	text NOT NULL,
  `data` 	mediumblob NOT NULL, -- JSON array?
  PRIMARY KEY  (`id`),
  KEY `q_id` (`q_id`)
) COMMENT='Holds each separate potential response tree for question' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `interaction_element`
--

CREATE TABLE IF NOT EXISTS `interaction_element` (
  `id`         int(11) NOT NULL auto_increment,
  `q_id`       int(11) NOT NULL COMMENT 'question id',
  `ans_key`    text NOT NULL,
  `data`       mediumblob NOT NULL, -- JSON array?
  PRIMARY KEY  (`id`),
  KEY `q_id` (`q_id`)
) COMMENT='Holds each separate interaction element for question' AUTO_INCREMENT=1 ;


-- ----------------------------------------------------------
-- `question` the table teachers actually use to author questions.
-- Students interact with the `deployed_question` table to answer versions.
--

CREATE TABLE IF NOT EXISTS `deployed_question` (
  `id`        bigint(10) unsigned NOT NULL auto_increment,
  `q_id`      bigint(10) unsigned NOT NULL COMMENT 'question id',

  `timecreated`   timestamp NOT NULL,
  `seed`          bigint(10) unsigned NOT NULL,

  `max_marks`     decimal(11,5) DEFAULT NULL,
  
  `variables`     text, -- Instantiated verion.
  `questiontext`  text, -- All the CAS variables have been replaced by their displayed values, but the tags remain for the IEs and PRTs.
  `solution`      text, -- Instantiated verion.
  `note`          text, -- Instantiated verion.

  PRIMARY KEY (`id`)
); 

-- --------------------------------------------------------

--
-- `attempts`
--
-- We search this table to find the user's last attempt at a 
-- particular question

CREATE TABLE IF NOT EXISTS `attempts` (
  `id`            int(11) NOT NULL AUTO_INCREMENT,
  `cache_id`      int(11) NOT NULL COMMENT 'cache id', -- Updated after information has been processed.  This is where they are *now*.
  `user_id`       int(11) NOT NULL,
  `time_stamp`    timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`,`cache_id`,`user_id`)
);

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE IF NOT EXISTS `cache` (
  `id` 			int(11) NOT NULL AUTO_INCREMENT,
  `dq_id` 		int(11) NOT NULL COMMENT 'deployed_question id',
  `score` 		decimal(11,5) DEFAULT NULL, -- Current total score for this position.
  `history`		text,  -- JSON array detailing the history of answers, status, marks & penalties up to this point
                       -- Some repeated data here, but otherwise we have many database calls.
  `html` 		text,
  PRIMARY KEY (`id`)
);

-- --------------------------------------------------------

--
-- Table structure for table `cache_sequence`
--

CREATE TABLE IF NOT EXISTS `cache_sequence` (
  `id` 	      int(11) NOT NULL AUTO_INCREMENT,
  `post`      text,
  `event`     int(5) DEFAULT NULL,  -- E.g. submit, show solution, navigate away.
  `current`   int(11) NOT NULL COMMENT 'cache id',
  `next`      int(11) NOT NULL COMMENT 'cache id',

  PRIMARY KEY (`id`)
);

-- --------------------------------------------------------

--
-- Table structure for table `attempt_answer`
--
-- Logs the raw student's answers for reporting purposes, and the status

CREATE TABLE IF NOT EXISTS `attempt_answer` (
  `id`         int(11) NOT NULL AUTO_INCREMENT,
  `cache_id`   int(11) NOT NULL COMMENT 'cache id',
  `ie_id`      int(11) NOT NULL COMMENT 'interaction_element id',
  `raw_ans`    varchar(255) DEFAULT NULL,
  `status`     int(3) NOT NULL,

  PRIMARY KEY (`id`),
);

-- --------------------------------------------------------

--
-- Table structure for table `attempt_prt`
--
-- Logs the outcome of a particular PRT for an attempt

CREATE TABLE IF NOT EXISTS `attempt_prt` (
  `id`        int(11) NOT NULL AUTO_INCREMENT,
  `cache_id`  int(11) NOT NULL COMMENT 'cache id',
  `prt_id`    int(11) NOT NULL COMMENT 'response_tree id',
  `raw_mark`  decimal(11,5) DEFAULT NULL,
  `mod_mark`  decimal(11,5) DEFAULT NULL,
  `ans_note`  longtext,
  `feedback`  longtext,
  `error`     tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
);


-- --------------------------------------------------------
--
--  `keywords`
--

CREATE TABLE IF NOT EXISTS `keywords` (
  `id`         int(8) NOT NULL auto_increment,
  `keyword`    text NOT NULL,
  PRIMARY KEY (`id`)
);


-- --------------------------------------------------------

--
--  `question_keywords`
--  keywords will be forced to lower case.

CREATE TABLE IF NOT EXISTS `question_keywords` (
  `qid`    int(11) NOT NULL COMMENT 'question id',
  `kwid`   int(8) NOT NULL COMMENT 'keywords id',
  PRIMARY KEY  (`qid`,`kwid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

