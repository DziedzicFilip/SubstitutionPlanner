-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 09, 2025 at 08:06 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `zarzadzanie_harmonogramem`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `grupy`
--

CREATE TABLE `grupy` (
  `id` int(11) NOT NULL,
  `nazwa` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grupy`
--

INSERT INTO `grupy` (`id`, `nazwa`) VALUES
(4, 'Grupa 1'),
(5, 'Grupa 2');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `harmonogram`
--

CREATE TABLE `harmonogram` (
  `id` int(11) NOT NULL,
  `id_pracownika` int(11) DEFAULT NULL,
  `dzien` enum('Poniedziałek','Wtorek','Środa','Czwartek','Piątek','Sobota','Niedziela') NOT NULL,
  `godzina_od` time NOT NULL,
  `godzina_do` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `harmonogram`
--

INSERT INTO `harmonogram` (`id`, `id_pracownika`, `dzien`, `godzina_od`, `godzina_do`) VALUES
(12, 2, 'Poniedziałek', '08:00:00', '16:00:00'),
(13, 2, 'Wtorek', '08:00:00', '16:00:00'),
(14, 12, 'Poniedziałek', '08:00:00', '16:00:00'),
(15, 12, 'Wtorek', '08:00:00', '16:00:00'),
(18, 13, 'Wtorek', '06:30:00', '08:00:00'),
(19, 14, 'Środa', '15:30:00', '20:30:00'),
(20, 12, 'Niedziela', '20:04:00', '21:04:00');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `nadgodziny`
--

CREATE TABLE `nadgodziny` (
  `id` int(11) NOT NULL,
  `id_pracownika` int(11) DEFAULT NULL,
  `liczba_godzin` int(11) NOT NULL,
  `data` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nadgodziny`
--

INSERT INTO `nadgodziny` (`id`, `id_pracownika`, `liczba_godzin`, `data`) VALUES
(8, 13, 1, '2025-02-14');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pracownik_grupa`
--

CREATE TABLE `pracownik_grupa` (
  `id` int(11) NOT NULL,
  `id_pracownika` int(11) DEFAULT NULL,
  `id_grupy` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pracownik_grupa`
--

INSERT INTO `pracownik_grupa` (`id`, `id_pracownika`, `id_grupy`) VALUES
(33, 2, 4),
(34, 2, 4),
(35, 12, 5),
(36, 12, 5),
(37, 2, 4),
(38, 13, 4),
(39, 14, 5),
(41, 12, 5);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `uzytkownicy`
--

CREATE TABLE `uzytkownicy` (
  `id` int(11) NOT NULL,
  `imie` varchar(50) NOT NULL,
  `nazwisko` varchar(50) NOT NULL,
  `login` varchar(50) NOT NULL,
  `haslo` varchar(255) NOT NULL,
  `rola` enum('admin','pracownik') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uzytkownicy`
--

INSERT INTO `uzytkownicy` (`id`, `imie`, `nazwisko`, `login`, `haslo`, `rola`) VALUES
(1, 'Filip', 'Dziedzic', 'admin', 'admin123', 'admin'),
(2, 'Anna', 'Nowak', 'pracownik1', 'haslo123', 'pracownik'),
(12, 'Piotr', 'Teścik', 'Piotr123', 'haslo123', 'pracownik'),
(13, 'Ala', 'Kot', 'Ala1234', '$2y$10$mZQGSWiCWY8aIXy9FMIvA.8oE2PWA9G45fwaxPfHYMl.uI1mFgAVm', 'pracownik'),
(14, 'Tomasz', 'Lipa', 'Tomasz123', '$2y$10$2Irx7o.k7UniKkwnR6pNPeQG8DrJD7cmRUprQ4xdiTvFD/Qz.9e1i', 'pracownik');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `zastepstwa`
--

CREATE TABLE `zastepstwa` (
  `id` int(11) NOT NULL,
  `id_pracownika_proszacego` int(11) DEFAULT NULL,
  `id_pracownika_zastepujacego` int(11) DEFAULT NULL,
  `data_zastepstwa` date NOT NULL,
  `godzina_od` time NOT NULL,
  `godzina_do` time NOT NULL,
  `status` enum('oczekujące','zatwierdzone','odrzucone','DoAkceptacji') DEFAULT 'oczekujące'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `zastepstwa`
--

INSERT INTO `zastepstwa` (`id`, `id_pracownika_proszacego`, `id_pracownika_zastepujacego`, `data_zastepstwa`, `godzina_od`, `godzina_do`, `status`) VALUES
(15, 2, NULL, '2025-02-04', '08:30:00', '16:00:00', 'oczekujące'),
(18, 14, NULL, '2025-02-10', '10:00:00', '12:00:00', 'oczekujące'),
(20, 14, 13, '2025-02-14', '21:58:00', '22:58:00', 'zatwierdzone');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `grupy`
--
ALTER TABLE `grupy`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nazwa` (`nazwa`);

--
-- Indeksy dla tabeli `harmonogram`
--
ALTER TABLE `harmonogram`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pracownika` (`id_pracownika`);

--
-- Indeksy dla tabeli `nadgodziny`
--
ALTER TABLE `nadgodziny`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pracownika` (`id_pracownika`);

--
-- Indeksy dla tabeli `pracownik_grupa`
--
ALTER TABLE `pracownik_grupa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pracownika` (`id_pracownika`),
  ADD KEY `id_grupy` (`id_grupy`);

--
-- Indeksy dla tabeli `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- Indeksy dla tabeli `zastepstwa`
--
ALTER TABLE `zastepstwa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pracownika_proszacego` (`id_pracownika_proszacego`),
  ADD KEY `id_pracownika_zastepujacego` (`id_pracownika_zastepujacego`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `grupy`
--
ALTER TABLE `grupy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `harmonogram`
--
ALTER TABLE `harmonogram`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `nadgodziny`
--
ALTER TABLE `nadgodziny`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `pracownik_grupa`
--
ALTER TABLE `pracownik_grupa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `zastepstwa`
--
ALTER TABLE `zastepstwa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `harmonogram`
--
ALTER TABLE `harmonogram`
  ADD CONSTRAINT `harmonogram_ibfk_1` FOREIGN KEY (`id_pracownika`) REFERENCES `uzytkownicy` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `nadgodziny`
--
ALTER TABLE `nadgodziny`
  ADD CONSTRAINT `nadgodziny_ibfk_1` FOREIGN KEY (`id_pracownika`) REFERENCES `uzytkownicy` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pracownik_grupa`
--
ALTER TABLE `pracownik_grupa`
  ADD CONSTRAINT `pracownik_grupa_ibfk_1` FOREIGN KEY (`id_pracownika`) REFERENCES `uzytkownicy` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pracownik_grupa_ibfk_2` FOREIGN KEY (`id_grupy`) REFERENCES `grupy` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `zastepstwa`
--
ALTER TABLE `zastepstwa`
  ADD CONSTRAINT `zastepstwa_ibfk_1` FOREIGN KEY (`id_pracownika_proszacego`) REFERENCES `uzytkownicy` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `zastepstwa_ibfk_2` FOREIGN KEY (`id_pracownika_zastepujacego`) REFERENCES `uzytkownicy` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
