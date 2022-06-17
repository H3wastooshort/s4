<?php
include 'authentifizierung.php';

/*if ($_SESSION['is_admin'] != true) {
	die("<h1>Unzureichende Berechtigungen</h1>");
}*/

if (isset($_POST["upload_img"])) {
	if (isset($_FILES["image"])) {
		$filename = $_POST['img_name'];
		$ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

		if (strpos($ext, 'php') !== false) {
			die("NOPE! Dies ist ein PHP Skript!");
		}

		if ($ext != pathinfo($filename, PATHINFO_EXTENSION)) {
			$filename = $filename . '.' . $ext;
		}
		if (!move_uploaded_file($_FILES['image']['tmp_name'], "../karte/bilder/" . $filename)) {die('<h1>Systemfehler</h1><p>Fehler beim beim verschieben der Datei!<br>Möglicherweise ist der Name zu lang oder enthält ungültige Zeichen.</p>');}
		header("Location: bilder_verwalten.php");
	}
	else {
		die("<h1>Systemfehler</h1>\n<p>Felder Fehlen!</p>");
	}
}


if (isset($_POST["delete_img"])) {
	if (isset($_POST["img_name"])) {
		if (!unlink("../karte/bilder/" . basename($_POST["img_name"]))) {die('<h1>Systemfehler</h1><p>Fehler beim beim löschen der Datei!</p>');}
		header("Location: bilder_verwalten.php");
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
<title>S4 Bilder</title>
<link rel="stylesheet" href="admin.css?nocache=<?php print(sha1(file_get_contents("admin.css")));?>">
</head>
<body>

<h1>Bilder Verwalten</h1>
<hr>

<h2>Bilder ansehen und löschen</h2>

<table>
<tr><th>Bild</th><th>Dateiname</th><th>Aktion</th></tr>
<?php
$images = glob('../karte/bilder/*.*');
natcasesort($images);
foreach ($images as $img) {
	print('<tr>');
	print('<td><img loading="lazy" class="table_pic" src="');
	print("../karte/bilder/" . urlencode(basename($img)));
	print('"></td>');
	
	print('<td><span onclick="copyName(this);" class="pic_name">');
	print(htmlspecialchars(basename($img)));
	print('</span></td>');
	
	print('<td><form action="bilder_verwalten.php" method="post" enctype="multipart/form-data">');
	print('<input name="img_name" type="hidden" value="');
	print(basename($img));
	print('">');
	print('<input name="delete_img" type="submit" value="Löschen">');
	print('</form></td>');
	print("</tr>\n");
}
?>
</table>

<script>
function copyName(e) {
  try {
    navigator.clipboard.writeText(e.innerText);
    alert('Der Dateiname "' + e.innerText + '" wurde in die Zwischenabage kopiert.');
  }
  catch (f) {
    if (document.body.createTextRange) {
      var sel_r = document.body.createTextRange();
      sel_r.moveToElementText(e);
      sel_r.select();
    }
  }
}
</script>

<h2>Neues Bild Hinzufügen</h2>

<form action="bilder_verwalten.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="25000000">
<input id="file" onchange="fileBox();" type="file" accept="image/*" name="image">(max. 25MB)<br>
<label for="img_name">Bild Name:</label>
<input id="filename" type="text" name="img_name"><br>
<input type="submit" name="upload_img" value="Bild Hochladen">
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