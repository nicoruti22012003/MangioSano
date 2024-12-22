<?php


require_once "config.php";
$currentDate = date('Y-m-d');  // Data corrente nel formato 'YYYY-MM-DD'

$usernameu=$_POST["username"];
$passwordu=$_POST["password"];
// Prepara la query usando Prepared Statements
    $stmt = $conn->prepare("SELECT id_user,password FROM user WHERE username = ?");
   // $sql="SELECT password FROM user WHERE username = username";

    $stmt->bind_param("s", $usernameu);
    $stmt->execute();
    
    // Ottieni il risultato
    $result = $stmt->get_result();
   // $result = $conn->query($sql);
    

if ($result->num_rows > 0) {
        // L'utente esiste, quindi verifica la password
        $row = $result->fetch_assoc();
        $hashedPassword = $row['password'];
        $userId = $row['id_user'];


        // Verifica se la password inserita corrisponde all'hash nel database
        if (password_verify($passwordu, $hashedPassword)) {
          

             // Seconda query per verificare se l'utente ha una riga associata in user_obj
        $stmtCheck = $conn->prepare("SELECT * FROM user_obj WHERE id_user = ?");
        $stmtCheck->bind_param("i", $userId);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();
        if ($resultCheck->num_rows > 0) {
            ?>
            <!DOCTYPE html>
            <html lang="it">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                
                <style>
                    body {
                       
                       text-align: center;
                        height: 100vh;
                        background-color: white;
                    }
                    .loader {
                        position: absolute;
                        top: 30%;
                        left: 45%;
                        border: 16px solid #f3f3f3;
                        border-top: 16px solid #3498db;
                        border-radius: 50%;
                        width: 60px;
                        height: 60px;
                        animation: spin 2s linear infinite;
                    }
                    @keyframes spin {
                        0% { transform: rotate(0deg); }
                        100% { transform: rotate(360deg); }
                    }
                </style>
            </head>
            <body>
            <div style="height:300px; color:green;"><h1>LOGIN EFFETTUATO CON SUCCESSO!</h1></div>
                    
            <div class="loader"></div>
            <script>
                // Attendi 3 secondi e poi reindirizza
                setTimeout(function() {
                    window.location.href = 'intro.php?user=<?php echo $usernameu; ?>&date=<?php echo $currentDate; ?>'; // Cambia con il nome della tua pagina
                }, 2500);
            </script>
            
            </body>
            </html>
            <?php

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
    if (!dayExists($conn, $date_str, $userId)) {
        $stmt_insert->bind_param('si', $date_str, $userId); // Associa i parametri (date, id_user)
        $stmt_insert->execute(); // Esegui la query per ciascun giorno
    }
}

// Inserimento della data attuale
if (!dayExists($conn, $current_date_str, $userId)) {
    $stmt_insert->bind_param('si', $current_date_str, $userId);
    $stmt_insert->execute();
}

// Inserimento dei 365 giorni successivi
for ($i = 1; $i <= $days_after; $i++) {
    $date = clone $current_date;
    $date->modify("+{$i} days"); // Calcola la data "i" giorni dopo
    $date_str = $date->format('Y-m-d');
    
    // Controlla se la data esiste già
    if (!dayExists($conn, $date_str, $userId)) {
        $stmt_insert->bind_param('si', $date_str, $userId); // Associa i parametri (date, id_user)
        $stmt_insert->execute(); // Esegui la query per ciascun giorno
    }
}

// Chiudi la query di inserimento e la connessione
$stmt_insert->close();


        } else {
            // Se non ci sono dati in user_obj, reindirizza al form di inserimento dati
            ?>
              <!DOCTYPE html>
            <html lang="it">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <style>
                    body { text-align: center; height: 100vh; background-color: white; }
                    .loader { position: absolute; top: 30%; left: 45%; border: 16px solid #f3f3f3; border-top: 16px solid #3498db; border-radius: 50%; width: 60px; height: 60px; animation: spin 2s linear infinite; }
                    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
                </style>
            </head>
            <body>
                <div style="height:300px; color:green;"><h1>LOGIN EFFETTUATO CON SUCCESSO!</h1></div>
                <div class="loader"></div>
                <script>
                    setTimeout(function() {
                        window.location.href = '../src/start.html?user=<?php echo urlencode($usernameu); ?>';
                    }, 3000);
                </script>
            </body>
            </html>
            <?php
        }

        // Chiudi lo statement di controllo
        $stmtCheck->close();
            
        } else {
            ?>
            <!DOCTYPE html>
            <html lang="it">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <script>
                 window.location.href = '../src/login.html?error=password';
                </script>
            </body>
            </html>
            <?php
        }
    } else {
        ?>
        <!DOCTYPE html>
        <html lang="it">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script>
             window.location.href = '../src/login.html?error=username';
            </script>
        </body>
        </html>
        <?php    }
    
    // Chiudi lo statement
    $stmt->close();

$conn->close();
?>