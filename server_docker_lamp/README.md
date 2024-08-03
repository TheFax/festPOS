## Server LAMP  su Docker
Per utilizzare festPOS tramite un server che gira in un container Docker:

1) **Installo ambiente docker**:
```
sudo apt-get install docker.io
reboot
```

2) **Scarico immagine** docker OPPURE prendo l'immagine che ho scaricato e salvato
```
sudo docker pull jakejarvis/lamp-php5
```
  OPPURE
```
sudo docker load -i NOME_DEL_FILE_LOCALE
```

3) **Creo le cartelle del progetto**
```
mkdir ~/Docker

```

4) **Clono questo progetto da GitHub**
```
cd ~/Docker
git clone https://github.com/TheFax/festPOS .
```

5) **Avvio immagine docker**:
```
sudo docker run -d --restart unless-stopped -v ~/Docker/www:/app --network host jakejarvis/lamp-php5 
```

6) Con un browser qualsiasi **mi collego al'IP locale**
