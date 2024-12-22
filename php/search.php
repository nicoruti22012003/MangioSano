<?php

require_once "config.php";

if (isset($_GET['q'])) {
    $query = $_GET['q'];
    $sql = "SELECT * FROM food WHERE food_name LIKE ?";
    $stmt = $conn->prepare($sql);
    $likeQuery = "%" . $query . "%";
    $stmt->bind_param("s", $likeQuery);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $suggestions = [];
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = ['id' => $row['id_food'], 'name' => $row['food_name']];
    }
    
    echo json_encode($suggestions);
}

$conn->close();
?>
