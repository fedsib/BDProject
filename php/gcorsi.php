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
		//Controllo che chi vede la pagina sia loggato e se lo e' prendo la variabile Tipo e controllo sia un amministratore
		if (!isset($_SESSION['User']) || ($_SESSION['Tipo']) != "Admin") {
			
			echo '<p>Bisogna effettuare il login come amministratore per vedere questa pagina.';
		
		//Se l'amministratore ha scelto di creare un nuovo corso mostro il form corrispondente
		} elseif ((isset($_GET['action'])) && ($_GET['action'] == "nuovo") && (isset($_POST['crea']))) {

			$nomecorso = $_POST['nomecorso'];
			$livello = $_POST['livello'];
			$lezioni = $_POST['lezioni'];
			$istruttore = $_POST['istruttore'];
			$errore = FALSE;
			
			//Verifico la validita' dei dati immessi
			if ((! preg_match('/^[a-zA-Z0-9 ]*$/', $nomecorso)) || $nomecorso == '') {
			$msg = "<b>Errore! Il nome del corso deve contenere solo lettere, numeri e spazi</b><br /><a href=gcorsi.php?action=nuovo>Torna indietro</a><br />";
			$errore=TRUE;
			};
			
			if (!$errore) {
			try {
			$conn = connessione();
			$conn->autocommit(0);
			
			//Se non e' stato impostrato un istruttore creo il corso semplicemente
			if ($istruttore == "Nessuno") {
				
			$sql = "INSERT INTO CORSO (NomeCorso, TipoCorso) VALUES ('".$nomecorso."','".$livello."')";
			$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
				if (!$result) { throw new Exception("Errore nell'inserimento non effettuo le operazioni."); }
			} else {
			//Altrimenti aggiungo anche il nome dell'istruttore
			$sql = "INSERT INTO CORSO (NomeCorso, TipoCorso, CodFiscale) VALUES ('".$nomecorso."','".$livello."','".$istruttore."')";
			$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
			if (!$result) { throw new Exception("Errore nell'inserimento non effettuo le operazioni."); }
			}
			//Recupero l'ID generato dal DB per il CodCorso e inizio a creare il numero di lezioni designato
			$codcorso = $conn->insert_id;
			if ($lezioni != 0) {
			for ($x = 1; $x <= $lezioni; $x++) {
			$result = $conn->query("INSERT INTO LEZIONE (CodCorso, CodLezione) VALUES ('$codcorso','$x')") or die("Errore nella query MySQL: ".$conn->error);
			if (!$result) { throw new Exception("Errore nell'inserimento non effettuo le operazioni."); }
			}
			}
			//Se non ci sono stati problemi effettuo tutte le modifiche
			$conn->commit();
			echo '<p>Nuovo Corso Inserito con successo <a href="gcorsi.php">Torna Indietro</a></p>';
			
			} catch (Exception $e) {

			$conn->rollback();
			echo $e->getMessage();
			} 
			} else {
				echo $msg;
			}
			//Se seleziona crea nuovo corso mostro il form per inserire i dati
		} elseif ((isset($_GET['action'])) && ($_GET['action'] == "nuovo")) {
			
			
			$conn = connessione();
			$sql = "SELECT PERSONA.Nome, PERSONA.Cognome, PERSONA.CodFiscale FROM PERSONA JOIN ISTRUTTORE ON PERSONA.CodFiscale = ISTRUTTORE.CodFiscale";
			$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
			
			echo '<table width="400" border="0" align="center" cellpadding="10" cellspacing="5" class="Table"><form action="" method="post">';
			echo '<tr><td>Inserisci nome Corso:<input type="text" value="" name="nomecorso"></td></tr>';
			echo '<tr><td>Inserisci Livello : <select name="livello">	<option value="Principiante">Principiante</option>	<option value="Intermedio">Intermedio</option>	<option value="Avanzato">Avanzato</option></select></td></tr>';
			echo '<tr><td>Seleziona Istruttore:<select name="istruttore"><option value="Nessuno">Nessuno</option>';
			if ($result->num_rows > 0) {
				
			while($row = $result->fetch_assoc()) {
				echo '<option value="'.$row['CodFiscale'].'">'.$row['Cognome'].' '.$row['Nome'].'</option>';
			}
			echo '</select></td></tr>';
			
			} 
			echo '<tr><td>Seleziona num. Lezioni:<select name="lezioni">';
			
			for ($x = 0; $x <= 20; $x++) {
			echo '<option value="'.$x.'">'.$x.'</option>';
			}
			echo '</td></tr>';
			echo '<tr><td><input name="crea" type="submit" value="Crea nuovo corso"/></td></tr>';
			echo '</form><tr><td><form action="gcorsi.php"><button >Annulla</button></form></td></tr></table>';
			
		} else { //Mostro la lista di tutti i corsi presenti, istruttori e se il corso e' attivo o meno
			
			$conn = connessione();
		$sql = "SELECT CORSO.CodCorso, CORSO.NomeCorso, CORSO.TipoCorso, CORSO.Attivo, PERSONA.Nome, PERSONA.Cognome
		FROM CORSO LEFT JOIN PERSONA ON CORSO.CodFiscale = PERSONA.CodFiscale";
		
		$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
		if ($result->num_rows > 0) {
				echo '<table width ="100%" border="0" align="center" cellpadding="5" cellspacing="2"><tr><th width="40%">Nome del Corso</th><th width="20%">Livello Corso</th><th width="20%">Istruttore</th><th width="20%"></th><th width="20%"></th></tr>';
				while($row = $result->fetch_assoc()) {					
				echo '<tr><td>'.$row['NomeCorso'].'</td>';
				echo '<td>'.$row['TipoCorso'].'</td>';
				if ($row['Nome']) { echo "<td>".$row['Nome']." ".$row['Cognome']."</td>"; } else { echo "<td>Nessun Istruttore</td>"; }
				if ($row['Attivo'] == 1) { echo "<td>Attivo</td>";} else {echo '<td>Non Attivo</td>';}
				echo '<td><form action="gestiscicorso.php" method="get"><button name="action" value="'.$row['CodCorso'].'">Gestisci il corso</button></form></td></tr>';
				}
				echo "</table>";
		} else {
			
			echo "<p>Non è presente alcun corso</p>";
			
		}
			
		echo '<br /><br /><form action="" method="get"><button name="action" value="nuovo">Aggiungi nuovo corso</button></form>';
			
		}
		
	
		
		
		
		
		?>
		
		
		
	</div>
</body>

</html>
