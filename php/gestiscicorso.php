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
	<p>Ti trovi in: Gestione Corsi -> Informazioni Corso</p>
	</div>

    <div id="nav"> 
		<?php
			menu();
		?>
    </div>

	<div id="content"> 

	
	
		<?php
		//Pagina per la gestione di un corso
		
		//Controllo che l'utente abbia fatto il login, se sì esiste controllo la variabile Tipo per controllare se ha i diritti di Amministratore
		if (!isset($_SESSION['User']) || ($_SESSION['Tipo']) != "Admin") {
			
			echo '<p>Bisogna effettuare il login come amministratore per vedere questa pagina.';
		
		//Se e' stato scelto di cancellare il corso prenso il codice del corso e lo rimuovo
		} elseif ((isset($_GET['action'])) && isset($_POST['cancella']) ) {
			
			$codcorso = $_GET['action'];
			
			$conn = connessione();
			$sql = "DELETE FROM CORSO WHERE CodCorso = '".$codcorso."'";
			$conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
			if ($conn->affected_rows > 0) {
				echo '<p>Corso eliminato con successo <a href="gcorsi.php">Torna ai corsi</a>';
			} else {
				echo "<p>Errore durante l'eliminazione, il corso selezionato potrebbe non esistere";
			}
		
		//Se e' stato scelto di cancellare la prenotazione di una lezione del corso recupero il codice del corso e della lezioni e elimino la prenotazione corrispondente
		} elseif ((isset($_GET['action'])) && isset($_POST['cancpren']) ) {
			
			$codcorso = $_GET['action'];
			$codlezione = $_POST['codlezione'];
			$conn = connessione();
			$sql = "DELETE FROM LEZIONE WHERE LEZIONE.CodLezione = '".$codlezione."' AND LEZIONE.CodCorso = '".$codcorso."'";
			$conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
			if ($conn->affected_rows > 0) {
				echo '<p>Lezione eliminata con successo <a href="gestiscicorso.php?action='.$codcorso.'">Torna indietro</a>';
			} else {
				echo '<p>Errore durante la eliminazione, la prenotazione selezionata potrebbe non esistere <a href="gestiscicorso.php?action='.$codcorso.'">Torna indietro</a>';
			}
		
		//Quando apro la pagina recupero il codice del corso per poter estrarre dal DB le informazione del corso
		} elseif (isset($_GET['action'])) {
			
			$codcorso = ($_GET['action']);
			$istruttore = FALSE;
			$conn = connessione();
			$sql = "SELECT CORSO.Attivo, CORSO.CodCorso, CORSO.NomeCorso, CORSO.TipoCorso, PERSONA.Nome, PERSONA.Cognome
			FROM CORSO
			LEFT JOIN PERSONA ON CORSO.CodFiscale = PERSONA.CodFiscale WHERE CORSO.CodCorso ='".$codcorso."'";
			$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
		if ($result->num_rows > 0) {
			echo '<table width ="100%" border="0" align="center" cellpadding="5" cellspacing="2"><tr><th width="30%" colspan="2">Nome del Corso</th><th width="20%">Livello Corso</th><th width="20%">Istruttore</th><th width="20%">Corso Avviato</th></tr>';
			while($row = $result->fetch_assoc()) {
				echo '<td colspan="2">'.$row['NomeCorso'].'</td>';
				echo '<td>'.$row['TipoCorso'].'</td>';
				if ($row['Nome']) { $istruttore= TRUE; echo "<td>".$row['Nome']." ".$row['Cognome'];} else { echo '<td>Nessun Istruttore';}
				echo '</td><td>';
				$attivo = $row['Attivo'];
				if ($attivo == 1) { echo 'Attivo</td>'; } else { echo 'Non Attivo</td>'; }
			}
			echo '</tr></table>';
			$sql = "SELECT PRENOTAZIONE.CodLezione, PRENOTAZIONE.Data, PRENOTAZIONE.Ora, PRENOTAZIONE.CodCampo FROM PRENOTAZIONE WHERE PRENOTAZIONE.CodCorso ='".$codcorso."'  ORDER BY - PRENOTAZIONE.Data DESC, - PRENOTAZIONE.Ora DESC";
			$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
			if ($result->num_rows > 0) {
				echo '<br /><br /><br /><br /><table width ="100%" border="0" align="center" cellpadding="5" cellspacing="2"><tr><th width="25%" heigth="50px">Lez.N.</th><th width="25%">Campo N.</th><th width="25%">Data</th><th width="25%">Ora</th><th></th></tr>';
				$numlezione = 1;
				while($row = $result->fetch_assoc()) {
				if ($row['CodCampo'] != NULL) {
					echo '<tr><td>'.$numlezione.'</td><td>'.$row['CodCampo'].'</td><td>'.$row['Data'].'</td><td>'.$row['Ora'].'</td><td>';
				$numlezione++;
				}
			}
			} else { echo '<tr><td height="50px"colspan="5">Lezioni non ancora prenotate.</td></tr>';}
			echo '</table>';
		
		
		
		echo '<p><br /><a href="iscritticorso.php?action='.$codcorso.'">Vedi Tutti gli iscritti</a></p>';
		if ($istruttore) {  echo '<p><br /><a href="prenotalezione.php?action='.$codcorso.'">Aggiungi una nuova lezione</a></p>';
		} else {
			echo '<p><br />Aggiungi una nuova lezione. Bisogna scegliere l\'istruttore prima di prenotare le lezioni</p>';
		}
		echo '<p><br /><a href="modificacorso.php?action='.$codcorso.'">Modifica informazioni del corso</a></p>';
		echo '<p><br /><a href="gcorsi.php">Torna Indietro</a></p>';
		
		echo '<br /><br /><br /><br /><br /><p><form action="" method="POST"><input name="cancella" type="submit" value="Cancella Corso"></form> attenzione, non annullabile. Cancella tutte le informazioni del corso.</p>';
		} else { echo '<p>Errone nel link <a href="gcorsi.php">torna indietro.</a></p>'; }
		} else {
		
		echo '<p>Errone nel link <a href="gcorsi.php">torna indietro.</a></p>';
		
		}
		
		?>
		
		
		
	</div>
</body>

</html>
