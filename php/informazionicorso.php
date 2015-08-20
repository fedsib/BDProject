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
	<p>Ti trovi in: Informazioni Corso</p>

	</div>

    <div id="nav"> 
				<?php
			menu();
		?>
    </div>

	<div id="content"> 

	
	
<?php
			if (!isset($_SESSION['User'])) {
			
			echo '<p>Bisogna effettuare il login come utente od amministratore per vedere questa pagina.</p>';
			
			
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
			$result = $conn->query($sql) or die("Errore nella query MySQL 4");
				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						$codfiscale = $row['CodFiscale'];
					}
				$sql1 = "INSERT INTO ISCRITTOCORSO (CodCorso, CodFiscale) VALUES ('".$codcorso."','".$codfiscale."')";
				$conn->query($sql1) or die("Errore nella query MySQL 5");
					
					echo "<p>Iscritto al corso con successo</p>";
				} else {
					echo "<p>Impossibile iscrivere l'utente al corso selezionato. L'utente potrebbe essere già iscritto oppure un amministratore</p>";
				}
			
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
			$result = $conn->query($sql) or die("Errore nella query MySQL 6");
				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						$codfiscale = $row['CodFiscale'];
					}
				$sql1 = "DELETE FROM ISCRITTOCORSO WHERE ISCRITTOCORSO.CodCorso = '".$codcorso."' AND ISCRITTOCORSO.CodFiscale = '".$codfiscale."'";
				$conn->query($sql1) or die("Errore nella query MySQL 7");
					
					echo "<p>Eliminato dal corso con successo</p>";
				} else {
					echo "<p>Impossibile cancellare l'utente dal corso selezionato. L'utente potrebbe essere già stato cancellato oppure non essere iscritto</p>";
				}
			
			
			
			
		}elseif ((isset($_GET['action']))) {
			
			$codcorso = ($_GET['action']);
			$conn = connessione();
			$sql = "SELECT CORSO.CodCorso, CORSO.NomeCorso, CORSO.TipoCorso, PERSONA.Nome, PERSONA.Cognome
			FROM CORSO
			JOIN PERSONA ON CORSO.CodFiscale = PERSONA.CodFiscale WHERE CORSO.CodCorso ='".$codcorso."' AND CORSO.Attivo = '1'";
			$result = $conn->query($sql) or die("Errore nella query MySQL 1");
		if ($result->num_rows > 0) {
			echo '<table width ="100%" border="0" align="center" cellpadding="5" cellspacing="2"><tr><th width="50%" colspan="2">Nome del Corso</th><th width="25%">Livello Corso</th><th width="25%">Istruttore</th></tr>';
			while($row = $result->fetch_assoc()) {
				echo '<td colspan="2">'.$row['NomeCorso'].'</td>';
				echo '<td>'.$row['TipoCorso'].'</td>';
				echo "<td>".$row['Nome']." ".$row['Cognome'];
				echo '</td></tr>';
			}
			
			$sql = "SELECT LEZIONE.CodLezione, PRENOTAZIONE.Data, PRENOTAZIONE.Ora, PRENOTAZIONE.CodCampo FROM LEZIONE LEFT JOIN PRENOTAZIONE ON LEZIONE.CodLezione = PRENOTAZIONE.CodLezione WHERE LEZIONE.CodCorso ='".$codcorso."' ORDER BY LEZIONE.CodLezione";
			$result = $conn->query($sql) or die("Errore nella query MySQL 2");
			if ($result->num_rows > 0) {
				echo '<tr><th width="25%">Lez.N.</th><th width="25%">Campo N.</th><th width="25%">Data</th><th width="25%">Ora</th></tr>';
				while($row = $result->fetch_assoc()) {
				if ($row['CodCampo'] != NULL) {
					echo '<tr><td>'.$row['CodLezione'].'</td><td>'.$row['CodCampo'].'</td><td>'.$row['Data'].'</td><td>'.$row['Ora'].'</td></tr>';
				} else { echo '<tr><td>'.$row['CodLezione'].'</td><td colspan="3">Lezione non ancora prenotata</td></tr>';}
				}
			} else { echo '<tr><b><td height="50px"colspan="4">Lezioni non ancora disponibili.</b></td></tr>';}
			
			$sql = "SELECT ISCRITTOCORSO.CodCorso FROM ISCRITTOCORSO JOIN ACCOUNT ON ISCRITTOCORSO.CodFiscale = ACCOUNT.CodFiscale WHERE ISCRITTOCORSO.CodCorso ='$codcorso' AND ACCOUNT.UserName='".$_SESSION['User']."'";
			$result = $conn->query($sql) or die("Errore nella query MySQL 3");
			if ($result->num_rows > 0) {
				
				echo '<tr><td colspan="4" height="50"><form action="" method="post"><input name="Cancella" type="submit" value="Cancella iscrizione al corso"/></form></td></tr>' ;
			} else {
				
				echo '<tr><td colspan="4" height="50"><form action="" method="post"><input name="Iscrizione" type="submit" value="Iscriviti a questo corso"/></form></td></tr>' ;
			}
			echo "</table>";
			
		} else { echo '<p>Corso non trovato <a href="corsi.php?action=vedi">Torna Indietro.</a></p>';}
			
			
		} else {
			
			echo '<p>Errore nel link <a href="corsi.php">Torna Indietro.</a></p>';
			
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
