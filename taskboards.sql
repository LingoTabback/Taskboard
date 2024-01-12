-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 08. Jan 2024 um 15:43
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
(2, 'Snowboard');

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
(1, 'Suse', 'Tran', 'st@mail.de', '123456');

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
(6, 2, 3, 'Erledigt', 'abgearbeitet, abgehetzt, abgekämpft, angeschlagen');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `taskarten`
--

CREATE TABLE `taskarten` (
  `id` int(16) NOT NULL,
  `taskart` varchar(256) NOT NULL,
  `taskartenicon` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `taskarten`
--

INSERT INTO `taskarten` (`id`, `taskart`, `taskartenicon`) VALUES
(1, 'Todo', 'PLACEHOLDER');

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
(1, 1, 1, 1, 0, 'Prokrastinieren', '2024-01-08', '2024-02-01 00:00:00', 0, 'Muss dringen erledigt werden!', 0, 0),
(2, 1, 1, 1, 1, 'Faulenzen', '2024-01-08', '2024-02-02 00:00:00', 1, 'Essenziell!', 0, 0),
(3, 1, 1, 3, 0, 'Chillen', '2023-12-01', '2024-01-01 00:00:00', 0, 'Wichtig!', 1, 0);

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
  MODIFY `id` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `personen`
--
ALTER TABLE `personen`
  MODIFY `id` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `spalten`
--
ALTER TABLE `spalten`
  MODIFY `id` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT für Tabelle `taskarten`
--
ALTER TABLE `taskarten`
  MODIFY `id` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
