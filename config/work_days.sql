CREATE TABLE `work_days` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `work_date` date NOT NULL,
  `required_time` time NOT NULL DEFAULT '08:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `work_date` (`work_date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
