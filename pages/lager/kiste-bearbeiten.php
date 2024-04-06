<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../login/login.php");
    exit;
} elseif (isset($_SESSION['wartungsmodus']) && $_SESSION["wartungsmodus"] == true) {
    header("location: ../login/logout.html");
    exit;
}

$id = $_GET['id'] ?? '';

require_once '../../assets/php/config.php';

$sql = "SELECT * FROM kisten WHERE idKiste = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $kisten = $result->fetch_assoc();
} else {
    echo "Keine Kiste mit der gegebenen Bezeichnung gefunden.";
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $bezeichnung_input = $_POST['bezeichnung_input'] ?? '';
    $kistenart_input = $_POST['kistenArt_input'] ?? '';
    $lagerort_input = $_POST['lagerort_input'] ?? '';

    $updates = [];
    if ($bezeichnung_input && $bezeichnung_input != $kisten['bezeichnung']) {
        $updates[] = "bezeichnung='$bezeichnung_input'";
    }
    if ($kistenart_input && $kistenart_input != $kisten['icon']) {
        $updates[] = "icon='$kistenart_input'";
    }
    if ($lagerort_input && $lagerort_input != $kisten['lagerort']) {
        $updates[] = "lagerort='$lagerort_input'";
    }

    if (!empty($updates)) {
        $sql = "UPDATE kisten SET " . implode(', ', $updates) . " WHERE idKiste=$kisten[idKiste]";
        $conn->query($sql);
        $id_code = urlencode($id);
        header("Location: ansicht-kiste.php?id=$id_code");
        exit();
    } else {
        $id_code = urlencode($id);
        header("Location: ansicht-kiste.php?id=$id_code");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gandalf Material Manager</title>
    <meta name="description" content="Der Gandalf Material Manager ist eine Web-App um das Stammes-Material zu organisieren und zu verwalten.">
    <meta name="author" content="Maurice Peltzer">

    <link rel="icon" type="image/x-icon" href="../../assets/icons/favicon.ico">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lexend">
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link rel="stylesheet" type="text/css" href="../../assets/css/standard.css">
</head>
<body>
    <header>
        <a href="../allgemein/dashboard.php"><img id="logo" src="../../assets/icons/logo-small.png"></a>
        <div id="user-header">
            <p id="username">Benutzername</p>
            <a onclick="toggleMenu()"><img id="user-image" src="../../assets/images/placeholders/Portrait_Placeholder.png"></a>
        </div>
    </header>
    <div id="user-menu">
        <ul>
            <li><a class="menu-link">Profil</a></li>
            <li><a class="menu-link">Einstellungen</a></li>
            <li><a class="menu-link">Abmelden</a></li>
        </ul>
    </div>

    <main>
        <div class="space-between">
            <h1><?php echo $kisten['bezeichnung']; ?></h1>
            <a onclick="openDel()"><span style="color: #232527;" data-feather="trash"></span></a>
        </div>
        <div class="space-between">
            <p class="subtitle">Lagerkiste</p>
            <p class="subtitle"><span data-feather="map-pin"></span><?php echo $kisten['lagerort']; ?></p>
        </div>
        <div class="space-between" style="margin-top: 12px;">
            <div>
            </div>
            <p><?php echo $kisten['idKiste']; ?></p>
        </div>

        <form class="infoHolder" method="post">
            <div class="disp-text">
                <input type="text" name="bezeichnung_input" class="text-container" value="<?php echo $kisten['bezeichnung']; ?>" autocomplete="off" required/>
                <p class="subname">Bezeichnung</p>
            </div>
            <div class="grid-2">
                <div class="disp-text">
                    <select class="text-container" name="kistenArt_input" required>
                        <option <?php if($kisten['icon'] == '2')echo 'selected'; ?> value="1">Eurobox 32cm</option>
                        <option <?php if($kisten['icon'] == '1')echo 'selected'; ?> value="2">Eurobox 17cm</option>
                        <option <?php if($kisten['icon'] == '3')echo 'selected'; ?> value="3">Gitterbox 32cm</option>
                        <option <?php if($kisten['icon'] == '4')echo 'selected'; ?> value="4">Gitterbox 17cm</option>
                        <option <?php if($kisten['icon'] == '5')echo 'selected'; ?> value="5">Karton</option>
                        <option <?php if($kisten['icon'] == '0')echo 'selected'; ?> value="0">Sonstiges</option>
                    </select>
                    <p class="subname">Kistenart</p>
                </div>
                <div class="disp-text">
                    <input type="text" name="lagerort_input" class="text-container" value="<?php echo $kisten['lagerort']; ?>" autocomplete="off" required/>
                    <p class="subname">Lagerort</p>
                </div>
            </div>
        
            <h3 style="margin-top: 16px;">Inhalt</h3>
            <ul class="bestand-list" style="margin-top:0; height:55%;">
                <?php
                    require_once('../../assets/php/config.php');

                    $sql = "SELECT bezeichnung, idMaterial, anzahl, status FROM material WHERE idKiste = $id";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            switch ($row["status"]) {
                                case "1":
                                    $statusIcon = 'data-feather="tool"';
                                    break;
                                case "2":
                                    $statusIcon = 'data-feather="chevrons-right"';
                                    break;
                                case "3":
                                    $statusIcon = 'data-feather="briefcase"';
                                    break;
                                default:
                                    $statusIcon = "";
                            }
                            echo '<li class="space-between" id="material_' . $row["idMaterial"] . '">
                                    <p>' . $row["bezeichnung"] . '</p>
                                    <div style="display: flex;flex-direction: row;gap: 8px;align-items: center;">
                                        <p style="text-align: center;">' . $row["anzahl"] . '</p><div class="vertical-line"></div><a class="delMatBtn" onclick="delMat(' . $row["idMaterial"] . ')"><span style="margin-top:1px;" data-feather="x"></span></a>
                                    </div>
                                </li>';
                        }
                    } else {
                        echo "<li>Keine Daten gefunden</li>";
                    }

                ?>
            </ul>
    </main>

    <div class="modal" id="delModal">
		<div class="modal-content">
			<div class="space-between">
				<a onclick="closeModal('delModal')"><span data-feather="arrow-left"></span></a>
				<p style="width: 100%; text-align: center; font-weight: 600;">Löschen</p>
			</div>
			<p>Möchtest du wirklich den Material-Eintrag aus der Datenbank löschen? Der Eintrag kann nicht wiederhergestellt werden.</p>
			<div class="space-between" style="margin-top: 16px;">
                <a id="delBtn" onclick="deleteKiste(<?php echo $kisten['idKiste']; ?>)" class="footer-button_long" style="background-color:#9B3535;">Löschen</a>
				<a onClick="closeModal('delModal')" class="footer-button_long light">Abbrechen</a>
			</div>
		</div>
	</div>

    <footer style="grid-template-columns: 1.5fr 1fr 2fr;">
        <a onclick="siteBack()" class="footer-button_long dark"><span data-feather="arrow-left"></span>Zurück</a>
        <button type="submit" class="footer-button_long" style="font-size:16px;">Speichern</button>
        <a onclick="openModal(modal)" class="footer-button"><span data-feather="plus"></span></a>
    </footer>
    </form>

    <div class="modal" id="modal">
		<div class="modal-content">
			<div class="space-between">
				<a onclick="closeModal('modal')"><span data-feather="arrow-left"></span></a>
				<p style="width: 100%; text-align: center;">Material</p>
			</div>
            <div>
                <form class="search-container">
                    <input type="search" id="search-bar" placeholder="Suchen..." onkeyup="searchBestand()">
                    <button type="button"><span data-feather="search"></span></button>
                </form>
                <p class="subname" style="margin-top: 2px; margin-bottom: 16px;" id="resultCount">0 Ergebnisse gefunden</p>
                <ul class="bestand-list" style="margin-top:0; height:40vh;">
                    <?php
                        require_once('../../assets/php/config.php');

                        $sql2 = "SELECT bezeichnung, idMaterial, anzahl FROM material WHERE idkiste IS NULL";
                        $result2 = $conn->query($sql2);
                        if ($result2->num_rows > 0) {
                            while ($row = $result2->fetch_assoc()) {
                                echo '<li class="space-between blli" id="material_' . $row["idMaterial"] . '">
                                        <p>' . $row["bezeichnung"] . '</p>
                                        <div style="display: flex;flex-direction: row;gap: 8px;align-items: center;">
                                            <p style="text-align: center;">' . $row["anzahl"] . '</p><div class="vertical-line"></div><a class="delMatBtn" style="background-color: #6D8788;" onclick="assignMaterial(' . $row["idMaterial"] . ', ' . $kisten["idKiste"] . ')"><span style="margin-top:1px;" data-feather="plus"></span></a>
                                        </div>
                                    </li>';
                            }
                        } else {
                            echo "<li>Keine unassignierten Materialien gefunden</li>";
                        }

                        $conn->close();
                    ?>
                </ul>
            </div>
			<div class="space-between" style="margin-top: 16px;">
				<a onClick="closeModal('modal')" class="footer-button_long dark"><span data-feather="arrow-left"></span>Zurück</a>
			</div>
		</div>
	</div>
</body>

<script>
feather.replace();

function delMat(matId) {
    $.ajax({
        url: '../../assets/php/delMat.php',
        type: 'POST',
        data: { id: matId },
        success: function(response) {
            $('#material_' + matId).remove();
            console.log('Material erfolgreich gelöscht.');
        },
        error: function(xhr, status, error) {
            console.error('Fehler beim Löschen des Materials:', error);
        }
    });
}

function deleteKiste(id) {
        $.ajax({
            url: "../../assets/php/kiste-del.php",
            type: "POST",
            data: { delete_id: id },
            success: function() {
                window.location.href="lager.php"
            }
        });
}
	
let camera = null;
let stream = null;

function toggleMenu(){
    var menu = document.getElementById('user-menu'); 
    if (menu.classList.contains('open')){ 
        menu.classList.remove('open');
    } else { 
        menu.classList.add('open'); 
    }
}

function siteBack(){
    window.history.back(); 
}

function openDel(){
    document.getElementById('delModal').classList.add('open');
}

function closeModal(modal){ 
    var x = document.getElementById(modal); 
    x.classList.remove('open'); 
    stopCamera(); 
}

function openModal(){ 
    var x = document.getElementById('modal'); 
    x.classList.add('open'); 
}

function assignMaterial(materialId) {
    $.ajax({
        url: '../../assets/php/assignBox.php',
        type: 'POST',
        data: { material_id: materialId, box_id: <?php echo $kisten['idKiste']; ?> },
        success: function(response) {
            location.reload();
        },
        error: function(xhr, status, error) {
            console.error('Fehler beim Zuweisen des Materials:', error);
        }
    });
}

function searchBestand() {
    var input, filter, ul, li, txtValue, searchResults;
    input = document.getElementById('search-bar');
    filter = input.value.toUpperCase().replace(/\s+/g, '');
    li = document.getElementsByClassName("blli");
    searchResults = 0;

    for (var i = 0; i < li.length; i++){
        li[i].style.display = 'none';
        txtValue = li[i].textContent || li[i].innerText;
        if(txtValue.toUpperCase().indexOf(filter) > -1){
            li[i].style.display = "flex"
            searchResults++;
        }
    }
    document.getElementById('resultCount').textContent = (searchResults + " Ergebnisse gefunden");
}
</script>
</html>