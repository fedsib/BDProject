<?php 
	session_start();
	require "./functions/phpfunctions.php" 
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">

<head> 		
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"  />
	<title>Progetto Basi di Dati</title>
	<meta name="language" content="italian it" />
	<link type="text/css" rel="stylesheet" href="./style/screen-style.css" media="screen" />

</head>

<body>
	<div id="header"> 

	</div> 

	<div id="barrasup">
	<p id="login">
		<?php
			loginlink();
		?>
	</p>
	<p>Ti trovi in: <span xml:lang="en">Home</span></p>

	</div>

    <div id="nav"> 
		<?php
			menu();
		?>
    </div>

	<div id="content"> 

	
	
		<?php
		if (!isset($_SESSION['User']) || ($_SESSION['User']) != "admin") {
			
			echo '<p>Bisogna effettuare il login come amministratore per vedere questa pagina.';
	
		} elseif ((isset($_GET['action'])) && isset($_POST['aggiungi'])) {
			
			$codcorso = ($_GET['action']);
			$numlezioni = ($_POST['lezioni']);
			
			$conn = connessione();
			$sql = "INSERT INTO LEZIONI (CodCorso, CodLezione)";
			$result = $conn->query($sql) or die("Errore nella query MySQL");
			
			
		} elseif (isset($_GET['action'])) {
			
			$codcorso = ($_GET['action']);
			
			echo '<table width="400" border="0" align="center" cellpadding="10" cellspacing="5" class="Table">
			<form action="" method="post">
			<tr><td>Seleziona num. Lezioni:<select name="lezioni">';
			for ($x = 0; $x <= 20; $x++) {
			echo '<option value="'.$x.'">'.$x.'</option>';
			}
			echo '</select></td></tr>';
			echo '<tr><td><input name"codcorso" type="hidden" readonly="readonly" value="'.$codcorso.'"></input><input name="crea" type="submit" value="Aggiungi le lezioni"/>';
			
		} else {
		
		echo '<p>Errone nel link <a href="gcorsi.php">torna indietro.</a></p>';
		
		}
		
		?>
		
		
		
	</div>
</body>

</html>
