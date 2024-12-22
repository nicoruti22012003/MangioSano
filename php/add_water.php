<?php

require_once "config.php";
// Recupera i valori inviati dal form
$litri = isset($_GET['litri']) ? $_GET['litri'] : 0;
$user = isset($_GET['user']) ? $_GET['user'] : '';
$date = isset($_GET['date']) ? $_GET['date'] : '';

// Verifica che i dati siano validi
if (!empty($user) && !empty($date)) {
    // 1. Recupera l'id_user dalla tabella users usando il nome utente
    $stmt = $conn->prepare("SELECT id_user FROM user WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    // Se l'utente esiste
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_user = $row['id_user'];  // Ottieni id_user dall'utente trovato

        // Recupera il valore attuale di acqua per l'utente e la data
        $stmt_day = $conn->prepare("SELECT water FROM day WHERE id_user = ? AND date = ?");
        $stmt_day->bind_param("is", $id_user, $date);
        $stmt_day->execute();
        $result_day = $stmt_day->get_result();

        // Se esiste un record per quella data, aggiorna il valore
        if ($result_day->num_rows > 0) {
            // Aggiorna il campo water nella tabella day con il nuovo valore
            $update_stmt = $conn->prepare("UPDATE day SET water = ? WHERE id_user = ? AND date = ?");
            $update_stmt->bind_param("dss", $litri, $id_user, $date);
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
