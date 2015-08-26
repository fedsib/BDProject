<?php 
	session_start();
	require "../cgi-bin/phpfunctions.php" 
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head> 		
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"  />
	<title>Progetto Basi di Dati</title>
	<meta name="language" content="italian it" />
	<link type="text/css" rel="stylesheet" href="../style/screen-style.css" media="screen" />
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
	<p>Ti trovi in: Gestione Utenti -> Modifica Istruttore</p>
	</div>

    <div id="nav"> 
		<?php
			menu();
		?>
    </div>

	<div id="content"> 

	
	
		<?php
		//Pagina per la modifica di un account admin
		
		//Controllo che l'utente abbia fatto il login, se sì esiste controllo la variabile Tipo per controllare se ha i diritti di Amministratore
		if (!isset($_SESSION['User']) || ($_SESSION['Tipo']) != "Admin") {
			
			echo '<p>Bisogna effettuare il login come amministratore per vedere questa pagina.';
		//Se e' loggato come amministratore vedo i dati dell'istruttore selezionato
		} elseif (isset($_GET['vedi'])) {
			
			$user = $_GET['vedi'];
			
			$conn = connessione();
			$sql = "SELECT  ACCOUNT.UserName, PERSONA.Nome, PERSONA.Cognome, PERSONA.CodFiscale, PERSONA.DataNasc, PERSONA.LuogoNasc, PERSONA.Telefono, PERSONA.Mail, PERSONA.Sesso, ISTRUTTORE.Qualifica, ISTRUTTORE.Retribuzione, ISTRUTTORE.DataAssunzione 
			FROM ACCOUNT JOIN PERSONA ON ACCOUNT.CodFiscale = PERSONA.CodFiscale JOIN ISTRUTTORE ON ACCOUNT.CodFiscale = ISTRUTTORE.CodFiscale WHERE ACCOUNT.UserName ='".$user."'";
			$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
				echo '<table width="600" border="0" align="center" cellpadding="5" cellspacing="5" class="Table">';
				echo '<tr><th colspan="2">Informazioni Amministratore</th></tr>';
			if ($result->num_rows > 0) {	
			while($row = $result->fetch_assoc()) {
				echo '<tr><td>Username:</td><td>'.$row['UserName'].'</td></tr>';
				echo '<tr><td>Nome:</td><td>'.$row['Nome'].'</td></tr>';
				echo '<tr><td>Cognome:</td><td>'.$row['Cognome'].'</td></tr>';
				echo '<tr><td>Cod. Fiscale:</td><td>'.$row['CodFiscale'].'</td></tr>';
				echo '<tr><td>Sesso:</td><td>'.$row['Sesso'].'</td></tr>';
				echo '<tr><td>Telefono:</td><td>'.$row['Telefono'].'</td></tr>';
				echo '<tr><td>Mail:</td><td>'.$row['Mail'].'</td></tr>';
				$datanasc = $row['DataNasc'];
				$data = date('d-m-Y', strtotime("$datanasc"));
				echo '<tr><td>Data Nascita:</td><td>'.$data.'</td></tr>';
				echo '<tr><td>Luogo Nascita:</td><td>'.$row['LuogoNasc'].'</td></tr>';
				echo '<tr><td>Qualifica:</td><td>'.$row['Qualifica'].'</td></tr>';
				echo '<tr><td>Retribuzione:</td><td>'.$row['Retribuzione'].'</td></tr>';
				echo '<tr><td>Data Assunzione:</td><td>'.$row['DataAssunzione'].'</td></tr>';
				echo '<tr><td><a href="modadmin.php?moddati='.$user.'">Modifica Dati Personali</a></td><td><a href="gutenti.php">Torna Indietro</a></tr>';
				
				echo '<tr><td colspan="3" height="50"><form action="" method="post"><button name="reset" value="'.$row['CodFiscale'].'">Reset Password</button></form></td></tr>';
				
				
			}
			//Se necessario si puo' resettare la password al codice fiscale cosi' che l'utente possa modificarla di nuovo
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
		//Se invio il form di modifica dei dati mostro i campi in maniera modificabile e inviabile al server
		} elseif (isset($_GET['moddati'])) {
			
			$errore = true; //Setto se mostro il form perché vuoto o per un errore o se proseguo con la modifica
			$msg = "";
			//Se e' stato inviato il form controllo i dati e li preparo per la modifica nel DB
			if  (isset($_POST['modifica'])) {
				
				$errore = false;
				
				$username = isset($_POST['username']) ? trim($_POST['username']) : '';
				if ((! preg_match('/^[a-zA-Z0-9]*$/', $username)) || $username == '') {
				$msg = $msg."<b>Errore! Il nome puo' contenere solo lettere e numeri</b><br />";
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

				$qualifica = isset($_POST['qualifica']) ? trim($_POST['qualifica']) : '';
				if (! preg_match('/^[a-zA-Z0-9 ]*$/', $qualifica)) {
				$msg = $msg."<b>Errore! La qualifica puo' contenere solo lettere, numeri e spazi.</b><br />";
				$errore=TRUE;

				};
				
				$retribuzione = isset($_POST['retribuzione']) ? trim($_POST['retribuzione']) : '';
				if ((! preg_match('/^[0-9]*$/', $retribuzione)) || $retribuzione == '') {
				$msg = $msg."<b>Errore! Retribuzione puo' contenere solo numeri</b><br />";
				$errore=TRUE;
				};
				
				$dataassunzione = isset($_POST['dataassunzione']) ? trim($_POST['dataassunzione']) : '';
				if ((! preg_match('/^[0-9\-]*$/', $dataassunzione)) || (($timestamp = strtotime($dataassunzione)) === FALSE)) {
				$msg = $msg."<b>Errore! La data deve contenere solo numeri e - ed essere nel formato corretto</b><br />";
				$errore = TRUE;
				} else {
				$sqldataass = date('Y-m-d', strtotime("$dataassunzione"));
				}
				
				$sesso = $_POST['sesso'];
				
			
			}
			
			//Mostro il form per inserire i dati con i dati attualmente presenti nel DB
			if ($errore) {
			$user = $_GET['moddati'];
			
			$conn = connessione();
			$sql = "SELECT  ACCOUNT.UserName, PERSONA.Nome, PERSONA.Cognome, PERSONA.CodFiscale, PERSONA.DataNasc, PERSONA.LuogoNasc, PERSONA.Telefono, PERSONA.Mail, PERSONA.Sesso, ISTRUTTORE.Qualifica, ISTRUTTORE.Retribuzione, ISTRUTTORE.DataAssunzione 
			FROM ACCOUNT JOIN PERSONA ON ACCOUNT.CodFiscale = PERSONA.CodFiscale JOIN ISTRUTTORE ON ACCOUNT.CodFiscale = ISTRUTTORE.CodFiscale WHERE ACCOUNT.UserName ='".$user."'";
			$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
				echo '<table width="100%" border="0" align="center" cellpadding="5" cellspacing="5" class="Table"><form action="" method="post" name="Form Modifica Dati Personali">';
				echo '<tr><th colspan="2">Informazioni Amministratore</th></tr>';
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
				echo '<tr><td>Telefono:</td><td colspan="2"><input name="telefono" type="text" value="'.$row['Telefono'].'"></input></td></tr>';
				echo '<tr><td>Mail:</td><td colspan="2"><input name="mail" type="text" value="'.$row['Mail'].'"></input></td></tr>';
				$datanasc = $row['DataNasc'];
				$data = date('d-m-Y', strtotime("$datanasc"));
				echo '<tr><td>Data Nascita:</td><td><input name="datanasc" type="text" value="'.$data.'"></input></td><td>Formato gg-mm-aaaa</td></tr>';
				echo '<tr><td>Luogo Nascita:</td><td colspan="2"><input name="luogonasc" type="text" value="'.$row['LuogoNasc'].'"></input></td></tr>';
				echo '<tr><td>Qualifica:</td><td colspan="2"><input name="qualifica" type="text" value="'.$row['Qualifica'].'"></input></td></tr>';
				echo '<tr><td>Retribuzione:</td><td colspan="2"><input name="retribuzione" type="text" value="'.$row['Retribuzione'].'"></input></td></tr>';
				echo '<tr><td>Data Assunzione:</td><td colspan="2"><input name="dataassunzione" type="text" value="'.$row['DataAssunzione'].'"></input></td></tr>';
				echo '<tr><td><button name="modifica" value="'.$row['CodFiscale'].'">Modifica</button></form></td><td><form action="" method="get"><button name="vedi" value="'.$row['UserName'].'">Annulla</button></form></td><td></td></tr>';
				echo '<tr><td colspan="3">'.$msg.'</td>';
			} 
				
			} else { echo '<tr><td>Errore, Utente non trovato <a href="gutenti.php">Torna Indietro</a></td></tr>'; }
				echo '</table>';
			
			
			//Se il form contiene tutti i dati necessari e sono corretti proseguo con la modifica nel DB
			} else {
				
				$codmodifica = $_POST['modifica'];				
				
				
				try {
				$conn = connessione();
				$conn->autocommit(0);
				$sql1 = "UPDATE ACCOUNT SET UserName = '$username' WHERE CodFiscale = '$codmodifica'";
				$sql2 = "UPDATE ISTRUTTORE SET ";
				if ($qualifica != '' ) { $sql2 = $sql2. "Qualifica = '$qualifica',";} else {$sql2 = $sql2. "Qualifica = NULL,";}
				$sql2 = $sql2 ."Retribuzione = '$retribuzione', DataAssunzione = '$sqldataass' WHERE CodFiscale = '$codmodifica'";
				$sql3 = "UPDATE PERSONA SET Nome = '$nome', Cognome = '$cognome', DataNasc = '$sqldatanasc', LuogoNasc = '$luogonasc',";
				if ($telefono != '') { $sql3 = $sql3. "Telefono = '$telefono', "; } else { $sql3 = $sql3. "Telefono = NULL, "; }
				$sql3 = $sql3."Telefono = '$telefono', Mail = '$mail', Sesso = '$sesso', CodFiscale = '$codfiscale' WHERE CodFiscale = '$codmodifica'";
				 
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
			
			echo '<tr><td>Errore nel link. <a href="gutenti.php">Torna IndietroTorna Indietro</a></td></tr>';

		}
		
		?>
		
		
		
	</div>
</body>

</html>