<?php

// ############################################################################
// ############################################################################
// LIBRERIE
// ############################################################################
// ############################################################################

/*Credit: https://github.com/mike42/escpos-php*/
require '../autoload.php';

use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;

// ############################################################################
// ############################################################################
// VARIABILI
// ############################################################################
// ############################################################################

include_once("../config.php");

// ############################################################################
// ############################################################################
// MAIN
// ############################################################################
// ############################################################################

$return = fortune($ip_printer);
echo $return;

// ############################################################################
// ############################################################################
// FUNCTIONS
// ############################################################################
// ############################################################################

function random_pic()
{
   $files = glob('random*.png');
   $file = array_rand($files);
   return $files[$file];
}

function fortune($ip_printer)
{
   try {
      $connector = new NetworkPrintConnector($ip_printer, 9100);
      $printer = new Printer($connector);

      $img = EscposImage::load(random_pic(), false);
      $printer->graphics($img);

      $printer->feed();

      $printer->cut();

   } catch (Exception $e) {
      return "ERRORE:<br>" . $e->getMessage() . "<br><br>Verificare la presenza di carta nelle stampanti e la loro connessione ethernet.";
   } finally {
      $printer->close();
   }
   return "Eseguito!";
}
