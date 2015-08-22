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
	<p>Ti trovi in: Gestione Corsi -> Informazione Corso</p>
	</div>

    <div id="nav"> 
		<?php
			menu();
		?>
    </div>

	<div id="content"> 

	
	
<?php
			//Mostra le informazioni del corso selezionato
			
			//Bisogna effettuare il login come utente per vedere questa pagina
			if (!isset($_SESSION['User'])) {
			
			echo '<p>Bisogna effettuare il login come utente od amministratore per vedere questa pagina.</p>';
			
			//Controllo che non sia un admin, solo i soci possono essere iscritti ai corsi
			} elseif (($_SESSION['Tipo']) == "Admin") { 
	
			echo '<p>Solo gli utenti possono iscriversi ai corsi, per vedere/gestire i corsi <a href="gcorsi.php">clicca qui</a>.';
			//Se e' stato inviato il form con il comando di iscrizione la eseguo
			} elseif ((isset($_GET['action'])) && (isset($_POST['Iscrizione']))) {
			
			$conn = connessione();
			$codcorso = $_GET['action'];
			$sql = "
			SELECT ACCOUNT.CodFiscale
FROM ACCOUNT
WHERE ACCOUNT.UserName = '".$_SESSION['User']."'
AND ACCOUNT.Admin = '0'
AND ACCOUNT.CodFiscale NOT
IN (
SELECT ACCOUNT.CodFiscale
FROM ISCRITTOCORSO
JOIN ACCOUNT ON 
ISCRITTOCORSO.CodFiscale = ACCOUNT.CodFiscale 
WHERE ACCOUNT.UserName = '".$_SESSION['User']."'  
AND ISCRITTOCORSO.CodCorso = '".$codcorso."')";
			$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						$codfiscale = $row['CodFiscale'];
					}
				$sql1 = "INSERT INTO ISCRITTOCORSO (CodCorso, CodFiscale) VALUES ('".$codcorso."','".$codfiscale."')";
				$conn->query($sql1) or die("Errore nella query MySQL: ".$conn->error);
					
					echo '<p>Iscritto al corso con successo. <a href="corsi.php">Torna Indietro</p>';
				} else {
					echo "<p>Impossibile iscrivere l'utente al corso selezionato. L'utente potrebbe essere già iscritto oppure un amministratore</p>";
				}
			//Se e' stato inviato il form con il comando di cancellazione lo eseguo
		} elseif ((isset($_GET['action'])) && (isset($_POST['Cancella']))) {
			
			$conn = connessione();
			$codcorso = $_GET['action'];
			$sql = "
			SELECT ACCOUNT.CodFiscale
FROM ACCOUNT
WHERE ACCOUNT.UserName = '".$_SESSION['User']."'
AND ACCOUNT.Admin = '0'
AND ACCOUNT.CodFiscale IN (
SELECT ACCOUNT.CodFiscale
FROM ISCRITTOCORSO
JOIN ACCOUNT ON 
ISCRITTOCORSO.CodFiscale = ACCOUNT.CodFiscale 
WHERE ACCOUNT.UserName = '".$_SESSION['User']."'  
AND ISCRITTOCORSO.CodCorso = '".$codcorso."')";
			$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						$codfiscale = $row['CodFiscale'];
					}
				$sql1 = "DELETE FROM ISCRITTOCORSO WHERE ISCRITTOCORSO.CodCorso = '".$codcorso."' AND ISCRITTOCORSO.CodFiscale = '".$codfiscale."'";
				$conn->query($sql1) or die("Errore nella query MySQL: ".$conn->error);
					
					echo "<p>Eliminato dal corso con successo</p>";
				} else {
					echo "<p>Impossibile cancellare l'utente dal corso selezionato. L'utente potrebbe essere già stato cancellato oppure non essere iscritto</p>";
				}
			
			
			
			//Mostro la parte principale della pagina con le informazioni del corso, informazioni delle lezioni ed un bottone per iscriversi o cancellare l'iscrizione al corso
		}elseif ((isset($_GET['action']))) {
			//Mostro le informazioni del Corso
			$codcorso = ($_GET['action']);
			$conn = connessione();
			$sql = "SELECT CORSO.CodCorso, CORSO.NomeCorso, CORSO.TipoCorso, PERSONA.Nome, PERSONA.Cognome
			FROM CORSO
			JOIN PERSONA ON CORSO.CodFiscale = PERSONA.CodFiscale WHERE CORSO.CodCorso ='".$codcorso."' AND CORSO.Attivo = '1'";
			$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
		if ($result->num_rows > 0) {
			echo '<table width ="100%" border="0" align="center" cellpadding="5" cellspacing="2"><tr><th width="50%" colspan="2">Nome del Corso</th><th width="25%">Livello Corso</th><th width="25%">Istruttore</th></tr>';
			while($row = $result->fetch_assoc()) {
				echo '<td colspan="2">'.$row['NomeCorso'].'</td>';
				echo '<td>'.$row['TipoCorso'].'</td>';
				echo "<td>".$row['Nome']." ".$row['Cognome'];
				echo '</td></tr>';
			}
			//Mostro le lezioni presenti e se sono gia' state fissate con la data o meno
			$sql = "SELECT LEZIONE.CodLezione, PRENOTAZIONE.Data, PRENOTAZIONE.Ora, PRENOTAZIONE.CodCampo FROM LEZIONE LEFT JOIN PRENOTAZIONE ON LEZIONE.CodLezione = PRENOTAZIONE.CodLezione WHERE LEZIONE.CodCorso ='".$codcorso."' ORDER BY LEZIONE.CodLezione";
			$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
			if ($result->num_rows > 0) {
				echo '<tr><th width="25%">Lez.N.</th><th width="25%">Campo N.</th><th width="25%">Data</th><th width="25%">Ora</th></tr>';
				while($row = $result->fetch_assoc()) {
				if ($row['CodCampo'] != NULL) {
					echo '<tr><td>'.$row['CodLezione'].'</td><td>'.$row['CodCampo'].'</td><td>'.$row['Data'].'</td><td>'.$row['Ora'].'</td></tr>';
				} else { echo '<tr><td>'.$row['CodLezione'].'</td><td colspan="3">Lezione non ancora prenotata</td></tr>';}
				}
			} else { echo '<tr><b><td height="50px"colspan="4">Lezioni non ancora disponibili.</b></td></tr>';}
			//Se l'utente e' iscritto mostro il bottone per cancellare l'iscrizione altrimenti per iscriversi al corso
			$sql = "SELECT ISCRITTOCORSO.CodCorso FROM ISCRITTOCORSO JOIN ACCOUNT ON ISCRITTOCORSO.CodFiscale = ACCOUNT.CodFiscale WHERE ISCRITTOCORSO.CodCorso ='$codcorso' AND ACCOUNT.UserName='".$_SESSION['User']."'";
			$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
			if ($result->num_rows > 0) {
				
				echo '<tr><td colspan="3" height="50"><form action="" method="post"><input name="Cancella" type="submit" value="Cancella iscrizione al corso"/></form></td>' ;
			} else {
				
				echo '<tr><td colspan="3" height="50"><form action="" method="post"><input name="Iscrizione" type="submit" value="Iscriviti a questo corso"/></form></td>' ;
			}
			echo '<td><form action="corsi.php"><button >Torna indietro</button></form></td></tr>/table>';
			
		} else { echo '<p>Corso non trovato <a href="corsi.php?action=vedi">Torna Indietro.</a></p>';}
			
			
		} else {
			
			echo '<p>Errore nel link <a href="corsi.php">Torna Indietro.</a></p>';
			
		}
			
			
			
			
			
			
			
			
		?>
		
		
		
	</div>
</body>

</html>
