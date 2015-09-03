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
	<p>Ti trovi in: Gestione Utenti -> Aggiungi Utente</p>
	</div>

    <div id="nav"> 
		<?php
			menu();
		?>
    </div>

	<div id="content"> 

	
	
		<?php
		/*Pagina per l'aggiunta di un utente
		
		Controllo che l'utente abbia fatto il login, se sì
		controllo la variabile Tipo per controllare se ha i diritti di Amministratore*/
		if (!isset($_SESSION['User']) || ($_SESSION['Tipo']) != "Admin") {
			
			echo '<p>Bisogna effettuare il login come amministratore per vedere questa pagina.';
			
		}  else {
				//Recupero le variabili se è stato inviato il form, se non sono settato le imposto a stringa vuota
				$nome = isset($_POST['Nome']) ? trim($_POST['Nome']) : '';
				$cognome = isset($_POST['Cognome']) ? trim($_POST['Cognome']) : '';
				$codfiscale = isset($_POST['CodFiscale']) ? trim($_POST['CodFiscale']) : '';
				$datanasc = isset($_POST['DataNasc']) ? $_POST['DataNasc'] : '';
				$luogonasc = isset($_POST['LuogoNasc']) ? $_POST['LuogoNasc'] : '';
				$telefono = isset($_POST['Telefono']) ? $_POST['Telefono'] : '';
				$mail = isset($_POST['Mail']) ? trim($_POST['Mail']) : '';
				$sesso = isset($_POST['Sesso']) ? trim($_POST['Sesso']) : '';
				$livello = isset($_POST['Livello']) ? trim($_POST['Livello']) : '';
				$errore = true; //Setto true così da mostrare il form comunque in caso non siano stati inviati dati
				$msg = "";

			if  (isset($_POST['aggiungi'])) {	
				//Controllo tutti i campi immessi se trovo errori setto errore a true e aggiungo il messaggio di errore a $msg
				$errore = false; //Setto false così da non mostrare il form se i dati sono tutti corretti, se c'e' un errore viene 
								 //rimessa a true e mostra il form con gli errori
				
				if ((! preg_match('/^[a-zA-Z]*$/', $nome)) || $nome == '') {
				$msg = $msg."<b>Errore! Il nome puo' contenere solo lettere</b><br />";
				$errore=TRUE;

				};
				
				if ((! preg_match('/^[a-zA-Z]*$/', $cognome)) || $cognome == ''){
				$msg = $msg."<b>Errore! Il cognome puo' contenere solo lettere</b><br />";
				$errore=TRUE;
				};
				
				if ((! preg_match('/^[a-zA-Z0-9]*$/', $codfiscale)) || $codfiscale == '' || strlen($codfiscale) != 16) {
				$msg = $msg."<b>Errore! Il codice fiscale deve contenere solo lettere e numeri ed esser di 16 caratteri</b><br />";
				$errore=TRUE;
				};
				
				if ((! preg_match('/^[0-9\-]*$/', $datanasc)) || (($timestamp = strtotime($datanasc)) === FALSE)) {
				$msg = $msg."<b>Errore! La data deve contenere solo numeri e - ed essere nel formato corretto</b><br />";
				$errore = TRUE;
				} else {
				$sqldata = date('Y-m-d', strtotime("$datanasc"));
				}

				if ((! preg_match('/^[a-zA-Z ]*$/', $luogonasc)) || $luogonasc == '') {
				$msg = $msg."<b>Errore! Il luogo di nascita puo' contenere solo lettere e spazi</b><br />";
				$errore=TRUE;
				};
				
				if (! preg_match('/^[0-9]*$/', $telefono)){
				$msg = $msg."<b>Errore! Il numero di telefono puo' contenere solo numeri</b><br />";
				$errore=TRUE;
				};
				
				if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
					$msg = $msg."<b>Errore! La mail contiene caratteri non ammessi</b><br />";
					$errore=TRUE;
				}

			}
				//Controllo se devo mostrare il form o meno
			if ($errore) {
			
			echo '<form action="" method="post" name="Form Modifica Dati Personali">
			<table width="600" border="0" align="center" cellpadding="5" cellspacing="5" class="Table">
			<tr>
			<td colspan="3" align="left" valign="top"><h3>Inserisci Dati Personali Utente</h3></td>
			</tr
			<tr>
			<td align="right">Nome *</td>
			<td colspan="2"><input name="Nome" type="text" class="Input" value="'; if(isset($nome)){ echo "$nome"; } echo'"></td>
			</tr>
			<tr>
			<td align="right">Cognome *</td>
			<td colspan="2"><input name="Cognome" type="text" class="Input" value="'; if(isset($cognome)){ echo "$cognome"; } echo'"></td>
			</tr>
			<tr>
			<td align="right">Codice Fiscale *</td>
			<td colspan="2"><input name="CodFiscale" type="text" class="Input" value="'; if(isset($codfiscale)){ echo "$codfiscale"; } echo'"></td>
			</tr>
			<tr>
			<td align="right">Data di Nascita*</td>
			<td><input name="DataNasc" type="text" class="Input" value="'; if(isset($datanasc)){ echo "$datanasc"; } echo'"></td><td>Formato: gg-mm-aaaa </td>
			</tr>
			<tr>
			<td align="right">Luogo di Nascita *</td>
			<td colspan="2"><input name="LuogoNasc" type="text" class="Input" value="'; if(isset($luogonasc)){ echo "$luogonasc"; } echo'"></td>
			</tr>
			<tr>
			<td align="right">Telefono</td>
			<td colspan="2"><input name="Telefono" type="text" class="Input" value="'; if(isset($telefono)){ echo "$telefono"; } echo'"></td>
			</tr>
			<tr>
			<td align="right">Indirizzo Mail *</td>
			<td colspan="2"><input name="Mail" type="text" class="Input" value="'; if(isset($mail)){ echo "$mail"; } echo'"></td>
			</tr>
			<tr>
			<td align="right">Sesso</td>
			<td colspan="2"><select name="Sesso">
			<option value="Maschio">Maschio</option>';
			if ($sesso == "Femmina") { echo '<option value="Femmina" selected>Femmina</option>'; } else { echo '<option value="Femmina">Femmina</option>'; }
			echo '</select></td>
			</tr>
			<tr>
			<td align="right">Livello</td>
			<td colspan="2"><select name="Livello"><option value="Principiante">Principiante</option>';
			if ($livello == "Intermedio") { echo '<option value="Intermedio" selected>Intermedio</option>'; } else { echo '<option value="Intermedio">Intermedio</option>'; }
			if ($livello == "Esperto") { echo '<option value="Esperto" selected>Esperto</option>'; } else { echo '<option value="Esperto">Esperto</option>'; }
			echo '</tr>
			<tr>
			<td colspan="2"><button name="aggiungi" type="submit" value="aggiungi">Aggiungi Utente</button></form></td><td><form action="gutenti.php"><button >Annulla</button></form></td>
			</tr>
			<tr><td colspan="3">*Questi campi non possono essere vuoti</td></tr>
			<tr><td colspan="3">'.$msg.'</td></tr>
			</table>
			';
			} else {
				/*Se ci sono i dati e sono corretti invece del form proseguo con l'inserimento dei dati
				Venendo generato da un admin imposto come default di nomeutente/password 
				il codice fiscale che sono poi modificabili dai singoli account*/
				$pass1 = substr($nome, 0, 3);
				$pass2 = substr($cognome, 0, 3);
				$user = $codfiscale;
				$pass = $pass1.$pass2;
				$pass = SHA1($user);
				
				//Eseguo la connessione e preparo le query da fare
				$conn = connessione();
				$conn->autocommit(0);
				$sql = "INSERT INTO PERSONA (Nome, Cognome, CodFiscale, DataNasc, LuogoNasc, ";
				if ($telefono != '') { $sql = $sql. "Telefono,";} 
				$sql = $sql. "Mail, Sesso) VALUES ('$nome','$cognome','$codfiscale','$sqldata','$luogonasc',";
				if ($telefono != '') { $sql = $sql. "'$telefono',";}
				$sql = $sql. "'$mail', '$sesso')";
				$data = date('Y-m-d');
				$sql1 = "INSERT INTO SOCIO (CodFiscale, DataIscrizione, Livello) VALUES ('$codfiscale','$data','$livello')";
				$sql2 = "INSERT INTO ACCOUNT (CodFiscale, UserName, Admin, Hash) VALUES ('$codfiscale','$user','0','$pass')";
				try {
				//Eseguo le tre query se ci sono problemi interrompo e non applico le modifiche
				$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
				if (!$result) { throw new Exception("Errore nell'inserimento non effettuo le operazioni."); }
				$result = $conn->query($sql1) or die("Errore nella query MySQL");
				if (!$result) { throw new Exception("Errore nell'inserimento non effettuo le operazioni."); }
				$result = $conn->query($sql2) or die("Errore nella query MySQL");
				if (!$result) { throw new Exception("Errore nell'inserimento non effettuo le operazioni."); }
				
				//Se non ci sono problemi applico le modifiche sul DB
				$conn->commit();
				echo '<table width="400" border="0" align="center" cellpadding="5" cellspacing="5">
				<tr><td colspan="2">Utente aggiunto con successo</td></tr>
				<tr><td>Username:</td><td>'.$user.'</td></tr>
				<tr><td>Password:</td><td>'.$user.'</td></tr>
				<tr><td colspan="2">Consigliare di cambiare password al primo login</td></tr>
				</table>';
				
				} catch (Exception $e) {

			$conn->rollback();
			echo $e->getMessage();
			} 
				
			}
			
		}
		?>
		
		
		
	</div>
</body>

</html>