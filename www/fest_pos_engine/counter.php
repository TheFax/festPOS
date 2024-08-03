<?php
//Questo file si occupa di restituire il numero di scontrino, aggiornando il file relativo.

// Impostazioni personalizzabili
$file_contatore = "statistiche/counter.txt";
$upper_limit = 999;

// Apro il file
$myfile = fopen($file_contatore, "c+") or die("Impossibile inizializzare il file di conteggio. Ref: FAX001");

// Attendo che il file sia effettivamente bloccato
while (!flock($myfile, LOCK_EX)) {
}

// Leggo la prima riga del file (max 1024 caratteri)
$x = fgets($myfile);

// Stringa troppo lunga? Allora per sicurezza azzero.
if (strlen($x) > 5) {
  $x = "0";
}

//Provo a convertire in intero
try {
  $x = intval($x);
} catch (Exception $e) {
  $x = 0;
}

// Incremento di uno
$x++;

// Verifico di essere dentro ai limiti
if ($x > $upper_limit) {
  $x = 1;
}

ftruncate($myfile, 0);         // Tronco il file a posizione 0
rewind($myfile);               // Riavvolgo il file (no, non e' scontato!)
fwrite($myfile, strval($x));   //Scrivo nuovo valore nel file
fflush($myfile);               // Flush
flock($myfile, LOCK_UN);       // Sblocco il file
fclose($myfile);               // Chiudo il file

echo $x;                       // Restituisco all'utente il numero
