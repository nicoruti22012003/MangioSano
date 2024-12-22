<?php
// Connessione al database

require_once "config.php";
// Ottieni i parametri dalla URL
$id_day = isset($_GET['id_day']) ? $_GET['id_day'] : '';
$pasto = isset($_GET['pasto']) ? $_GET['pasto'] : '';
if (is_null($pasto) || $pasto === '') {
    $sql = "
    SELECT 
        mf.id_food,
        f.food_name, 
        mf.quantity, 
        f.calories, 
        f.carbo, 
        f.protein, 
        f.fat,
        bu.bmr_calories, 
        bu.bmr_carbo, 
        bu.bmr_protein, 
        bu.bmr_fat
    FROM meal_food mf
    JOIN food f ON mf.id_food = f.id_food
    JOIN day d ON mf.id_day = d.id_day
    JOIN bmr_user bu ON d.id_user = bu.id_user
    WHERE mf.id_day = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_day);
$stmt->execute();
$result = $stmt->get_result();
} else {
   // Query per recuperare i dati dalla tabella meal_food, food, day, e bmr_user...
$sql = "
SELECT 
    mf.id_food,
    f.food_name, 
    mf.quantity, 
    f.calories, 
    f.carbo, 
    f.protein, 
    f.fat,
    bu.bmr_calories, 
    bu.bmr_carbo, 
    bu.bmr_protein, 
    bu.bmr_fat
FROM meal_food mf
JOIN food f ON mf.id_food = f.id_food
JOIN day d ON mf.id_day = d.id_day
JOIN bmr_user bu ON d.id_user = bu.id_user
WHERE mf.id_day = ? AND mf.type = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $id_day, $pasto);
$stmt->execute();
$result = $stmt->get_result();
}


// Array per memorizzare i risultati
$alimenti = [];

while ($row = $result->fetch_assoc()) {
    // Memorizza i dati necessari in un array
    $alimenti[] = [
        'id_food'=> $row['id_food'],
        'food_name' => $row['food_name'],
        'quantity' => $row['quantity'],
        'calories' => $row['calories'],
        'carbo' => $row['carbo'],
        'protein' => $row['protein'],
        'fat' => $row['fat'],
        'bmr_calories' => $row['bmr_calories'], // Valori BMR
        'bmr_carbo' => $row['bmr_carbo'],
        'bmr_protein' => $row['bmr_protein'],
        'bmr_fat' => $row['bmr_fat']
    ];
}

// Imposta l'intestazione per indicare che la risposta è JSON
header('Content-Type: application/json');

// Restituisci i dati in formato JSON
echo json_encode($alimenti);

// Chiudi la connessione
$stmt->close();
$conn->close();
?>
