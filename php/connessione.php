<?php

require_once "config.php";

$nome = $_POST["fname"];
$cognome = $_POST["lname"];
$email = $_POST["email"];
$telefono = $_POST["phone"];
$compleanno = $_POST["yyyy"] . "-" . $_POST["mm"] . "-" . $_POST["dd"];
$username = $_POST["uname"];
$passwordu = $_POST["pword"];
$password_hashed = password_hash($passwordu, PASSWORD_DEFAULT);

// Controlla se l'username è già in uso
$stmt_check = $conn->prepare("SELECT * FROM user WHERE username = ?");
$stmt_check->bind_param("s", $username);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    // L'username esiste già
    header("Location: ../src/form.html?error=username_taken&fname=$nome&lname=$cognome&email=$email&phone=$telefono&yyyy=".$_POST["yyyy"]."&mm=".$_POST["mm"]."&dd=".$_POST["dd"]);
    if($username!='' || $username!=null)
    { ?>
    <html>
        <body>
            <script>    
                alert("Errore! Utente già esistente");
            </script>
        </body>
    </html>
      <?php
    }
    exit;
}

// Inserimento nel database
$stmt = $conn->prepare("INSERT INTO user (name, surname, email, phone, birthday, username, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssisss", $nome, $cognome, $email, $telefono, $compleanno, $username, $password_hashed);

if ($stmt->execute()) {
    ?>
    <!DOCTYPE html>
    <html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            body {
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                background-color: #f0f0f0;
            }
            .loader {
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
    <div class="loader"></div>
    <script>
        setTimeout(function() {
            window.location.href = '../src/index.html';
        }, 3000);
    </script>
    </body>
    </html>
    <?php
} else {
    // Mostra l'errore
    echo "Errore: " . $stmt->error;
    exit;
}

$stmt_check->close();
$stmt->close();
$conn->close();
?>
