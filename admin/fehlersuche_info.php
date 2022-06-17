<?php session_start();?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8">
<title>S4 Benutzerinfo</title>
<link rel="stylesheet" href="admin.css?nocache=<?php print(sha1(file_get_contents("admin.css")));?>">
</head>
<body>
<pre>
<?php
print("Angemeldet: ");
print($_SESSION['logged_in']);
print("\n");
print("Admin: ");
print($_SESSION['is_admin']);
print("\n");
print("CSRF Token: ");
print($_SESSION['csrf_token']);
print("\n");
print("Name: ");
print($_SESSION['user']);
print("\n\n");
print("PHP Version ID: ");
print(PHP_VERSION_ID);
print("\n");

?>
</pre>
</body>
</html>