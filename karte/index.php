<?php
$conf_file = file_get_contents("../admin/konfiguration.json");
if ($conf_file == false) {
	http_response_code(500);
	die("<h1>Systemfehler!</h1>\n<p>Die Konfigurationsdatei konnte nicht gelesen werden!</p>");
}
$config = json_decode($conf_file, true);


$mysqli = new mysqli($config["sql_server"], $config["sql_user"], $config["sql_password"], $config["sql_database"]);

if ($mysqli->connect_errno) {
	http_response_code(500);
	die("<h1>Systemfehler</h1>\n<h2>Fehler beim Verbindungsaufbau mit der Datenbank</h2>\n<p>" . $mysqli->connect_error . "</p>");
}

$result = $mysqli->query('SELECT * FROM s4_karte');
$f_array = $result->fetch_all(MYSQLI_ASSOC);

//Locaiton related things
$l_currency = $config['l_currency'];

//Restaurant branding
$r_name = $config['r_name'];
$r_logo = $config['r_logo'];
$r_css = $config['r_css'];


//Meta
$result = $mysqli->query('SELECT * FROM `s4_seiten`');

$c_pages =  $result->fetch_all(MYSQLI_ASSOC);
usort($c_pages, function($a, $b) {
    return $a['p_num'] <=> $b['p_num'];
});
$c_max_page = count($c_pages);


//Each Entry contains a Dictionary wich includes the following keys:
/*
 * f_name (string): Name of the Food
 * f_price (float): Price of the food
 * f_num (int): Ordering Number of food
 * f_desc (string): Description of Food (html)
 * f_pid (int): ID of the Page the Food should appear in
 * f_img (string): URL to an image of the food
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 */
 
 /*
$f_array =
[
	array(
		"f_name" => "Maxi-Mampf",
		"f_price" => 9.99,
		"f_num" => 44,
		"f_desc" => "Viel Futter für wenig Geld",
		"f_pid" => 0,
		"f_img" => "bilder/maxi.jpg"
	),
	array(
		"f_name" => "Medi-Mampf",
		"f_price" => 5.99,
		"f_num" => 23,
		"f_desc" => "Futter für Geld",
		"f_pid" => 0,
		"f_img" => "bilder/medi.jpg"
	),
	array(
		"f_name" => "Mini-Mampf",
		"f_price" => 3.99,
		"f_num" => 10,
		"f_desc" => "Futter für wenig Geld",
		"f_pid" => 0
	),

	array(
		"f_name" => "Maxi-Schlürf",
		"f_price" => 2.99,
		"f_num" => 104,
		"f_desc" => "Viel Schlürf für wenig Geld",
		"f_pid" => 1,
		"f_img" => "bilder/schlürf1.jpg"
	),
	array(
		"f_name" => "Medi-Schlürf",
		"f_price" => 1.99,
		"f_num" => 103,
		"f_desc" => "Viel Schlürf für wenig Geld",
		"f_pid" => 1,
		"f_img" => "bilder/schlürf2.jpg"
	)
];
*/

//page number
$c_page = 0;
if (isset($_GET["seite"])) {
	$page = intval($_GET["seite"]);
	if (is_int($page)) {
		$c_page = $page;
	}
}

?>

<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?php print($r_name); ?></title>
<link rel="icon" href=<?php print($r_logo);?>>
<link rel="stylesheet" href="<?php print("styles/"); print($r_css); print("?"); print(sha1(file_get_contents("styles/" . $r_css)));?>">
</head>
<body>
<h1><img src="<?php print($r_logo);?>" class="r_logo" loading="lazy"> <?php print($r_name); ?></h1>

<ul class="page_sel">
<?php
for ($p = 0; $p < $c_max_page; $p++) {
	print("<li");
	if ($p == $c_page) {
		print(" class=\"c_page\" ");
	}
	print("><a href=\"?seite=");
	print($p);
	print("\">");
	print($c_pages[$p]["p_name"]);
	print("</a></li>\n");
}
?>
</ul>

<br>

<table class="card">
<tr><th>Nummer</th> <th>Gericht</th> <th>Preis</th></tr>
<?php
foreach($f_array as $f_entry) {
	if ($f_entry["f_pid"] == $c_page) {
		print("<tr class=\"f_row\" onclick=\"showImage(");
		print($f_entry["f_num"]);
		print(");\"> ");
		
		print("<td><p class=\"f_num\">");
		print($f_entry["f_num"]);
		print("</p></td> ");
		
		print("<td><h3 class=\"f_name\">");
		print($f_entry["f_name"]);
		print("</h3> <p class=\"f_desc\">");
		print($f_entry["f_desc"]);
		print("</p></td> ");
		
		print("<td><p class=\"f_price\">");
		print($f_entry["f_price"]);
		print($l_currency);
		print("</p></td> ");
		
		print("</tr>\n");
	}
}
?>
</table>

<script>
var f_images = <?php
$f_img_arr = [];

foreach($f_array as $f_entry) {
	if (isset($f_entry["f_img"])) {
		$f_img_arr += array($f_entry["f_num"] => "bilder/" . urlencode($f_entry["f_img"]));
	}
}
print(json_encode($f_img_arr));
?>;

function showImage(num) {
	//There must be a better way to do this
	var image = "bilder/kein_bild.jpg";
	
	try {
		image = f_images[num];
	}
	catch (f) {}
	
	if (!(typeof image === 'string' || image instanceof String)) {
		image = "bilder/kein_bild.jpg";
	}
	
	var pic_box = document.createElement("div");
	
	var pic = document.createElement("img");
	pic.src = image;
	pic_box.appendChild(pic);
	
	var br = document.createElement("br");
	pic_box.appendChild(br);

	var btn = document.createElement("button");
	btn.setAttribute("onclick", "closeImage();");
	btn.innerText = "Bild Schließen";
	pic_box.appendChild(btn);
	
	var wrap_box = document.createElement("div");
	wrap_box.className = "f_img";
	wrap_box.appendChild(pic_box);
	
	document.body.appendChild(wrap_box);
}

function closeImage() {
	try {
		document.getElementsByClassName("f_img").item(0).remove(); //this seems janky but if for some reason there are multiple boxes, you can still close them all
	}
	catch (f) {//just so it wont block the page if sth goes wrong
		window.location.reload();
	}
}
</script>
</body>
</html>