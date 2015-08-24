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
	<p>Ti trovi in: Prenotazione Campi</p>
	</div>

    <div id="nav"> 
		<?php
			menu();
		?>
    </div>

	<div id="content"> 

	
	
		<?php
		//Pagina per la gestione delle prenotazioni personali
		
		//Controllo che sia stati fatto il login
		if (!isset($_SESSION['User'])) {
			
			echo '<p>Bisogna effettuare il login come utente od amministratore per vedere questa pagina.';
			
		//Se si sceglie di cancellare una delle proprio prenotazioni la elimino dal DB
		} elseif (isset($_POST['Cancella'])) {
		$data = $_POST['data'];
		$ora = $_POST['ora'];
		$campo = $_POST['campo'];
		
		$conn = connessione();
		$sql = "DELETE FROM PRENOTAZIONE WHERE CodCampo='$campo' AND Data='$data' AND Ora='$ora'";
		$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
		echo 'Prenotazione Cancellata con successo.<br />
		<a href="prenotazione.php">Torna Indietro</a>';
		
		//Quando selezione la prenotazione da fare la aggiungo al DB
		} elseif (isset($_POST['Prenotazione'])) {
			
			$conn = connessione();
			$sql = "SELECT PERSONA.CodFiscale FROM ACCOUNT JOIN PERSONA ON ACCOUNT.CodFiscale = PERSONA.CodFiscale WHERE ACCOUNT.UserName = '".$_SESSION['User']."'";
			$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
			if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$cod = $row['CodFiscale'];
			}
				$campo = $_POST['campo'];
				$data = $_POST['data'];
				$ora = $_POST['ora'];
				$sql = "SELECT ControlloPrenotazione('$cod','$data','$ora','$campo')";
				$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
				if ($result->num_rows > 0) {
				while($row = $result->fetch_row()) {
				echo $row[0];
				}
				};
				
			} else {
				echo 'Errore utente non trovato, prova a rieffettuare il login<br /><a href="logout.php">Torna Indietro</a>';
			}
		//Se scelgo di effettuare una nuova prenotazione mostro la pagina col calendario e le possibilita' di scelta	
		} elseif ((isset($_GET['action'])) && ($_GET['action'] == 'nuova') ) {
			
			$dataoggi = date('Y-m-d');
			$tipocampi = array();
			
			$conn = connessione();
			$sql = "SELECT CAMPO.TipoSup FROM CAMPO";
				$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
			if (($result->num_rows) > 0) {
				$numrighe = $result->num_rows;
				while($row = $result->fetch_assoc()) {
					array_push($tipocampi, $row['TipoSup']);
				}
			}
			
			echo '<table width ="100%" border="0" align="center" cellpadding="5" cellspacing="2"">';
			for ($x = 1; $x <= $numrighe; $x++) {
				$datacal = date('Y-m-d', strtotime("$dataoggi +1 day"));
				echo '<tr><th colspan="7" style="height: 30px;">Prenotazioni per il campo n.'.($x).' in '. $tipocampi[$x-1].' nel giorno</th></tr><tr>';
				for ($y = 1; $y <= 7; $y++) {
					$nodisp = true;
					echo '<td style="height: 20px; padding-bottom:30px;">'.date('d-m-Y', strtotime("$datacal")).'<br />
					<form action="" method="post">
						<input type="hidden" value="'.$x.'" name="campo">
						<input type="hidden" value="'.$datacal.'" name="data">
						<select name="ora">';
					for ($z = 9; $z <= 17; $z++) {
						$sql = "SELECT PRENOTAZIONE.Data, PRENOTAZIONE.Ora, PRENOTAZIONE.CodCampo 
						FROM 
						PRENOTAZIONE 
						WHERE PRENOTAZIONE.CodCampo = '$x' AND PRENOTAZIONE.Data = '$datacal' AND PRENOTAZIONE.Ora = '$z'";
						$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
						if (!($result->num_rows)) {
							if ($z < 10) {	
								$orario = "0".$z.".00";
							} else {
								$orario = $z.".00";
							}
							
						echo '<option value="'.$z.'">'.$orario.'</option>';
						$nodisp = false;
						}
						
					}
					if ($nodisp) {echo '</select><br />Nessuna Disponibilità.';
					} else {
					echo ' </select><input name="Prenotazione" type="submit" value="Prenota"/></form></td>';
					}
				$datacal = date('Y-m-d', strtotime("$datacal +1 day"));
				} 
				echo '</tr>';
			} 
			chiusura($conn);
			echo '</table><br /><form action="prenotazione.php"><button >Torna Indietro</button></form>';
			//Se apro solo la pagina mostro la lista delle prenotazioni dell'utente che attualmetne vede la pagina
		} else {

		$dataoggi = date("Y-m-d");
		
		$conn = connessione();
		$sql = "SELECT PRENOTAZIONE.Data, PRENOTAZIONE.Ora, PRENOTAZIONE.CodCampo 
		FROM 
		ACCOUNT 
		JOIN PRENOTAZIONE ON ACCOUNT.CodFiscale=PRENOTAZIONE.CodFiscale
		WHERE ACCOUNT.UserName ='".$_SESSION['User']."' AND PRENOTAZIONE.Data >= '$dataoggi' ORDER BY PRENOTAZIONE.Data, PRENOTAZIONE.Ora";
		$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
		$numrighe = $result->num_rows;
		echo "Attenzione si possono avere solo 7 prenotazioni attive.<br /><br />";
		echo "<table>";
		if ($result->num_rows > 0) {
			echo '<table width ="100%" border="0" align="center" cellpadding="5" cellspacing="2"> 
			<tr><td width="25%">Data</td><td width="25%">Ora</td><td width="25%">Campo</td><td width="25%"> </td></tr>';
		while($row = $result->fetch_array()) {
			echo '<tr><form action="" method="post">
			<td width="25%">
			<input  type="text" name="data" value="'.$row['Data'].'" readonly="readonly"></input></td><td width="25%">
			<input  type="text" name="ora" value="'.$row['Ora'].'" readonly="readonly"></input></td><td width="25%">
			<input  type="text" name="campo" value="'.$row['CodCampo'].'" readonly="readonly"></input></td><td width="25%">';
			if ($row['Data'] == $dataoggi) { 
				echo "Non puoi eliminiare questa prenotazione";
			} else {
				echo '<input name="Cancella" type="submit" value="Cancella Prenotazione"/>';
			}
			echo '</td>
			
			</form>
			</td></tr>
			';
		}
		echo '</table>';
		



		} else {
			echo 'Nessuna prenotazione personale attiva al momento.';
		}
		
		if ( $numrighe < 7) {
		echo '<p><br /><br /><a href="prenotazione.php?action=nuova">Aggiungi nuova prenotazione</a></p><br /><br /';
		} else {
			echo '<br /><p>Non puoi effettuare altre prenotazioni al momento.</p><br /><br />';
		}
		
		$datamax = date('Y-m-d', strtotime("$dataoggi +16 day"));
		//Estraggo le prenotazioni relative ai corsi che si seguono o si tengono
		$sql = "SELECT PRENOTAZIONE.CodCampo, PRENOTAZIONE.Data, PRENOTAZIONE.Ora, PRENOTAZIONE.CodCorso
				FROM PRENOTAZIONE LEFT JOIN CORSO ON PRENOTAZIONE.CodCorso = CORSO.CodCorso LEFT JOIN ISCRITTOCORSO ON CORSO.CodCorso = ISCRITTOCORSO.CodCorso  JOIN ACCOUNT ON (ACCOUNT.CodFiscale = CORSO.CodFiscale OR ACCOUNT.CodFiscale = ISCRITTOCORSO.CodFiscale)
				WHERE ACCOUNT.UserName = '".$_SESSION['User']."' AND PRENOTAZIONE.Data >= '$dataoggi' AND PRENOTAZIONE.Data <= '$datamax' ORDER BY PRENOTAZIONE.Data, PRENOTAZIONE.Ora, PRENOTAZIONE.CodCampo";
		$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
		$numrighe = $result->num_rows;
		echo "<table>";
		if ($result->num_rows > 0) {
			echo '<table width ="100%" border="0" align="center" cellpadding="5" cellspacing="2">
			<tr><th colspan="4">Prossime lezioni dei Corsi (fino a 15 giorni)</th></tr>
			<tr><td width="25%">Data</td><td width="25%">Ora</td><td width="25%">Campo</td><td width="25%"> </td></tr>';
		while($row = $result->fetch_array()) {
			echo '<tr><td width="25%">'.$row['Data'].'</td><td width="25%">'.$row['Ora'].'</td><td width="25%">'.$row['CodCampo'].'</td><td width="25%">';
			if ($_SESSION['Tipo'] = "Admin") { 
				echo '<a href="gestiscicorso.php?action='.$row['CodCorso'].'">Vedi Informazioni Corso</a>';
			} else {
				echo '<a href="informazionicorso.php?action='.$row['CodCorso'].'">Vedi Informazioni Corso</a>';
			}
			echo '</td></td></tr>';
		}
		echo '</table>';
			

		}
		

		}
		
		?>
		
		
		
	</div>
</body>

</html>
