<?php
// ############################################################################
// ############################################################################
// LIBRERIE
// ############################################################################
// ############################################################################

/*
Credit: https://github.com/mike42/escpos-php
*/

require '../autoload.php';

use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;

// ############################################################################
// ############################################################################
// VARIABILI
// ############################################################################
// ############################################################################

ob_start(); //You could use ob_buffers to send the output to oblivion
include_once("../config.php");
ob_end_clean();

/*Arrivano via POST:
data : jsonString ,
totale : totale ,
contanti : contanti ,
resto : resto ,
numero : numero_scontrino
*/

$acquisti = json_decode(stripslashes($_POST['data']));
/*
$acquisti[riga][x]
               [0] = Descrizione a schermo
               [1] = Descrizione a scontrino
               [2] = Prezzo unitario
               [3] = Quantita' acquistata
               [4] = Categoria prodotto
               [5] = Opzioni oppure contenuto porzioni
*/
$totale = $_POST['totale'];
$contanti = $_POST['contanti'];
$resto = $_POST['resto'];
$numero_scontrino = $_POST['numero'];


// ############################################################################
// ############################################################################
// FUNCTIONS
// ############################################################################
// ############################################################################

//##########################################################################################################################
// Stampa scontrini
//##########################################################################################################################

function stampa_scontrini($acquisti, $totale, $contanti, $resto, $numero_scontrino, $testata_scontrino, $ip_printer)
{
  try {
    $connector = new NetworkPrintConnector($ip_printer, 9100);
    $printer = new Printer($connector);

    // ############################################################################
    // Scontrino con prezzi
    // ############################################################################

    $printer->setFont(Printer::FONT_A);
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->setEmphasis(true);
    $printer->setTextSize(5, 5);
    $printer->text("N." . $numero_scontrino . "\n");

    $img = EscposImage::load("sagra_campipiani.png", false);
    $printer->graphics($img);

    $printer->setFont(Printer::FONT_A);
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->setEmphasis(true);
    $printer->setTextSize(2, 2);
    $printer->text($testata_scontrino . "\n");

    $printer->setFont(Printer::FONT_A);
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->setEmphasis(false);
    $printer->setTextSize(1, 1);
    $printer->text("________________________________________________\n");

    $printer->feed();

    $printer->setJustification(Printer::JUSTIFY_LEFT);
    //48 caratteri a disposizione
    //$printer -> text("1--------01--------01--------01--------01-------\n");
    $printer->text("QTA  DESCRIZIONE                        PREZZO\n");
    foreach ($acquisti as $riga_acquisto) {
      //string str_pad ( string $input , int $pad_length [, string $pad_string [, int $pad_type ]] )
      $printer->text(str_pad($riga_acquisto[3], 5, " "));
      $printer->text(str_pad($riga_acquisto[1], 35, " "));
      $printer->text(str_pad($riga_acquisto[2], 6, " "));
      $printer->text("\n");
      if ($riga_acquisto[5] != "") {
        $printer->text("      ");
        $printer->text($riga_acquisto[5]);
        $printer->text("\n");
      }
    }
    $printer->text("________________________________________________\n");

    $printer->setFont(Printer::FONT_A);
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->setEmphasis(true);
    $printer->setTextSize(2, 2);

    $printer->text("Totale:      " . $totale . " euro\n");

    $printer->setEmphasis(false);
    $printer->setTextSize(2, 1);
    if ($contanti != 0) {
      $printer->text("Contanti:    " . $contanti . " euro\n");
    }
    if ($resto != 0) {
      $printer->text("Resto:       " . $resto . " euro\n");
    }

    $printer->feed();

    $printer->setFont(Printer::FONT_A);
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->setEmphasis(false);
    $printer->setTextSize(1, 1);
    date_default_timezone_set("Europe/Rome");
    $printer->text(date("d-m-Y H:i:s") . "\n");
    //$printer -> text("https://tinyurl.com/festpos\n");
    $printer->text("Scontrino non fiscale\n");
    $printer->text("stampato con festpos: github.com/TheFax/festPOS\n");
    $printer->feed();
    $printer->feed();
    $printer->cut();

    // ############################################################################
    // Scontrini per categoria
    // ############################################################################

    //Identifico tutte le categorie presenti tra gli acquisti
    $indice = 0;
    foreach ($acquisti as $riga_acquisto) {
      $array_categorie[$indice] = $riga_acquisto[4];
      $indice++;
    }
    $array_categorie_uniche = array_unique($array_categorie);



    //Verifico se tra le categorie c'è una di quelle che incrementano il contatore "servito"
    $array_elementi_che_incrementano_contatore_servito = ["Cucina", "altra_categoria_che_vuoi_tu"];
    if (!empty(array_intersect($array_categorie_uniche, $array_elementi_che_incrementano_contatore_servito))) {
      // E' stato trovato un elemento che prevede l'incremento del contatore.
      $numero_servito = incrementa_numero_servito();
    }

    foreach ($array_categorie_uniche as $categoria_corrente) {

      if (in_array($categoria_corrente, $array_elementi_che_incrementano_contatore_servito)) {
        // E' stato trovato un elemento che prevede la scritta del "numero servito"
        $printer->setFont(Printer::FONT_A);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setEmphasis(true);
        $printer->setTextSize(3, 3);
        $printer->text("N." . $numero_servito . "\n");
      }

      switch ($categoria_corrente) {
        case "Cucina":
          $img = EscposImage::load("categoria_eat.png", false);
          $printer->graphics($img);
          break;
        case "Bevande":
          $img = EscposImage::load("categoria_drink.png", false);
          $printer->graphics($img);
          break;
        case "Extra":
          $img = EscposImage::load("categoria_fast.png", false);
          $printer->graphics($img);
          break;
        case "Vini DOC":
          $img = EscposImage::load("categoria_vinidoc.png", false);
          $printer->graphics($img);
          break;
        case "BAR":
          $img = EscposImage::load("categoria_drink.png", false);
          $printer->graphics($img);
          break;
        case "Caffè":
          $img = EscposImage::load("categoria_caffe.png", false);
          $printer->graphics($img);
          break;
      }

      $printer->setFont(Printer::FONT_A);
      $printer->setJustification(Printer::JUSTIFY_CENTER);
      $printer->setEmphasis(true);
      $printer->setTextSize(3, 3);
      $printer->text($categoria_corrente . "\n");
      $printer->setTextSize(1, 1);
      $printer->text("________________________________________________\n");

      $printer->feed();

      $printer->setFont(Printer::FONT_A);
      $printer->setJustification(Printer::JUSTIFY_LEFT);
      $printer->setEmphasis(true);
      $printer->setTextSize(2, 2);
      $printer->text("QTA  DESCRIZIONE\n");

      $printer->setEmphasis(false);
      foreach ($acquisti as $riga_acquisto) {
        if ($riga_acquisto[4] == $categoria_corrente) {
          //string str_pad ( string $input , int $pad_length [, string $pad_string [, int $pad_type ]] )
          $printer->text(str_pad($riga_acquisto[3], 5, " "));
          $printer->text($riga_acquisto[1]);
          $printer->text("\n");
          if ($riga_acquisto[5] != "") {
            $printer->text("      ");
            $printer->text($riga_acquisto[5]);
            $printer->text("\n");
          }
          $printer->text("\n");
        }
      }

      $printer->feed();
      $printer->cut();
    }

    $printer->close();

    return "+Lo scontrino n." . $numero_scontrino . "e' stato stampato.";
    //TODO: tutto in una stringa... non echo.
  } catch (Exception $e) {
    return "-ERRORE: \n" . $e->getMessage() . "\nVerificare la presenza di carta nelle stampanti e la loro connessione ethernet";
  } finally {
  }
}

//##########################################################################################################################
//Salvataggio dati statistici
//##########################################################################################################################

function salva_csv_scontrino($acquisti, $numero_scontrino)
{
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
  $myfile = fopen("../statistiche/scontrini.csv", "a") or die("Impossibile salvare dati statistici scontrino.");
  while (!flock($myfile, LOCK_EX)) {
  }                    // Attendo che il file sia effettivamente bloccato
  //Con il prossimo ciclo for, tramuto il contenuto dello scontrino in una stringa CSV
  $contenuto_scontrino = "";
  foreach ($acquisti as $riga_acquisto) {

    $contenuto_scontrino .= $numero_scontrino . "," . $riga_acquisto[0] . "," . $riga_acquisto[5] . "," . $riga_acquisto[3] . "," . strval($riga_acquisto[2] * $riga_acquisto[3]) . "\r\n";
  }
  fwrite($myfile, $contenuto_scontrino);   //Scrivo nuovo valore nel file
  fflush($myfile);        // Flush
  flock($myfile, LOCK_UN); // Sblocco il file
  fclose($myfile);       // Chiudo il file
}

function salva_incasso($acquisti)
{
  $myfile = fopen("../statistiche/incasso.txt", "c+") or die("Impossibile salvare dati statistici scontrino.");
  while (!flock($myfile, LOCK_EX)) {
  }                    // Attendo che il file sia effettivamente bloccato
  $in_file = fgets($myfile);   // Leggo la prima riga del file (max 1024 caratteri)
  $in_file = floatval($in_file);       // Converto in intero
  //Con le prossime righe, calcolo il totale in euro di questo scontrino.
  $totale_scontrino = 0;
  foreach ($acquisti as $riga_acquisto) {
    $totale_scontrino += ($riga_acquisto[2] * $riga_acquisto[3]);
  }
  $grand_total = $in_file + $totale_scontrino;  //Sommo quello che già conteneva il file con lo scontrino corrente
  ftruncate($myfile, 0);  // Tronco il file a posizione 0
  rewind($myfile);        // Riavvolgo il file (no, non e' scontato!)
  fwrite($myfile, strval($grand_total));   //Scrivo nuovo valore nel file
  fflush($myfile);        // Flush
  flock($myfile, LOCK_UN); // Sblocco il file
  fclose($myfile);       // Chiudo il file
}

function conteggio_prodotti_venduti($acquisti)
{
  require '../json_database_class.php';

  try {
    $db = new JsonDatabase('../statistiche/prodotti_venduti.json');

    foreach ($acquisti as $riga_acquisto) {
      $db->increment($riga_acquisto[0], $riga_acquisto[3]);
    }

    // Salva le modifiche nel file JSON
    $db->save();
  } catch (Exception $e) {
    echo 'Errore: ' . $e->getMessage();
  }
}

function incrementa_numero_servito()
{
  $filename = "numero_servito.txt";
  $numero_init = 0;
  $numero_max = 99; // Valore al quale il contatore si riavvolge
  $numero_servito = $numero_init; // Valore di default

  // Tenta di leggere il contenuto del file
  if (file_exists($filename)) {
    $content = file_get_contents($filename);

    // Verifica se il contenuto è un numero valido tra 0 e 99
    if ($content !== false && ctype_digit($content) && (int)$content >= 0 && (int)$content <= 99) {
      $numero_attuale = (int)$content;
      $numero_incrementato = ($numero_attuale + 1) % ($numero_max + 1); // Incrementa e riavvolge a 0 se maggiore di $numero_max
    } else {
      // Contenuto inatteso o errore di lettura, inizializza a 0
      $numero_servito = $numero_init;
    }
  } else {
    // Il file non esiste, inizializza a 0
    $numero_servito = $numero_init;
  }

  // Salva il nuovo numero nel file
  if (file_put_contents($filename, (int)$numero_servito) === false) {
    // Gestione dell'errore di scrittura (es. permessi)
    error_log("Errore: Impossibile scrivere nel file '$filename'. Controlla i permessi.");
    // Potresti voler gestire l'errore in modo diverso, ad esempio mostrando un messaggio all'utente
  }

  $numero_servito = str_pad($numero_incrementato, 2, '0', STR_PAD_LEFT); // Metto zero pad iniziali se necessario

  return $numero_servito;
}

// ############################################################################
// ############################################################################
// MAIN
// ############################################################################
// ############################################################################

$ritorno = stampa_scontrini($acquisti, $totale, $contanti, $resto, $numero_scontrino, $testata_scontrino, $ip_printer);
if (substr($ritorno, 0, 1) === "-") {
  echo $ritorno;
  die();
}
salva_csv_scontrino($acquisti, $numero_scontrino);
salva_incasso($acquisti);
conteggio_prodotti_venduti($acquisti);
