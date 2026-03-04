CREATE TABLE `timetracker` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reporting_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL COMMENT 'Real time',
  `end_time` time DEFAULT NULL COMMENT 'Real time',
  `start_time_reported` time DEFAULT NULL COMMENT 'Time as it should be tracked in other systems',
  `end_time_reported` time DEFAULT NULL COMMENT 'Time as it should be tracked in other systems',
  `task_id` varchar(63) DEFAULT NULL,
  `task_name` varchar(255) DEFAULT NULL,
  `description` varchar(511) DEFAULT NULL,
  `description_long` text DEFAULT NULL,
  `created` datetime DEFAULT current_timestamp(),
  `modified` datetime DEFAULT current_timestamp(),
  `transfer` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'Defines if this entry should be tranferred to compamies time tracking',
  `transfered_intern` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Defines if this entry is transferred to the compyny''s internal time tracking',
  `transfered_jira` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Defines if this entry is transferred to the compyny''s jira time tracking',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;