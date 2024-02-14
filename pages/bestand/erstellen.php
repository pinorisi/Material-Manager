<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gandalf Material Manager</title>
    <meta name="description" content="Der Gandalf Material Manager ist eine Web-App um das Stammes-Material zu organisieren und zu verwalten.">
    <meta name="author" content="Maurice Peltzer">

    <link rel="icon" type="image/x-icon" href="../../../assets/icons/favicon.ico">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lexend">
    <script src="https://unpkg.com/feather-icons"></script>

    <link rel="stylesheet" type="text/css" href="../../assets/css/standard.css">
</head>
<body>
    <header>
        <a href="#dashboard"><img id="logo" src="../../assets/icons/logo-small.png"></a>
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
        <h1>Hinzufügen</h1>
        <p class="subname">Einen neuen Gegenstand in die Datenbank einfügen.</p>
        

        <form class="infoHolder" method="post" action="../../assets/php/mat-add.php">
            <div class="disp-text">
                <input type="text" id="bezeichnung_input" name="bezeichnung_input" class="text-container" autocomplete="off" required/>
                <p class="subname">Bezeichnung</p>
            </div>
            <div class="grid-2">
                <div class="disp-text">
                    <input type="number" id="anzahl_input" name="anzahl_input" class="text-container" style="text-decoration: none; color: #232527;" autocomplete="off" required/>
                    <p class="subname">Anzahl</p>
                </div>
                <div class="disp-text">
                    <input type="text" id="lagerort_input" name="lagerort_input" class="text-container" autocomplete="off" required/>
                    <p class="subname">Lagerort</p>
                </div>
            </div>
            <div class="grid-2">
                <div class="disp-text">
                    <select class="text-container" id="kategorie_input" name="kategorie_input" required>
                        <option disabled selected hidden></option>
                        <option value="Zeltmaterial">Zeltmaterial</option>
                        <option value="Freizeit">Freizeit</option>
                        <option value="Küchenmaterial">Küchenmaterial</option>
                        <option value="Werkzeug">Werkzeug</option>
                        <option value="Ersatzteil">Ersatzteil</option>
                        <option value="Sonstiges">Sonstiges</option>
                    </select>
                    <p class="subname">Kategorie</p>
                </div>
                <div class="disp-text">
                    <input type="number" id="anschaffung_input" name="anschaffung_input" class="text-container" style="text-decoration: none; color: #232527;" autocomplete="off" required/>
                    <p class="subname">Anschaffung</p>
                </div>
            </div>
            <div class="grid-2">
                <div class="disp-text">
                    <a style="text-decoration: none; color: #232527;">
                        <input type="text" id="einkauf_input" name="einkauf_input" class="text-container" autocomplete="off" required/>
                        <p class="subname">Einkauf-Link<span data-feather="link"></span></p>
                    </a>
                </div>
                <div class="disp-text">
                    <select class="text-container" id="verpackung_input" name="verpackung_input" required>
                        <option disabled selected hidden></option>
                        <option value="Packsack">Packsack</option>
                        <option value="Kiste">Kiste</option>
                        <option value="Tasche">Tasche</option>
                        <option value="Sonstiges">Sonstiges</option>
                        <option value="Keine">Keine</option>
                    </select>
                    <p class="subname">Verpackung</p>
                </div>
            </div>
            <div class="grid-2">
                <div class="disp-text">
                    <input type="text" name="einkaufText_input" class="text-container" style="text-decoration: none; color: #232527;" placeholder="Rüsthaus 22,00€" autocomplete="off" required/>
                    <p class="subname">Einkauf Text</p>
                </div>
            </div>
            <div class="disp-text">
                <input type="text" id="bemerkung_input" name="bemerkung_input" class="text-bem" autocomplete="off"/>
                <p class="subname">Bemerkungen</p>
            </div>
    </main>

    <footer style="grid-template-columns: 1.5fr 1fr 2fr;">
        <a onclick="siteBack()" class="footer-button_long dark"><span data-feather="arrow-left"></span>Zurück</a>
        <button type="submit" class="footer-button_long" style="Font-size:16px">Hinzufügen</button>
</form>
    </footer>
</body>

<script>
feather.replace();

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
</script>
</html>