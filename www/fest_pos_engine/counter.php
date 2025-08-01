<?php
// Questo file si occupa di restituire il numero di scontrino, aggiornando il file relativo.

// Impostazioni personalizzabili
$file_contatore = "statistiche/counter.txt";
$upper_limit = 999;

// Apro il file
$myfile = fopen($file_contatore, "c+") or die("Impossibile inizializzare il file di conteggio. Ref: FAX001");

// Attendo che il file sia effettivamente bloccato
while (!flock($myfile, LOCK_EX | LOCK_NB)) {
  // Il file è bloccato da un altro processo, attendo un breve periodo (es. 10ms = 10000 microsecondi)
  usleep(10000);
}

$x = fgets($myfile);  // Leggo la prima riga del file (max 1024 caratteri)
$x = trim($x);        // Trimmo la riga, per eliminare eventuali caporiga.

if (strlen($x) > 5) {
  // Stringa troppo lunga? Allora per sicurezza azzero.
  $x = "0";
}

$x = intval($x);      // Provo a convertire in intero. Se non ci riesco, intval restituisce 0
$x++;                 // Incremento di uno

if ($x > $upper_limit) {
  // Verifico di essere dentro ai limiti, altrimenti riavvolgo il contatore
  $x = 1;
}

ftruncate($myfile, 0);         // Tronco il file a posizione 0
rewind($myfile);               // Riavvolgo il file (no, non e' scontato!)
fwrite($myfile, strval($x));   // Scrivo nuovo valore nel file
fflush($myfile);               // Flush
flock($myfile, LOCK_UN);       // Sblocco il file
fclose($myfile);               // Chiudo il file

echo $x;                       // Restituisco all'utente il numero
