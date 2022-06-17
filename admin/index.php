<?php
include 'authentifizierung.php';

if (isset($_POST["logout"])) {
	session_destroy();
	header("Location: .");
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8">
<title>S4 Admin</title>
<link rel="stylesheet" href="admin.css?nocache=<?php print(sha1(file_get_contents("admin.css")));?>">
</head>
<body>
<h1>Admin Panel</h1>
<form action="." method="post" enctype="multipart/form-data">
<input style="float: right;" type="submit" value="[Logout]" name="logout">
</form>
<p>Willkommen zurück, <?php print($_SESSION['user']);?>
</p>
<hr>

<h2>Speisekarten Optionen</h2>
<ul class="btn_list">
<li><a href="speisekarte_bearbeiten.php">Speisekarte Bearbeiten</a></li>
<li><a href="bilder_verwalten.php">Bilder Verwalten</a></li>
</ul>

<?php if($_SESSION['is_admin']): ?>
<hr>
<h2>System Optionen</h2>
<ul class="btn_list">
<li><a href="konfiguration_bearbeiten.php">Grundkonfiguration Bearbeiten</a></li>
<li><a href="benutzer_bearbeiten.php">Benutzer Bearbeiten</a></li>
</ul>
<?php endif ?>
</body>
</html>