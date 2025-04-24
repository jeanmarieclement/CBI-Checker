<?php
// Imposta gli header per la risposta JSON
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

// Percorso allo schema XSD
$xsdPath = 'CBIPaymentRequest.00.04.01.xsd';

// Funzione per gestire gli errori libxml e convertirli in un formato più leggibile
function formatLibXmlError($error) {
    $return = [];
    
    switch ($error->level) {
        case LIBXML_ERR_WARNING:
            $return['type'] = 'Warning';
            break;
        case LIBXML_ERR_ERROR:
            $return['type'] = 'Error';
            break;
        case LIBXML_ERR_FATAL:
            $return['type'] = 'Fatal Error';
            break;
    }
    
    $return['message'] = trim($error->message);
    $return['line'] = intval($error->line);
    $return['column'] = intval($error->column);
    
    // Estrai il nodo XML dall'errore quando possibile
    if (preg_match('/Element \'([^\']+)\'/', $error->message, $matches)) {
        $return['node'] = $matches[1];
    }
    
    return $return;
}

// Controlla se è stata inviata una richiesta POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['xml'])) {
    // Recupera il contenuto XML dalla richiesta
    $xmlContent = $_POST['xml'];
    
    // Verifica se il file XSD esiste
    if (!file_exists($xsdPath)) {
        echo json_encode([
            'valid' => false,
            'message' => 'Schema XSD non trovato',
            'errors' => [
                [
                    'line' => 0,
                    'message' => 'File di schema XSD (CBIPaymentRequest.00.04.01.xsd) non trovato sul server'
                ]
            ]
        ]);
        exit;
    }
    
    // Attiva il reporting degli errori libxml
    libxml_use_internal_errors(true);
    
    // Carica il documento XML
    $dom = new DOMDocument();
    $loadResult = $dom->loadXML($xmlContent, LIBXML_NOWARNING);
    
    if (!$loadResult) {
        // Raccolta degli errori di parsing
        $errors = [];
        foreach (libxml_get_errors() as $error) {
            $formattedError = formatLibXmlError($error);
            $errors[] = [
                'line' => $formattedError['line'],
                'message' => $formattedError['type'] . ': ' . $formattedError['message']
            ];
        }
        
        // Pulisci gli errori
        libxml_clear_errors();
        
        echo json_encode([
            'valid' => false,
            'message' => 'XML non valido: errori di sintassi',
            'errors' => $errors
        ]);
        exit;
    }
    
    // Verifica il namespace
    $rootElement = $dom->documentElement;
    $expectedNamespace = 'urn:CBI:xsd:CBIPaymentRequest.00.04.01';
    
    if ($rootElement->namespaceURI !== $expectedNamespace) {
        echo json_encode([
            'valid' => false,
            'message' => 'Namespace non valido',
            'errors' => [
                [
                    'line' => 1, // Assumiamo che il namespace sia dichiarato nella prima linea
                    'message' => 'Namespace errato. Atteso: ' . $expectedNamespace . ', Trovato: ' . ($rootElement->namespaceURI ?: 'nessuno')
                ]
            ]
        ]);
        exit;
    }
    
    // Verifica l'elemento radice
    if ($rootElement->localName !== 'CBIPaymentRequest') {
        echo json_encode([
            'valid' => false,
            'message' => 'Elemento radice non valido',
            'errors' => [
                [
                    'line' => 1, // Assumiamo che l'elemento radice sia nella prima linea
                    'message' => 'Elemento radice errato. Atteso: CBIPaymentRequest, Trovato: ' . $rootElement->localName
                ]
            ]
        ]);
        exit;
    }
    
    // Validazione contro lo schema XSD
    $isValid = $dom->schemaValidate($xsdPath);
    
    if ($isValid) {
        // Esegui controlli aggiuntivi specifici per CBI
        // Esempio: verifica la presenza di elementi obbligatori
        $requiredElements = ['GrpHdr', 'PmtInf'];
        $missingElements = [];
        
        foreach ($requiredElements as $element) {
            $elements = $dom->getElementsByTagNameNS($expectedNamespace, $element);
            if ($elements->length === 0) {
                $missingElements[] = $element;
            }
        }
        
        if (!empty($missingElements)) {
            $errorMessages = [];
            foreach ($missingElements as $element) {
                $errorMessages[] = [
                    'line' => 1, // Non possiamo sapere la linea esatta senza analisi dettagliata
                    'message' => 'Elemento obbligatorio mancante: ' . $element
                ];
            }
            
            echo json_encode([
                'valid' => false,
                'message' => 'Elementi obbligatori mancanti',
                'errors' => $errorMessages
            ]);
            exit;
        }
        
        // Verifica altri aspetti specifici del CBI Payment Request
        // Per esempio, controlli sui codici IBAN, sugli importi, ecc.
        
        // Se arriviamo qui, il documento è valido
        echo json_encode([
            'valid' => true,
            'message' => 'Il documento XML è valido secondo lo schema CBI Payment Request'
        ]);
        exit;
    } else {
        // Raccolta e formattazione degli errori di validazione
        $errors = [];
        foreach (libxml_get_errors() as $error) {
            $formattedError = formatLibXmlError($error);
            $errors[] = [
                'line' => $formattedError['line'],
                'message' => $formattedError['type'] . ': ' . $formattedError['message'],
                'node' => isset($formattedError['node']) ? $formattedError['node'] : null
            ];
        }
        
        // Pulisci gli errori
        libxml_clear_errors();
        
        echo json_encode([
            'valid' => false,
            'message' => 'XML non valido secondo lo schema',
            'errors' => $errors
        ]);
        exit;
    }
} else {
    // Nessun XML fornito
    echo json_encode([
        'valid' => false,
        'message' => 'Nessun contenuto XML fornito',
        'errors' => [
            [
                'line' => 0,
                'message' => 'È necessario fornire il contenuto XML per la validazione'
            ]
        ]
    ]);
}
?>