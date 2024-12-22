
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../src/style3.css">
    <link rel="icon" type="image/vnd.icon" href="../src/img/logo_webapp_MangioSano.png">
    <title>Macronutrienti e Energia Accumulata</title>
</head>
<body>
    <?php
  
require_once "config.php";

 $pasto_colazione="Colazione";
 $pasto_pranzo="Pranzo";
 $pasto_cena="Cena";
 $pasto_snack="Snack";
// Ottiene il nome utente dall'URL
$user = isset($_GET['user']) ? $_GET['user'] : '';

// Verifica che il parametro user sia presente
if ($user) {
    // Prima query per ottenere l'id_user dal nome utente
    $stmt = $conn->prepare("SELECT id_user FROM user WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica se è stato trovato un utente con il nome specificato
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_user = $row['id_user'];

        
        // Seconda query per ottenere il BMR dell'utente tramite l'id_user
        $stmt_bmr = $conn->prepare("SELECT bmr_calories FROM bmr_user WHERE id_user = ?");
        $stmt_bmr->bind_param("i", $id_user);
        $stmt_bmr->execute();
        $result_bmr = $stmt_bmr->get_result();
        
        if ($result_bmr->num_rows > 0) {
            $row_bmr = $result_bmr->fetch_assoc();
            $bmr = intval($row_bmr['bmr_calories']); // Usa intval per assicurarti che sia un numero
        } else {
            $bmr = 'non disponibile';
            echo "<script>console.log('BMR non trovato per l\'utente');</script>";
        }

        $stmt_bmr->close();
        //Terza query
      
        $stmt_obj = $conn->prepare("SELECT weight_act ,purpous FROM user_obj WHERE id_user = ?");
        $stmt_obj->bind_param("i", $id_user);
        $stmt_obj->execute();
        $result_obj = $stmt_obj->get_result();
        if ($result_obj->num_rows > 0) {
            $row_obj = $result_obj->fetch_assoc();
            $weight_act=$row_obj['weight_act'];
            $purpous=$row_obj['purpous'];
          
        } else {
           
            echo "<script>console.log('OBJ non trovato per l\'utente');</script>";
        }

        $stmt_obj->close();
        
        $date = isset($_GET['date']) ? $_GET['date'] : '';
        if($date){
        // Preparazione della query
        $stmt_day = $conn->prepare("SELECT id_day,water FROM day WHERE id_user = ? AND date = ?");
        // Binding dei parametri: "i" per l'intero (id_user), "s" per la stringa (date)
        $stmt_day->bind_param("is", $id_user, $date);
        // Esecuzione della query
        $stmt_day->execute();
        // Recupero del risultato
        $result_day = $stmt_day->get_result();
        $water=null;
        if ($result_day ->num_rows > 0) {
            $row_day = $result_day->fetch_assoc();
            $water = $row_day['water'];
            $id_day=$row_day['id_day'];
        }else{
            $water=0;
        }
        $stmt_day->close();
        
    }else{
        $water="errore";
    }


    } else {
        // Utente non trovato
        $bmr = 'error';
        echo "<script>console.log('BMR non trovato');</script>";
    }

    $stmt->close();
} else {
    // Messaggio se manca il parametro user nell'URL
    $bmr = 'Parametro user mancante';
    echo "<script>console.log('Parametro user mancante');</script>";
}


$conn->close();
    ?>

    <!-- Intro principale -->
    <div class="body_intro">
    <div class="container_menu">
        <div class="menu-button" onclick="toggleMenu()">
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
        </div>
        <nav id="sideMenu" class="side-menu">
            <ul>
                <li><a href="../src/index.html">Home</a></li>
                <li><a href="../src/diete_personalizzate.html?purpous=<?php echo htmlspecialchars($purpous); ?>">Diete Personalizzate</a></li>
                <li><a href="../src/chi_siamo.html">Chi siamo</a></li>
                <li><a href="../src/login.html">Log Out</a></li>
            </ul>
        </nav>
        <div class="content" onclick="closeMenu()">
        </div>
    </div>
    <script>
        function toggleMenu() {
            var menu = document.getElementById('sideMenu');
            var menuButton = document.querySelector('.menu-button');
            menu.classList.toggle('active'); // Aggiungi o rimuovi la classe 'active'

            // Gestisci l'opacità del pulsante del menu
            if (menu.classList.contains('active')) {
                menuButton.style.opacity = '0'; // Nascondi le barre del menu
            } else {
                menuButton.style.opacity = '1'; // Ripristina l'opacità
            }
        }

        function closeMenu() {
            var menu = document.getElementById('sideMenu');
            var menuButton = document.querySelector('.menu-button');

            if (menu.classList.contains('active')) {
                menu.classList.remove('active'); // Chiudi il menu
                menuButton.style.opacity = '1'; // Ripristina le barre del menu
            }
        }

        // Chiudi il menu se clicchi al di fuori
        window.onclick = function(event) {
            var menu = document.getElementById('sideMenu');
            var menuButton = document.querySelector('.menu-button');
            if (!menu.contains(event.target) && !menuButton.contains(event.target) && menu.classList.contains('active')) {
                closeMenu();
            }
        }
    </script>
        <!-- Profilo utente -->
        <div class="container-user">
            <div class="user-profile">
                <!-- Avatar Utente -->
                <div class="user-avatar">
                    <img src="../src/img/user-icon.png" alt="User Icon">
                </div>
                <!-- Nome Utente -->
                <div class="username"> Benvenuto <span id="username-value"><?php echo htmlspecialchars($user); ?></span><br>
                    BMR: <span id="bmr-value"><?php echo htmlspecialchars($bmr); ?></span> kcal</div>
            </div>
        </div>
       

        <div class="container_d">
            <div class="days-container" id="daysContainer">
                </div>
                <!-- Icona del Calendario -->
                <div class="calendar-icon" onclick="openCalendar()">
                    <img src="../src/img/calendar.png" alt="Calendario" />
                </div>
            </div>
    
            <!-- Popup del calendario -->
            <div class="calendar-popup" id="calendarPopup">
                <input type="date" id="dateInput" onchange="selectDate()" />
                <button onclick="closeCalendar()">Chiudi</button>
            </div>
            <script>
        // Funzione per formattare una data in formato "YYYY-MM-DD"
        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        // Funzione per formattare la visualizzazione in formato "DD Mon"
        function formatDisplayDate(date) {
            const day = String(date.getDate()).padStart(2, '0');
            const month = date.toLocaleString('it-IT', { month: 'short' });
            return `${day} ${month}`;
        }

        // Funzione per generare la barra dei giorni
        function generateDays(targetDate = null) {
            const daysContainer = document.getElementById("daysContainer");
            daysContainer.innerHTML = ""; // Resetta il contenitore dei giorni

            // Se targetDate è nullo, usa la data di oggi
            const baseDate = targetDate ? new Date(targetDate) : new Date();
            const daysBefore = 7;
            const daysAfter = 7;
            let dayElements = [];

            // Aggiungi i giorni precedenti
            for (let i = daysBefore; i > 0; i--) {
                const prevDay = new Date(baseDate);
                prevDay.setDate(baseDate.getDate() - i);
                const dayStr = formatDate(prevDay);
                const displayStr = formatDisplayDate(prevDay);
                const dayElement = createDayElement(dayStr, displayStr);
                dayElements.push(dayElement);
            }

            // Aggiungi il giorno target (centrale)
            const targetStr = formatDate(baseDate);
            const targetDisplayStr = formatDisplayDate(baseDate);
            const targetElement = createDayElement(targetStr, targetDisplayStr, true);
            dayElements.push(targetElement);

            // Aggiungi i giorni successivi
            for (let i = 1; i <= daysAfter; i++) {
                const nextDay = new Date(baseDate);
                nextDay.setDate(baseDate.getDate() + i);
                const dayStr = formatDate(nextDay);
                const displayStr = formatDisplayDate(nextDay);
                const dayElement = createDayElement(dayStr, displayStr);
                dayElements.push(dayElement);
            }

            // Aggiungi gli elementi generati al contenitore
            dayElements.forEach(dayElement => daysContainer.appendChild(dayElement));

            // Centra il giorno target
            centerCurrentDay(targetElement);
        }

        // Funzione per creare un elemento giorno
        function createDayElement(date, label, isToday = false) {
            const dayElement = document.createElement("div");
            dayElement.classList.add("day");
            if (isToday) {
                dayElement.classList.add("today");
                dayElement.classList.add("selected");
            }
            dayElement.innerText = label;
            dayElement.setAttribute("data-date", date);
            dayElement.onclick = function () {
                selectDay(dayElement, date);
            };
            return dayElement;
        }

        // Funzione per selezionare un giorno
        function selectDay(dayElement, date) {
            document.querySelectorAll(".day.selected").forEach(el => el.classList.remove("selected"));
            dayElement.classList.add("selected");
            centerCurrentDay(dayElement);
            navigateToDay(date);
        }

        // Funzione per navigare alla pagina specifica
        function navigateToDay(date) {
            const currentUrlDate = new URLSearchParams(window.location.search).get("date");
            if (currentUrlDate !== date) {
                window.location.href = `intro.php?user=<?php echo $user; ?>&date=${date}`;
                setTimeout(() => {
            window.location.reload();
        }, 100); // Ricarica la pagina dopo un breve intervallo (100ms)
            }
        }

        // Funzione per centrare il giorno selezionato nella vista
        function centerCurrentDay(dayElement) {
            const daysContainer = document.getElementById("daysContainer");
            const containerWidth = daysContainer.offsetWidth;
            const dayWidth = dayElement.offsetWidth;
            const scrollPosition = dayElement.offsetLeft - (containerWidth / 2) + (dayWidth / 2);
            daysContainer.scrollLeft = scrollPosition;
        }

        // Funzione per aprire il calendario
        function openCalendar() {
            document.getElementById("calendarPopup").style.display = "block";
        }

        // Funzione per chiudere il calendario
        function closeCalendar() {
            document.getElementById("calendarPopup").style.display = "none";
        }

        // Funzione per selezionare la data dal calendario
        let selectedDateFromCalendar = null;

        function selectDate() {
            const selectedDate = document.getElementById("dateInput").value;
            if (selectedDate) {
                selectedDateFromCalendar = selectedDate;
                generateDays(selectedDateFromCalendar);
                navigateToDay(selectedDateFromCalendar);
                closeCalendar();
            }
        }

        // Inizializza la barra dei giorni quando la pagina è pronta
        document.addEventListener("DOMContentLoaded", function () {
            const urlParams = new URLSearchParams(window.location.search);
            const dateFromUrl = urlParams.get("date");
            generateDays(dateFromUrl);
        });
    </script>
            
            
        <!-- Nuovo contenitore con sfondo verdino per i grafici -->
        <div class="graphs-container" id="graphs-container">
            <!-- Grafici e Box Pasti -->
            <div class="container-graphs">
                <!-- Grafico a torta -->
                <div class="pie-chart-section">
                    <div class="titolo_rip">Ripartizione percentuale dell'energia</div>
                    <div class="pie-chart" id="pie-chart"></div>
                    <div class="legend">
    <div class="legend-item">
        <div class="legend-color proteine"></div>
        <div class="legend-text"></div>
    </div>
    <div class="legend-item">
        <div class="legend-color carboidrati"></div>
        <div class="legend-text"></div>
    </div>
    <div class="legend-item">
        <div class="legend-color grassi"></div>
        <div class="legend-text"></div>
    </div>
</div>
                </div>

                <!-- Grafico a barre -->
                <div class="bar-chart-section">
                    <div class="titolo_mac">Contenuto in macronutrienti</div>
                    <div class="chart-container">
                        <div class="chart-bar proteine" > 
                            <span></span> 
                        </div>
                        <div class="chart-bar carboidrati" > 
                            <span></span> 
                        </div>
                        <div class="chart-bar grassi" > 
                            <span></span> 
                        </div>
                    </div>
                    <div class="chart-labels">
                        <div class="chart-label">Proteine</div>
                        <div class="chart-label">Carboidrati</div>
                        <div class="chart-label">Grassi</div>
                    </div>
                    <div class="energy-total"></div>
                </div>
            </div>
        </div>

     <!-- Contenitore dei pasti -->
    <div class="meal-box" onclick="windowschange('<?php echo $pasto_colazione?>')">
     <div class="meal-name">Colazione</div>
     <div class="plus-icon">+</div>
    </div>
    <div class="arrow-container" onclick="toggleDropdown('Colazione_dropdown','breakfast-arrow')">
        <div class="arrow-icon" id="breakfast-arrow">	&#x27A4;

        </div>
    </div>
    <div class="dropdown-content" id="Colazione_dropdown">
        <ul id="Colazione">
           
        </ul>
    </div>

    <div class="meal-box" onclick="windowschange('<?php echo $pasto_pranzo?>')">
        <div class="meal-name">Pranzo</div>
        <div class="plus-icon">+</div>
    </div>
    <div class="arrow-container" onclick="toggleDropdown('Pranzo_dropdown','lauch-arrow')">
        <div class="arrow-icon" id="lauch-arrow">	&#x27A4;</div>
    </div>
    <div class="dropdown-content" id="Pranzo_dropdown">
        <ul id="Pranzo">
        
        </ul>
    </div>

    <div class="meal-box" onclick="windowschange('<?php echo $pasto_cena?>')">
        <div class="meal-name">Cena</div>
        <div class="plus-icon">+</div>
    </div>
    <div class="arrow-container" onclick="toggleDropdown('Cena_dropdown','dinner-arrow')">
        <div class="arrow-icon" id="dinner-arrow">	&#x27A4;</div>
    </div>
    <div class="dropdown-content" id="Cena_dropdown">
        <ul id="Cena">
          
        </ul>
    </div>

    <div class="meal-box" onclick="windowschange('<?php echo $pasto_snack?>')">
        <div class="meal-name">Snack</div>
        <div class="plus-icon">+</div>
    </div>
    <div class="arrow-container" onclick="toggleDropdown('Snack_dropdown','snack-arrow')">
        <div class="arrow-icon" id="snack-arrow">	&#x27A4;</div>
    </div>
    <div class="dropdown-content" id="Snack_dropdown">
        <ul id="Snack">
           
        </ul>
    </div>

    
    <script>
        function windowschange(pasto){
            window.location.href="../src/pasto.html?id_day="+<?php echo $id_day; ?>+"&date="+'<?php echo $date; ?>'+"&user="+'<?php echo $user; ?>'+"&pasto="+pasto;
        }
    function toggleDropdown(id,id2) {
        const dropdown = document.getElementById(id);
        const arrowIcon = document.getElementById(id2);
        dropdown.classList.toggle('show'); // usa la classe "show" per il dropdown
        arrowIcon.classList.toggle('rotate-down'); // cambia direzione alla freccia
    }
    </script>

        <div class="action-container">
    <!-- Box per visualizzare l'acqua attuale e il peso attuale -->
    <div class="action-display-container">
        <div class="current-value-box">
            <span>Acqua attuale: <?php echo htmlspecialchars($water); ?> lt</span>
        </div>
        <div class="current-value-box">
            <span>Peso attuale: <?php echo htmlspecialchars($weight_act); ?> kg</span>
        </div>
    </div>

    <!-- Pulsanti di aggiornamento -->
    <div class="action-buttons">
        <button class="action-btn" onclick="aggwater()">Aggiungi acqua</button>
        <button class="action-btn" onclick="aggweight()">Aggiungi peso</button>
    </div>
</div>

<script>
    function aggwater(){
        window.location.href="../src/agg_acqua.html?user=<?php
        echo $user;?>&date=<?php echo $date;?>&water=<?php echo $water;?>"
    }
    function aggweight(){
        window.location.href="../src/agg_peso.html?user=<?php
        echo $user;?>&weight=<?php echo htmlspecialchars($weight_act); ?>&date=<?php echo $date;?>"
    }
</script>
       

        <!-- Totali giornalieri -->
        <div class="container-totals">
            <ul class="totals-list">
                <li class="calories"></li>
                <li class="carbo"></li>
                <li class="protein"></li>
                <li class="fat"></li>
            </ul>
        </div>

    </div> <!-- End body_intro -->
    <script>
    
// Usa PHP per generare dinamicamente i valori nel JavaScript
let id_day = <?php echo $id_day ?>;
  let pasto_colazione = "Colazione";
  let pasto_pranzo = "Pranzo";
  let pasto_cena = "Cena";
  let pasto_snack = "Snack";
function updatechart(){
    
let totalCalories = 0;
let totalCarbo = 0;
let totalProtein = 0;
let totalFat = 0;
fetch('get_alimenti.php?id_day=' + id_day)
.then(response => response.json())
.then(data => {
  if (data.length > 0) {
    const bmr_calories = data[0].bmr_calories;
const bmr_carbo = data[0].bmr_carbo;
const bmr_protein = data[0].bmr_protein;
const bmr_fat = data[0].bmr_fat;
           data.forEach(item => {
          // Somma i valori delle rispettive proprietà
totalCalories += (item.calories/100)*item.quantity;
totalCarbo += (item.carbo/100)*item.quantity;
totalProtein += (item.protein/100)*item.quantity;
totalFat += (item.fat/100)*item.quantity;

      });

      totalCaloriesperc=totalProtein+totalCarbo+totalFat;
    const proteinePercent = (totalProtein / totalCaloriesperc) * 100;
    const carboidratiPercent = (totalCarbo / totalCaloriesperc) * 100;
    const grassiPercent = (totalFat / totalCaloriesperc) * 100;
    // Aggiorna l'altezza delle barre (percentuale)
    document.querySelector('.chart-bar.proteine').style.height = proteinePercent + '%';
    document.querySelector('.chart-bar.carboidrati').style.height = carboidratiPercent + '%';
    document.querySelector('.chart-bar.grassi').style.height = grassiPercent + '%';

    // Aggiorna i valori numerici nelle barre
    document.querySelector('.chart-bar.proteine span').textContent = totalProtein.toFixed(1) + 'g';
    document.querySelector('.chart-bar.carboidrati span').textContent = totalCarbo.toFixed(1) + 'g';
    document.querySelector('.chart-bar.grassi span').textContent = totalFat.toFixed(1) + 'g';
    
    // Aggiorna l'energia totale
    document.querySelector('.energy-total').textContent = `Energia totale: ${totalCalories.toFixed(0)} kcal`;


    let pieChart = document.getElementById('pie-chart');

    // Crea il conic-gradient dinamico con le percentuali aggiornate
        let proteinePercentFormatted = proteinePercent.toFixed(2);
let carboidratiPercentFormatted = carboidratiPercent.toFixed(2);
let grassiPercentFormatted = grassiPercent.toFixed(2);

pieChart.style.background = `conic-gradient(
    #4CAF50 0% ${proteinePercentFormatted}%,   
    #FF9800 ${proteinePercentFormatted}% ${parseFloat(proteinePercentFormatted) + parseFloat(carboidratiPercentFormatted)}%,  
    #f44336 ${parseFloat(proteinePercentFormatted) + parseFloat(carboidratiPercentFormatted)}% 100%   
)`;
document.querySelector('.legend .proteine + .legend-text').textContent = 'Proteine: '+proteinePercentFormatted + '%';
    document.querySelector('.legend .carboidrati + .legend-text').textContent = 'Carboidrati: '+carboidratiPercentFormatted + '%';
    document.querySelector('.legend .grassi + .legend-text').textContent = 'Grassi: '+grassiPercentFormatted + '%';

    document.querySelector('.totals-list .calories').textContent = 'Calorie: '+totalCalories.toFixed(0)+ '/'+bmr_calories.toFixed(0)+' kcal';
    document.querySelector('.totals-list .carbo').textContent = 'Carboidrati: '+totalCarbo.toFixed(0) + '/'+bmr_carbo.toFixed(0)+' gr';
    document.querySelector('.totals-list .protein').textContent = 'Proteine: '+totalProtein.toFixed(0) + '/'+bmr_protein.toFixed(0)+' gr';
    document.querySelector('.totals-list .fat').textContent = 'Grassi: '+totalFat.toFixed(0) + '/'+bmr_fat.toFixed(0)+' gr';



    const graphs = document.getElementById('graphs-container');
            const caloriesElement = document.querySelector('.totals-list .calories');

// Leggi il contenuto testuale dell'elemento
const caloriesValue = caloriesElement.textContent.trim(); // Usa trim() per rimuovere eventuali spazi

// Controllo
if (caloriesValue === "" || caloriesValue === null) {
} else {
    graphs.classList.toggle('show');
}
  } 
})


}
// Funzione per ottenere gli alimenti dal server tramite PHP
function fetchAlimenti(pasto) {

fetch('get_alimenti.php?id_day=' + id_day + '&pasto=' + pasto)
.then(response => response.json())
.then(data => {
  if (data.length > 0) {
      const bmr_calories = data[0].bmr_calories;
const bmr_carbo = data[0].bmr_carbo;
const bmr_protein = data[0].bmr_protein;
const bmr_fat = data[0].bmr_fat;
      // Pulisci la lista alimenti
      const alimentList = document.getElementById(pasto);
      alimentList.innerHTML = '';
     
      // Aggiungi ogni alimento trovato nella lista
      data.forEach(item => {
      


          let listItem = document.createElement('ul');
          // Crea un elemento <a> (link) che avvolge il nome dell'alimento

let aliment=document.createElement('li');
aliment.style.display = 'flex';  // Usa flex per allineare testo e bottone sulla stessa riga
    aliment.style.justifyContent = 'space-between';  // Spinge il bottone "x" a destra
    aliment.style.alignItems = 'center';  // Allinea verticalmente il testo e il bottone
    aliment.style.margin='10px';
aliment.textContent = `${item.food_name} ${item.quantity} gr`;
  // Aggiungi un bottone "x" per eliminare l'alimento
  let removeButton = document.createElement('button');
      removeButton.textContent = 'x';
      removeButton.classList.add('remove-btn');
      removeButton.onclick = function() {
          removeAlimento(item.id_food,pasto);
      };
// Appendi il bottone al li
aliment.appendChild(removeButton);

// Aggiungi l'elemento <li> con il nome dell'alimento e il bottone al <ul>
listItem.appendChild(aliment);

// Aggiungi l'elemento <ul> alla lista degli alimenti
alimentList.appendChild(listItem);
      });
  

  } else {
      document.getElementById(pasto).innerHTML = '<li>Nessun alimento trovato</li>';
  }
})
.catch(error => {
  console.error('Errore nella richiesta:', error);
});
}


// Funzione per rimuovere l'alimento dal DB
function removeAlimento(id_food,pasto) {
window.location.href="remove_alimento.php?id_food="+id_food+"&id_day="+id_day+"&pasto="+pasto;
setTimeout(function() {
window.location.reload();
}, 100);
}

document.addEventListener('DOMContentLoaded', function () {

    window.addEventListener('pageshow', function() {
    fetchAlimenti(pasto_colazione);
    fetchAlimenti(pasto_pranzo);
    fetchAlimenti(pasto_cena);
    fetchAlimenti(pasto_snack);
    updatechart();
    
  });

  window.onload = function() {
    fetchAlimenti(pasto_colazione);
    fetchAlimenti(pasto_pranzo);
    fetchAlimenti(pasto_cena);
    fetchAlimenti(pasto_snack);

  }
});

</script> 


</body>
</html>
