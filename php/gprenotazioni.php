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
	<p>Ti trovi in: Gestione Prenotazioni</p>
	</div>

    <div id="nav"> 
		<?php
			menu();
		?>
    </div>

	<div id="content"> 

	
	
		<?php
		//Pagina per la gestione delle prenotazioni
		
		//Controllo che l'utente abbia fatto il login, se sì esiste controllo la variabile Tipo per controllare se ha i diritti di Amministratore
		if (!isset($_SESSION['User']) || ($_SESSION['Tipo']) != "Admin") {
			
			echo '<p>Bisogna effettuare il login come amministratore per vedere questa pagina.';
			
		//Se ha fatto il login mostro la pagina per vedere la lista delle prenotazioni
		} else {
			//Se e' stato inviato il form per cancellare una prenotazione seleziono il codice del campo, data e ora e la elimino
			if (isset($_POST['cancella']) ) {
			
			$data = $_POST['data'];
			$ora = $_POST['ora'];
			$codcampo = $_POST['codcampo'];
			$conn = connessione();
			$sql = "DELETE FROM PRENOTAZIONE WHERE PRENOTAZIONE.CodCampo = '".$codcampo."' AND PRENOTAZIONE.Data = '".$data."' AND PRENOTAZIONE.Ora = '".$ora."'";
			$conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
			if ($conn->affected_rows > 0) {
				echo '<p><b>Prenotazione eliminata con successo</b></p><br /><br />';
			} else {
				echo '<p><b>Errore durante la eliminazione, la prenotazione selezionata potrebbe non esistere.</b></p><br /><br />';
			}
			}
			
			//Se sono presenti informazioni inviate dal form sulla address bar li recupero e controllo che siano validi, altrimenti viene segnalato un errore o li imposto come vuoti
			if (isset($_GET['action']) && is_int(($_GET['action'])/15)) { $inizio=$_GET['action']; } else {$inizio = 0;}
			$msg = "";
			$errore = FALSE;
			//Inizializzo la parte della query con i requisiti della ricerca, se i dati inseriti sono validi li aggiungo per formare la query di ricerca
			$sqlwhere = ' WHERE 1 ';
			
			if (isset($_GET['nome']) && ((trim($_GET['nome'])) != '')) {
			$nome = $_GET['nome'];
			$nome = trim($nome);
			if (! preg_match('/^[a-zA-Z]*$/', $nome)) {
			$msg = $msg."<b>Errore! Il nome deve contenere solo lettere</b><br />";
				$errore = TRUE;
			} else {
				$sqlwhere = $sqlwhere." AND PERSONA.Nome = '".$nome."' ";
			}
			
			} else { 
			$nome = ''; 
			}
			
			if (isset($_GET['cognome']) && (trim($_GET['cognome'])) != '') {
			$cognome = $_GET['cognome'];
			$cognome = trim($cognome);
			if (! preg_match('/^[a-zA-Z]*$/', $cognome)) {
			$msg = $msg."<b>Errore! Il cognome deve contenere solo lettere</b><br />";
				$errore = TRUE;
			} else {
				$sqlwhere = $sqlwhere." AND PERSONA.Cognome = '".$cognome."' ";
			}
			
			} else { 
			$cognome = ''; 
			}
			
			if (isset($_GET['codfiscale']) && (trim($_GET['codfiscale'])) != '') {
			$codfiscale = $_GET['codfiscale'];
			$codfiscale = trim($codfiscale);
			if (! preg_match('/^[a-zA-Z0-9]*$/', $codfiscale)) {
			$msg = $msg."<b>Errore! Il codfiscale deve contenere solo lettere e numeri</b><br />";
				$errore = TRUE;
			} else {
				$sqlwhere = $sqlwhere." AND PERSONA.CodFiscale = '".$codfiscale."' ";
			}
			
			} else { 
			$codfiscale = ''; 
			}
		
			if (isset($_GET['datain']) && (trim($_GET['datain'])) != '') {
			$datain = $_GET['datain'];
			$datain = trim($datain);
			if ((! preg_match('/^[0-9\-]*$/', $datain)) || (($time = strtotime($datain)) === FALSE)) {
			$msg = $msg."<b>Errore! La data deve contenere solo numeri e - ed essere nel formato corretto</b><br />";
				$errore = TRUE;
			} else {
				$sqldatain = date('Y-m-d', strtotime("$datain"));
			}
			
			} else { 
			$datain = ''; 
			$sqldatain = '';
			}
			
			if (isset($_GET['datafin']) && (trim($_GET['datafin'])) != '') {
			$datafin = $_GET['datafin'];
			$datafin = trim($datafin);
			if ((! preg_match('/^[0-9\-]*$/', $datafin)) || (($time = strtotime($datafin)) === FALSE)) {
			$msg = $msg."<b>Errore! La data deve contenere solo numeri e - ed essere nel formato corretto</b><br />";
				$errore = TRUE;
			} else {
				$sqldatafin = date('Y-m-d', strtotime("$datafin"));
			}
			
			} else { 
			$datafin = '';
			$sqldatafin = '';
			}
			
			if (($sqldatain !='') && ($sqldatafin !='') && ($sqldatain > $sqldatafin)) {
				
				$msg = $msg."<b>Errore! La data di inizio e' superiore della data di fine ricerca</b><br />";
				$errore = TRUE;
			} else {
				if ($sqldatain != '') {$sqlwhere = $sqlwhere." AND PRENOTAZIONE.Data >= '".$sqldatain."'"; }
				if ($sqldatafin != '') { $sqlwhere = $sqlwhere." AND PRENOTAZIONE.Data <= '".$sqldatafin."'";  }
				
			}
			
			if (isset($_GET['tipo']) && $_GET['tipo'] == "corso") {
				$tipo = $_GET['tipo'];
				$sqlwhere = $sqlwhere." AND CORSO.NomeCorso IN (SELECT CORSO.NomeCorso FROM CORSO JOIN PRENOTAZIONE ON CORSO.CodCorso = PRENOTAZIONE.CodCorso)";
			} elseif (isset($_GET['tipo']) && $_GET['tipo'] == "privata") {
				$tipo = $_GET['tipo'];
				$sqlwhere = $sqlwhere." AND ACCOUNT.UserName IN (SELECT ACCOUNT.UserName FROM ACCOUNT JOIN PRENOTAZIONE ON ACCOUNT.CodFiscale = PRENOTAZIONE.CodFiscale)";
			} else { 
			$tipo = 'tutti'; 
			}
			
			$link = "gprenotazioni.php?nome=".$nome."&cognome=".$cognome."&codfiscale=".$codfiscale."&datain=".$datain."&datafin=".$datafin."&tipo=".$tipo."&cerca=Cerca";
			//Mostro il form per la ricerca con le informazioni passate tramite GET o vuoto
			echo '<table width="100%" border="0" align="center" cellpadding="10" cellspacing="5" class="Table"><tr><th colspan="5">Ricerca</th></tr><form action="" method="get">';
			echo '<tr><td>Nome:</td><td><input name="nome" type="text" value="'.$nome.'"></input></td><td></td></tr>';
			echo '<tr><td>Cognome:</td><td><input name="cognome" type="text" value="'.$cognome.'"></input></td><td></td></tr>';
			echo '<tr><td>Cod Fiscale:</td><td><input name="codfiscale" type="text" value="'.$codfiscale.'"></input></td><td></td></tr>';
			echo '<tr><td>Dal:</td><td><input name="datain" type="text" value="'.$datain.'"></input></td><td>Formato: gg-mm-aaaa</td></tr>';
			echo '<tr><td>Al:</td><td><input name="datafin" type="text" value="'.$datafin.'"></input></td><td>Formato: gg-mm-aaaa</td></tr>';
			echo '<tr><td>Tipo:</td><td><select name="tipo"><option value="tutti" selected>Tutti</option>';
			if ($tipo == "corso") { echo '<option value="corso" selected>Corso</option>'; } else { echo '<option value="corso">Corso</option>'; }
			if ($tipo == "privata") { echo '<option value="privata" selected>Privata</option>'; } else { echo '<option value="privata">Privata</option>'; }
			echo '</td><td></td></tr>';
			if ($errore) { echo '<tr><td colspan="3">'.$msg.'</td></tr>'; }
			echo '<tr><td colspan="3"><input name="cerca" type="submit" value="Cerca"/></td></tr>';
			echo '</form></table><br /><br />';
			
			$sql = "FROM PRENOTAZIONE LEFT JOIN CORSO ON PRENOTAZIONE.CodCorso = CORSO.CodCorso JOIN PERSONA ON (PRENOTAZIONE.CodFiscale = PERSONA.CodFiscale) OR (CORSO.CodFiscale = PERSONA.CodFiscale) LEFT JOIN ACCOUNT ON PRENOTAZIONE.CodFiscale = ACCOUNT.CodFiscale";
			//Aggiungo le informazioni della query di ricerca che sono stati immessi e sono valide
			$sql = $sql.' '.$sqlwhere;
			//Conto quanti risultati ha la ricerca per, eventualmente, aggiungere altre pagine e non avere una lista di risultati troppo grane
			$sqlcount = "SELECT count(*) ".$sql;
			$conn = connessione();
			$result = $conn->query($sqlcount) or die("Errore nella query MySQL: ".$conn->error);
			$resultrighe = $result->fetch_row();
			$numrighe = $resultrighe[0];
			if ($numrighe > 0) {
			
			//Aggiungo l'ultima parte della query di ricerca che indica i campi che devo estrarre e l'ordine di visualizzazione con il limite di dati da estrarre
			$sql = "SELECT PERSONA.Nome, PERSONA.Cognome, ACCOUNT.UserName, PRENOTAZIONE.CodCampo, PRENOTAZIONE.Data, PRENOTAZIONE.Ora, CORSO.NomeCorso ".$sql;
			$sql = $sql."ORDER BY PRENOTAZIONE.Data, PRENOTAZIONE.Ora LIMIT $inizio, 15";
			$conn = connessione();
			$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
			
			if ($result->num_rows > 0) {
				echo '<table border="1" align="center" cellpadding="5" cellspacing="2" class="Table" width ="100%">';
				echo '<tr><th  width="30%">Cognome/Nome</th><th width ="10%">Corso</th><th width="30%">Account/NomeCorso</th><th width="5%">Campo</th><th width="10%">Data</th><th width ="15%">Cancella</th></tr>';
				while($row = $result->fetch_assoc()) {
					echo '<tr>';
					echo '<td>'.$row['Cognome'].' '.$row['Nome'].'</td>';
					if ($row['NomeCorso']) { echo '<td>Corso</td>';} else { echo '<td>Privata</td>'; }
					if ($row['NomeCorso']) { echo '<td>'.$row['NomeCorso'].'</td>';} else { echo '<td>'.$row['UserName'].'</td>'; }
					echo '<td>'.$row['CodCampo'].'</td>';
					$z = $row['Ora'];
					if ($z < 10) {	
								$orario = "0".$z.".00";
							} else {
								$orario = $z.".00";
							}
					$data = $row['Data'];
					$data = date('d-m-Y', strtotime("$data"));
					echo '<td>'.$data.' '.$orario.'</td>';
					echo '<td>
					
					<form action="" method="post">
					<input name="codcampo" type="hidden" readonly="readonly" value="'.$row['CodCampo'].'">
					<input name="data" type="hidden" readonly="readonly" value="'.$row['Data'].'">
					<input name="ora" type="hidden" readonly="readonly" value="'.$row['Ora'].'">
					</input><input name="cancella" type="submit" value="Cancella"/></form>
					
					
					</td>';
					echo '</tr>';
				}
				echo '</table>';
			}
			
			if ($numrighe > 15) {
				
				
				
				
				
				
				echo '<p align="center"><br /><br />';
				//Se ci sono più risultati di quelli mostrati genero dei link a fine pagina che vanno avanti o indietro nei risultati di 15 alla volta
				if ((isset($_GET['action'])) && ((is_int($inizio/15)) && (($inizio/15) >0 ))) { echo '<a href="'.$link.'&action='.($inizio-15).'">Indietro</a>'; }
				echo "   -   ";
				if (($numrighe-$inizio) > 15 ) { echo '<a href="'.$link.'&action='.($inizio+15).'">Avanti</a>'; }
				echo '<br /><br /></p>';
			}
			
			} else  {
				
			echo "<p>Nessun risultato trovato con i criteri immessi</p>";
		
		}
		
		}
	
		
		
		
		
		?>
		
		
		
	</div>
</body>

</html>
