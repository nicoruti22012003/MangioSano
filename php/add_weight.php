<?php

require_once "config.php";
// Recupera i valori inviati dal form
$weight = isset($_GET['weight']) ? $_GET['weight'] : 0;
$user = isset($_GET['user']) ? $_GET['user'] : '';
$date = isset($_GET['date']) ? $_GET['date'] : '';

// Verifica che i dati siano validi
if (!empty($user)) {
    // 1. Recupera l'id_user dalla tabella users usando il nome utente
    $stmt = $conn->prepare("SELECT id_user FROM user WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    // Se l'utente esiste
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_user = $row['id_user'];  // Ottieni id_user dall'utente trovato

        // Recupera il valore attuale di peso per l'utente
        $stmt_peso = $conn->prepare("SELECT weight_act FROM user_obj WHERE id_user = ?");
        $stmt_peso->bind_param("i", $id_user);
        $stmt_peso->execute();
        $result_peso = $stmt_peso->get_result();

        if ($result_peso->num_rows > 0) {
            // Aggiorna il campo weight_act nella tabella user_obj con il nuovo valore
            $update_stmt = $conn->prepare("UPDATE user_obj SET weight_act = ? WHERE id_user = ?");
            $update_stmt->bind_param("ds", $weight, $id_user);
            $update_stmt->execute();

            header("Location: intro.php?user=" . urlencode($user) . "&date=" . urlencode($date));

            exit();
        } else {
            // Se non esiste ancora una riga per quella data, inserisci un nuovo record
            $insert_stmt = $conn->prepare("INSERT INTO day (id_user, date, water) VALUES (?, ?, ?)");
            $insert_stmt->bind_param("isd", $id_user, $date, $litri);
            $insert_stmt->execute();

            echo "Nuovo record inserito con " . $litri . " litri.";
        }
    } else {
        echo "Errore: Utente non trovato.";
    }
} else {
    echo "Errore: Dati mancanti o invalidi.";
}
?>
