<?php

require_once "config.php";


$id_day = isset($_GET['id_day']) ? $_GET['id_day'] : '';
$pasto = isset($_GET['pasto']) ? $_GET['pasto'] : '';
$id_food = isset($_GET['id_food']) ? $_GET['id_food'] : '';

if($id_food ){
    if($id_day){
        if($pasto){
           

            $stmt = $conn->prepare("DELETE FROM meal_food WHERE type = ? AND id_food=? AND id_day = ?");
            $stmt->bind_param("sii", $pasto,$id_food, $id_day);
            $stmt->execute();
            $result = $stmt->get_result();

            header("Location: javascript:history.back()");
exit();
        }else{
            echo "pasto non presente";
        }


    }else{
        echo "data non presente";
    }


}else{
    echo "nome errato";
}
$stmt->close();
$conn->close();
?>
