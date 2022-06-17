<?php
include 'authentifizierung.php';

if ($_SESSION['is_admin'] != true) {
	die("<h1>Unzureichende Berechtigungen</h1>");
}

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

if (isset($_POST["change_user"])) {
	if (isset($_POST["user"]) and isset($_POST["password"]) and isset($_POST["change_password"])) {
		$sql = "UPDATE `s4_users` SET `password` = ? WHERE `username` = ?;";
		$statement = $mysqli->prepare($sql);
		$statement->bind_param("ss", password_hash($_POST["password"], PASSWORD_BCRYPT), $_POST["user"]);
		if(!$statement->execute()) {
			echo "SQL QUERY ERROR!\n".$statement->error;
		}
	}
	if (isset($_POST["user"])) {
		$sql = "UPDATE `s4_users` SET `is_admin` = ? WHERE `username` = ?;";
		$statement = $mysqli->prepare($sql);
		$statement->bind_param("is", intval(isset($_POST["admin"])), $_POST["user"]);
		if(!$statement->execute()) {
			echo "SQL QUERY ERROR!\n".$statement->error;
		}
	}
}

if (isset($_POST["add_user"])) {
	if (isset($_POST["user"]) and isset($_POST["password"])) {
		$sql = "INSERT INTO `s4_users` (`username`, `password`, `is_admin`) VALUES (?, ?, ?);";
		$statement = $mysqli->prepare($sql);
		$statement->bind_param("ssi", $_POST["user"], password_hash($_POST["password"], PASSWORD_BCRYPT), intval(isset($_POST["admin"])));
		if(!$statement->execute()) {
			echo "SQL QUERY ERROR!\n".$statement->error;
		}
	}
	else {
		die("<h1>Systemfehler</h1>\n<p>Felder Fehlen!</p>");
	}
}

if (isset($_POST["delete_user"])) {
	if (isset($_POST["user_to_delete"])) {
		$sql="DELETE FROM `s4_users` WHERE `username` = ?;";
		$statement = $mysqli->prepare($sql);
		$statement->bind_param("s", $_POST["user_to_delete"]);
		if(!$statement->execute()) {
			echo "SQL QUERY ERROR!\n".$statement->error;
		}
	}
	else {
		die("<h1>Systemfehler</h1>\n<p>Felder Fehlen!</p>");
	}
}

$result = $mysqli->query('SELECT * FROM s4_users');
$user_table = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8">
<title>S4 Benutzer</title>
<link rel="stylesheet" href="admin.css?nocache=<?php print(sha1(file_get_contents("admin.css")));?>">
</head>
<body>

<h1>Benutzer Verwalten</h1>
<hr>

<form action="benutzer_bearbeiten.php" method="post" enctype="multipart/form-data">
Benutzername: <input name="user" autocomplete="no" required>
<br>
Neues Passwort: <input name="password" type="password" required></span>
<br>
Administrative Berechtigungen: <input name="admin" type="checkbox">
<br><br>
<input type="submit" value="Nutzer Hinzufügen" name="add_user" style="font-size: 1.25em;">
</form>

<hr>

<form action="benutzer_bearbeiten.php" method="post" enctype="multipart/form-data">
Benutzername: 
<select name="user" onchange="adminBox();" id="user" required>
<?php
foreach ($user_table as $user) {
	print('<option value="');
	print($user["username"]);
	print('">');
	print($user["username"]);
	print("</option>\n");
}
?>
</select>
<br>
Passwort Verändern: <input name="change_password" type="checkbox" id="pw_change" onchange="pwBox();">
<br>
<span id="pw_field" style="visibility: hidden;">Neues Passwort: <input name="password" id="pw_box" type="password" required disabled></span>
<br>
Administrative Berechtigungen: <input name="admin" type="checkbox" id="admin">
<br><br>
<input type="submit" value="Nutzer Ändern" name="change_user" style="font-size: 1.25em;">
</form>

<hr>

<form action="benutzer_bearbeiten.php" method="post" enctype="multipart/form-data">
<select name="user_to_delete" required>
<option value="" selected>--Nutzer Auswählen--</option>
<?php
foreach ($user_table as $user) {
	print('<option value="');
	print($user["username"]);
	print('">');
	print($user["username"]);
	print("</option>\n");
}
?>
</select>
<input type="submit" value="Nutzer Löschen" name="delete_user" style="font-size: 1.25em;">
</form>

<script>
var user_admin_rel = <?php
$u_a_r = array();

foreach ($user_table as $user) {
	$u_a_r[$user["username"]] = boolval($user["is_admin"]);
}

print(json_encode($u_a_r));
?>;
function pwBox() {
	document.getElementById("pw_field").style.visibility = document.getElementById("pw_change").checked ? "visible" : "hidden";
	document.getElementById("pw_box").disabled = !document.getElementById("pw_change").checked;
	document.getElementById("pw_box").required = !document.getElementById("pw_change").checked;
}
pwBox();

function adminBox() {
  var user = "";
	var sel = document.getElementById("user");
	var user = sel.options[sel.selectedIndex].text;
	document.getElementById("admin").checked = user_admin_rel[user];
}
adminBox();
</script>

<hr>
<a class="anchorButton" href=".">Zurück zur Übersicht</a>
</body>
</html>