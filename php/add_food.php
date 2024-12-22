<?php
require_once "config.php";


// Controlla se i parametri sono stati passati nell'URL
if (isset($_GET['id_day']) && isset($_GET['id_food']) && isset($_GET['quantita']) && isset($_GET['pasto'])) {
    // Recupera i parametri dalla query string
    $id_day = $_GET['id_day'];
    $id_food = $_GET['id_food'];
    $quantita = $_GET['quantita'];
    $pasto = $_GET['pasto'];

    // Prepara e esegui l'inserimento nella tabella meal_food
    $stmt = $conn->prepare("INSERT INTO meal_food (type, id_food, id_day, quantity) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $pasto, $id_food, $id_day, $quantita);

    // Esegui l'inserimento
    if ($stmt->execute()) {
        // Se l'inserimento è riuscito, reindirizza alla pagina precedente
        // Usando JavaScript per fare il reindirizzamento due volte alla pagina precedente
        echo '<script>
                window.history.go(-2);
              </script>';
    } else {
        echo "Errore nell'inserimento: " . $stmt->error;
    }

    // Chiudi la dichiarazione e la connessione
    $stmt->close();
} else {
    echo "Parametri mancanti nell\'URL.";
}

// Chiudi la connessione al database
$conn->close();

?>