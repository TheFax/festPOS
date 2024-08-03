<?php

// ############################################################################
// ############################################################################
// LIBRERIE
// ############################################################################
// ############################################################################

/*Credit: https://github.com/mike42/escpos-php*/
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
    $printer->text("./statistiche/*.*\n");

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
  if (is_readable($filename)) {
    $myfile = fopen($filename, "r");
    while (!flock($myfile, LOCK_EX)) {
    }                                       // Attendo che il file sia effettivamente bloccato
    $in_file = fgets($myfile);                // Leggo la prima riga del file (max 1024 caratteri)
    flock($myfile, LOCK_UN);                       // Sblocco il file
    fclose($myfile);                             // Chiudo il file
  } else {
    return "Chiusura cassa gia' effettuata";
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
}
