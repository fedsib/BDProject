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
	<p>Ti trovi in: Iscrizione Corsi</p>

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
			
	} elseif (($_SESSION['Tipo']) == "Admin") { 
	
			echo '<p>Solo gli utenti possono iscriversi ai corsi, per vedere/gestire i corsi <a href="gcorsi.php">clicca qui</a>.';
		
	} elseif ((isset($_GET['action'])) && ($_GET['action']) =="vedi" ) {
		
		$conn = connessione();
		$sql = "SELECT CORSO.CodCorso, CORSO.NomeCorso, CORSO.TipoCorso, ACCISC.UserName, PERSONA.Nome, PERSONA.Cognome
FROM CORSO JOIN PERSONA ON CORSO.CodFiscale = PERSONA.CodFiscale
LEFT JOIN
(SELECT ACCOUNT.UserName, ISCRITTOCORSO.CodCorso FROM ACCOUNT JOIN ISCRITTOCORSO ON ACCOUNT.CodFiscale = ISCRITTOCORSO.CodFiscale WHERE ACCOUNT.UserName='".$_SESSION['User']."') AS ACCISC
ON CORSO.CodCorso = ACCISC.CodCorso WHERE CORSO.Attivo='1'";
		
		$result = $conn->query($sql) or die("Errore nella query MySQL 1");
		if ($result->num_rows > 0) {
				echo '<table width ="100%" border="0" align="center" cellpadding="5" cellspacing="2"><tr><th width="20%">Nome del Corso</th><th width="20%">Livello Corso</th><th width="20%">Istruttore</th><th width="20%"></th><th width="20%"></th></tr>';
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
		
	} else {
		
		$conn = connessione();
		$sql = "SELECT CORSO.CodCorso, CORSO.NomeCorso, CORSO.TipoCorso, PERSONA.Nome, PERSONA.Cognome
FROM CORSO
JOIN ISCRITTOCORSO ON CORSO.CodCorso = ISCRITTOCORSO.CodCorso
JOIN ACCOUNT ON ISCRITTOCORSO.CodFiscale = ACCOUNT.CodFiscale
JOIN PERSONA ON CORSO.CodFiscale = PERSONA.CodFiscale
WHERE ACCOUNT.UserName='".$_SESSION['User']."'";
		$result = $conn->query($sql) or die("Errore nella query MySQL 2");
		if ($result->num_rows > 0) {
			echo '<table width ="100%" border="0" align="center" cellpadding="5" cellspacing="2"><tr><th width="25%">Nome del Corso</th><th width="25%">Livello Corso</th><th width="25%">Istruttore</th><th></th></tr>';
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
		
		echo '<a href="corsi.php?action=vedi">Vedi tutti i corsi</a>';

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
