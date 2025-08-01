<?php

// ############################################################################
// ############################################################################
// LIBRERIE
// ############################################################################
// ############################################################################

/*Credit: https://github.com/mike42/escpos-php*/

use function Dom\import_simplexml;

require 'autoload.php';

use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;

// ############################################################################
// ############################################################################
// VARIABILI
// ############################################################################
// ############################################################################

/*Arrivano via POST:
data : jsonString ,
totale : totale ,
contanti : contanti ,
resto : resto ,
numero : numero_scontrino
*/

include_once("config.php");


// ############################################################################
// ############################################################################
// MAIN
// ############################################################################
// ############################################################################

echo "Lancio stampa scontrino di chiusura...";
chiusura_cassa($ip_printer);
echo "ESEGUITA. <br>";

echo "Rinomina file statistici...";
rinomina_file();
echo "ESEGUITO. <br>";

// ############################################################################
// ############################################################################
// FUNCTIONS
// ############################################################################
// ############################################################################


function chiusura_cassa($ip_printer)
{
  try {
    $connector = new NetworkPrintConnector($ip_printer, 9100);
    $printer = new Printer($connector);

    // ############################################################################
    // Scontrino chiusura cassa
    // ############################################################################

    $img = EscposImage::load("chiusura_cassa.png", false);
    $printer->graphics($img);

    $printer->setFont(Printer::FONT_A);
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->setEmphasis(true);
    $printer->setTextSize(2, 2);
    $printer->text("Chiusura cassa\n\n");

    $printer->setFont(Printer::FONT_A);
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->setEmphasis(false);
    $printer->setTextSize(1, 1);
    date_default_timezone_set("Europe/Rome");
    $printer->text(date("d-m-Y H:i:s") . "\n\n");

    $printer->text("Incasso dall'ultima chiusura [Euro]: \n");
    $printer->text(get_file("./statistiche/incasso.txt") . "\n\n");

    $printer->text("Numero scontrini emessi [N]: \n");
    $printer->text(get_file("./statistiche/counter.txt") . "\n\n");

    $printer->text("Posizione file statistici:\n");
    $printer->text("./statistiche/*.*\n\n");

    // Cerco di stampare l'elenco dei prodotti venduti
    $filePath = './statistiche/prodotti_venduti.json';
    $prodottiVenduti = null;
    // Verifica se il file esiste
    if (file_exists($filePath)) {
      // Legge il contenuto del file in una stringa
      $jsonData = file_get_contents($filePath);

      // Decodifica la stringa JSON in un array associativo o un oggetto
      // true come secondo parametro per ottenere un array associativo
      $prodottiVenduti = json_decode($jsonData, true);

      // Verifica se la decodifica è andata a buon fine
      if (json_last_error() === JSON_ERROR_NONE) {
        $printer->text("Elenco prodotti venduti:\n");
        $printer->text("--------------------------------------\n");
        ksort($prodottiVenduti);
        // Itera direttamente sulle coppie chiave-valore
        foreach ($prodottiVenduti as $prodottoNome => $quantitaVenduta) {
          $printer->text("> " . str_pad($prodottoNome, 23, " ") . " Qtà: " . $quantitaVenduta . "\n");
        }
        $printer->text("--------------------------------------\n\n");
      }
    } else {
      $printer->text("Impossibile leggere il file dei\n");
      $printer->text("prodotti venduti.\n");
      $printer->text("Forse la cassa è già stata chiusa?\n");
    }

    $printer->feed();

    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->qrCode("http://goodstone.altervista.org/fest_pos/", Printer::QR_ECLEVEL_L, 5, Printer::QR_MODEL_2);

    $printer->feed();

    $printer->cut();

    $printer->close();

    return "+Lo scontrino di chiusura e' stato stampato.";
  } catch (Exception $e) {
    return "-ERRORE: \n" . $e->getMessage() . "\nVerificare la presenza di carta nelle stampanti e la loro connessione ethernet";
  } finally {
  }
}

/*##########################################################################################################################
Salvataggio dati statistici
##########################################################################################################################*/

/*
mode 	Descrizione
'r' 	Apre in sola lettura; posiziona il puntatore all'inizio del file.
'r+' 	Apre in lettura e scrittura; posiziona il puntatore all'inizio del file.
'w' 	Apre il file in sola scrittura; posiziona il puntatore all'inizio del file e tronca il file alla lunghezza zero. Se il file non esiste, tenta di crearlo.
'w+' 	Apre in lettura e scrittura; posiziona il puntatore all'inizio del file e tronce il file alla lunghezza zero. Se il file non esiste, tenta di crearlo.
'a' 	Apre in sola scrittura; posiziona il puntatore alla fine del file. Se il file non esiste, tenta di crearlo.
'a+' 	Apre in lettura e scrittura; posiziona il puntatore alla fine del file. Se il file non esiste, tenta di crearlo.
'x' 	Crea ed apre il file in sola scrittura; posiziona il puntatore all'inizio del file. Se il file esiste già la chiamata a fopen() fallirà restituendo FALSE e verrà generato un errore di lievllo E_WARNING. Se il file non esiste si tenterà di crearlo. Questo equivale a specificare i flag O_EXCL|O_CREAT nella sottostante chiamata a open(2) . Questa opzione è supportata a partire dalla versione 4.3.2 di PHP, e funziona solo con i file locali.
'x+' 	Crea ed apre il file in lettura e scrittura; posiziona il puntatore all'inizio del file. Se il file esiste già la chiamata a fopen() fallirà restituendo FALSE e verrà generato un errore di lievllo E_WARNING. Se il file non esiste si tenterà di crearlo. Questo equivale a specificare i flag O_EXCL|O_CREAT nella sottostante chiamata a open(2) . Questa opzione è supportata a partire dalla versione 4.3.2 di PHP, e funziona solo con i file locali.
*/


function get_file($filename)
{
    // 1. Controlla prima se il file esiste / è leggibile
    if (!file_exists($filename)) {
        return "Cassa già chiusa.";
    }

    if (!is_readable($filename)) {
        return "File '$filename' non leggibile";
    }

    $myfile = @fopen($filename, "r"); // Usiamo '@' per sopprimere gli errori di fopen e gestirli manualmente

    // 2. Controllo se l'apertura del file è andata a buon fine
    if ($myfile === false) {
        return "Impossibile leggere '$filename'.";
    }

    $max_attempts = 10; // Numero massimo di tentativi per il blocco
    $attempt = 0;
    $locked = false;

    // 3. Blocco del file con timeout
    while ($attempt < $max_attempts) {
        if (flock($myfile, LOCK_EX | LOCK_NB)) { // LOCK_NB per non bloccare l'esecuzione
            $locked = true;
            break; // Il blocco è stato acquisito
        }
        usleep(100000); // Aspetta 100ms prima di riprovare
        $attempt++;
    }

    if (!$locked) {
        fclose($myfile);
        return "Festpos in uso?\nImpossibile acquisire blocco\nsu '$filename'.";
    }

    $in_file = ""; // Inizializza la variabile per sicurezza

    // 4. Lettura della prima riga (se questo è l'intento)
    // Se vuoi leggere l'intero contenuto, usa $in_file = file_get_contents($filename); dopo lo sblocco e la chiusura (o senza flock)
    // o se vuoi leggere l'intero contenuto con blocco, leggi tutto qui: while (!feof($myfile)) { $in_file .= fgets($myfile); }
    $in_file = fgets($myfile); 

    flock($myfile, LOCK_UN); // Sblocco il file
    fclose($myfile); // Chiudo il file

    // 5. Gestione del caso in cui il file sia vuoto o fgets non legga nulla
    if ($in_file === false || $in_file === "") {
        return "File '$filename' vuoto.";
    }

    return $in_file;
}

function rinomina_file()
{
  date_default_timezone_set("Europe/Rome");
  $now = date("Y-m-d_H-i-s");
  if (is_readable("./statistiche/counter.txt")) {
    rename("./statistiche/counter.txt", "./statistiche/" . $now . "_counter.txt");
  }
  if (is_readable("./statistiche/incasso.txt")) {
    rename("./statistiche/incasso.txt", "./statistiche/" . $now . "_incasso.txt");
  }
  if (is_readable("./statistiche/scontrini.csv")) {
    rename("./statistiche/scontrini.csv", "./statistiche/" . $now . "_scontrini.csv");
  }
  if (is_readable("./statistiche/prodotti_venduti.json")) {
    rename("./statistiche/prodotti_venduti.json", "./statistiche/" . $now . "_prodotti_venduti.json");
  }
  if (file_exists("./numero_servito.txt")) {
    // Prova a cancellare il file
    unlink("./numero_servito.txt");
  }
}
