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
	<p>Ti trovi in: Gestione Utenti</p>
	</div>

    <div id="nav"> 
		<?php
			menu();
		?>
    </div>

	<div id="content"> 

	
	
		<?php
		//Pagina per la gestione degli utenti
		
		//Controllo che l'utente abbia fatto il login, se sì esiste controllo la variabile Tipo per controllare se ha i diritti di Amministratore
		if (!isset($_SESSION['User']) || ($_SESSION['User']) != "admin") {
			
			echo '<p>Bisogna effettuare il login come amministratore per vedere questa pagina.';
		
		}  else {
			//mostro la pagina con la lista degli utenti
			if (isset($_GET['action']) && is_int(($_GET['action'])/15)) { $inizio=$_GET['action']; } else {$inizio = 0;}
			$msg = "";
			$errore = FALSE;
			$sqlwhere = ' WHERE 1 '; //Inizializzo la stringa che mi serve per aggiungere i acmpi di ricerca alla query
			
			//Controllo se ci sono dei dati di ricerca inseriti, se ci sono e sono validi li aggiungo alla query
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
			
			if (isset($_GET['tipo']) && $_GET['tipo'] == "user") {
				$tipo = $_GET['tipo'];
				$sqlwhere = $sqlwhere." AND ACCOUNT.Admin = 0";
			} elseif (isset($_GET['tipo']) && $_GET['tipo'] == "admin") {
				$tipo = $_GET['tipo'];
				$sqlwhere = $sqlwhere." AND ACCOUNT.Admin = 1";
			} else { 
			$tipo = 'tutti'; 
			}
			//mostro i bottoni per aprire le pagine di aggiunta utente/amministratore
			echo '<table width="40%" border="0" align="center" cellpadding="10" cellspacing="5" style="float:right"><tr>
			<td align="center"><br /><form name="adduser" action="adduser.php" method="get"><button type="submit">Aggiungi Utente</button></form><br /></td></tr><tr>
			<td align="center"><form  name="addadmin" action="addadmin.php" method="get"><button type="submit">Aggiungi Amministratore</button></form></td></tr></table>';
			//Mostro la tabella di ricerca degli utenti
			echo '<table width="60%" border="0" align="center" cellpadding="10" cellspacing="5" style="float:left"><tr><th colspan="2">Ricerca</th></tr><form  name="cerca" action="" method="get">';
			echo '<tr><td>Nome:</td><td><input name="nome" type="text" value="'.$nome.'"></input></td></tr>';
			echo '<tr><td>Cognome:</td><td><input name="cognome" type="text" value="'.$cognome.'"></input></td></tr>';
			echo '<tr><td>Cod Fiscale:</td><td><input name="codfiscale" type="text" value="'.$codfiscale.'"></input></td></tr>';
			echo '<tr><td>Tipo:</td><td><select name="tipo"><option value="tutti" selected>Tutti</option>';
			if ($tipo == "user") { echo '<option value="user" selected>User</option>'; } else { echo '<option value="user">User</option>'; }
			if ($tipo == "admin") { echo '<option value="admin" selected>Admin</option>'; } else { echo '<option value="admin">Admin</option>'; }
			echo '</td></tr>';
			if ($errore) { echo '<tr><td colspan="2">'.$msg.'</td></tr>'; }
			echo '<tr><td colspan="2"><input name="cerca" type="submit" value="Cerca"/></td></tr>';
			echo '</form></table><br />';
			//Costruisco la query di ricerca
			$sql = " FROM ACCOUNT JOIN PERSONA ON ACCOUNT.CodFiscale = PERSONA.CodFiscale ";
			
			$sql = $sql.' '.$sqlwhere;
			//Conto quanti sono i risultati per avere piu' pagine se necessario
			$sqlcount = "SELECT count(*) ".$sql;
			$conn = connessione();
			$result = $conn->query($sqlcount) or die("Errore nella query MySQL: ".$conn->error);
			$resultrighe = $result->fetch_row();
			$numrighe = $resultrighe[0];
			//Eseguo la ricerca e mostro i risultati trovati
			if ($numrighe > 0) {
			$sql = "SELECT PERSONA.Nome, PERSONA.Cognome, ACCOUNT.UserName, ACCOUNT.Admin ".$sql;
			$sql = $sql." ORDER BY PERSONA.Cognome, PERSONA.Nome LIMIT $inizio, 15";
			$conn = connessione();
			$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
			
			if ($result->num_rows > 0) {
				echo '<table border="0" align="center" cellpadding="5" cellspacing="2" class="Table" width ="100%">';
				echo '<tr><th  width="40%">Cognome/Nome</th><th width ="15%">Tipo</th><th width="30%">Username</th><th width="15%">Modifica</th></tr>';
				while($row = $result->fetch_assoc()) {
					echo '<tr>';
					echo '<td>'.$row['Cognome'].' '.$row['Nome'].'</td>';
					$admin = $row['Admin'];
					if ($admin) { echo '<td>Admin</td>';} else { echo '<td>User</td>'; }
					
					echo '<td>'.$row['UserName'].'</td>';
					
					if ($admin) {
					echo '<td>
					<form action="modadmin.php" method="get"><button name="vedi" type="submit" value="'.$row['UserName'].'">Vedi Dati</button></form></td>';
					} else {
					echo '<td>
					<form action="moduser.php" method="get"><button name="vedi" type="submit" value="'.$row['UserName'].'">Vedi Dati</button></form></td>';
					}
					echo '</tr>';
				}
				echo '</table>';
			}
			
			if ($numrighe > 15) {
				//Se ci sono piu' risultati dei 15 mostrati aggiungo i link per vedere gli altri
				echo '<p align="center"><br /><br />';
				if ((isset($_GET['action'])) && ((is_int($inizio/15)) && (($inizio/15) >0 ))) { echo '<a href="'.$link.'&action='.($inizio-15).'">Indietro</a>'; }
				echo "   -   ";
				if (($numrighe-$inizio) > 15 ) { echo '<a href="'.$link.'&action='.($inizio+15).'">Avanti</a>'; }
				echo '</p>';
			}
			
			} else  {
				
			echo "<p>Nessun risultato trovato con i criteri immessi</p>";
		
			}
		}
		?>
		
		
		
	</div>
</body>

</html>