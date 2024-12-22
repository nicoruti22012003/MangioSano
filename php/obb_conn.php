<?php
require_once "config.php";
$currentDate = date('Y-m-d');  // Data corrente nel formato 'YYYY-MM-DD'
// Recupera i dati inviati dal form
$obb = $_POST["obiettivo"];
$genere = $_POST["genere"];
$altezza = $_POST["altezza"];
$peso_att = $_POST["peso"];
$obb_peso = $_POST["pesoObiettivo"];
$attivita = $_POST["attivita"];
$username = $_POST["username"]; // Assumendo che venga passato dal form

// Recupera id_user dall'utente tramite il nome utente (o un altro campo univoco)
$stmt_check = $conn->prepare("SELECT id_user, birthday FROM user WHERE username = ?");
$stmt_check->bind_param("s", $username);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows > 0) {
    // Estrai l'id_user e la data di nascita
    $row = $result->fetch_assoc();
    $id_user = $row['id_user'];
    $birthday = $row['birthday'];

    // Inserisci i dati nella tabella user_obj collegandoli tramite id_user
    $stmt_user_obj = $conn->prepare("INSERT INTO user_obj (id_user, purpous, gender, height, weight_act, weight_fin, activity) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt_user_obj->bind_param("iisiiii", $id_user, $obb, $genere, $altezza, $peso_att, $obb_peso, $attivita);
    if (!$stmt_user_obj->execute()) {
        echo "Errore durante l'inserimento in user_obj: " . $stmt_user_obj->error;
    }
    $stmt_user_obj->close();

    // Calcolo dell'età se il campo birthday non è nullo
    if ($birthday) {
        $birthDate = new DateTime($birthday); // Data di nascita
        $today = new DateTime();              // Data corrente
        $eta = $today->diff($birthDate)->y;   // Differenza in anni
    } else {
        echo "Data di nascita non disponibile per l'utente.";
        $eta = 0; // Default se la data non è disponibile
    }

    // Calcolo del BMR
    if ($genere == "Uomo") {
        $BMR = (10 * $peso_att) + (6.25 * $altezza) - (5 * $eta) + 5;
    } else {
        $BMR = (10 * $peso_att) + (6.25 * $altezza) - (5 * $eta) - 161;
    }

    // Modifica del BMR in base all'attività
    switch ($attivita) {
        case 1:
            $BMR *= 1.2;
            break;
        case 2:
            $BMR *= 1.375;
            break;
        case 3:
            $BMR *= 1.55;
            break;
        case 4:
            $BMR *= 1.725;
            break;
        default:
            $BMR *= 1.9;
    }

    // Calcolo dei macronutrienti
    if ($obb == 1) { // Perdita peso
        $carbo = ($BMR * 0.45)/4;
        $protein = ($BMR * 0.30)/4;
        $fat = ($BMR * 0.25)/9;
    } elseif ($obb == 2) { // Aumento
        $carbo = ($BMR * 0.50)/4;
        $protein = ($BMR * 0.25)/4;
        $fat = ($BMR * 0.25)/9;
    } else { // mantenimento massa
        $carbo = ($BMR * 0.50)/4;
        $protein = ($BMR * 0.20)/4;
        $fat = ($BMR * 0.30)/9;
    }
    $calories = $BMR;

    // Inserimento dei dati nella tabella bmr_user
    $stmt_bmr_user = $conn->prepare("INSERT INTO bmr_user (bmr_calories, bmr_carbo, bmr_protein, bmr_fat, id_user) VALUES (?, ?, ?, ?, ?)");
    $stmt_bmr_user->bind_param("ddddi", $calories, $carbo, $protein, $fat, $id_user);

    if ($stmt_bmr_user->execute()) {

// Data attuale
$current_date = new DateTime(); // Data attuale
$current_date_str = $current_date->format('Y-m-d'); // Stringa per la data attuale in formato 'YYYY-MM-DD'

// Numero di giorni da generare
$days_before = 60; // Giorni prima
$days_after = 365; // Giorni dopo

// Funzione per controllare se una data esiste già per l'utente
function dayExists($conn, $date, $userId) {
    $query = "SELECT 1 FROM day WHERE date = ? AND id_user = ?";
    $stmt_day = $conn->prepare($query);
    $stmt_day->bind_param('si', $date, $userId);
    $stmt_day->execute();
    $stmt_day->store_result();
    $exists = $stmt_day->num_rows > 0; // True se la data esiste già
    $stmt_day->close();
    return $exists;
}

// Preparazione della query di inserimento
$stmt_insert = $conn->prepare("INSERT INTO day (date, water, id_user) VALUES (?, 0, ?)");

// Controlla se la preparazione è riuscita
if (!$stmt_insert) {
    die("Errore nella preparazione della query di inserimento: " . $conn->error);
}

// Inserimento dei 60 giorni precedenti
for ($i = $days_before; $i > 0; $i--) {
    $date = clone $current_date;
    $date->modify("-{$i} days"); // Calcola la data "i" giorni prima
    $date_str = $date->format('Y-m-d');
    
    // Controlla se la data esiste già
    if (!dayExists($conn, $date_str, $id_user)) {
        $stmt_insert->bind_param('si', $date_str, $id_user); // Associa i parametri (date, id_user)
        $stmt_insert->execute(); // Esegui la query per ciascun giorno
    }
}

// Inserimento della data attuale
if (!dayExists($conn, $current_date_str, $id_user)) {
    $stmt_insert->bind_param('si', $current_date_str, $id_user);
    $stmt_insert->execute();
}

// Inserimento dei 365 giorni successivi
for ($i = 1; $i <= $days_after; $i++) {
    $date = clone $current_date;
    $date->modify("+{$i} days"); // Calcola la data "i" giorni dopo
    $date_str = $date->format('Y-m-d');
    
    // Controlla se la data esiste già
    if (!dayExists($conn, $date_str, $id_user)) {
        $stmt_insert->bind_param('si', $date_str, $id_user); // Associa i parametri (date, id_user)
        $stmt_insert->execute(); // Esegui la query per ciascun giorno
    }
}

// Chiudi la query di inserimento e la connessione
$stmt_insert->close();


        header("Location: intro.php?user=$username&date=$currentDate");
    } else {
        echo "Errore durante l'inserimento in bmr_user: " . $stmt_bmr_user->error;
    }

    $stmt_bmr_user->close();
} else {
    echo "Errore: utente non trovato.";
}

$stmt_check->close();
$conn->close();
?>
