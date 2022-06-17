<?php
include 'authentifizierung.php';

/*if ($_SESSION['is_admin'] != true) {
	die("<h1>Unzureichende Berechtigungen</h1>");
}*/

if (isset($_POST["upload_img"])) {
	if (isset($_FILES["style"])) {
		$filename = $_POST['style_name'];
		$ext = pathinfo($_FILES['style']['name'], PATHINFO_EXTENSION);

		if (strpos($ext, 'php') !== false) {
			die("NOPE! Dies ist ein PHP Skript!");
		}

		if ($ext != pathinfo($filename, PATHINFO_EXTENSION)) {
			$filename = $filename . '.' . $ext;
		}
		if (!move_uploaded_file($_FILES['style']['tmp_name'], "../karte/style/" . $filename)) {die('<h1>Systemfehler</h1><p>Fehler beim beim verschieben der Datei!<br>Möglicherweise ist der Name zu lang oder enthält ungültige Zeichen.</p>');}
		header("Location: stile_verwalten.php");
	}
	else {
		die("<h1>Systemfehler</h1>\n<p>Felder Fehlen!</p>");
	}
}


if (isset($_POST["delete_style"])) {
	if (isset($_POST["style_name"])) {
		if (!unlink("../karte/styles/" . basename($_POST["style_name"]))) {die('<h1>Systemfehler</h1><p>Fehler beim beim löschen der Datei!</p>');}
		header("Location: stile_verwalten.php");
	}
	else {
		die("<h1>Systemfehler</h1>\n<p>Felder Fehlen!</p>");
	}
}

?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8">
<title>S4 Stile</title>
<link rel="stylesheet" href="admin.css?nocache=<?php print(sha1(file_get_contents("admin.css")));?>">
</head>
<body>

<h1>Stile Verwalten</h1>
<hr>

<h2>Stile löschen</h2>

<table>
<tr><th>Dateiname</th><th>Aktion</th></tr>
<?php
$styles = glob('../karte/styles/*.css');
natcasesort($styles);
foreach ($styles as $style) {	
	print('<td><span class="pic_name">');
	print(htmlspecialchars(basename($style)));
	print('</span></td>');
	
	print('<td><form action="stile_verwalten.php" method="post" enctype="multipart/form-data">');
	print('<input name="style_name" type="hidden" value="');
	print(basename($style));
	print('">');
	print('<input name="delete_style" type="submit" value="Löschen">');
	print('</form></td>');
	print("</tr>\n");
}
?>
</table>

<h2>Neuen Stil Hinzufügen</h2>

<form action="stile_verwalten.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="1000000">
<input id="file" onchange="fileBox();" type="file" accept="text/css" name="style">(max. 1MB)<br>
<label for="style_name">Stiel Name:</label>
<input id="filename" type="text" name="style_name"><br>
<input type="submit" name="upload_style" value="Bild Hochladen">
</form>

<script>
function fileBox() {
	var fileBox = document.getElementById("file");
	var nameBox = document.getElementById("filename");
	nameBox.value = fileBox.files[0].name;
}
</script>

<hr>
<a class="anchorButton" href=".">Zurück zur Übersicht</a>
</body>
</html>