<?php
include 'authentifizierung.php';

if ($_SESSION['is_admin'] != true) {
	die("<h1>Unzureichende Berechtigungen</h1>");
}

if (isset($_POST["submit_change"])) {
	if (isset($_POST["sql_server"]) and isset($_POST["sql_user"]) and isset($_POST["sql_password"]) and isset($_POST["sql_database"]) and isset($_POST["r_name"]) and isset($_POST["r_logo"]) and isset($_POST["l_currency"])) {
		checkCSRF();
		$conf_arr = [];
		$conf_arr["sql_server"] = $_POST["sql_server"];
		$conf_arr["sql_user"] = $_POST["sql_user"];
		$conf_arr["sql_password"] = $_POST["sql_password"];
		$conf_arr["sql_database"] = $_POST["sql_database"];
		$conf_arr["r_name"] = $_POST["r_name"];
		$conf_arr["r_logo"] = $_POST["r_logo"];
		$conf_arr["l_currency"] = $_POST["l_currency"];
		$conf_arr["r_css"] = $_POST["r_css"];
		file_put_contents("konfiguration.json", json_encode($conf_arr));
		header("Location: konfiguration_bearbeiten.php");
	}
	else {
		header("Content-Type: text/plain");
		die("Felder Fehlen. Dies kann sowohl ein Problem des Systems, als auch ihres Endgerätes sein.\nVersuchen sie es erneut mit einem anderen Browser oder Gerät. Ansonsten kontaktieren sie bitte den Support.");
	}
}

$json = file_get_contents("konfiguration.json");
if ($json == false) {
	$conf_arr = [];
	$conf_arr["sql_server"] = "";
	$conf_arr["sql_user"] = "";
	$conf_arr["sql_password"] = "";
	$conf_arr["sql_database"] = "";
	$conf_arr["r_name"] = "";
	$conf_arr["r_logo"] = "";
	$conf_arr["l_currency"] = "";
	$conf_arr["r_css"] = "";
}
else {
	$conf_arr = json_decode($json, true);
}
?><!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8">
<title>Konfiguration Bearbeiten</title>
<link rel="stylesheet" href="admin.css?nocache=<?php print(sha1(file_get_contents("admin.css")));?>">
</head>
<body>
<h1>Konfiguration Bearbeiten</h1>

<form action="konfiguration_bearbeiten.php" method="post" enctype="multipart/form-data">
<hr>
<h2>Restaurant</h2>
<label for="r_name">Restaurant Name:</label> <input name="r_name" value="<?php print($conf_arr["r_name"]); ?>" required>
<br>
<label for="r_logo">Restaurant Logo URL:</label> <input name="r_logo" value="<?php print($conf_arr["r_logo"]); ?>" required>
<br>
<label for="l_currency">Währung:</label> <input name="l_currency" value="<?php print($conf_arr["l_currency"]); ?>" required>
<br>
<label for="r_css">Restaurant Stylesheet:</label> 
<select name="r_css" required>
<option value="<?php print($conf_arr["r_css"]); ?>" selected>Nicht Ändern (<?php print($conf_arr["r_css"]); ?>)</option>
<?php
$styles = glob("../karte/styles/*.css");
foreach ($styles as $style) {
	print('<option value="');
	print(basename($style));
	print('">');
	print(basename($style));
	print("</option>\n");
}
?>
</select>
<hr>
<h2>System</h2>
<label for="sql_server">SQL Server:</label> <input name="sql_server" value="<?php print($conf_arr["sql_server"]); ?>" placeholder="localhost" required>
<br>
<label for="sql_user">SQL Benutzer:</label> <input name="sql_user" value="<?php print($conf_arr["sql_user"]); ?>" required>
<br>
<label for="sql_password">SQL Passwort:</label> <input name="sql_password" type="password" value="<?php print($conf_arr["sql_password"]); ?>" required>
<br>
<label for="sql_database">SQL Datenbank:</label> <input name="sql_database" value="<?php print($conf_arr["sql_database"]); ?>" required>
<br>

<hr>
<input type="hidden" name="csrf" value="<?php print($_SESSION['csrf_token']); ?>">
<input type="submit" value="Konfiguration Ändern" name="submit_change" style="font-size: 1.5em;">
</form>

<hr>
<a class="anchorButton" href=".">Zurück zur Übersicht</a>
</body>
</html>