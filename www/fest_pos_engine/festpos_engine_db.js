var acquisti = new Array();
/*
L'array acquisti è bidimensionale ed è così congegnato:
acquisti[indice_acquisto][0-5]
[0] = Descrizione a schermo
[1] = Descrizione a scontrino
[2] = Prezzo unitario
[3] = Quantità acquistata
[4] = Categoria prodotto
[5] = Opzioni oppure contenuto porzioni
*/

function add_prodotto_universale(txttab, txtscontrino, prezzo, categoria, opzioni) {
  //Aggiunge un elemento al vettore acquisti, tenendo conto che lo stesso prodotto deve
  //essere considerato un aumento di quantità.

  if (txtscontrino == "IDEM") {
    txtscontrino = txttab;
  }

  var fatto = 0;
  for (var i = 0; i < acquisti.length; i++) {
    //Cerco se c'è già una riga nel vettore con lo stesso oggetto che si vuole acquistare
    if (acquisti[i][0] == txttab && acquisti[i][5] == opzioni) {
      //Arrivo qui se c'è una riga uguale, quindi devo solo incrementare la quantità di uno.
      acquisti[i][3]++;
      fatto = 1;
    }
  }

  if (fatto == 0) {
    //Arrivo qui se non c'era una riga uguale nel vettore, quindi devo crearla da zero.
    acquisti.push([txttab, txtscontrino, con_decimali(prezzo), 1, categoria, opzioni]);
  }

  //Aggiorno lo schermo
  visualizza();
}

function add_prodotto(txttab, txtscontrino, prezzo, categoria) {
  //Aggiunge un elemento SENZA OPZIONI al vettore acquisti
  add_prodotto_universale(txttab, txtscontrino, prezzo, categoria, "");
}

function add_prodotto_radio_radio(txttab, txtscontrino, prezzo, categoria, radioA, radioB) {
  //Aggiunge un elemento CON OPZIONI al vettore acquisti
  var fatto = 0;
  var opzioni = "";
  var opz1 = "";
  var opz2 = "";

  //Cerco quale primo è stato selezionato tra i Radio Buttons
  var myRadio = document.getElementsByName(radioA);
  for (var i = 0; i < myRadio.length; i++) {
    if (myRadio[i].checked) {
      opz1 += myRadio[i].value + " ";
    }
  }
  //Cerco quale secondo è stato selezionato tra i Radio Buttons
  var myRadio = document.getElementsByName(radioB);
  for (var i = 0; i < myRadio.length; i++) {
    if (myRadio[i].checked) {
      opz2 += myRadio[i].value + " ";
    }
  }

  if (opz1 == "" || opz2 == "") {
    //Arrivo qui se stai cercando di aggiungere una porzione SENZA primo e/o SENZA secondo
    return;
  }

  opzioni = opz1 + "+ " + opz2;

  //A questo punto ho tutte le info che mi servono.
  //Aggiungo quindi la riga acquisto.
  add_prodotto_universale(txttab, txtscontrino, prezzo, categoria, opzioni)
}

function con_decimali(numero) {
  //Dai un numero qualsiasi a questa funzione, e lei te lo restituisce con due decimali, anche se sono zeri.
  //Questa funzione è stata aggiunta perchè Javascript ha uno strano modo di concepire i numeri float.
  return parseFloat(Math.round(numero * 100) / 100).toFixed(2);
}

function togli_uno(index) {
  //Acquista un elemento di meno (passare alla funzione il numero di indice del vettore)
  //Se la quantità diventa zero, toglie l'elemento dal vettore acquisti.
  acquisti[index][3]--;
  if (acquisti[index][3] <= 0) {
    //Se la quantità risultante dal vettore acquisti è uguale o inferiore a zero, toglie la riga.
    acquisti.splice(index, 1);
  }
  //Aggiorna lo schermo
  visualizza();
}

function aggiungi_uno(index) {
  //Acquista un elemento in puù (passare alla funzione il numero di indice del vettore)
  acquisti[index][3]++;
  //Aggiorna lo schermo
  visualizza();
}

function stampa(totale, contanti, resto) {
  //Questa funzione si occupa di:
  //-Prelevare dal server il prossimo numero scontrino
  //-Richiamare la pagina PHP che si occupa della stampa

  //TODO: Questa funzione deve essere in un file personalizzabile forse...

  if (calcola_totale() == 0) {  //TODO: Fare qualche controllo in più qui...
    alert("ERRORE: Importo zero per questo scontrino! REF ALERT 1");
    return;
  }

  var request = new XMLHttpRequest();
  //TODO: Le pagine PHP vanno inserite in variabili se possibile.
  request.open('GET', 'counter.php', false);  // "false"  -> Richiesta sincrona, quindi attendo fin che non arriva una risposta

  try {
    request.send(null);
    if (request.status === 200) {
      //Arrivo qui se la risposta con il numero dello scontrino è arrivata dal server.
      var numero_scontrino = request.responseText;
      //DEBUG
      //console.log("Il server risponde con numero scontrino: " + numero_scontrino);
    } else {
      //Arrivo qui se non è arrivata risposta del server.
      alert("ERRORE: il server non sta fornendo l'ID dello scontrino. REF ALERT 2");
      return;
    }
  }
  catch (err) {
    //Arrivo qui se (probabilmente) non sono proprio riuscito ad inoltrare la richiesta al server.
    alert("ERRORE: impossibile eseguire richiesta al server. REF ALERT 3");
    return;
  }

  //Invio via jQuery/Ajax/POST la stringa degli acquisti, il totale, il resto e i contanti alla pagina PHP che si occupa di stampare
  var jsonString = JSON.stringify(acquisti);
  $.ajax({
    type: "POST",
    url: config_folder + "/receipt_print.php",
    data: { data: jsonString, totale: totale, contanti: contanti, resto: resto, numero: numero_scontrino },
    cache: false,
    success: function (data) { if (data != '') alert(data); reset(); }  //la variabile data contiene la risposta PHP inviata in echo
  });
  alert("Scontrino numero: " + numero_scontrino);
}

function reset() {
  azzera();
  calcolatrice_hide();
}

function azzera() {
  //Azzera il vettore acquisti
  var lenghtacq = acquisti.length;
  //array.splice(index, howmany, item1, ....., itemX)
  acquisti.splice(0, lenghtacq);
  //Aggiorna lo schermo
  visualizza();
}

function calcola_totale() {
  var TotaleInEuro = 0;
  for (var i = 0; i < acquisti.length; i++) {
    TotaleInEuro = TotaleInEuro + (acquisti[i][2] * acquisti[i][3]);
    //TotaleInEuro.toFixed(2);  //TODO: Credo che questo sia un errore
  }
  return TotaleInEuro;
}

function visualizza() {
  //Visualizza il vettore acquisti sullo schermo

  //DEBUG
  //console.log("Visualizzazione array acquisti:");  for(var i=0;i<acquisti.length;i++) { console.log(acquisti[i]); }

  //Calcolo e visualizzo la tabella dei prodotti acquistati
  var myTab = document.getElementById('prodotti');
  var htmlCalcolato = ""
  htmlCalcolato = "<table class='table_prodotti_scelti'>";
  for (var i = 0; i < acquisti.length; i++) {
    var aggiungi_questa_stringa = "";
    aggiungi_questa_stringa += "<tr class='tr_acquisti'>";
    aggiungi_questa_stringa += "<td class='td_acquisti'>";
    aggiungi_questa_stringa += "<button onclick=togli_uno(" + i + ")>-</button>  "
    aggiungi_questa_stringa += acquisti[i][3] + " X  ";
    aggiungi_questa_stringa += "<button onclick=aggiungi_uno(" + i + ")>+</button>"
    aggiungi_questa_stringa += "</td>";
    //aggiungi_questa_stringa += "<td class='td_acquisti'>";
    //aggiungi_questa_stringa += "<button onclick=togli_uno(" + i + ")>-</button>"
    //aggiungi_questa_stringa += "<button onclick=aggiungi_uno(" + i + ")>+</button>"
    //aggiungi_questa_stringa += "</td>"
    aggiungi_questa_stringa += "<td class='td_acquisti'>";
    aggiungi_questa_stringa += acquisti[i][0];
    if (acquisti[i][5] != "") { aggiungi_questa_stringa += "<br>" + acquisti[i][5]; }
    aggiungi_questa_stringa += "</td>";
    aggiungi_questa_stringa += "<td class='td_acquisti' align='right'>"
    aggiungi_questa_stringa += con_decimali(acquisti[i][2]) + " €";
    aggiungi_questa_stringa += "</td>";
    aggiungi_questa_stringa += "</tr>";
    htmlCalcolato = htmlCalcolato + aggiungi_questa_stringa;
  }
  htmlCalcolato = htmlCalcolato + "</table>";
  myTab.innerHTML = htmlCalcolato;

  //Calcolo e visualizzo il totale
  var myTotale = document.getElementById('totale');
  myTotale.innerHTML = "<br><big><strong>Totale: &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp Euro " + con_decimali(calcola_totale()) + "</strong></big>";
}