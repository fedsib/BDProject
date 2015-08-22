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
	<p>Ti trovi in: Gestione Corsi -> Modifica Corso</p>
	</div>

    <div id="nav"> 
		<?php
			menu();
		?>
    </div>

	<div id="content"> 

	
	
		<?php
		//Pagina per la modifica dei dati di un corso
		
		//Controllo che l'utente abbia fatto il login, se sì esiste controllo la variabile Tipo per controllare se ha i diritti di Amministratore
		if (!isset($_SESSION['User']) || ($_SESSION['User']) != "admin") {
			
			echo '<p>Bisogna effettuare il login come amministratore per vedere questa pagina.';
			//Se e' stato inviato il form per la modifica dei dati controllo che siano corretti e li invio
		} elseif ((isset($_GET['action'])) && (isset($_POST['modifica']))) {
			
			$codcorso = $_GET['action'];
			$nomecorso = $_POST['nomecorso'];
			$livello = $_POST['livello'];
			$istruttore = $_POST['istruttore'];
			$attivo = $_POST['attivo'];
			$errore = FALSE;
			//Se non ho scelto un istruttore setto il valore a null
			if ($istruttore == "Nessuno") { $istruttore = "NULL"; }
			
			if ((! preg_match('/^[a-zA-Z0-9 ]*$/', $nomecorso)) || $nomecorso == '') {
			$msg = "<b>Errore! Il nome del corso deve contenere solo lettere, numeri e spazi</b><br />";
			$errore=TRUE;
			}
			
			//Se non e' presente un istruttore non permetto di impostare il corso come attivo
			if (($attivo == "attivo") && ($istruttore == "NULL")) { 
			$msg = "<b>Errore, il corso non puo' essere attivo senza un istruttore</b><br />";
			$errore=TRUE;
			}
			
			if ($attivo == "attivo") {$attivo = 1; } else {$attivo = 0;}
			
			//Se non ci sono errori invio i dati al DB
			if (!$errore) {
				
				$conn = connessione();
				//A seconda che ci sia o meno l'istruttore uso una diversa query
				if  ($istruttore == "NULL" ) {  
				$sql = "UPDATE CORSO SET CORSO.NomeCorso = '".$nomecorso."', CORSO.TipoCorso = '".$livello."', CORSO.CodFiscale = NULL, CORSO.Attivo = ".$attivo." WHERE CORSO.CodCorso = '".$codcorso."'";
				} else {
				$sql = "UPDATE CORSO SET CORSO.NomeCorso = '".$nomecorso."', CORSO.TipoCorso = '".$livello."', CORSO.CodFiscale = '".$istruttore."', CORSO.Attivo = ".$attivo." WHERE CORSO.CodCorso = '".$codcorso."'";
				}
				$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
				if ( $conn->affected_rows > 0 ) { echo '<p>Corso modificato con successo <a href="gcorsi.php">Torna indietro</a>'; } else 
					{ echo '<p>Errore, nessuna modifica effettuata <a href="modificacorso.php?action='.$codcorso.'">Torna indietro</a>'; }
				
			} else {
				
				echo '<p>'.$msg.'<a href="modificacorso.php?action='.$codcorso.'">Torna indietro</a></p>';
			}
			
			
			//Se sto visualizzando la pagina recupero il codice del corso e mostro i dati corrispondenti
		} elseif (isset($_GET['action'])) {
			
			$codcorso = $_GET['action'];
			
			$conn = connessione();
			$sql = "SELECT PERSONA.CodFiscale, CORSO.TipoCorso, CORSO.NomeCorso, CORSO.Attivo FROM PERSONA RIGHT JOIN CORSO ON PERSONA.CodFiscale = CORSO.CodFiscale WHERE CORSO.CodCorso = '".$codcorso."'";
			$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
				$codfiscale = $row['CodFiscale'];
				$livello = $row['TipoCorso'];
				$nomecorso = $row['NomeCorso'];
				$attivo = $row['Attivo'];
				}
			echo '<table width="400" border="0" align="center" cellpadding="10" cellspacing="5" class="Table"><form action="" method="post">';
			echo '<tr><td>Mome Corso:</td><td><input type="text" value="'.$nomecorso.'" name="nomecorso"></td></tr>';
			echo '<tr><td>Livello : </td><td><select name="livello">';
			if ($livello == "Principiante") { echo '<option value="Principiante" selected>Principiante</option>'; } else { echo '<option value="Principiante">Principiante</option>'; }
			if ($livello == "Intermedio") { echo '<option value="Intermedio" selected>Intermedio</option>'; } else { echo '<option value="Intermedio">Intermedio</option>'; }
			if ($livello == "Avanzato") { echo '<option value="Avanzato" selected>Avanzato</option>'; } else { echo '<option value="Avanzato">Avanzato</option>'; }
			echo '</select></td></tr>';
			
			
			//Seleziono tutti gli istruttori per fare una lista e permettere di sceglierne uno
			$conn = connessione();
			$sql = "SELECT PERSONA.CodFiscale, PERSONA.Nome, PERSONA.Cognome FROM PERSONA JOIN ISTRUTTORE ON PERSONA.CodFiscale = ISTRUTTORE.CodFiscale";
			$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
			echo '<tr><td>Seleziona Istruttore:</td><td><select name="istruttore"><option value="Nessuno">Nessuno</option>';
			if ($result->num_rows > 0) {	
				while($row = $result->fetch_assoc()) {
					if ($row['CodFiscale'] == $codfiscale) {
					echo '<option value="'.$row['CodFiscale'].'" selected>'.$row['Cognome'].' '.$row['Nome'].'</option>';
					} else {
					echo '<option value="'.$row['CodFiscale'].'">'.$row['Cognome'].' '.$row['Nome'].'</option>';
					}		
				}
			}
			echo '</select></td></tr>';

			if ($attivo == "0") { echo '<tr><td><input type="radio" name="attivo" value="attivo">Attivo<br /><input type="radio" name="attivo" value="nonattivo" checked>Non Attivo</input></td></tr>'; 
			} else {
				echo '<tr><td colspan="2"><input type="radio" name="attivo" value="attivo" checked>Attivo<br /><input type="radio" name="attivo" value="nonattivo">Non Attivo</input></td></tr>'; 
			}
			
			echo '<tr><td><input name="modifica" type="submit" value="Modifica Corso"/></form></td><td><form action="gestiscicorso.php" method="get"><button name="action" value="'.$codcorso.'">Torna Indietro</button></form></td></tr>';
			echo '</table>';
		
		} else { echo '<br /><p>Errore nella ricerca informazioni del corso <a href="gcorsi.php">Torna indietro</a></p>';}

			
		} else {
		
		echo '<p>Errone nel link <a href="gcorsi.php">torna indietro.</a></p>';
		
		}
		
	
		
		
		
		
		?>
		
		
		
	</div>
</body>

</html>