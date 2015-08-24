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
	<p>Ti trovi in: Gestione Account</p>
	</div>

    <div id="nav"> 
		<?php
			menu();
		?>
    </div>

	<div id="content"> 

	
	
		<?php
		//Pagina per la modifica dell'account
		
		//Controllo che sia stato effettuato il login
		if (!isset($_SESSION['User'])) {
			
			echo '<p>Bisogna effettuare il login come amministratore per vedere questa pagina.';
		
		//Se si sceglie di cancellare il proprio account lo elimino dal DB
		} elseif (isset($_POST['cancella'])) {
			
			$codice = $_POST['cancella'];
			$conn = connessione();
			$sql = "DELETE FROM PERSONA WHERE CodFiscale ='$codice'";
			$conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
			if ($conn->affected_rows > 0) {
				echo '<tr><td><p>Account eliminato con successo. <a href="index.php">Torna Indietro</a></p></td></tr>';
				session_destroy(); //Effettuo il logout se elimino l'account attuale
			} else {
				echo '<tr><td><p>Errore, utente non trovato. <a href="utenti.php">Torna Indietro</a></p></td></tr>';
			}
		
		
		//Se scelgo di modificare i dati personali li estraggo e li mostro in un form editabile
		} elseif (isset($_GET['moddati'])) {
			
			$admin = ($_SESSION['Tipo'] == "Admin");
			$errore = true; //Controllo se mostrare il form o procedere con le modifiche
			$msg = "";
			//Se ho inviato il form controllo se tutti i dati sono presenti e se ci sono errori
			if  (isset($_POST['modifica'])) {
				
				$errore = false;
				
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
				if (!$admin) { $livello = $_POST['livello']; }
				if ($admin) {
					
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
					
					
				}
			
			}
			
			//Se non ci sono i dati o ci sono errori mostro il form con i dati modificabili
			if ($errore) {
			$user = $_SESSION['User'];
			
			$conn = connessione();
			$sql = "SELECT PERSONA.Nome, PERSONA.Cognome, PERSONA.CodFiscale, PERSONA.DataNasc, PERSONA.LuogoNasc, PERSONA.Telefono, PERSONA.Mail, PERSONA.Sesso, ";
			if ($admin) { $sql = $sql."ISTRUTTORE.Qualifica, ISTRUTTORE.Retribuzione, ISTRUTTORE.DataAssunzione 
			FROM ACCOUNT JOIN PERSONA ON ACCOUNT.CodFiscale = PERSONA.CodFiscale JOIN ISTRUTTORE ON ACCOUNT.CodFiscale = ISTRUTTORE.CodFiscale"; } else { $sql = $sql."SOCIO.Livello, SOCIO.DataIscrizione FROM ACCOUNT JOIN PERSONA ON ACCOUNT.CodFiscale = PERSONA.CodFiscale JOIN SOCIO ON ACCOUNT.CodFiscale = SOCIO.CodFiscale";}
			$sql = $sql." WHERE ACCOUNT.UserName ='".$user."'";
			$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
				echo '<table width="100%" border="0" align="center" cellpadding="5" cellspacing="5" class="Table"><form action="" method="post" name="Form Modifica Dati Personali">';
				echo '<tr><th colspan="2">Informazioni Utente</th></tr>';
			if ($result->num_rows > 0) {	
			while($row = $result->fetch_assoc()) {
				echo '<tr><td width="25%">Nome:</td><td width=75% colspan="2"><input name="nome" type="text" value="'.$row['Nome'].'"></input></td></tr>';
				echo '<tr><td>Cognome:</td><td colspan="2"><input name="cognome" type="text" value="'.$row['Cognome'].'"></input></td></tr>';
				echo '<tr><td>Cod. Fiscale:</td><td colspan="2"><input name="codfiscale" type="text" value="'.$row['CodFiscale'].'"></input></td></tr>';
				echo '<tr><td>Sesso:</td><td><select name="sesso">';
				if ($sesso == "Maschio") { echo '<option value="Maschio" selected>Maschio</option>'; } else { echo '<option value="Maschio">Maschio</option>'; }
				if ($sesso == "Femmina") { echo '<option value="Femmina" selected>Femmina</option>'; } else { echo '<option value="Femmina">Femmina</option>'; }
				echo '</td></select>';
				if (!$admin) { echo '<tr><td>Livello:</td><td><select name="livello">';
				if ($livello == "Principiante") { echo '<option value="Principiante" selected>Principiante</option>'; } else { echo '<option value="Principiante">Principiante</option>'; }
				if ($livello == "Intermedio") { echo '<option value="Intermedio" selected>Intermedio</option>'; } else { echo '<option value="Intermedio">Intermedio</option>'; }
				if ($livello == "Esperto") { echo '<option value="Esperto" selected>Esperto</option>'; } else { echo '<option value="Esperto">Esperto</option>'; }
				echo '</td></select>'; }
				echo '<tr><td>Telefono:</td><td colspan="2"><input name="telefono" type="text" value="'.$row['Telefono'].'"></input></td></tr>';
				echo '<tr><td>Mail:</td><td colspan="2"><input name="mail" type="text" value="'.$row['Mail'].'"></input></td></tr>';
				$datanasc = $row['DataNasc'];
				$data = date('d-m-Y', strtotime("$datanasc"));
				echo '<tr><td>Data Nascita:</td><td><input name="datanasc" type="text" value="'.$data.'"></input></td><td>Formato gg-mm-aaaa</td></tr>';
				echo '<tr><td>Luogo Nascita:</td><td colspan="2"><input name="luogonasc" type="text" value="'.$row['LuogoNasc'].'"></input></td></tr>';
				if ($admin) {
					echo '<tr><td>Qualifica:</td><td colspan="2"><input name="qualifica" type="text" value="'.$row['Qualifica'].'"></input></td></tr>';
					echo '<tr><td>Retribuzione:</td><td colspan="2"><input name="retribuzione" type="text" value="'.$row['Retribuzione'].'"></input></td></tr>';
					echo '<tr><td>Data Assunzione:</td><td colspan="2"><input name="dataassunzione" type="text" value="'.$row['DataAssunzione'].'"></input></td></tr>';
				}

				echo '<tr><td><button name="modifica" value="'.$row['CodFiscale'].'">Modifica</button></form></td><td><form action="" method="get"><button >Annulla</button></form></td><td></td></tr>';
				echo '<tr><td colspan="3">'.$msg.'</td>';
			} 
				
			} else { echo '<tr><td>Errore, Utente non trovato <a href="utenti.php">Torna Indietro</a></td></tr>'; }
				echo '</table>';
			
			
			//Se i dati sono presenti e corretti procedo con la modifica nel DB
			} else {
				
				$codmodifica = $_POST['modifica'];				
				
				
				try {
				$conn = connessione();
				$conn->autocommit(0);
				if ($admin) { $sql2 = "UPDATE ISTRUTTORE SET Qualifica = '$qualifica', Retribuzione = '$retribuzione', DataAssunzione = '$sqldataass' WHERE CodFiscale = '$codmodifica'"; } else { $sql2 = "UPDATE SOCIO SET Livello = '$livello' WHERE CodFiscale = '$codmodifica'"; }
				$sql3 = "UPDATE PERSONA SET Nome = '$nome', Cognome = '$cognome', DataNasc = '$sqldatanasc', LuogoNasc = '$luogonasc',";
				if ($telefono != '') { $sql3 = $sql3. "Telefono = '$telefono', "; } else { $sql3. "Telefono = NULL, "; }
				$sql3 = $sql3. "Mail = '$mail', Sesso = '$sesso', CodFiscale = '$codfiscale' WHERE CodFiscale = '$codmodifica'";
				 
				$result = $conn->query($sql2) or die("Errore nella query MySQL: ".$conn->error);
				if (!$result) { throw new Exception("Errore nell'inserimento non effettuo le operazioni."); }
				$result = $conn->query($sql3) or die("Errore nella query MySQL: ".$conn->error);
				if (!$result) { throw new Exception("Errore nell'inserimento non effettuo le operazioni."); }
				
				$conn->commit();
				
				echo 'Dati modificati con successo <a href="utenti.php">Torna Indietro</a>';
				
				
				} catch (Exception $e) {

				$conn->rollback();
				echo $e->getMessage();
				
			} 
			}
			
			//Se scelgo di modificare username e password mostro il form corrispondente
			} elseif (isset($_GET['moduspa'])) {
				
				$errore = true;
				$msg = "";
				
				//Se ho inviato i dati del form controllo che siano accettabili e le password coincidano
				if  (isset($_POST['modifica'])) {
				
				$errore = false;
				
				$username = isset($_POST['Username']) ? trim($_POST['Username']) : '';
				if ((! preg_match('/^[a-zA-Z0-9]*$/', $username)) || $username == '') {
				$msg = $msg."<b>Errore! L'username puo' contenere solo lettere e numeri</b><br />";
				$errore=TRUE;

				};
				
				$pass = isset($_POST['Password']) ? $_POST['Password'] : '';
				if ((! preg_match('/^[a-zA-Z0-9]*$/', $pass)) || $pass =='') {
				$msg = $msg."<b>Errore! La Password può contenere solo lettere e numeri</b><br />";
				$errore=TRUE;
				};
				
				$rpass = isset($_POST['RPassword']) ? $_POST['RPassword'] : '';
				if (!($pass == $rpass)) {
				$msg = $msg."<b>Errore! Le Password non coincidono</b><br />";
				$errore=TRUE;
				};
				
				}
				
				
			//Se non ci sono i dati o sono nel formato sbagliato rimostro il form
			if ($errore) {
			$user = $_SESSION['User'];
				
				$conn = connessione();
			$sql = "SELECT ACCOUNT.UserName, ACCOUNT.CodFiscale FROM ACCOUNT WHERE ACCOUNT.UserName = '".$user."'";
			$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
			if ($result->num_rows > 0) {	
			while($row = $result->fetch_assoc()) {
				echo '<form action="" method="post" name="Form Modifica Username e Password">
			<table width="400" border="0" align="center" cellpadding="5" cellspacing="5" class="Table">
			<tr>
			<td colspan="2" align="left" valign="top"><h3>Form Modifica Username e Password</h3></td>
			<tr>
			<td align="right" valign="top">Username</td>
			<td><input name="Username" type="text" class="Input" value='; if(isset($user)){ echo $user; } echo'></td>
			</tr>
			<tr>
			<td align="right">Password</td>
			<td><input name="Password" type="password" class="Input"></td>
			</tr>
			<tr>
			<td align="right">Ripeti Password</td>
			<td><input name="RPassword" type="password" class="Input"></td>
			</tr><td><button name="modifica" type="submit" value="'.$row['CodFiscale'].'" class="Button">Modifica</button></form></td>
			<td><form action="utenti.php"><button >Annulla</button></form></td>
			</tr>';
			if(isset($msg)){
				echo '<tr>
				<td colspan="2" align="center" valign="top">'. $msg .'</td>
				</tr>';
			}
				echo '<tr>
			</table>
			';
			}} else { echo '<tr><td>Errore, Utente non trovato <a href="utenti.php">Torna Indietro</a></td></tr>';	}
				
				
				
			//Se ci sono e sono corretti procedo con l'inserimento nel DB
			} else {
				$codmodifica = $_POST['modifica'];
				$pass = SHA1($pass);
				$conn = connessione();
				$sql = "UPDATE ACCOUNT SET UserName='$username', Hash='$pass' WHERE CodFiscale='".$codmodifica."'";
				$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
				chiusura($conn);
				$_SESSION['User'] = $username;
				session_destroy();
				echo 'Nome Utente e Password Cambiati con successo. Bisogna effettuare il login nuovamente <a href="login.php">Login</a>';
				
			}
				
				
			} else {
			
			$admin = ($_SESSION['Tipo'] == "Admin");
			$user = ($_SESSION['User']);
			
			
			$conn = connessione();
			$sql = "SELECT  ACCOUNT.UserName, PERSONA.Nome, PERSONA.Cognome, PERSONA.CodFiscale, PERSONA.DataNasc, PERSONA.LuogoNasc, PERSONA.Telefono, PERSONA.Mail, PERSONA.Sesso, ";
			if ($admin) { $sql = $sql."ISTRUTTORE.Qualifica, ISTRUTTORE.Retribuzione, ISTRUTTORE.DataAssunzione 
			FROM ACCOUNT JOIN PERSONA ON ACCOUNT.CodFiscale = PERSONA.CodFiscale JOIN ISTRUTTORE ON ACCOUNT.CodFiscale = ISTRUTTORE.CodFiscale"; } else { $sql = $sql."SOCIO.Livello, SOCIO.DataIscrizione FROM ACCOUNT JOIN PERSONA ON ACCOUNT.CodFiscale = PERSONA.CodFiscale JOIN SOCIO ON ACCOUNT.CodFiscale = SOCIO.CodFiscale";}
			$sql = $sql." WHERE ACCOUNT.UserName ='".$user."'";
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
				if (!$admin) {echo '<tr><td>Livello:</td><td>'.$row['Livello'].'</td></tr>';
				$dataisc = $row['DataIscrizione'];
				$data = date('d-m-Y', strtotime("$dataisc"));
				echo '<tr><td>Data Iscrizione:</td><td>'.$data.'</td></tr>';}
				echo '<tr><td>Telefono:</td><td>'.$row['Telefono'].'</td></tr>';
				echo '<tr><td>Mail:</td><td>'.$row['Mail'].'</td></tr>';
				$datanasc = $row['DataNasc'];
				$data = date('d-m-Y', strtotime("$datanasc"));
				echo '<tr><td>Data Nascita:</td><td>'.$data.'</td></tr>';
				echo '<tr><td>Luogo Nascita:</td><td>'.$row['LuogoNasc'].'</td></tr>';
				if ($admin) { echo '<tr><td>Qualifica:</td><td>'.$row['Qualifica'].'</td></tr>';
				echo '<tr><td>Retribuzione:</td><td>'.$row['Retribuzione'].'</td></tr>';
				echo '<tr><td>Data Assunzione:</td><td>'.$row['DataAssunzione'].'</td></tr>'; }
				echo '<tr><td><a href="utenti.php?moddati=mod">Modifica Dati Personali</a></td></tr>';
				echo '<tr><td><a href="utenti.php?moduspa=mod">Modifica Username e Password</a></td></tr>';
				echo '<tr><td colspan="3" height="50"></td></tr><tr><td colspan="3" height="50"><form action="" method="post"><button name="cancella" value="'.$row['CodFiscale'].'">Cancella Account</button></form> Cancella tutte le informazioni dell\'utente</td></tr>';
			}
				
			} else { echo '<tr><td>Errore, Utente non trovato <a href="utenti.php">Torna Indietro</a></td></tr>'; }
				echo '</table>';
		}
		
		
		
		
		
		
		
		
		?>
		
		
		
	</div>
</body>

</html>