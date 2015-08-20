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
	
		
		} elseif ((isset($_GET['action'])) && isset($_POST['cancella']) ) {
			
			$codcorso = $_GET['action'];
			
			$conn = connessione();
			$sql = "DELETE FROM CORSO WHERE CodCorso = '".$codcorso."'";
			$conn->query($sql) or die("Errore nella query MySQL");
			if ($conn->affected_rows > 0) {
				echo '<p>Corso eliminato con successo <a href="gcorsi.php">Torna ai corsi</a>';
			} else {
				echo "<p>Errore durante l'eliminazione, il corso selezionato potrebbe non esistere";
			}
		
		} elseif ((isset($_GET['action'])) && isset($_POST['canclez']) ) {
		
			$codcorso = $_GET['action'];
			$codlezione = $_POST['codlezione'];
			$conn = connessione();
			$sql = "DELETE FROM LEZIONE WHERE LEZIONE.CodLezione = '".$codlezione."' AND LEZIONE.CodCorso = '".$codcorso."'";
			$conn->query($sql) or die("Errore nella query MySQL");
			if ($conn->affected_rows > 0) {
				echo '<p>Lezione eliminata con successo <a href="gestiscicorso.php?action='.$codcorso.'">Torna indietro</a>';
			} else {
				echo '<p>Errore durante la eliminazione, la lezioni selezionata potrebbe non esistere <a href="gestiscicorso.php?action='.$codcorso.'">Torna indietro</a>';
			}
		
		} elseif ((isset($_GET['action'])) && isset($_POST['cancpren']) ) {
			
			$codcorso = $_GET['action'];
			$codlezione = $_POST['codlezione'];
			$conn = connessione();
			$sql = "DELETE FROM PRENOTAZIONE WHERE PRENOTAZIONE.CodLezione = '".$codlezione."' AND PRENOTAZIONE.CodCorso = '".$codcorso."'";
			$conn->query($sql) or die("Errore nella query MySQL");
			if ($conn->affected_rows > 0) {
				echo '<p>Prenotazione eliminata con successo <a href="gestiscicorso.php?action='.$codcorso.'">Torna indietro</a>';
			} else {
				echo '<p>Errore durante la eliminazione, la prenotazione selezionata potrebbe non esistere <a href="gestiscicorso.php?action='.$codcorso.'">Torna indietro</a>';
			}
			
		} elseif (isset($_GET['action'])) {
			
			$codcorso = ($_GET['action']);
			$conn = connessione();
			$sql = "SELECT CORSO.Attivo, CORSO.CodCorso, CORSO.NomeCorso, CORSO.TipoCorso, PERSONA.Nome, PERSONA.Cognome
			FROM CORSO
			LEFT JOIN PERSONA ON CORSO.CodFiscale = PERSONA.CodFiscale WHERE CORSO.CodCorso ='".$codcorso."'";
			$result = $conn->query($sql) or die("Errore nella query MySQL");
		if ($result->num_rows > 0) {
			echo '<table width ="100%" border="0" align="center" cellpadding="5" cellspacing="2"><tr><th width="40%" colspan="2">Nome del Corso</th><th width="20%">Livello Corso</th><th width="20%">Istruttore</th><th width="20%"></th></tr>';
			while($row = $result->fetch_assoc()) {
				echo '<td colspan="2">'.$row['NomeCorso'].'</td>';
				echo '<td>'.$row['TipoCorso'].'</td>';
				if ($row['Nome']) {echo "<td>".$row['Nome']." ".$row['Cognome'];} else { echo '<td>Nessun Istruttore';}
				echo '</td>';
				$attivo = $row['Attivo'];
				if ($attivo == 1) { echo '<td>Attivo</td>'; } else { echo '<td>Non Attivo</td>'; }
			}
			
			$sql = "SELECT LEZIONE.CodLezione, PRENOTAZIONE.Data, PRENOTAZIONE.Ora, PRENOTAZIONE.CodCampo FROM LEZIONE LEFT JOIN PRENOTAZIONE ON (LEZIONE.CodLezione = PRENOTAZIONE.CodLezione AND LEZIONE.CodCorso = PRENOTAZIONE.CodCorso) WHERE LEZIONE.CodCorso ='".$codcorso."' ORDER BY LEZIONE.CodLezione";
			$result = $conn->query($sql) or die("Errore nella query MySQL");
			if ($result->num_rows > 0) {
				echo '<tr><th width="25%">Lez.N.</th><th width="25%">Campo N.</th><th width="25%">Data</th><th width="25%">Ora</th><th></th></tr>';
				while($row = $result->fetch_assoc()) {
				if ($row['CodCampo'] != NULL) {
					echo '<tr><td>'.$row['CodLezione'].'</td><td>'.$row['CodCampo'].'</td><td>'.$row['Data'].'</td><td>'.$row['Ora'].'</td><td>
					<form action="" method="post"><input name="codlezione" type="hidden" readonly="readonly" value="'.$row['CodLezione'].'"></input><input name="cancpren" type="submit" value="Cancella Prenotazione"/></form></td></tr>';
				} else { echo '<tr><td>'.$row['CodLezione'].'</td><td colspan="2">Lezione non ancora prenotata</td><td><a href="prenotalezione.php?action='.$codcorso.'&action1='.$row['CodLezione'].'">Prenota Lezione</a></td><td><form action="" method="post"><input name="codlezione" type="hidden" readonly="readonly" value="'.$row['CodLezione'].'"></input><input name="canclez" type="submit" value="Cancella Lezione"/></form></td></tr>';}
				}
			} else { echo '<tr><td height="50px"colspan="5">Lezioni non ancora disponibili.</td></tr>';}
			echo '</table>';
		
		
		
		echo '<br /><a href="iscritticorso.php?action='.$codcorso.'">Vedi Tutti gli iscritti</a>';
		echo '<br /><a href="aggiungilezione.php?action='.$codcorso.'">Aggiungi Lezioni</a>';
		echo '<br /><a href="modificacorso.php?action='.$codcorso.'">Modifica informazioni corso</a>';
		
		
		echo '<br /><br /><br /><br /><br /><p><form action="" method="POST"><input name="cancella" type="submit" value="Cancella Corso"></form> attenzione, non annullabile. Cancella tutte le informazioni del corso.</p>';
		} else { echo '<p>Errone nel link <a href="gcorsi.php">torna indietro.</a></p>'; }
		} else {
		
		echo '<p>Errone nel link <a href="gcorsi.php">torna indietro.</a></p>';
		
		}
		
		?>
		
		
		
	</div>
</body>

</html>
