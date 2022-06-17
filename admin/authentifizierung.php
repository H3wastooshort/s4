<?php
session_set_cookie_params([
    'lifetime' => 172800,
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Strict'
]);
session_start();

$auth = false;
if (isset($_SESSION['logged_in'])) {
	if ($_SESSION['logged_in'] == true) {
		$auth = true;
	}
}
if ($auth == false) {
	http_response_code(401);
	header("Location: anmeldung.php");
	die("<h1>Nicht authentifiziert!</h2>\n<p>Bitte melden sie sich hier an: <a href=\"anmeldung.php\">anmeldung.php</a></p>");
}

function checkCSRF() {//nicht sicher ob das nützlich ist
	if ($_SESSION['csrf_token'] != $_POST["csrf"] and $_SESSION['csrf_token'] != $_GET["csrf"]) {
		die("<h1>Mögiche CSRF erkannt.</h1>\n<p>Der CSRF-Schutz Token wurde nicht oder inkorrekt mitgesendet. Dies könnte ein Angriffsversuch sein.<br>Änderungen wurden nicht übernommen.</p>");
	}
}

//<input type="hidden" name="csrf" value="< ?php print($_SESSION['csrf_token']); ? >">
?>