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
	
		} elseif ((isset($_GET['action'])) && (isset($_POST['Cancella']))) {
	
			$codcorso = ($_GET['action']);
			$codfiscale = ($_POST['codfiscale']);
			$conn = connessione();
			$sql = "DELETE FROM ISCRITTOCORSO WHERE ISCRITTOCORSO.CodCorso = '$codcorso' AND ISCRITTOCORSO.CodFiscale = '$codfiscale'";
			$conn->query($sql) or die("Errore nella query MySQL 1");
			if ($conn->affected_rows > 0) { echo '<p>Iscritto Cancellato con successo. <a href="iscritticorso.php?action='.$codcorso.'">Torna indietro.</a></p>'; }
			else {
				echo '<p>Errore, utente non iscritto al corso. <a href="iscritticorso.php?action='.$codcorso.'">Torna indietro.</a></p>';
			}
			
		} elseif (isset($_GET['action'])) {
				
				$codcorso = ($_GET['action']);
				$conn = connessione();
				$sql = "SELECT CORSO.Attivo, CORSO.CodCorso, CORSO.NomeCorso, CORSO.TipoCorso, PERSONA.Nome, PERSONA.Cognome
			FROM CORSO
			LEFT JOIN PERSONA ON CORSO.CodFiscale = PERSONA.CodFiscale WHERE CORSO.CodCorso ='".$codcorso."'";
				$result = $conn->query($sql) or die("Errore nella query MySQL 1");
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
				echo '</tr></table>';
			
				$conn = connessione();
				$sql = "SELECT PERSONA.CodFiscale, PERSONA.Nome, PERSONA.Cognome, SOCIO.Livello FROM ISCRITTOCORSO JOIN PERSONA ON ISCRITTOCORSO.CodFiscale = PERSONA.CodFiscale JOIN SOCIO ON ISCRITTOCORSO.CodFiscale = SOCIO.CodFiscale WHERE ISCRITTOCORSO.CodCorso='".$codcorso."'";
				$result = $conn->query($sql) or die("Errore nella query MySQL 1");
				if ($result->num_rows > 0) {
					echo '<table width ="100%" border="0" align="center" cellpadding="5" cellspacing="2"><tr><th>Cognome</th><th>Nome</th><th>Livello</th><th></th></tr>';
					while($row = $result->fetch_assoc()) {
						echo '<tr><td>'.$row['Cognome'].'</td><td>'.$row['Nome'].'</td><td>'.$row['Livello'].'</td><td>
						<form action="" method="post"><input name="codfiscale" type="hidden" readonly="readonly" value="'.$row['CodFiscale'].'"></input><input name="Cancella" type="submit" value="Cancella Iscrizione"/></form>
						</td></tr>';
					}
					echo '</table>';
				} else { echo '<p>Nessun Iscritto</p>';}
				
				} else {
					echo '<p>Errone nel link <a href="gcorsi.php">torna indietro.</a></p>';
				}
			
		} else {
				
			echo '<p>Errone nel link <a href="gcorsi.php">torna indietro.</a></p>';
				
		}
		
		?>
		
		
		
	</div>
</body>

</html>