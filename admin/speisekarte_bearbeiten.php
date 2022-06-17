<?php
include 'authentifizierung.php';

$json = file_get_contents("konfiguration.json");
if ($json == false) {
	http_response_code(500);
	die("<h1>Systemfehler!</h1><p>Konfigurationsdatei konnte nicht gelesen werden</h1>");
}
else {
	$conf_arr = json_decode($json, true);
}

$mysqli = new mysqli($conf_arr["sql_server"], $conf_arr["sql_user"], $conf_arr["sql_password"], $conf_arr["sql_database"]);

if ($mysqli->connect_errno) {
	http_response_code(500);
	die("<h1>Systemfehler</h1>\n<h2>Fehler beim Verbindungsaufbau mit der Datenbank</h2>\n<p>" . $mysqli->connect_error . "</p>");
}

if (isset($_POST["add_item"])) {
	if (isset($_POST["f_num"]) and isset($_POST["f_name"]) and isset($_POST["f_desc"]) and isset($_POST["f_price"]) and isset($_POST["f_pid"]) and isset($_POST["f_img"])) {
		$sql = "INSERT INTO s4_karte(f_num,f_name,f_desc,f_price,f_pid,f_img) VALUES (?, ?, ?, ?, ?, ?);";
		$statement = $mysqli->prepare($sql);
		$statement->bind_param("issdis", $_POST["f_num"], $_POST["f_name"], $_POST["f_desc"], $_POST["f_price"], $_POST["f_pid"], $_POST["f_img"]);
		if(!$statement->execute()) {
			echo "SQL QUERY ERROR!\n".$statement->error;
		}
	}

	else {
		header("Content-Type: text/plain");
		die("Felder Fehlen. Dies kann sowohl ein Problem des Systems, als auch ihres Endgerätes sein.\nVersuchen sie es erneut mit einem anderen Browser oder Gerät. Ansonsten kontaktieren sie bitte den Support.");
	}
}

if (isset($_POST["edit_item"])) {
	if (isset($_POST["f_num"]) and isset($_POST["f_name"]) and isset($_POST["f_desc"]) and isset($_POST["f_price"]) and isset($_POST["f_pid"]) and isset($_POST["f_img"])) {
		$sql = "UPDATE `s4_karte` SET f_num=?, f_name=?, f_desc=?, f_price=?, f_pid=?, f_img=? WHERE `f_num` = ?;";
		$statement = $mysqli->prepare($sql);
		$statement->bind_param("issdisi", $_POST["f_num"], $_POST["f_name"], $_POST["f_desc"], $_POST["f_price"], $_POST["f_pid"], $_POST["f_img"], $_POST["f_num"]);
		if(!$statement->execute()) {
			echo "SQL QUERY ERROR!\n".$statement->error;
		}
	}

	else {
		header("Content-Type: text/plain");
		die("Felder Fehlen. Dies kann sowohl ein Problem des Systems, als auch ihres Endgerätes sein.\nVersuchen sie es erneut mit einem anderen Browser oder Gerät. Ansonsten kontaktieren sie bitte den Support.");
	}
}

if (isset($_POST["delete_item"])) {
	if (isset($_POST["f_num"])) {
		$sql = "DELETE FROM `s4_karte` WHERE `f_num` = ?;";
		$statement = $mysqli->prepare($sql);
		$statement->bind_param("i", $_POST["f_num"]);
		if(!$statement->execute()) {
			echo "SQL QUERY ERROR!\n".$statement->error;
		}
	}
	else {
		header("Content-Type: text/plain");
		die("Felder Fehlen. Dies kann sowohl ein Problem des Systems, als auch ihres Endgerätes sein.\nVersuchen sie es erneut mit einem anderen Browser oder Gerät. Ansonsten kontaktieren sie bitte den Support.");
	}
}


if (isset($_POST["add_page"])) {
	if (isset($_POST["p_num"]) and isset($_POST["p_name"]) and isset($_POST["p_desc"])) {
		$sql = "INSERT INTO s4_seiten(p_num,p_name,p_desc) VALUES (?, ?, ?);";
		$statement = $mysqli->prepare($sql);
		$statement->bind_param("iss", $_POST["p_num"], $_POST["p_name"], $_POST["p_desc"]);
		if(!$statement->execute()) {
			echo "SQL QUERY ERROR!\n".$statement->error;
		}
	}

	else {
		header("Content-Type: text/plain");
		die("Felder Fehlen. Dies kann sowohl ein Problem des Systems, als auch ihres Endgerätes sein.\nVersuchen sie es erneut mit einem anderen Browser oder Gerät. Ansonsten kontaktieren sie bitte den Support.");
	}
}

if (isset($_POST["edit_page"])) {
	if (isset($_POST["p_num"]) and isset($_POST["p_name"]) and isset($_POST["p_desc"])) {
		$sql = "UPDATE `s4_seiten` SET p_num=?, p_name=?, p_desc=? WHERE `p_num` = ?;";
		$statement = $mysqli->prepare($sql);
		$statement->bind_param("issi", $_POST["p_num"], $_POST["p_name"], $_POST["p_desc"], $_POST["p_num"]);
		if(!$statement->execute()) {
			echo "SQL QUERY ERROR!\n".$statement->error;
		}
	}

	else {
		header("Content-Type: text/plain");
		die("Felder Fehlen. Dies kann sowohl ein Problem des Systems, als auch ihres Endgerätes sein.\nVersuchen sie es erneut mit einem anderen Browser oder Gerät. Ansonsten kontaktieren sie bitte den Support.");
	}
}

if (isset($_POST["delete_page"])) {
	if (isset($_POST["p_num"])) {
		$sql = "DELETE FROM `s4_seiten` WHERE `p_num` = ?;";
		$statement = $mysqli->prepare($sql);
		$statement->bind_param("i", $_POST["p_num"]);
		if(!$statement->execute()) {
			echo "SQL QUERY ERROR!\n".$statement->error;
		}
	}
	else {
		header("Content-Type: text/plain");
		die("Felder Fehlen. Dies kann sowohl ein Problem des Systems, als auch ihres Endgerätes sein.\nVersuchen sie es erneut mit einem anderen Browser oder Gerät. Ansonsten kontaktieren sie bitte den Support.");
	}
}

$result = $mysqli->query('SELECT * FROM s4_karte');
$karte = $result->fetch_all(MYSQLI_ASSOC);


//Meta
$result = $mysqli->query('SELECT * FROM `s4_seiten`');
$pages = $result->fetch_all(MYSQLI_ASSOC);
$max_page = max(0,count($pages) - 1); //TODO: make this less stupid and use SQL for the counting



?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8">
<title>Speisekarte Verwalten</title>
<link rel="stylesheet" href="admin.css?nocache=<?php print(sha1(file_get_contents("admin.css")));?>">
</head>
<body>
<h1>Gerichte Verwalten</h1>

<datalist id="images">
<?php
$images = glob("../karte/bilder/*.*");
foreach ($images as $img) {
	print('<option value="');
	print(basename($img));
	print("\">\n");
}
?>
</datalist>

<hr>
<h2>Gerichte Bearbeiten</h2>

<table>
<tr><th>Bestellnummer</th><th>Name</th><th>Beschreibung</th><th>Preis</th><th>Seite</th><th>Bild URL</th><th>Aktion</th></tr>
<?php
foreach ($karte as $entry) {
	print('<tr>');
	print('<form action="speisekarte_bearbeiten.php" method="post" enctype="multipart/form-data">');
	print('<td><input type="number" name="f_num" value="');
	print($entry["f_num"]);
	print('"></td>');
	print('<td><input type="text" name="f_name" value="');
	print($entry["f_name"]);
	print('"></td>');
	print('<td><input type="text" name="f_desc" value="');
	print($entry["f_desc"]);
	print('"></td>');
	print('<td><input type="number" step="0.01" name="f_price" value="');
	print($entry["f_price"]);
	print('"></td>');
	print('<td><input type="number" name="f_pid" value="');
	print($entry["f_pid"]);
	print('"></td>');
	print('<td><input type="text" name="f_img" list="images" value="');
	print($entry["f_img"]);
	print('"></td>');
	print('<td><input name="edit_item" type="submit" value="Bearbeitung Absenden">');
	print('</form>');
	
	print('<form action="speisekarte_bearbeiten.php" method="post" enctype="multipart/form-data">');
	print('<input name="f_num" type="hidden" value="');
	print($entry["f_num"]);
	print('">');
	print('<input name="delete_item" type="submit" value="Gericht Löschen">');
	print('</form></td>');
	print('</tr>');
}
?>
</table>

<hr>
<h2>Gericht Hinzufügen</h2>

<form action="speisekarte_bearbeiten.php" method="post" enctype="multipart/form-data">
Bestellnummer: <input type="number" name="f_num" placeholder="0"><br>
Name: <input type="text" name="f_name" placeholder="Gericht"><br>
Beschreibung: <input type="text" name="f_desc" placeholder="Es schmeckt sehr gut."><br>
Preis (<?php print($conf_arr["l_currency"]);?>): <input type="number" step="0.01" name="f_price" placeholder="0.00"><br>
Seite (0-<?php print($max_page) ?>): <input type="number" name="f_pid" placeholder="0" max="<?php print($max_page) ?>"><br>
Bild URL: <input type="text" name="f_img" list="images"><br>
<input name="add_item" type="submit" value="Gericht Hinzufügen">
</form>

<hr><hr>
<h1>Seiten Verwalten</h1>
<hr>

<h2>Seiten Bearbeiten</h2>

<table>
<tr><th>Seite Verschieben nach</th><th>Name</th><th>Beschreibung</th><th>Aktion</th></tr>
<?php
foreach ($pages as $page) {
	print('<tr>');
	print('<form action="speisekarte_bearbeiten.php" method="post" enctype="multipart/form-data">');
	print('<td><input type="number" name="p_num" value="');
	print($page["p_num"]);
	print('"></td>');
	print('<td><input type="text" name="p_name" value="');
	print($page["p_name"]);
	print('"></td>');
	print('<td><input type="text" name="p_desc" value="');
	print($page["p_desc"]);
	print('"></td>');
	print('<td><input name="edit_page" type="submit" value="Bearbeitung Absenden">');
	print('</form>');
	
	print('<form action="speisekarte_bearbeiten.php" method="post" enctype="multipart/form-data">');
	print('<input name="p_num" type="hidden" value="');
	print($page["p_num"]);
	print('">');
	print('<input name="delete_page" type="submit" value="Seite Löschen">');
	print('</form></td>');
	print('</tr>');
}
?>
</table>

<hr>
<h2>Seiten Hinzufügen</h2>

<form action="speisekarte_bearbeiten.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="p_num" value="<?php print(count($pages)) ?>"><br>
Name: <input type="text" name="p_name" placeholder="Kartenseite"><br>
Beschreibung: <input type="text" name="p_desc" placeholder="Kann Spuren von leckeren Gerichten enthalten."><br>
<input name="add_page" type="submit" value="Seite Hinzufügen">
</form>

<hr>
<hr>
<a class="anchorButton" href=".">Zurück zur Übersicht</a>
</body>
</html>