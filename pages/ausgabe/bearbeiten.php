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

$sql = "SELECT * FROM aktionen WHERE idAktion = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $aktion = $result->fetch_assoc();
} else {
    echo "Keine Aktion mit der gegebenen Id gefunden.";
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

    <link rel="stylesheet" type="text/css" href="../../assets/css/standard.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header>
        <a href="../allgemein/dashboard.php"><img id="logo" src="../../assets/icons/logo-small.png"></a>
        <div id="user-header" title="Account">
            <p id="username"><?php echo $_SESSION['username'] ?></p>
            <a onclick="toggleMenu('user-menu')"><img id="user-image" src="../../assets/images/placeholders/Portrait_Placeholder.png"></a>
        </div>
    </header>
    <div id="user-menu">
        <ul>
            <li><a href="profil.php" class="menu-link">Profil</a></li>
            <li><a class="menu-link">Einstellungen</a></li>
            <li>
                <form method="post" action="../../assets/php/users/logout.php">
                    <input type="submit" name="logout" value="Logout">
                </form>
            </li>
        </ul>
    </div>

    <main>
        <div class="space-between">
            <h1><?php echo $aktion['bezeichnung']; ?></h1>
            <a onclick="openModal('delModal')"><span style="color: #232527;" data-feather="trash"></span></a>
        </div>

        <form class="infoHolder" method="post" action="../../assets/php/aktionen/update.php">
            <div class="disp-text">
                <input type="text" id="bezeichnung_input" name="bezeichnung_input" class="text-container" autocomplete="off" value="<?php echo $aktion['bezeichnung']; ?>" required/>
                <p class="subname">Bezeichnung</p>
            </div>
            <div class="grid-2">
                <div class="disp-text">
                    <input type="date" id="beginn_input" name="beginn_input" class="text-container" autocomplete="off" style="font-size: 14px;" value="<?php echo $aktion['beginn'] ?>" required/>
                     <p class="subname">Ausgabe</p>
                </div>
                <div class="disp-text">
                    <input type="date" id="ende_input" name="ende_input" class="text-container" autocomplete="off" style="font-size: 14px;" value="<?php echo $aktion['ende'] ?>" required/>
                     <p class="subname">Rücknahme</p>
                </div>
            </div>
            <div class="disp-text">
                <input type="text" id="verantwortlicher_input" name="verantwortlicher_input" class="text-container" autocomplete="off" value="<?php echo $aktion['ansprechpartner'] ?>" required/>
                <p class="subname">Verantwortliche:r</p>
            </div>
            <input type="hidden" id="idAktion" name="idAktion" value="<?php echo $id; ?>" />

            <h3 style="margin-top: 16px;">Material hinzufügen</h3>
            <ul class="bestand-list" style="margin-top:0; height:55%;">
                <?php
                    require_once('../../assets/php/config.php');

                    $sql = "SELECT tk.idTransportkiste, tk.bezeichnung AS Transportkiste
                            FROM transportkisten tk
                            LEFT JOIN material_transportkiste_aktion mta ON tk.idTransportkiste = mta.idTransportkiste
                            LEFT JOIN aktionen a ON mta.idAktion = a.idAktion
                            WHERE a.idAktion = $id
                            GROUP BY tk.idTransportkiste";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<li style='height:auto; background-color:white;' id='transportkiste_" . $row['idTransportkiste'] . "' class='listDetail'><details>
                                    <summary>
                                        <p style='width:80%'>$row[Transportkiste]</p>
                                        <div>
                                            <a class='delMatBtn' onclick='unassignTransportkiste(\"" . $row["idTransportkiste"] . "\", \"" . $aktion["idAktion"] . "\")'><span data-feather='x'></span></a>
                                            <a class='delMatBtn' style='background-color: #6D8788;' onclick='assignMaterialModal(\"" . $row['idTransportkiste'] . "\")'><span style='margin-top:1px;' data-feather='plus'></span></a>
                                        </div>
                                    </summary>
                                    <ul>";
                                    $sql2 = "SELECT m.bezeichnung AS Material, m.anzahl AS Anzahl, m.idMaterial AS MaterialId
                                             FROM material_transportkiste_aktion mta
                                             JOIN material m ON mta.idMaterial = m.idMaterial
                                             WHERE mta.idTransportkiste = $row[idTransportkiste] AND mta.idAktion = $id";
                                    $result2 = $conn->query($sql2);
                                    if ($result2->num_rows > 0) {
                                    
                                        while ($row2 = $result2->fetch_assoc()) {
                                            echo "<li class='subname space-between' id='materialkiste_" . $row2['MaterialId'] . "' style='padding-right:11px;'><p>$row2[Material]</p><a class='delMatBtn' onclick='unassignMaterial(\"" . $row2['MaterialId'] . "\", \"" . $aktion['idAktion'] . "\")'><span style='margin-top:1px; margin-left:0px;' data-feather='x'></span></a></li>";
                                        }
                                    } else {
                                        echo "<li class='subname space-between'><p>Kein Material</p></li>";
                                    }
                            echo "</ul>
                                  </details></li>";
                        }
                    } else {
                        echo "<li style='height:auto;'><p class='subtitle'>Der Aktion wurde kein Material zugeordnet</p></li>";
                    }
                ?>
            </ul>
    </main>

    <footer>
        <a onclick="siteBack()" class="footer-button_long dark"><span data-feather="arrow-left"></span>Zurück</a>
        <a onclick="siteBack()" class="footer-button_long" style="font-size:16px;">Speichern</a>
        <a onclick="openModal('modalKisten')" class="footer-button"><span data-feather="plus"></span></a>
    </footer>

    <div class="modal" id="modalKisten">
		<div class="modal-content">
			<div class="space-between">
				<a onclick="closeModal('modalKisten')"><span data-feather="arrow-left"></span></a>
				<p style="width: 100%; text-align: center;">Transportkiste hinzufügen</p>
			</div>
            <div>
                <ul class="bestand-list" style="margin-top:40px; height:40vh;">
                    <?php
                        require_once('../../assets/php/config.php');

                        $sql2 = "SELECT * FROM transportkisten 
                                  WHERE idTransportkiste NOT IN (
                                      SELECT idTransportkiste FROM material_transportkiste_aktion 
                                      WHERE idAktion = ?
                                  )";
                        $stmt = $conn->prepare($sql2);
                        $stmt->bind_param("i", $id);
                        $id = $aktion["idAktion"];
                        $stmt->execute();
                        $result2 = $stmt->get_result();
                                
                        if ($result2->num_rows > 0) {
                            while ($row = $result2->fetch_assoc()) {
                                switch ($row["icon"]) {
                                    case "1":
                                        $chestIcon = "../../assets/icons/full_box.svg";
                                        break;
                                    default:
                                        $chestIcon = "../../assets/icons/half_box.svg";
                                }
                                echo '<li class="space-between blli" id="transportkiste' . $row["idTransportkiste"] . '">
                                        <p>' . $row["bezeichnung"] . '</p>
                                        <div style="display: flex;flex-direction: row;gap: 8px;align-items: center;">
                                        <img src="' . $chestIcon . '" style="height: 16px; aspect-ratio: 1/1;"><div class="vertical-line"></div><a class="delMatBtn" style="background-color: #6D8788;" onclick="assignTransportkiste(\'' . $row["idTransportkiste"] . '\', \'' . $aktion["idAktion"] . '\')"><span style="margin-top:1px;" data-feather="plus"></span></a>
                                        </div>
                                    </li>';
                            }
                        } else {
                            echo "<li style='height:auto;'>Keine Transportkisten gefunden</li>";
                        }
                    ?>
                </ul>
            </div>
			<div class="space-between" style="margin-top: 16px;">
				<a onClick="closeModal('modalKisten')" class="footer-button_long dark"><span data-feather="arrow-left"></span>Zurück</a>
			</div>
		</div>
	</div>

    <div class="modal" id="modalMaterial">
		<div class="modal-content">
			<div class="space-between">
				<a onclick="closeModal('modalMaterial')"><span data-feather="arrow-left"></span></a>
				<p style="width: 100%; text-align: center;">Material hinzufügen</p>
			</div>
            <div>
                <div class="search-container">
                    <input type="search" id="search-bar" placeholder="Suchen..." onkeyup="searchBestand()">
                    <button type="button"><span data-feather="search"></span></button>
                    </div>
                <p class="subname" style="margin-top: 2px; margin-bottom: 16px;" id="resultCount">0 Ergebnisse gefunden</p>
                <ul class="bestand-list" style="margin-top:0; height:40vh;">
                    <?php
                        require_once('../../assets/php/config.php');

                        $sql2 = "SELECT m.bezeichnung, m.idMaterial, m.anzahl 
                        FROM material m
                        LEFT JOIN material_transportkiste_aktion mta ON m.idMaterial = mta.idMaterial
                        WHERE mta.idMaterial IS NULL OR mta.idAktion <> '$id'";
                        $result2 = $conn->query($sql2);
                        if ($result2->num_rows > 0) {
                            while ($row = $result2->fetch_assoc()) {
                                echo '<li class="space-between blli" id="material_' . $row["idMaterial"] . '">
                                        <p>' . $row["bezeichnung"] . '</p>
                                        <div style="display: flex;flex-direction: row;gap: 8px;align-items: center;">
                                            <p style="text-align: center;">' . $row["anzahl"] . '</p><div class="vertical-line"></div><a class="delMatBtn" style="background-color: #6D8788;" onclick="assignMaterial(\'' . $row["idMaterial"] . '\', \'' . $id . '\')"><span style="margin-top:1px;" data-feather="plus"></span></a>
                                        </div>
                                    </li>';
                            }
                        } else {
                            echo "<li style='height:auto;>Keine verfügbaren Materialien gefunden</li>";
                        }

                        $conn->close();
                    ?>
                </ul>
            </div>
			<div class="space-between" style="margin-top: 16px;">
				<a onClick="closeModal('modalMaterial')" class="footer-button_long dark"><span data-feather="arrow-left"></span>Zurück</a>
			</div>
		</div>
	</div>

    <div class="modal" id="delModal">
		<div class="modal-content">
			<div class="space-between">
				<a onclick="closeModal('delModal')"><span data-feather="arrow-left"></span></a>
				<p style="width: 100%; text-align: center; font-weight: 600;">Löschen</p>
			</div>
			<p>Möchtest du wirklich die Aktion aus der Datenbank löschen? Der Eintrag kann nicht wiederhergestellt werden.</p>
			<div class="space-between" style="margin-top: 16px;">
                <a id="delBtn" onclick="deleteAktion(<?php echo $aktion['idAktion']; ?>)" class="footer-button_long" style="background-color:#9B3535;">Löschen</a>
				<a onClick="closeModal('delModal')" class="footer-button_long light">Abbrechen</a>
			</div>
		</div>
	</div>
</body>

<script>
feather.replace();

function toggleMenu(id){
    var menu = document.getElementById(id);
    if (menu.classList.contains('open')){
        menu.classList.remove('open');
    } else {
        menu.classList.add('open');
    }
}

function siteBack(){
    window.history.back(); 
}

function openModal(id){
    var x = document.getElementById(id);
    x.classList.add('open');
}

function closeModal(id) {
    var modal = document.getElementById(id);
    modal.classList.remove('open');
}

function toAktionspage(id){
    window.location.href = '../ausgabe/ansicht-aktion.php?id=' + encodeURIComponent(id);
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

var selectedTransportkiste;

function assignMaterialModal(idTransportkiste){
    selectedTransportkiste = idTransportkiste;
    document.getElementById('modalMaterial').classList.add('open');
}

function assignMaterial(idMaterial, idAktion){
    $.ajax({
        url: '../../assets/php/aktionen/assign-material.php',
        type: 'POST',
        data: { material_id: idMaterial, aktion_id: idAktion, transportkiste_id: selectedTransportkiste},
        success: function(response) {
            location.reload();
        },
        error: function(xhr, status, error) {
            console.error('Fehler beim zuweisen des Materials zur Transportkiste der Aktion:', error);
        }
    });
}

function assignTransportkiste(idTransportkiste, idAktion){
    $.ajax({
        url: '../../assets/php/aktionen/assign-box.php',
        type: 'POST',
        data: { transportkiste_id: idTransportkiste, aktion_id: idAktion},
        success: function(response) {
            location.reload();
        },
        error: function(xhr, status, error) {
            console.error('Fehler beim zuweisen der Transportkiste zur Aktion:', error);
        }
    });
}

function unassignTransportkiste(idTransportkiste, idAktion) {
    $.ajax({
        url: '../../assets/php/aktionen/unassign-box.php',
        type: 'POST',
        data: {transportkiste_id: idTransportkiste, aktion_id: idAktion},
        success: function(response) {
            $('#transportkiste_' + idTransportkiste).remove();
            console.log('Transportkiste ' + idTransportkiste + ' erfolgreich aus Aktion ' + idAktion + ' entfernt.');
        },
        error: function(xhr, status, error) {
            console.error('Fehler beim entfernen der Transportkiste:', error);
        }
    });
}

function unassignMaterial(idMaterial, idAktion) {
    $.ajax({
        url: '../../assets/php/aktionen/unassign-material.php',
        type: 'POST',
        data: {material_id: idMaterial, aktion_id: idAktion},
        success: function(response) {
            $('#materialkiste_' + idMaterial).remove();
            console.log('Material (' + idMaterial + ') erfolgreich aus Aktion (' + idAktion + ') entfernt.');
        },
        error: function(xhr, status, error) {
            console.error('Fehler beim entfernen der Transportkiste:', error);
        }
    });
}

function deleteAktion(idAktion){
    console.log(idAktion);
    $.ajax({
            url: "../../assets/php/aktionen/delete.php",
            type: "POST",
            data: { delete_id: idAktion },
            success: function() {
                window.location.href="uebersicht.php"
                console.log("erfolgreich gelöscht.")
            }
        });
}

function delAktion(){
    <?php  ?>
}
</script>
</html>
