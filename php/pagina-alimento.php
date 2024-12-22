<?php
require_once "config.php";

$pasto = isset($_GET['pasto']) ? $_GET['pasto'] : '';
$id_day=isset($_GET['id_day']) ? $_GET['id_day'] : '';
if (isset($_GET['id_food'])) {
    $id_food = $_GET['id_food'];
    $sql = "SELECT * FROM food WHERE id_food = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_food);
    $stmt->execute();
    $result = $stmt->get_result();
    $food = $result->fetch_assoc();
    if (!$food) {
        echo "<p>Alimento non trovato.</p>";
        exit;
    }
} else {
    echo "<p>ID non specificato.</p>";
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" type="image/vnd.icon" href="../src/img/logo_webapp_MangioSano.png">

    <title>Dettagli Alimento - <?php echo htmlspecialchars($food['food_name']); ?></title>
    <style>
        .body_alim {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            flex-direction: column;
        }
   /* Stile per il contenitore del bottone */
.back_btn {
    position: absolute;
    left: 5%;
    top: 5%;
    font-size: 16px;
    transition: transform 0.3s ease;
}

/* Rimuove il comportamento di stile di default del link */
.back_btn a {
    text-decoration: none;   /* Rimuove la sottolineatura del link */
}

/* Stile per il bottone */
.back_btn button {
    padding: 12px 24px;         /* Dimensioni coerenti e adattabili */
    font-size: 16px;
    color: #fff;
    background-color: gray;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    text-align: center;
    display: flex;               /* Per allineare l'icona correttamente */
    align-items: center;        /* Allinea l'icona verticalmente */
    justify-content: center;    /* Centra l'icona orizzontalmente */
    transition: background-color 0.3s ease, transform 0.3s ease;
}

/* Effetto hover sul bottone */
.back_btn button:hover {
    background-color: #555;   /* Colore leggermente più scuro al passaggio */
    transform: scale(1.05);    /* Effetto leggero di ingrandimento */
}

/* Stile per rendere il pulsante più accessibile su dispositivi mobili */
@media (max-width: 768px) {
    .back_btn button {
        padding: 10px 20px;      /* Riduzione delle dimensioni del padding */
        font-size: 14px;         /* Font leggermente più piccolo su schermi piccoli */
    }
}

        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 600px;
            text-align: center;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .input-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        #quantity {
            width: 100px;
            padding: 10px;
            font-size: 16px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        #add {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .nutrition-info {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }

        .info-box {
            background-color: #f4f4f4;
            padding: 15px;
            border-radius: 8px;
            width: 120px;
            text-align: center;
        }

        .info-box h2 {
            margin: 0;
            font-size: 20px;
        }

        .info-box p {
            margin: 5px 0 0;
            font-size: 16px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="body_alim">
<div class="back_btn">
      <a href="javascript:void(0);" onclick="history.back();"><button  class="back_btn_hover" >
          <i class="fas fa-arrow-left icon"></i>
          
         </button>
      </a>
     </div>
<div class="container">
    <h1><?php echo htmlspecialchars($food['food_name']); ?></h1>

    <div class="input-container">
        <input type="number" id="quantity" placeholder="Grammi" min="1" oninput="updateNutritionValues()">
        <button id="add">Aggiungi</button>
    </div>
</div>

<div class="container nutrition-info">
    <div class="info-box">
        <h2 id="calories">0</h2>
        <p>Calorie</p>
    </div>
    <div class="info-box">
        <h2 id="carbohydrates">0</h2>
        <p>Carboidrati</p>
    </div>
    <div class="info-box">
        <h2 id="proteins">0</h2>
        <p>Proteine</p>
    </div>
    <div class="info-box">
        <h2 id="fats">0</h2>
        <p>Grassi</p>
    </div>
</div>

<script>
    // Valori nutrizionali per 100 grammi presi dal database
    const foodNutrition = {
        calories: <?php echo json_encode($food['calories']); ?>,
        carbohydrates: <?php echo json_encode($food['carbo']); ?>,
        proteins: <?php echo json_encode($food['protein']); ?>,
        fats: <?php echo json_encode($food['fat']); ?>
    };

    function updateNutritionValues() {
        const quantity = parseFloat(document.getElementById('quantity').value);

        if (!isNaN(quantity) && quantity > 0) {
            document.getElementById('calories').innerText = ((foodNutrition.calories * quantity) / 100).toFixed(2);
            document.getElementById('carbohydrates').innerText = ((foodNutrition.carbohydrates * quantity) / 100).toFixed(2);
            document.getElementById('proteins').innerText = ((foodNutrition.proteins * quantity) / 100).toFixed(2);
            document.getElementById('fats').innerText = ((foodNutrition.fats * quantity) / 100).toFixed(2);
        } else {
            document.getElementById('calories').innerText = "0";
            document.getElementById('carbohydrates').innerText = "0";
            document.getElementById('proteins').innerText = "0";
            document.getElementById('fats').innerText = "0";
        }
    }
    // Funzione per nascondere il bottone e mostrare la quantità
    window.onload = function() {
        const urlParams = new URLSearchParams(window.location.search);
        const quantity = urlParams.get('quantity');
        
        if (quantity) {
            document.getElementById('quantity').value = quantity; // Imposta il valore nella input
             document.getElementById('quantity').disabled = true; 
            document.getElementById('add').style.display = 'none'; // Nasconde il bottone "Aggiungi"
            updateNutritionValues(); // Calcola subito i valori nutrizionali
        }
    };

    document.getElementById('add').addEventListener('click', function() {
        const quantity = document.getElementById('quantity').value;

        if (quantity > 0) {
            // Reindirizza alla pagina di ricerca, passando l'ID e la quantità
            window.location.href = 'add_food.php?id_day=<?php echo $id_day ?>&id_food=<?php echo $food['id_food']; ?>&quantita=' + quantity+'&pasto=<?php echo $pasto ?>';
        } else {
            alert("Inserisci una quantità valida in grammi.");
        }
    });
</script>
</div>
</body>
</html>
