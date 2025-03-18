CREATE TABLE `zastepstwa_uzytkownicy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_zastepstwa` int(11) NOT NULL,
  `id_uzytkownika` int(11) NOT NULL,
  `status` enum('oczekujące', 'zaakceptowane', 'odrzucone') DEFAULT 'oczekujące',
  PRIMARY KEY (`id`),
  KEY `id_zastepstwa` (`id_zastepstwa`),
  KEY `id_uzytkownika` (`id_uzytkownika`),
  CONSTRAINT `zastepstwa_uzytkownicy_ibfk_1` FOREIGN KEY (`id_zastepstwa`) REFERENCES `zastepstwa` (`id`) ON DELETE CASCADE,
  CONSTRAINT `zastepstwa_uzytkownicy_ibfk_2` FOREIGN KEY (`id_uzytkownika`) REFERENCES `uzytkownicy` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;