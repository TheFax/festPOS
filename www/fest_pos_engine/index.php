<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="icon" href="favicon.png" type="image/png" />
    <title>festPOS</title>
</head>

<?php include_once ('config.php'); ?>
<script src="jquery-1.12.4.js"></script>
<!-- Jquery serve per la comunicazione tra Javascript e PHP -->
<script src="festpos_engine_db.js"></script>
<link href="festpos_stile.css" rel="stylesheet" type="text/css">
<link href="festpos_prodotti.css" rel="stylesheet" type="text/css">

<body>
   
    <table class="tbl_con_bordo" style="width:100%; height:600px;">
        <tr>
            <td>Prodotti scelti</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td class="tbl_con_bordo sfondo_grigio">
                <div id="prodotti"></div>
                <div id="totale"></div>
            </td>
            <td class="td_buttons">
                <?php include ($config_folder.'/colonna_sx.php'); ?>
            </td>
            <td class="td_buttons">
                <?php include ($config_folder.'/colonna_dx.php'); ?>
            </td>
        </tr>
        <tr>
            <td class="tbl_con_bordo sfondo_grigio">
                <input type=button value="Emetti scontrino" class="mybutton" style="background-color: #E83448; width: 390px;" onclick="calcolatrice_show()"><br>
            </td>
            <td class="tbl_con_bordo sfondo_grigio">
                <input type=button value="Torna alla pagina iniziale" class="mybutton" style="background-color: #E83448; width: 390px;" onclick="location.href = '../';"><br>
            </td>
            <td class="tbl_con_bordo sfondo_grigio">
                <!--<input type=button class="mybutton" value="[debug] refresh" onclick=visualizza()>-->
                <input type=button value="Azzera" class="mybutton" style="background-color: #FF0000; " onclick=azzera()>
            </td>
        </tr>
    </table>

    <!-- CALCOLATRICE CHE ENTRA DA SINISTRA -->
    <div id="calcolatore" style="position: fixed; top: 0px; left: -100%; background: #BF844E; height:100%; width:100%; visibility: visible; transition: left 0.5s;">
        <script type="text/javascript">
            function calcolatrice_show() {
                if (calcola_totale() == 0) {
                    alert("WARNING: Non c'è nulla di acquistato! REF ALERT 5");
                    return;
                }
                var myCalc = document.getElementById("calcolatore");
                myCalc.style.left = "0px";
                var myCalc = document.getElementById("calcolatrice_display");
                myCalc.value = '';
                var myCalc = document.getElementById("CalcTotale");
                myCalc.innerHTML = "Totale: € " + con_decimali(calcola_totale());
                var myCalc = document.getElementById("CalcResto");
                myCalc.innerHTML = "Resto: € ---";
            }

            function calcolatrice_hide() {
                var myCalc = document.getElementById("calcolatore");
                myCalc.style.left = "-100%";
            }

            function calcolatrice_display_press(valore) {
                var myDisplay = document.getElementById("calcolatrice_display");

                if (valore == "CA") {
                    myDisplay.value = '';
                } else if (valore == "del") {
                    if (myDisplay.value.length >= 1) {
                        myDisplay.value = myDisplay.value.slice(0, myDisplay.value.length - 1);
                    }
                    valore = '';
                } else {
                    myDisplay.value += valore;
                }

                calcolatrice_update_resto();
            }

            function calcolatrice_update_resto() {
                var myDisplay = document.getElementById("calcolatrice_display");
                var myResto = document.getElementById("CalcResto");

                if (calcola_totale() < myDisplay.value) {
                    myResto.innerHTML = "Resto: € " + con_decimali(myDisplay.value - calcola_totale());
                } else {
                    myResto.innerHTML = "Resto: € ---";
                }
            }

            function procedi_con_stampa() {
                var myDisplay = document.getElementById("calcolatrice_display");
                var myResto = document.getElementById("CalcResto");
                var Totale = calcola_totale();
                var Contanti = myDisplay.value;
                var Resto = Contanti - Totale;
                if (Resto < 0) Resto = 0;
                Totale = con_decimali(Totale);
                Contanti = con_decimali(Contanti);
                Resto = con_decimali(Resto);
                stampa(Totale, Contanti, Resto);
            }

            function sterilizza_input(key_code) {
                //DEBUG
                //alert(key_code);
                
                var myDisplay = document.getElementById("calcolatrice_display");

                if (key_code == 13) { //INVIO
                    alert("TO DO: Stamperei scontrino ora");
                }
                if (key_code == 27) { //ESC
                    myDisplay.value = "";
                }

                myDisplay.value = myDisplay.value.replace(/[^0-9.]/g, '');

                calcolatrice_update_resto();
            }

        </script>

        <style>
            .calcolatrice_style {
                width: 100%;
                height: 100%;
                font-size: 40px;
                border-radius: 4px;
                /*background-color: #AAAAAA;  /*Colore dello sfondo (se non diversamente specificato) */
                display: inline-block;
                border: none;
                margin: 2px 1px;
                /*margine superiore/inferiore sinistro/destro */
                cursor: pointer;
            }

            #CalcTotale,
            #CalcResto {
                font-size: 40px;
                text-align: center;
                font-weight: bold;
                position: absolute;
                left: 20px;
                width: 400px;
                height: 50px;
                background: #FFB068;
                border: 3px solid #7F5834;
                border-radius: 4px;
            }

            #CalcTotale {
                top: 20px;
            }

            #CalcResto {
                top: 120px;
            }

            #calc {
                width: 400px;
                /*Larghezza della calcolatrice*/
                position: absolute;
                /*Questa riga serve per centrarla nello schermo SX-DX*/
                /*margin-left:-200px;  /*Questa riga serve per centrarla nello schermo SX-DX*/
                /* half of width */
                /*left:50%;            /*Questa riga serve per centrarla nello schermo SX-DX*/
                left: 480px;
            }

        </style>

        <div id="CalcTotale"></div>
        <div id="CalcResto"></div>
        <form Name="calc">
            <table id="calc" border=0>
                <tr>
                    <td colspan=3><input id="calcolatrice_display" class="calcolatrice_style" name="display" onkeyup="sterilizza_input(event.keyCode)" type="text"></td>
                    <!--return event.charCode >= 48 && event.charCode <= 57-->
                    <td style="display:none"><input name="M" type="number"></td>
                </tr>
                <tr>
                    <td colspan=2><input class="calcolatrice_style" type=button value="CA" OnClick="calcolatrice_display_press('CA')"></td>
                    <td><input class="calcolatrice_style" type=button value="<" OnClick="calcolatrice_display_press('del')"></td>
                </tr>
                <tr>
                    <td><input class="calcolatrice_style" type=button value="7" OnClick="calcolatrice_display_press('7');"></td>
                    <td><input class="calcolatrice_style" type=button value="8" OnClick="calcolatrice_display_press('8')"></td>
                    <td><input class="calcolatrice_style" type=button value="9" OnClick="calcolatrice_display_press('9')"></td>
                </tr>
                <tr>
                    <td><input class="calcolatrice_style" type=button value="4" OnClick="calcolatrice_display_press('4')"></td>
                    <td><input class="calcolatrice_style" type=button value="5" OnClick="calcolatrice_display_press('5')"></td>
                    <td><input class="calcolatrice_style" type=button value="6" OnClick="calcolatrice_display_press('6')"></td>
                </tr>
                <tr>
                    <td><input class="calcolatrice_style" type=button value="1" OnClick="calcolatrice_display_press('1')"></td>
                    <td><input class="calcolatrice_style" type=button value="2" OnClick="calcolatrice_display_press('2')"></td>
                    <td><input class="calcolatrice_style" type=button value="3" OnClick="calcolatrice_display_press('3')"></td>
                </tr>
                <tr>
                    <td><input class="calcolatrice_style" type=button value="0" OnClick="calcolatrice_display_press('0')"></td>
                    <td colspan=2><input class="calcolatrice_style" type=button value="." OnClick="calcolatrice_display_press('.')"></td>
                </tr>
                <tr>
                    <td colspan=3><input class="calcolatrice_style" type=button value="<< Indietro" OnClick="calcolatrice_hide()"></td>
                </tr>
                <tr>
                    <td colspan=3><input class="calcolatrice_style" type=button value="STAMPA" OnClick="procedi_con_stampa()"></td>
                </tr>
                <!--
<tr>
<td colspan=3><input class="calcolatrice_style" type=button value="STAMPA" OnClick="procedi_con_stampa()"></td>
</tr>
-->
            </table>
        </form>
    </div>
    <!-- FINE CALCOLATRICE CHE ENTRA DA SINISTRA -->

</body>

</html>
