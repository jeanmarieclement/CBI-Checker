# CBI Checker

## Descrizione
CBI Checker è un'applicazione web per la validazione e la formattazione di file XML conformi allo schema CBI Payment Request (CBIPaymentRequest.00.04.01.xsd). Questo strumento permette agli utenti di verificare la validità dei propri file XML secondo lo standard CBI (Corporate Banking Interbancario) utilizzato nel sistema bancario italiano.

## Funzionalità principali

- **Validazione XML**: Verifica la conformità dei file XML rispetto allo schema CBI Payment Request
- **Formattazione XML**: Formatta i file XML mantenendo la validità del documento
- **Drag and Drop**: Caricamento dei file tramite trascinamento diretto nell'interfaccia
- **Evidenziazione errori**: Visualizzazione chiara degli errori con riferimento alla linea specifica
- **Navigazione errori**: Possibilità di cliccare sugli errori per navigare alla linea corrispondente

## Requisiti tecnici

- Server web con supporto PHP
- Docker (per l'esecuzione tramite container)

## Installazione

### Utilizzo con Docker

1. Clona il repository o copia i file nella directory desiderata
2. Assicurati che Docker sia installato e in esecuzione
3. Dalla directory del progetto, esegui:

```bash
docker-compose up -d
```

4. Accedi all'applicazione tramite browser all'indirizzo: http://localhost:8080

### Installazione manuale

1. Copia i file in una directory servita dal tuo web server
2. Assicurati che PHP sia correttamente configurato
3. Accedi all'applicazione tramite browser

## Struttura del progetto

- `index.html`: Interfaccia utente dell'applicazione
- `validate.php`: Script PHP per la validazione XML
- `CBIPaymentRequest.00.04.01.xsd`: Schema XSD per la validazione
- `.htaccess`: Configurazione Apache per sicurezza e performance
- `cbi-xml-example.xml`: File XML di esempio
- `docker-compose.yml`: Configurazione Docker per l'esecuzione in container

## Come utilizzare l'applicazione

1. **Caricamento file**: Trascina un file XML nella zona di drop o utilizza il pulsante "Seleziona file"
2. **Formattazione**: Clicca su "Formatta XML" per migliorare la leggibilità del documento
3. **Validazione**: Clicca su "Valida XML" per verificare la conformità con lo schema CBI
4. **Analisi errori**: In caso di errori, esamina i dettagli forniti e correggi il file XML
5. **Esempio**: Utilizza il pulsante "Carica esempio" per vedere un file XML CBI valido

## Disclaimer

Questo strumento è fornito a scopo dimostrativo. La validazione XML è basata sullo schema CBI Payment Request, ma non garantisce la completa conformità con tutti i requisiti bancari. Verificare sempre i file con gli strumenti ufficiali prima dell'invio.

## Note tecniche

- L'applicazione utilizza CodeMirror per l'evidenziazione della sintassi XML
- La validazione avviene lato server tramite le funzioni PHP di validazione XML
- La formattazione XML è implementata con un algoritmo personalizzato che preserva la validità del documento

## Licenza

Questo progetto è distribuito con licenza MIT. Sei libero di utilizzarlo, modificarlo e distribuirlo secondo i termini della licenza.
