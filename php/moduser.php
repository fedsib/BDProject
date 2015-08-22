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
	<p>Ti trovi in: Gestione Utenti -> Modifica Utente</p>
	</div>

    <div id="nav"> 
		<?php
			menu();
		?>
    </div>

	<div id="content"> 

	
	
		<?php
		//Pagina per la modifica dei dati dei soci
		
		//Controllo che l'utente abbia fatto il login, se sì esiste controllo la variabile Tipo per controllare se ha i diritti di Amministratore
		if (!isset($_SESSION['User']) || ($_SESSION['User']) != "admin") {
			
			echo '<p>Bisogna effettuare il login come amministratore per vedere questa pagina.';
		//Se e' stato scelto di cancellare l'account selezionato viene eliminato
		} elseif (isset($_POST['cancella'])) {
			
			$codice = $_POST['cancella'];
			$conn = connessione();
			$sql = "DELETE FROM PERSONA WHERE CodFiscale ='$codice'";
			$conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
			if ($conn->affected_rows > 0) {
				echo '<tr><td><p>Account eliminato con successo. <a href="gutenti.php">Torna Indietro</a></p></td></tr>';
			} else {
				echo '<tr><td><p>Errore, utente non trovato. <a href="gutenti.php">Torna Indietro</a></p></td></tr>';
			}
		//Se e' stato selezionato di vedere un utente specifico mostro i suoti dati
		} elseif (isset($_GET['vedi'])) {
			
			$user = $_GET['vedi'];
			
			$conn = connessione();
			$sql = "SELECT  ACCOUNT.UserName, PERSONA.Nome, PERSONA.Cognome, PERSONA.CodFiscale, PERSONA.DataNasc, PERSONA.LuogoNasc, PERSONA.Telefono, PERSONA.Mail, PERSONA.Sesso, SOCIO.Livello, SOCIO.DataIscrizione 
			FROM ACCOUNT JOIN PERSONA ON ACCOUNT.CodFiscale = PERSONA.CodFiscale JOIN SOCIO ON ACCOUNT.CodFiscale = SOCIO.CodFiscale WHERE ACCOUNT.UserName ='".$user."'";
			$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
				echo '<table width="600" border="0" align="center" cellpadding="5" cellspacing="5" class="Table">';
				echo '<tr><th colspan="2">Informazioni Utente</th></tr>';
			if ($result->num_rows > 0) {	
			while($row = $result->fetch_assoc()) {
				echo '<tr><td>Username:</td><td>'.$row['UserName'].'</td></tr>';
				echo '<tr><td>Nome:</td><td>'.$row['Nome'].'</td></tr>';
				echo '<tr><td>Cognome:</td><td>'.$row['Cognome'].'</td></tr>';
				echo '<tr><td>Cod. Fiscale:</td><td>'.$row['CodFiscale'].'</td></tr>';
				echo '<tr><td>Sesso:</td><td>'.$row['Sesso'].'</td></tr>';
				echo '<tr><td>Livello:</td><td>'.$row['Livello'].'</td></tr>';
				$dataisc = $row['DataIscrizione'];
				$data = date('d-m-Y', strtotime("$dataisc"));
				echo '<tr><td>Data Iscrizione:</td><td>'.$data.'</td></tr>';
				echo '<tr><td>Telefono:</td><td>'.$row['Telefono'].'</td></tr>';
				echo '<tr><td>Mail:</td><td>'.$row['Mail'].'</td></tr>';
				$datanasc = $row['DataNasc'];
				$data = date('d-m-Y', strtotime("$datanasc"));
				echo '<tr><td>Data Nascita:</td><td>'.$data.'</td></tr>';
				echo '<tr><td>Luogo Nascita:</td><td>'.$row['LuogoNasc'].'</td></tr>';
				echo '<tr><td><form action="" method="get"><button name="moddati" value="'.$user.'">Modifica Dati Personali</button></form></td><td><form action="gutenti.php"><button >Torna Indietro</button></form></tr>';
				
				echo '<tr><td colspan="3" height="50"><form action="" method="post"><button name="reset" value="'.$row['CodFiscale'].'">Reset Password</button></form></td></tr>';
				echo '<tr><td colspan="3" height="50"></td></tr><tr><td colspan="3" height="50"><form action="" method="post"><button name="cancella" value="'.$row['CodFiscale'].'">Cancella Account</button></form> Cancella tutte le informazioni dell\'utente</td></tr>';
			} 
				//Se necessario e' possibile resettare la password dell'utente al suo codice fiscale
				if (isset($_POST['reset'])) {
				$codice = $_POST['reset'];
				$hash = SHA1($codice);
				$conn = connessione();
				$sql = "UPDATE ACCOUNT SET Hash='$hash' WHERE CodFiscale ='$codice'";
				$conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
				if ($conn->affected_rows > 0) {
					echo '<tr><td><p>Password resettata al codice fiscale.</p></td></tr>';
				} else {
					echo '<tr><td><p>Errore, utente non trovato oppure la password era già uguale al cod fiscale.</p></td></tr>';
				}
				
				
			}
			
				
				
				
			} else { echo '<tr><td>Errore, Utente non trovato <a href="gutenti.php">Torna Indietro</a></td></tr>'; }
				echo '</table>';
		//Se seleziono la modifica dei dati mostro le informazioni del DB in una form modificabile
		} elseif (isset($_GET['moddati'])) {
			
			$errore = true; //setto se mostrare il form o proseguire con la modifica
			$msg = "";
			//Se e' stato inviato inviato il form coi dati da modificare li controllo, se non ci sono errori li modifico sul DB
			if  (isset($_POST['modifica'])) {
				
				$errore = false;
				
				$username = isset($_POST['username']) ? trim($_POST['username']) : '';
				if ((! preg_match('/^[a-zA-Z]*$/', $username)) || $username == '') {
				$msg = $msg."<b>Errore! Il nome puo' contenere solo lettere</b><br />";
				$errore=TRUE;

				};
				
				$nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
				if ((! preg_match('/^[a-zA-Z]*$/', $nome)) || $nome == '') {
				$msg = $msg."<b>Errore! Il nome puo' contenere solo lettere</b><br />";
				$errore=TRUE;

				};
				
				$cognome = isset($_POST['cognome']) ? trim($_POST['cognome']) : '';
				if ((! preg_match('/^[a-zA-Z]*$/', $cognome)) || $cognome == ''){
				$msg = $msg."<b>Errore! Il cognome puo' contenere solo lettere</b><br />";
				$errore=TRUE;
				};
				
				$codfiscale = isset($_POST['codfiscale']) ? trim($_POST['codfiscale']) : '';
				if ((! preg_match('/^[a-zA-Z0-9]*$/', $codfiscale)) || $codfiscale == '' || strlen($codfiscale) != 16) {
				$msg = $msg."<b>Errore! Il codice fiscale deve contenere solo lettere e numeri ed esser di 16 caratteri</b><br />";
				$errore=TRUE;
				};
				
				$telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
				if (! preg_match('/^[0-9]*$/', $telefono)){
				$msg = $msg."<b>Errore! Il numero di telefono puo' contenere solo numeri</b><br />";
				$errore=TRUE;
				};
				
				$mail = isset($_POST['mail']) ? trim($_POST['mail']) : '';
				if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
					$msg = $msg."<b>Errore! La mail contiene caratteri non ammessi</b><br />";
					$errore=TRUE;
				}
				
				$datanasc = isset($_POST['datanasc']) ? trim($_POST['datanasc']) : '';
				if ((! preg_match('/^[0-9\-]*$/', $datanasc)) || (($timestamp = strtotime($datanasc)) === FALSE)) {
				$msg = $msg."<b>Errore! La data deve contenere solo numeri e - ed essere nel formato corretto</b><br />";
				$errore = TRUE;
				} else {
				$sqldatanasc = date('Y-m-d', strtotime("$datanasc"));
				}
				
				$luogonasc = isset($_POST['luogonasc']) ? trim($_POST['luogonasc']) : '';
				if ((! preg_match('/^[a-zA-Z ]*$/', $luogonasc)) || $luogonasc == '') {
				$msg = $msg."<b>Errore! Il luogo di nascita puo' contenere solo lettere e spazi</b><br />";
				$errore=TRUE;
				};
				
				
				$sesso = $_POST['sesso'];
				$livello = $_POST['livello'];
			
			}
			
			//Se non e' ancora stato inviato il form o ci sono errori sui dati mostro il form editabile con i dati dal DB
			if ($errore) {
			$user = $_GET['moddati'];
			
			$conn = connessione();
			$sql = "SELECT  ACCOUNT.UserName, PERSONA.Nome, PERSONA.Cognome, PERSONA.CodFiscale, PERSONA.DataNasc, PERSONA.LuogoNasc, PERSONA.Telefono, PERSONA.Mail, PERSONA.Sesso, SOCIO.Livello
			FROM ACCOUNT JOIN PERSONA ON ACCOUNT.CodFiscale = PERSONA.CodFiscale JOIN SOCIO ON ACCOUNT.CodFiscale = SOCIO.CodFiscale WHERE ACCOUNT.UserName ='".$user."'";
			$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
				echo '<table width="100%" border="0" align="center" cellpadding="5" cellspacing="5" class="Table"><form action="" method="post" name="Form Modifica Dati Personali">';
				echo '<tr><th colspan="2">Informazioni Utente</th></tr>';
			if ($result->num_rows > 0) {	
			while($row = $result->fetch_assoc()) {
				echo '<tr><td>Username:</td><td><input name="username" type="text" value="'.$row['UserName'].'"></input></td></tr>';
				echo '<tr><td width="25%">Nome:</td><td width=75% colspan="2"><input name="nome" type="text" value="'.$row['Nome'].'"></input></td></tr>';
				echo '<tr><td>Cognome:</td><td colspan="2"><input name="cognome" type="text" value="'.$row['Cognome'].'"></input></td></tr>';
				echo '<tr><td>Cod. Fiscale:</td><td colspan="2"><input name="codfiscale" type="text" value="'.$row['CodFiscale'].'"></input></td></tr>';
				echo '<tr><td>Sesso:</td><td><select name="sesso">';
				if ($sesso == "Maschio") { echo '<option value="Maschio" selected>Maschio</option>'; } else { echo '<option value="Maschio">Maschio</option>'; }
				if ($sesso == "Femmina") { echo '<option value="Femmina" selected>Femmina</option>'; } else { echo '<option value="Femmina">Femmina</option>'; }
				echo '</td></select>';
				echo '<tr><td>Livello:</td><td><select name="livello">';
				if ($livello == "Principiante") { echo '<option value="Principiante" selected>Principiante</option>'; } else { echo '<option value="Principiante">Principiante</option>'; }
				if ($livello == "Intermedio") { echo '<option value="Intermedio" selected>Intermedio</option>'; } else { echo '<option value="Intermedio">Intermedio</option>'; }
				if ($livello == "Esperto") { echo '<option value="Esperto" selected>Esperto</option>'; } else { echo '<option value="Esperto">Esperto</option>'; }
				echo '</td></select>';
				echo '<tr><td>Telefono:</td><td colspan="2"><input name="telefono" type="text" value="'.$row['Telefono'].'"></input></td></tr>';
				echo '<tr><td>Mail:</td><td colspan="2"><input name="mail" type="text" value="'.$row['Mail'].'"></input></td></tr>';
				$datanasc = $row['DataNasc'];
				$data = date('d-m-Y', strtotime("$datanasc"));
				echo '<tr><td>Data Nascita:</td><td><input name="datanasc" type="text" value="'.$data.'"></input></td><td>Formato gg-mm-aaaa</td></tr>';
				echo '<tr><td>Luogo Nascita:</td><td colspan="2"><input name="luogonasc" type="text" value="'.$row['LuogoNasc'].'"></input></td></tr>';

				echo '<tr><td><button name="modifica" value="'.$row['CodFiscale'].'">Modifica</button></form></td><td><form action="" method="get"><button name="vedi" value="'.$row['UserName'].'">Annulla</button></form></td><td></td></tr>';
				echo '<tr><td colspan="3">'.$msg.'</td>';
			} 
				
			} else { echo '<tr><td>Errore, Utente non trovato <a href="gutenti.php">Torna Indietro</a></td></tr>'; }
				echo '</table>';
			
			
			//Se non ci sono errori e i dati sono stati inseriti proseguo con la modifica dell'utente selezionato
			} else {
				
				$codmodifica = $_POST['modifica'];				
				
				
				try {
				$conn = connessione();
				$conn->autocommit(0);
				$sql1 = "UPDATE ACCOUNT SET UserName = '$username' WHERE CodFiscale = '$codmodifica'";
				$sql2 = "UPDATE SOCIO SET Livello = '$livello' WHERE CodFiscale = '$codmodifica'";
				$sql3 = "UPDATE PERSONA SET Nome = '$nome', Cognome = '$cognome', DataNasc = '$sqldatanasc', LuogoNasc = '$luogonasc',";
				if ($telefono != '') { $sql3 = $sql3. "Telefono = '$telefono', "; } else { $sql3 = $sql3. "Telefono = NULL, "; }
				$sql3 = $sql3." Mail = '$mail', Sesso = '$sesso', CodFiscale = '$codfiscale' WHERE CodFiscale = '$codmodifica'";
				 
				$result = $conn->query($sql1) or die("Errore nella query MySQL: ".$conn->error);
				if (!$result) { throw new Exception("Errore nell'inserimento non effettuo le operazioni."); }
				$result = $conn->query($sql2) or die("Errore nella query MySQL: ".$conn->error);
				if (!$result) { throw new Exception("Errore nell'inserimento non effettuo le operazioni."); }
				$result = $conn->query($sql3) or die("Errore nella query MySQL: ".$conn->error);
				if (!$result) { throw new Exception("Errore nell'inserimento non effettuo le operazioni."); }
				
				$conn->commit();
				
				echo 'Dati modificati con successo <a href="gutenti.php">Torna Indietro</a>';
				
				
				} catch (Exception $e) {

				$conn->rollback();
				echo $e->getMessage();
				
			} 
			}
		}  else {
			
			echo '<tr><td>Errore nel link. <a href="gutenti.php">Torna Indietro</a></td></tr>';

		}
		
		?>
		
		
		
	</div>
</body>

</html>