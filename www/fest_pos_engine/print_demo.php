<?php
require 'autoload.php';

use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;

include_once("config.php");

echo "Printer IP: " . $ip_printer . "<br>";
echo "PHP version: " . phpversion() . "<br>";
echo "Apache version: " . apache_get_version() . "<br>";
$extensions = get_loaded_extensions();
foreach ($extensions as $extension) {
   if ($extension == "imagick" or $extension == "gd") {
      echo "Estensione $extension (ver. " . phpversion($extension) . ")\n";
   }
}
echo "<br><br>";
echo "Lancio stampa di prova...";

$connector = new NetworkPrintConnector($ip_printer, 9100);
$printer = new Printer($connector);
try {
   $img = EscposImage::load("demo.png", false);

   $printer->setFont(Printer::FONT_A);
   $printer->setEmphasis(false);
   $printer->setTextSize(1, 1);
   $printer->setJustification(Printer::JUSTIFY_CENTER);
   $printer->text("print_demo.php\n");
   $printer->text("Dimostrazione di stampa via POS\n");
   $printer->text("by FAX con libreria escpos/Mike42\n\n");
   $printer->setJustification(Printer::JUSTIFY_LEFT);
   $printer->text("Printer IP:" . $ip_printer . "\n");
   $printer->text("PHP version:" . phpversion() . "\n");
   $printer->text(apache_get_version() . "\n\n");

   $printer->setJustification(Printer::JUSTIFY_LEFT);
   $printer->setFont(Printer::FONT_A);
   $printer->setEmphasis(false);
   $printer->setTextSize(1, 1);
   $printer->text("________________________________________________\n");
   $printer->text("DEMO> Stampa immagine PNG:\n");
   $printer->graphics($img);

   $printer->setJustification(Printer::JUSTIFY_LEFT);
   $printer->setFont(Printer::FONT_A);
   $printer->setEmphasis(false);
   $printer->setTextSize(1, 1);
   $printer->text("________________________________________________\n");
   $printer->text("DEMO> Giustificazione righe:\n");
   $printer->setJustification(Printer::JUSTIFY_LEFT);
   $printer->text("setJustification(Printer::JUSTIFY_LEFT);\n");
   $printer->setJustification(Printer::JUSTIFY_CENTER);
   $printer->text("setJustification(Printer::JUSTIFY_CENTER);\n");
   $printer->setJustification(Printer::JUSTIFY_RIGHT);
   $printer->text("setJustification(Printer::JUSTIFY_RIGHT);\n\n");

   $printer->setJustification(Printer::JUSTIFY_LEFT);
   $printer->setFont(Printer::FONT_A);
   $printer->setEmphasis(false);
   $printer->setTextSize(1, 1);
   $printer->text("________________________________________________\n");
   $printer->text("DEMO> FONT A\n");
   $printer->setTextSize(1, 1);
   $printer->text("(1,1)48 caratteri con Epson T80II\n");
   $printer->setTextSize(2, 2);
   $printer->text("(2,2)24 caratteri\n");
   $printer->setTextSize(3, 3);
   $printer->text("(3,3)16 car\n");
   $printer->setTextSize(4, 4);
   $printer->text("(4,4)12 car\n");
   $printer->setTextSize(5, 5);
   $printer->text("(5,5)9 ca\n");

   $printer->setJustification(Printer::JUSTIFY_LEFT);
   $printer->setFont(Printer::FONT_A);
   $printer->setEmphasis(false);
   $printer->setTextSize(1, 1);
   $printer->text("________________________________________________\n");
   $printer->text("DEMO> FONT B\n");
   $printer->setFont(Printer::FONT_B);
   $printer->setTextSize(1, 1);
   $printer->text("(1,1)64 caratteri con Epson T80II\n");
   $printer->setTextSize(2, 2);
   $printer->text("(2,2)32 caratteri Epson\n");
   $printer->setTextSize(3, 3);
   $printer->text("(3,3)22 caratteri\n");
   $printer->setTextSize(4, 4);
   $printer->text("(4,4)16 caratt\n");
   $printer->setTextSize(5, 5);
   $printer->text("(5,5)13 cara\n");

   $printer->setJustification(Printer::JUSTIFY_LEFT);
   $printer->setFont(Printer::FONT_A);
   $printer->setEmphasis(false);
   $printer->setTextSize(1, 1);
   $printer->text("________________________________________________\n");
   $printer->text("DEMO> dimensioni asimmetriche\n");
   $printer->setTextSize(1, 2);
   $printer->text("(1,2)\n");
   $printer->setTextSize(2, 1);
   $printer->text("(2,1)\n");
   $printer->setTextSize(1, 3);
   $printer->text("(1,3)\n");
   $printer->setTextSize(3, 1);
   $printer->text("(3,1)\n");

   $printer->setJustification(Printer::JUSTIFY_LEFT);
   $printer->setFont(Printer::FONT_A);
   $printer->setEmphasis(false);
   $printer->setTextSize(1, 1);
   $printer->text("________________________________________________\n");
   $printer->text("DEMO> Emphasis\n");
   $printer->text("NOTA: il numero di caratteri diminuisce!\n");
   $printer->setEmphasis(false);
   $printer->setTextSize(1, 1);
   $printer->text("(1,1+noemph+fontA)\n");
   $printer->setTextSize(2, 2);
   $printer->text("(2,2+noemph+fontA)\n");
   $printer->setEmphasis(true);
   $printer->setTextSize(1, 1);
   $printer->text("(1,1+emphasis+fontA)\n");
   $printer->setTextSize(2, 2);
   $printer->text("(2,2+emphasis+fontA)\n");
   $printer->setFont(Printer::FONT_B);
   $printer->setEmphasis(false);
   $printer->setTextSize(1, 1);
   $printer->text("(1,1+noemph+fontB)\n");
   $printer->setTextSize(2, 2);
   $printer->text("(2,2+noemph+fontB)\n");
   $printer->setEmphasis(true);
   $printer->setTextSize(1, 1);
   $printer->text("(1,1+emphasys+fontB)\n");
   $printer->setTextSize(2, 2);
   $printer->text("(2,2+emphasys+fontB)\n");

   $printer->setJustification(Printer::JUSTIFY_LEFT);
   $printer->setFont(Printer::FONT_A);
   $printer->setEmphasis(false);
   $printer->setTextSize(1, 1);
   $printer->text("________________________________________________\n");
   $printer->text("DEMO> ReverseColors\n");
   $printer->setReverseColors(true);
   $printer->text("Bianco su nero\n");
   $printer->setReverseColors(false);
   $printer->text("Nero su bianco\n");
   $printer->text("________________________________________________\n");
   $printer->text("\n");
   $printer->text("Made with PHP by FAX, and CUT!\n");
   $printer->feed();
   $printer->feed();
   $printer->cut();
} catch (Exception $e) {
   echo "Catturata eccezione:<br>";
   echo $e . "<br><br><br>";
   echo "Sicuro di aver installato le estensioni PHP imagick oppure gd?<br><br>sudo apt-get install php-imagick<br><br>";
} finally {
   $printer->close();
}

echo "Fine dello script.";
