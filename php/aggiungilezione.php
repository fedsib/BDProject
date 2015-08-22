<?php 
	session_start();
	require "./cgi-bin/phpfunctions.php" 
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
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
	<p>Ti trovi in: Gestione Corsi -> Aggiungi Lezione</p>
	</div>

    <div id="nav"> 
		<?php
			menu();
		?>
    </div>

	<div id="content"> 

	
		<?php
		//Pagina per l'aggiunta di lezioni ad un corso
		
		//Controllo che l'utente abbia fatto il login, se sì esiste controllo la variabile Tipo per controllare se ha i diritti di Amministratore
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
