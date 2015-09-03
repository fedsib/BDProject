<?php 
	session_start();
	require "../cgi-bin/phpfunctions.php" 
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head> 		
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"  />
	<title>Progetto Basi di Dati</title>
	<meta name="language" content="italian it" />
	<link type="text/css" rel="stylesheet" href="../style/screen-style.css" media="screen" />
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
	<p>Ti trovi in: Gestione Corsi -> Prenota Lezione</p>
	</div>

    <div id="nav"> 
		<?php
			menu();
		?>
    </div>

	<div id="content"> 

	
	
		<?php
		//Pagina per la gestione degli utenti iscritti ad un corso
		
		//Controllo che sia stato effettuato il login e che sia un amministratore
		if (!isset($_SESSION['User']) || ($_SESSION['Tipo']) != "Admin") {
			
			echo '<p>Bisogna effettuare il login come amministratore per vedere questa pagina.';
		//Se e' stato fatto il login ed e' stato inviato il form per cancellare un utente recupero il codice del corso e dell'utente e lo elimino dalla tabella degli iscritti
		} elseif (isset($_POST['aggiungi'])) {
		
		$conn = connessione();
			$corso = $_POST['corso'];
			$data = $_POST['data'];
			$ora = $_POST['ora'];
			$campo = $_POST['campo'];
			$data = date('Y-m-d', strtotime("$data"));
			//Seleziono il codice fiscale dell'istruttore che segue il corso per controllare la sua disponibilita'
			$sql = "SELECT CORSO.CodFiscale FROM CORSO WHERE CORSO.CodCorso = '$corso'";
			$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
			if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$cod = $row['CodFiscale'];
			}
				//Controllo che l'istruttore non sia gia' occupato in quella data e aggiungo la prentoazione se libero
				$sql = "SELECT ControlloPrenotazioneCorso('$cod','$data','$ora','$campo','$corso')";
				$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
				if ($result->num_rows > 0) {
				while($row = $result->fetch_row()) {
				echo $row[0];
				}
				};
				echo ' <a href="gestiscicorso.php?action='.$corso.'">Torna Indietro</a>';
			} else {
				echo '<p>Errore, bisogna che ci sia un istruttore per prenotare le lezioni <a href="gestiscicorso.php?action='.$corso.'">Torna Indietro</a></p>';
			}
			
		
		} else {
			//Mostro un form per l'aggiunta delle prenotazioni delle lezioni, prima faccio scegliere il campo ed il giorno poi controllo le ore e mostro quali sono libere
			$corso = $_GET['action'];
			
			$conn = connessione();
			
			if (isset($_GET['data']) AND isset($_GET['campo'])) {
			$data = $_GET['data'];
			$campo = $_GET['campo'];
			//Mostra le ore disponibili nel campo e data scelta
			echo '<form action="" method="POST"><table width ="100%" border="0" align="center" cellpadding="5" cellspacing="2"><tr><th>Scegli Ora</th></tr>';
			echo '<tr><td>Prenota Campo: '.$campo.' per il giorno '.$data.' alle : <select name="ora">';
			$datapren = date('Y-m-d', strtotime("$data"));
			for ($y = 9; $y < 18; $y++) { 
				$sql = "SELECT PRENOTAZIONE.CodCampo 
				FROM 
				PRENOTAZIONE 
				WHERE PRENOTAZIONE.CodCampo = '$campo' AND PRENOTAZIONE.Data = '$datapren' AND PRENOTAZIONE.Ora = '$y'";
				$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
				if (!($result->num_rows)) {
				if ($y < 10) {	
					$orario = "0".$y.".00";
				} else {
					$orario = $y.".00";
				}
				echo '<option value="'.$y.'">'.$orario.'</option>';
				}
			}
			echo '</select></td></tr>';
			echo '<tr><td>';
			echo '<input type="hidden" name="campo" value="'.$campo.'" readonly="readonly"></input>';
			echo '<input type="hidden" name="data" value="'.$data.'" readonly="readonly"></input>';
			echo '<input type="hidden" name="corso" value="'.$corso.'" readonly="readonly"></input>';
			echo '<button name="aggiungi">Effettua Prenotazione</button></td></tr></form></table><br /><br /><br /><br />';
			}
			
			//Form per la scelta del campo e della data di cui si vuole vedere le ore disponibili
			echo '<form action="" method="get"><table width ="100%" border="0" align="center" cellpadding="5" cellspacing="2"><tr><th colspan="4">Cerca ore disponibili</th></tr>';
			echo '<tr><td>Campo</td><td>';
			
			$sql = "SELECT CodCampo FROM CAMPO";
			$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
			if ($result->num_rows > 0) {
				echo '<select name ="campo">';
			while($row = $result->fetch_assoc()) {
				echo '<option value'.$row['CodCampo'].'>'.$row['CodCampo'].'</option>';
			}
			}
		
			echo '</td><td>Giorno</td><td>';
			echo '<select name ="data">';
			$datacal = date("d-m-Y");
			for ($y = 1; $y <= 30; $y++) {
			$datacal = date('d-m-Y', strtotime("$datacal +1 day"));
			echo '<option value'.$datacal.'>'.$datacal.'</option>';
			}
			echo '</select></td></tr>';
			echo '<tr><td colspan="4"><input name ="action" type="hidden" readonly="readonly" value="'.$corso.'"></input><input type ="submit" value="Vedi Ore Disponibili" /></td></tr></form></table>';
			
		}
		
		
		
		
		
		?>
		
		
		
	</div>
</body>

</html>