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

if (isset($_POST["init"])) { //decimal type untested
	$sql = 'CREATE TABLE `s4_karte` (
 `f_num` int(11) NOT NULL,
 `f_name` text NOT NULL,
 `f_desc` text NOT NULL,
 `f_price` decimal(5,2)  NOT NULL,
 `f_pid` int(11) NOT NULL,
 `f_img` text NOT NULL,
 UNIQUE KEY `f_num` (`f_num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

CREATE TABLE `s4_seiten` (
 `p_num` int(11) NOT NULL,
 `p_name` text NOT NULL,
 `p_desc` text NOT NULL,
 UNIQUE KEY `p_num` (`p_num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `s4_users` (
 `username` text NOT NULL,
 `password` text NOT NULL,
 `is_admin` tinyint(1) NOT NULL,
 UNIQUE KEY `username` (`username`) USING HASH
) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
	$statement = $mysqli->prepare($sql);
	if(!$statement->execute()) {
		die("SQL QUERY ERROR!\n".$statement->error);
	}
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8">
<title>SQL Initialisierung</title>
<link rel="stylesheet" href="admin.css?nocache=<?php print(sha1(file_get_contents("admin.css")));?>">
</head>
<body>
<h1>SQL Initialisierung</h1>
<p>Führen sie dies nur aus, wenn die Tabellen noch nicht eingerichtet wurden</p>
<hr>
<form action="initialisierung.php" method="post" enctype="multipart/form-data">
<input type="submit" value="SQL Initialisierung" name="init">
</form>
</body>
</html>