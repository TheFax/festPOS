<html lang="en-US">

<head>
  <title>Contatore sagra</title>
  <link href="custom.css" rel="stylesheet" type="text/css">
  <meta charset="utf-8">
</head>

<body onkeypress="myFunction(event)">
  <div id="titolo"> <span> Stiamo servendo... </span> </div>
  <div id="container">
    <!-- Da modificare in : [?php include 'contatore.php';?] -->

    <?php
    $myfile = fopen("last", "r+") or fopen("last", "w+") or die("Impossibile inizializzare file");

    // Attendo che il file sia effettivamente bloccato
    while (!flock($myfile, LOCK_EX)) {
    }

    $x = fgets($myfile);
    $x = intval($x);
    if (intval($_GET['skip']) != 0) {
      $x = intval($_GET['skip']);
    }
    if (
      $_GET['code'] == 43 or
      $_GET['code'] == 32
    ) {
      $x = $x + 1;
    }
    if ($_GET['code'] == 45) {
      $x = $x - 1;
    }
    if ($x > 999) {
      $x = 0;
    } elseif ($x < 0) {
      $x = 999;
    }

    ftruncate($myfile, 0);  // Tronco il file a posizione 0
    rewind($myfile);        // Riavvolgo il file (no, non e' scontato!)
    fwrite($myfile, strval($x));   //Scrivo nuovo valore nel file 

    fflush($myfile);
    flock($myfile, LOCK_UN);
    fclose($myfile);

    echo $x;
    ?>
  </div>
  <div id="footer"> <span> Sagra Campipiani</span> </div>
</body>

<script>
  var audio = new Audio('ding.mp3');
  audio.play();

  function myFunction(event) {
    var chCode = ('charCode' in event) ? event.charCode : event.keyCode;
    //alert(chCode);
    if (chCode == 115 || chCode == 47) { //lettera S o tasto /
      var input = prompt("Salto al numero...", "");
      window.location = 'counter.php?skip=' + input;
    } else {
      window.location = 'counter.php?code=' + chCode;
    }
  }
</script>

</html>