<?php 
function logMessage($level, $message, $userID)
{
    $logFile = __DIR__ . '/log.txt'; // Ścieżka do pliku log.txt w folderze głównym
    $date = date('Y-m-d H:i:s'); // Aktualna data i czas
    $logEntry = "$date | $level | $userID | $message" . PHP_EOL; // Tworzenie wpisu logu
    file_put_contents($logFile, $logEntry, FILE_APPEND); // Zapis do pliku log.txt
}
?>