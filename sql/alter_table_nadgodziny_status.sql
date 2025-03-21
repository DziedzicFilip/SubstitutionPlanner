ALTER TABLE nadgodziny 
ADD COLUMN status ENUM('aktywne', 'cofniete') NOT NULL DEFAULT 'aktywne';