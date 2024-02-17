-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 17, 2024 at 07:22 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `taskboards`
--

DELIMITER $$
--
-- Procedures
--
CREATE PROCEDURE `create_col` (IN `boardid` INT, IN `name` VARCHAR(256) CHARSET utf8mb4, IN `description` VARCHAR(512) CHARSET utf8mb4)  SQL SECURITY INVOKER BEGIN

    DECLARE maxsortid INT;
    START TRANSACTION;

    SELECT COALESCE(MAX(s.sortid), -1) INTO maxsortid FROM spalten s WHERE s.boardsid = boardid;

    INSERT INTO spalten (boardsid, sortid, spalte, spaltenbeschreibung)
    VALUES (boardid, maxsortid + 1, name, description);
    COMMIT;
END$$

CREATE PROCEDURE `create_task` (IN `userid` INT, IN `typeid` INT, IN `columnid` INT, IN `name` VARCHAR(256) CHARSET utf8mb4, IN `createdate` DATE, IN `reminddate` DATETIME, IN `usereminder` BOOLEAN, IN `notes` TEXT CHARSET utf8mb4)  SQL SECURITY INVOKER BEGIN

    DECLARE maxsortid INT;
    START TRANSACTION;

    SELECT COALESCE(MAX(t.sortid), -1) INTO maxsortid FROM tasks t WHERE t.spaltenid = columnid;

    INSERT INTO tasks (personenid, taskartenid, spaltenid, sortid, tasks, erstelldatum, erinnerungsdatum, erinnerung, notizen)
    VALUES (userid, typeid, columnid, maxsortid + 1, name, createdate, reminddate, usereminder, notes);
    COMMIT;
END$$

CREATE PROCEDURE `delete_marked` ()  SQL SECURITY INVOKER BEGIN
DELETE t FROM tasks t JOIN spalten s on t.spaltenid = s.id JOIN boards b ON s.boardsid = b.id WHERE t.geloescht != 0 OR s.geloescht != 0 OR b.geloescht != 0;
DELETE s FROM spalten s JOIN boards b ON s.boardsid = b.id WHERE s.geloescht != 0 OR b.geloescht != 0;
DELETE b FROM boards b WHERE b.geloescht != 0;
END$$

CREATE PROCEDURE `move_col` (IN `colid` INT, IN `siblingid` INT, IN `targetbrd` INT)  SQL SECURITY INVOKER BEGIN

  DECLARE newsort INT;
  DECLARE newbrd INT;
  START TRANSACTION;

  IF siblingid <= 0
  THEN
      IF targetbrd <= 0 THEN
          SELECT s.boardsid INTO targetbrd FROM spalten s WHERE s.id = colid;
      END IF;
      SELECT MAX(s.sortid) + 1 INTO newsort FROM spalten s WHERE s.boardsid = targetbrd;
      SET newbrd = targetbrd;
  ELSE
      SELECT s.boardsid, s.sortid - 1 INTO newbrd, newsort FROM spalten s WHERE s.id = siblingid;
  END IF;

  IF EXISTS(SELECT 1 FROM spalten s WHERE s.sortid = newsort AND s.boardsid = newbrd AND s.id != colid)
  THEN
      UPDATE spalten s2
      SET s2.sortid = s2.sortid + 1
      WHERE s2.boardsid = newbrd AND s2.sortid > newsort;
      SET newsort = newsort + 1;
  END IF;
  UPDATE spalten s3 SET s3.sortid = newsort, s3.boardsid = newbrd WHERE s3.id = colid;
  COMMIT;
END$$

CREATE PROCEDURE `move_task` (IN `taskid` INT, IN `siblingid` INT, IN `targetcol` INT)  SQL SECURITY INVOKER BEGIN

  DECLARE newsort INT;
  DECLARE newcol INT;
  START TRANSACTION;

  IF siblingid <= 0
  THEN
      IF targetcol <= 0 THEN
          SELECT t.spaltenid INTO targetcol FROM tasks t WHERE t.id = taskid;
      END IF;
      SELECT MAX(t.sortid) + 1 INTO newsort FROM tasks t WHERE t.spaltenid = targetcol;
      SET newcol = targetcol;
  ELSE
      SELECT t.spaltenid, t.sortid - 1 INTO newcol, newsort FROM tasks t WHERE t.id = siblingid;
  END IF;

  IF EXISTS(SELECT 1 FROM tasks t WHERE t.sortid = newsort AND t.spaltenid = newcol AND t.id != taskid)
  THEN
      UPDATE tasks t2
      SET t2.sortid = t2.sortid + 1
      WHERE t2.spaltenid = newcol AND t2.sortid > newsort;
      SET newsort = newsort + 1;
  END IF;
  UPDATE tasks t3 SET t3.sortid = newsort, t3.spaltenid = newcol WHERE t3.id = taskid;
  COMMIT;
END$$

CREATE PROCEDURE `sanitize_sortids` ()  SQL SECURITY INVOKER BEGIN
UPDATE tasks t JOIN (SELECT id, ROW_NUMBER() OVER(PARTITION BY spaltenid ORDER BY sortid) AS i FROM tasks) rn ON rn.id = t.id SET t.sortid = rn.i - 1;
UPDATE spalten s JOIN (SELECT id, ROW_NUMBER() OVER(PARTITION BY boardsid ORDER BY sortid) AS i FROM spalten) rn ON rn.id = s.id SET s.sortid = rn.i - 1;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `boards`
--

CREATE TABLE `boards` (
  `id` int(11) NOT NULL,
  `board` varchar(256) NOT NULL,
  `geloescht` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `boards`
--

INSERT INTO `boards` (`id`, `board`, `geloescht`) VALUES
(1, 'Mainboard', 0),
(2, 'Snowboard', 0);

-- --------------------------------------------------------

--
-- Table structure for table `personen`
--

CREATE TABLE `personen` (
  `id` int(11) NOT NULL,
  `vorname` varchar(256) NOT NULL,
  `name` varchar(256) NOT NULL,
  `email` varchar(256) NOT NULL,
  `passwort` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `personen`
--

INSERT INTO `personen` (`id`, `vorname`, `name`, `email`, `passwort`) VALUES
(1, 'Suse', 'Tran', 'st@mail.de', '123456'),
(2, 'Klea', 'Grube', 'kg@mail.de', '654321'),
(3, 'Ernst', 'Lustig', 'el@mail.de', 'pa55wort'),
(4, 'Gernhardt', 'Reinholzen', 'gr@mail.de', 'bo$$man69'),
(5, 'Jana', 'Türlich', 'jt@mail.de', 'naklar1'),
(6, 'Albert', 'Rum', 'ar@mail.de', 'nurSpa55'),
(7, 'Hugh', 'Mungus', 'hm@mail.com', 'what?');

-- --------------------------------------------------------

--
-- Table structure for table `spalten`
--

CREATE TABLE `spalten` (
  `id` int(11) NOT NULL,
  `boardsid` int(11) NOT NULL,
  `sortid` int(11) NOT NULL DEFAULT 0,
  `spalte` varchar(256) NOT NULL,
  `spaltenbeschreibung` varchar(512) NOT NULL,
  `geloescht` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `spalten`
--

INSERT INTO `spalten` (`id`, `boardsid`, `sortid`, `spalte`, `spaltenbeschreibung`, `geloescht`) VALUES
(1, 1, 0, 'Offen', 'geöffnet, offen stehend, aufgeschlossen, nicht verschlossen', 0),
(2, 1, 1, 'In Bearbeitung', 'Be|ar|bei|tung, die; Substantiv, feminin', 0),
(3, 1, 2, 'Erledigt', 'abgearbeitet, abgehetzt, abgekämpft, angeschlagen', 0),
(4, 2, 0, 'Offen', 'geöffnet, offen stehend, aufgeschlossen, nicht verschlossen', 0),
(5, 2, 1, 'In Bearbeitung', 'Be|ar|bei|tung, die; Substantiv, feminin', 0),
(6, 2, 2, 'Erledigt', 'abgearbeitet, abgehetzt, abgekämpft, angeschlagen', 0);

-- --------------------------------------------------------

--
-- Table structure for table `taskarten`
--

CREATE TABLE `taskarten` (
  `id` int(11) NOT NULL,
  `taskart` varchar(256) NOT NULL,
  `taskartenicon` varchar(256) NOT NULL,
  `taskarteniconunicode` varchar(16) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `taskarten`
--

INSERT INTO `taskarten` (`id`, `taskart`, `taskartenicon`, `taskarteniconunicode`) VALUES
(1, 'Todo', 'fa-solid fa-briefcase', 'f0b1'),
(2, 'Besuch', 'fa-solid fa-house-chimney', 'e3af'),
(3, 'Mail', 'fa-solid fa-envelope-open-text', 'f658'),
(4, 'Paket', 'fa-solid fa-box', 'f466'),
(5, 'Telefonat', 'fa-solid fa-phone', 'f095'),
(6, 'Festival', 'fa-solid fa-flag', 'f024'),
(7, 'WhatsApp', 'fa-brands fa-whatsapp', 'f232');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `personenid` int(11) NOT NULL,
  `taskartenid` int(11) NOT NULL,
  `spaltenid` int(11) NOT NULL,
  `sortid` int(11) NOT NULL,
  `tasks` varchar(256) NOT NULL,
  `erstelldatum` date NOT NULL,
  `erinnerungsdatum` datetime NOT NULL,
  `erinnerung` tinyint(1) NOT NULL,
  `notizen` text NOT NULL,
  `erledigt` tinyint(1) NOT NULL DEFAULT 0,
  `geloescht` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `personenid`, `taskartenid`, `spaltenid`, `sortid`, `tasks`, `erstelldatum`, `erinnerungsdatum`, `erinnerung`, `notizen`, `erledigt`, `geloescht`) VALUES
(1, 4, 1, 2, 0, 'Internet Deinstallieren', '2024-01-08', '2024-01-10 12:00:00', 0, 'Genug ist genug.', 0, 0),
(2, 1, 1, 3, 0, 'Faulenzen', '2024-01-08', '2024-02-15 03:00:00', 1, 'Essenziell!', 0, 0),
(3, 5, 1, 1, 0, 'Chillen', '2023-12-01', '2024-01-01 00:00:00', 0, 'Wichtig!', 0, 0),
(4, 2, 7, 1, 2, 'Relaxen', '2024-01-10', '2024-01-10 18:59:00', 1, 'Einfach mal die Füßchen hochlegen.', 0, 0),
(6, 6, 1, 5, 0, 'Schnee von gestern loswerden', '2024-01-10', '2024-01-10 19:00:00', 1, 'Art von Schnee: unspezifiziert.\r\nadawdawd\r\nawdqwadfawfawfaw\r\nawfaw\r\nfaw\r\nfawf\r\nawf\r\n\r\nawffwawf\r\nawf\r\naw\r\nfaw\r\nfawfawf\r\nawf\r\nawf', 0, 0),
(7, 3, 1, 6, 0, 'Einen Task erledigen', '2024-01-10', '2024-01-10 19:00:00', 1, 'Ist erledigt!', 0, 0),
(8, 4, 1, 4, 0, 'Einkaufliste', '2024-01-10', '2024-01-10 19:00:00', 1, 'Einkaufliste für letzte Woche schreiben.', 0, 0),
(13, 6, 4, 1, 1, 'jep', '2024-02-03', '2024-02-03 17:29:00', 0, 'this is fine', 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `boards`
--
ALTER TABLE `boards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `personen`
--
ALTER TABLE `personen`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `spalten`
--
ALTER TABLE `spalten`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cnst_boards_spalten` (`boardsid`);

--
-- Indexes for table `taskarten`
--
ALTER TABLE `taskarten`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cnst_tasks_spalte` (`spaltenid`),
  ADD KEY `cnst_tasks_personen` (`personenid`),
  ADD KEY `cnst_tasks_taskarten` (`taskartenid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `boards`
--
ALTER TABLE `boards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `personen`
--
ALTER TABLE `personen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `spalten`
--
ALTER TABLE `spalten`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `taskarten`
--
ALTER TABLE `taskarten`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `spalten`
--
ALTER TABLE `spalten`
  ADD CONSTRAINT `cnst_boards_spalten` FOREIGN KEY (`boardsid`) REFERENCES `boards` (`id`);

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `cnst_tasks_personen` FOREIGN KEY (`personenid`) REFERENCES `personen` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cnst_tasks_spalte` FOREIGN KEY (`spaltenid`) REFERENCES `spalten` (`id`),
  ADD CONSTRAINT `cnst_tasks_taskarten` FOREIGN KEY (`taskartenid`) REFERENCES `taskarten` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

DELIMITER $$
--
-- Events
--
CREATE EVENT `periodic_delete` ON SCHEDULE EVERY 1 WEEK ON COMPLETION NOT PRESERVE ENABLE DO CALL delete_marked()$$

CREATE EVENT `periodic_sortid_cleanup` ON SCHEDULE EVERY 1 WEEK ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
UPDATE tasks t JOIN (SELECT id, ROW_NUMBER() OVER(PARTITION BY spaltenid ORDER BY sortid) AS i FROM tasks) rn ON rn.id = t.id SET t.sortid = rn.i - 1;
UPDATE spalten s JOIN (SELECT id, ROW_NUMBER() OVER(PARTITION BY boardsid ORDER BY sortid) AS i FROM spalten) rn ON rn.id = s.id SET s.sortid = rn.i - 1;
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
