-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 11. Feb 2024 um 19:08
-- Server-Version: 10.4.28-MariaDB
-- PHP-Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `taskboards`
--

DELIMITER $$
--
-- Prozeduren
--
CREATE PROCEDURE `create_task` (IN `userid` INT, IN `typeid` INT, IN `columnid` INT, IN `name` VARCHAR(512) CHARSET utf8, IN `createdate` DATE, IN `reminddate` DATETIME, IN `usereminder` BOOLEAN, IN `notes` TEXT CHARSET utf8)  SQL SECURITY INVOKER BEGIN

DECLARE maxsortid INT;

SELECT MAX(t.sortid) INTO maxsortid FROM tasks t WHERE t.spaltenid = columnid;

INSERT INTO tasks (personenid, taskartenid, spaltenid, sortid, tasks, erstelldatum, erinnerungsdatum, erinnerung, notizen, erledigt, geloescht)
VALUES (userid, typeid, columnid, maxsortid + 1, name, createdate, reminddate, usereminder, notes, 0, 0);

END$$

CREATE PROCEDURE `move_task` (IN `taskid` INT, IN `siblingid` INT, IN `targetcol` INT)  SQL SECURITY INVOKER BEGIN

DECLARE newsort INT;
DECLARE newcol INT;

IF siblingid < 0
THEN
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

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `boards`
--

CREATE TABLE `boards` (
  `id` int(16) NOT NULL,
  `board` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `boards`
--

INSERT INTO `boards` (`id`, `board`) VALUES
(1, 'Mainboard'),
(2, 'Snowboard'),
(5, 'Test1'),
(6, 'Test2'),
(7, 'Test3'),
(8, 'Test4'),
(9, 'Test5'),
(10, 'Test6');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `personen`
--

CREATE TABLE `personen` (
  `id` int(16) NOT NULL,
  `vorname` varchar(256) NOT NULL,
  `name` varchar(256) NOT NULL,
  `email` varchar(256) NOT NULL,
  `passwort` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `personen`
--

INSERT INTO `personen` (`id`, `vorname`, `name`, `email`, `passwort`) VALUES
(1, 'Suse', 'Tran', 'st@mail.de', '123456'),
(2, 'Klea', 'Grube', 'kg@mail.de', '654321'),
(3, 'Ernst', 'Lustig', 'el@mail.de', 'pa55wort'),
(4, 'Gernhardt', 'Reinholzen', 'gr@mail.de', 'bo$$man69'),
(5, 'Jana', 'Türlich', 'jt@mail.de', 'naklar1'),
(6, 'Albert', 'Rum', 'ar@mail.de', 'nurSpa55');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `spalten`
--

CREATE TABLE `spalten` (
  `id` int(16) NOT NULL,
  `boardsid` int(16) NOT NULL,
  `sortid` int(16) NOT NULL DEFAULT 0,
  `spalte` varchar(256) NOT NULL,
  `spaltenbeschreibung` varchar(512) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `spalten`
--

INSERT INTO `spalten` (`id`, `boardsid`, `sortid`, `spalte`, `spaltenbeschreibung`) VALUES
(1, 1, 0, 'Offen', 'geöffnet, offen stehend, aufgeschlossen, nicht verschlossen'),
(2, 1, 1, 'In Bearbeitung', 'Be|ar|bei|tung, die; Substantiv, feminin'),
(3, 1, 3, 'Erledigt', 'abgearbeitet, abgehetzt, abgekämpft, angeschlagen'),
(4, 2, 0, 'Offen', 'geöffnet, offen stehend, aufgeschlossen, nicht verschlossen'),
(5, 2, 1, 'In Bearbeitung', 'Be|ar|bei|tung, die; Substantiv, feminin'),
(6, 2, 3, 'Erledigt', 'abgearbeitet, abgehetzt, abgekämpft, angeschlagen'),
(11, 5, 0, '1', '123'),
(12, 5, 0, '2', '1234');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `taskarten`
--

CREATE TABLE `taskarten` (
  `id` int(16) NOT NULL,
  `taskart` varchar(256) NOT NULL,
  `taskartenicon` varchar(256) NOT NULL,
  `taskarteniconunicode` varchar(16) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `taskarten`
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
-- Tabellenstruktur für Tabelle `tasks`
--

CREATE TABLE `tasks` (
  `id` int(16) NOT NULL,
  `personenid` int(16) NOT NULL,
  `taskartenid` int(16) NOT NULL,
  `spaltenid` int(16) NOT NULL,
  `sortid` int(16) NOT NULL,
  `tasks` varchar(512) NOT NULL,
  `erstelldatum` date NOT NULL,
  `erinnerungsdatum` datetime NOT NULL,
  `erinnerung` smallint(6) NOT NULL,
  `notizen` text NOT NULL,
  `erledigt` smallint(6) NOT NULL,
  `geloescht` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `tasks`
--

INSERT INTO `tasks` (`id`, `personenid`, `taskartenid`, `spaltenid`, `sortid`, `tasks`, `erstelldatum`, `erinnerungsdatum`, `erinnerung`, `notizen`, `erledigt`, `geloescht`) VALUES
(1, 4, 1, 2, 1, 'Internet Deinstallieren', '2024-01-08', '2024-01-10 12:00:00', 0, 'Genug ist genug.', 0, 0),
(2, 1, 1, 1, 4, 'Faulenzen', '2024-01-08', '2024-02-15 03:00:00', 1, 'Essenziell!', 0, 0),
(3, 5, 1, 3, 0, 'Chillen', '2023-12-01', '2024-01-01 00:00:00', 0, 'Wichtig!', 1, 0),
(4, 2, 7, 1, 3, 'Relaxen', '2024-01-10', '2024-01-10 18:59:00', 1, 'Einfach mal die Füßchen hochlegen.', 0, 0),
(6, 6, 1, 5, 0, 'Schnee von gestern loswerden', '2024-01-10', '2024-01-10 19:00:00', 1, 'Art von Schnee: unspezifiziert.\r\nadawdawd\r\nawdqwadfawfawfaw\r\nawfaw\r\nfaw\r\nfawf\r\nawf\r\n\r\nawffwawf\r\nawf\r\naw\r\nfaw\r\nfawfawf\r\nawf\r\nawf', 0, 0),
(7, 3, 1, 6, 0, 'Einen Task erledigen', '2024-01-10', '2024-01-10 19:00:00', 1, 'Ist erledigt!', 0, 0),
(8, 4, 1, 4, 0, 'Einkaufliste', '2024-01-10', '2024-01-10 19:00:00', 1, 'Einkaufliste für letzte Woche schreiben.', 0, 0),
(13, 6, 4, 3, 1, 'jep', '2024-02-03', '2024-02-03 17:29:00', 0, 'this is fine', 0, 0),
(15, 1, 1, 11, 0, 'test', '2024-02-11', '2024-02-11 11:05:00', 0, '', 0, 0);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `boards`
--
ALTER TABLE `boards`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `personen`
--
ALTER TABLE `personen`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `spalten`
--
ALTER TABLE `spalten`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cnst_boards_spalten` (`boardsid`);

--
-- Indizes für die Tabelle `taskarten`
--
ALTER TABLE `taskarten`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cnst_tasks_spalte` (`spaltenid`),
  ADD KEY `cnst_tasks_personen` (`personenid`),
  ADD KEY `cnst_tasks_taskarten` (`taskartenid`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `boards`
--
ALTER TABLE `boards`
  MODIFY `id` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT für Tabelle `personen`
--
ALTER TABLE `personen`
  MODIFY `id` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT für Tabelle `spalten`
--
ALTER TABLE `spalten`
  MODIFY `id` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT für Tabelle `taskarten`
--
ALTER TABLE `taskarten`
  MODIFY `id` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT für Tabelle `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `spalten`
--
ALTER TABLE `spalten`
  ADD CONSTRAINT `cnst_boards_spalten` FOREIGN KEY (`boardsid`) REFERENCES `boards` (`id`);

--
-- Constraints der Tabelle `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `cnst_tasks_personen` FOREIGN KEY (`personenid`) REFERENCES `personen` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cnst_tasks_spalte` FOREIGN KEY (`spaltenid`) REFERENCES `spalten` (`id`),
  ADD CONSTRAINT `cnst_tasks_taskarten` FOREIGN KEY (`taskartenid`) REFERENCES `taskarten` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
