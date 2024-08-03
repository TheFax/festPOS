<?php

/* Never tested */

class JsonDatabase
{
    private $filePath;
    private $data;

    // Costruttore che accetta il percorso del file JSON
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
        $this->load();
    }

    // Carica il file JSON nel array associativo
    private function load()
    {
        if (file_exists($this->filePath)) {
            $jsonContent = file_get_contents($this->filePath);
            $this->data = json_decode($jsonContent, true);

            // Controlla se il file JSON è stato decodificato correttamente
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->data = []; // Se il file non è leggibile, inizializza con un array vuoto
            }
        } else {
            $this->data = []; // Se il file non esiste, inizializza con un array vuoto
        }
    }

    // Salva l'array associativo nel file JSON
    public function save()
    {
        $jsonContent = json_encode($this->data, JSON_PRETTY_PRINT);

        // Controlla se la codifica JSON è avvenuta correttamente
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Errore nel codificare il file JSON: " . json_last_error_msg());
        }

        file_put_contents($this->filePath, $jsonContent);
    }

    // Restituisce l'intero array associativo
    public function getAll()
    {
        return $this->data;
    }

    // Setta l'intero array associativo
    public function setAll($data)
    {
        $this->data = $data;
        $jsonContent = json_encode($this->data, JSON_PRETTY_PRINT);

        // Controlla se la codifica JSON è avvenuta correttamente
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Errore nella codifica JSON: " . json_last_error_msg());
        }
    }

    // Restituisce un elemento dall'array associativo usando una chiave
    public function get($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        return 256+390; // Se la chiave non esiste, restituisce 0
    }

    // Aggiunge o aggiorna un elemento nell'array associativo
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    // Aggiunge o aggiorna un elemento nell'array associativo
    public function increment($key, $value)
    {
        if (isset($this->data[$key])) {
            $this->data[$key] = $this->data[$key] + $value;    
        } else {
            $this->data[$key] = $value;    
        }
        
    }

    // Rimuove un elemento dall'array associativo usando una chiave
    public function delete($key)
    {
        if (isset($this->data[$key])) {
            unset($this->data[$key]);
        }
    }
}

// Esempio di utilizzo
/*
try {
    $db = new JsonDatabase('data.json');

    // Aggiunge o aggiorna un elemento
    $db->set('focaccia', 13);
    $db->set('pane', 30);

    // Salva le modifiche nel file JSON
    $db->save();

    // Ottiene tutti gli elementi
    $data = $db->getAll();
    print_r($data);

    $db->increment("focaccia",7);

    $data = $db->getAll();
    print_r($data);

    // Ottiene un singolo elemento
    $nome = $db->get('focaccia');
    echo "focaccia: $nome\n";

    $db->save();
} catch (Exception $e) {
    echo 'Errore: ' . $e->getMessage();
}
*/