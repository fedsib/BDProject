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
	<p>Ti trovi in: Prenotazione Campi</p>

	</div>

    <div id="nav"> 
		<?php
			menu();
		?>
    </div>

	<div id="content"> 

	
	
		<?php
		if (!isset($_SESSION['User'])) {
			
			echo '<p>Bisogna effettuare il login come utente od amministratore per vedere questa pagina.';
			
		} elseif (isset($_POST['Cancella'])) {
		$data = $_POST['data'];
		$ora = $_POST['ora'];
		$campo = $_POST['campo'];
		
		$conn = connessione();
		$sql = "DELETE FROM PRENOTAZIONE WHERE CodCampo='$campo' AND Data='$data' AND Ora='$ora'";
		$result = $conn->query($sql) or die("Errore nella query MySQL 1");
		echo 'Prenotazione Cancellata con successo.<br />
		<a href="prenotazione.php">Torna Indietro</a>';
		
		} elseif (isset($_POST['Prenotazione'])) {
			
			$conn = connessione();
			$sql = "SELECT PERSONA.CodFiscale FROM ACCOUNT JOIN PERSONA ON ACCOUNT.CodFiscale = PERSONA.CodFiscale WHERE ACCOUNT.UserName = '".$_SESSION['User']."'";
			$result = $conn->query($sql) or die("Errore nella query MySQL 2");
			if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$cod = $row['CodFiscale'];
			}
				$campo = $_POST['campo'];
				$data = $_POST['data'];
				$ora = $_POST['ora'];
				$sql = "INSERT INTO PRENOTAZIONE (CodFiscale, CodCampo, Data, Ora) VALUES ('$cod','$campo','$data','$ora')";
				$result = $conn->query($sql) or die("Errore nella query MySQL 3");
				echo 'Prenotazioni aggiunta con successo.<br /><br /><a href="prenotazione.php">Torna Indietro</a>';
				
			} else {
				echo 'Errore utente non trovato, prova a rieffettuare il login<br /><a href="logout.php">Torna Indietro</a>';
			}
			
		} elseif ((isset($_GET['action'])) && ($_GET['action'] == 'nuova') ) {
			
			$dataoggi = date('Y-m-d');
			$tipocampi = array();
			
			$conn = connessione();
			$sql = "SELECT CAMPO.TipoSup FROM CAMPO";
				$result = $conn->query($sql) or die("Errore nella query MySQL 4");
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
						$result = $conn->query($sql) or die("Errore nella query MySQL 5");
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
			echo '</table>';
		} else {

		$dataoggi = date("Y-m-d");
		
		$conn = connessione();
		$sql = "SELECT PRENOTAZIONE.Data, PRENOTAZIONE.Ora, PRENOTAZIONE.CodCampo 
		FROM 
		ACCOUNT 
		JOIN PRENOTAZIONE ON ACCOUNT.CodFiscale=PRENOTAZIONE.CodFiscale
		WHERE ACCOUNT.UserName ='".$_SESSION['User']."' AND PRENOTAZIONE.Data >= '$dataoggi'";
		$result = $conn->query($sql) or die("Errore nella query MySQL 5");
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
			echo 'Nessuna prenotazione attiva al momento.';
		}
		
		if ( $numrighe < 7) {
		echo '<br /><a href="prenotazione.php?action=nuova">Aggiungi nuova prenotazione</a>';
		} else {
			echo '<br /><p>Non puoi effettuare altre prenotazioni al momento.</p>';
		}

		}
		
		?>
		
		
		
	</div>

	<div id="footer">
		<ul>
			<li id="footleft"><a href="chisiamo.html">Chi Siamo</a></li>
			<li id="footmid" accesskey="C"><a href="contatti.html">Contatti</a></li>
			<li id="footmid" accesskey="3"><a href="mappa.html">Mappa del sito</a></li> 
			<li id="footright"><a href="notelegali.html">Note Legali</a></li>         
		</ul> 
    </div>
</body>

</html>
