<?php
session_set_cookie_params([
    'lifetime' => 172800,
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Strict'
]);
session_start();

if (isset($_SESSION['logged_in'])) {
	if ($_SESSION['logged_in'] == true) {
		header("Location: .");
	}
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

if (isset($_POST["login"])) {
	if (isset($_POST["user"]) and isset($_POST["password"])) {
		$sql = "SELECT * FROM `s4_users` WHERE `username` = ?";
		$statement = $mysqli->prepare($sql);
		$statement->bind_param("s", $_POST["user"]);
		$statement->bind_result($user, $pass_hash, $is_admin);
		if (!($statement->execute() and $statement->fetch())) {
			echo "SQL QUERY ERROR!\n".$statement->error;
		}
		
		if (isset($user)) {
			if ($user == $_POST["user"]) {
				if (password_verify($_POST["password"], $pass_hash)) {
					$_SESSION['logged_in'] = true;
					$_SESSION['is_admin'] = $is_admin;
					$_SESSION['user'] = $_POST["user"];
					$_SESSION['csrf_token'] = random_int(0, 9999999999);
					session_commit();
					header("Location: .");
					
				}
				else {
					header("Location: anmeldung.php?falsch");
				}
			}
			else {
				header("Location: anmeldung.php?falsch");
			}
		}
		else {
			header("Location: anmeldung.php?falsch");
		}
	}
	else {
		header("Content-Type: text/plain");
		die("Felder Fehlen. Dies kann sowohl ein Problem des Systems, als auch ihres Endgerätes sein.\nVersuchen sie es erneut mit einem anderen Browser oder Gerät. Ansonsten kontaktieren sie bitte den Support.");
	}
}

?><!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8">
<title>S4 Anmeldung</title>
<link rel="stylesheet" href="admin.css?nocache=<?php print(sha1(file_get_contents("admin.css")));?>">
</head>
<body>
<?php if (isset($_GET["falsch"])): ?>
<p style="color: red">Ihr Anmeldeversuch ist fehlgeschlagen. Bitte vergewissern sie sich, dass Nutzername und Passwort korrekt sind.</p>
<?php endif ?>
<h1>Anmeldung</h1>
<p>Bitte melden sie sich an.</p>
<hr>

<form action="anmeldung.php" method="post" enctype="multipart/form-data">
<label for="user">Benutzername:</label> <input name="user" autocomplete="username" spellcheck="false" autocorrect="off" required>
<br>
<label for="password">Passwort:</label> <input name="password" type="password" autocomplete="current-password" maxlength=72 required>
<br><br>
<input type="submit" value="Anmelden" name="login" style="font-size: 1.25em;">
</form>

<script>
if (window.location.protocol == "http:") {
alert("Diese Seite wurde mit http:// aufgerufen. Dies ist unsicher.\n\nBitte benutzen sie https:// damit ihr Passwort verschlüsselt übertragen wird. ");
}
</script>

</body>
</html>