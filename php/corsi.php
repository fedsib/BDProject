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
	<p>Ti trovi in: Gestione Corsi</p>
	</div>

    <div id="nav"> 
		<?php
			menu();
		?>
    </div>

	<div id="content"> 

	
	<?php
	
	//Controllo che l'utente sia loggato
	if (!isset($_SESSION['User'])) {
			
			echo '<p>Bisogna effettuare il login come utente od amministratore per vedere questa pagina.';
			
			
	//Controllo che non sia un admin, solo i socio possono iscriversi ai corso
	} elseif (($_SESSION['Tipo']) == "Admin") { 
	
			echo '<p>Solo gli utenti possono iscriversi ai corsi, per vedere/gestire i corsi <a href="gcorsi.php">clicca qui</a>.';
		
	//Se ha selezionato mostra tutti i corso cerco nel DB tutti i corsi attivi con i rispettivi istruttori e segnale se l'utente è già iscritto o meno
	} elseif ((isset($_GET['action'])) && ($_GET['action']) =="vedi" ) {
		
		$conn = connessione();
		$sql = "SELECT CORSO.CodCorso, CORSO.NomeCorso, CORSO.TipoCorso, ACCISC.UserName, PERSONA.Nome, PERSONA.Cognome
FROM CORSO JOIN PERSONA ON CORSO.CodFiscale = PERSONA.CodFiscale
LEFT JOIN
(SELECT ACCOUNT.UserName, ISCRITTOCORSO.CodCorso FROM ACCOUNT JOIN ISCRITTOCORSO ON ACCOUNT.CodFiscale = ISCRITTOCORSO.CodFiscale WHERE ACCOUNT.UserName='".$_SESSION['User']."') AS ACCISC
ON CORSO.CodCorso = ACCISC.CodCorso WHERE CORSO.Attivo='1'";
		
		$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
		if ($result->num_rows > 0) {
				echo '<table width ="100%" border="0" align="center" cellpadding="5" cellspacing="2"><tr><th width="45%">Nome del Corso</th><th width="	15%">Livello Corso</th><th width="20%">Istruttore</th><th width="20%"></th></tr>';
				while($row = $result->fetch_assoc()) {					
				echo '<tr><td>'.$row['NomeCorso'].'</td>';
				echo '<td>'.$row['TipoCorso'].'</td>';
				echo "<td>".$row['Nome']." ".$row['Cognome']."</td>";
				echo "<td>";
				if ($row['UserName']) { echo "ISCRITTO"; } echo "</td>";
				echo '<td><a href="informazionicorso.php?action='.$row['CodCorso'].'">Informazioni sul corso</a></td></tr>';
				}
				echo "</table>";
		} else {
			
			echo "<p>Nessun corso disponibile.</p>";
			
		}
	//Se l'utente ha appena aperto la pagina mostro semplicemente la lista dei corsi a cui lui è iscritto con rispettivi istruttori del corso
	} else {
		
		$conn = connessione();
		$sql = "SELECT CORSO.CodCorso, CORSO.NomeCorso, CORSO.TipoCorso, PERSONA.Nome, PERSONA.Cognome
FROM CORSO
JOIN ISCRITTOCORSO ON CORSO.CodCorso = ISCRITTOCORSO.CodCorso
JOIN ACCOUNT ON ISCRITTOCORSO.CodFiscale = ACCOUNT.CodFiscale
JOIN PERSONA ON CORSO.CodFiscale = PERSONA.CodFiscale
WHERE ACCOUNT.UserName='".$_SESSION['User']."'";
		$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
		if ($result->num_rows > 0) {
			echo '<table width ="100%" border="0" align="center" cellpadding="5" cellspacing="2"><tr><th width="45%">Nome del Corso</th><th width="	15%">Livello Corso</th><th width="20%">Istruttore</th><th width="20%"></th></tr>';
			while($row = $result->fetch_assoc()) {
				echo '<tr><td>'.$row['NomeCorso'].'</td>';
				echo '<td>'.$row['TipoCorso'].'</td>';
				echo "<td>".$row['Nome']." ".$row['Cognome'];
				echo '</td>';
				echo '<td><a href="informazionicorso.php?action='.$row['CodCorso'].'">Informazioni sul corso</a></td></tr>';
			}
			echo "</table>";
		} else {
			
			echo "<p>Al momento non sei iscritto a nessun corso.</p>";
			
		}
		
		echo '<br /><br /><br /><a href="corsi.php?action=vedi">Vedi tutti i corsi</a>';

	}
	
	
	
	
	
	
	?>
		
		
		
	</div>
</body>

</html>
