# festPOS
festPOS è il software per la cassa, su misura per la tua sagra.

![screenshot1](https://github.com/TheFax/festPOS/blob/master/Screenshot%201.jpg)
![screenshot2](https://github.com/TheFax/festPOS/blob/master/Screenshot%202.jpg)
![foto1](https://github.com/TheFax/festPOS/blob/master/Foto%201.jpg)
![foto2](https://github.com/TheFax/festPOS/blob/master/Foto%202.jpg)

# Il sito web
[sito web di festPOS](https://goodstone.altervista.org/festpos)

# Decrizione
festPOS e' un software registratore di cassa, progettato per essere utilizzato su computer o tablet tramite un semplice browser come Firefox o Chrome. Tra i punti di forza di questo programma, quello di poter stampare gli scontrini provenienti da piu' casse verso un'unica stampante, mantenendo il "numero d'ordine" coerente. In poche parole, i clienti saranno serviti nel giusto ordine, anche se hanno richiesto lo scontrino da casse diverse.

Immaginate di poter ricevere ordini da PC e tablet contemporaneamente e di stampare scontrini separati per bibite, cibo ed altre categorie da voi impostabili. Con festPOS e' possibile.

festPOS puo' essere utilizzato da un PC qualsiasi (con un browser recente), da MAC, da tablet. Per la massima usabilità è suggerito utilizzare un display touchscreen.

# Utilizzo
A seconda se stai lavorando su Linux o su Windows, segui una delle due guide che trovi all'interno della cartella `server_docker_lamp` (per Linux), o `server_microapache` (per Windows).

A grandi linee, sarai guidato a clonare il progetto in una specifica directory e ad avviare uno dei due server in dotazione per rendere disponibile via rete la cartella www.

Ricordati puoi utilizzare un qualsiasi server LAMP, quindi non sei affatto vincolato ai due che io ho "imbastito". L'unico limite, per ora, è che la versione di PHP deve essere la 5, infatti la [libreria ESC-POS di Mike42](https://github.com/mike42/escpos-php) allegata non è compatibile con PHP 7 o PHP 8.

```
cd Cartella_di_destinazione
git clone https://github.com/TheFax/festPos .
```

# avahi
Se non sai cosa sia `avahi`, sappi che questo componente non è correlato in alcun modo con il funzionamento del software festPOS e quindi puoi farne tranquillamente a meno. Questo programma è utile se ci sono più host connessi allo stesso server, è utile utilizzare il servizio `avahi` che di norma è già presente in gran parte delle distribuzioni linux per facilitare il collegamento di un host al server, in quanto il server acquisirà un nome mnemonicamente facile da ricordare al posto del nioso indirizzo IP. 

In questo caso configurare il file `/etc/avahi/avahi-daemon.conf` con il corretto hostname e domain name, poi riavviare il demone con il seguente comando:
```
sudo systemctl restart avahi-daemon.service
```

# Cercasi sviluppatori
festPOS è un progetto che merita un po' di più. Se vuoi contribuire visita il sito web indicato qui sopra e contattami.