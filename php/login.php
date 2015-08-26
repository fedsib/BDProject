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
	<p>Ti trovi in: Login</p>
	</div>

    <div id="nav"> 
		<?php
			menu();
		?>
    </div>
	
	<div id="content"> 
	<?php 
		//Funzione per il login dopo che è stato premuto il tasto di invio User/Pass
		if(isset($_POST['Submit'])){
		
		//Inizializzo variabili che vengono usate per il login
		$msg = "";
		$errore = FALSE;
		$User = trim($_POST['Username']);
		$Pass = SHA1(trim($_POST['Password']));
		
		//Controllo che nome utente e password usino solo i caratteri ammessi
		if (! preg_match('/^[a-zA-Z0-9]*$/', $User)) {
		$msg = $msg."<b>Errore! Il nome utente può contenere solo lettere e numeri</b><br />";
		$errore=TRUE;
		};
	
		if (! preg_match('/^[a-zA-Z0-9]*$/', $Pass)) {
		$msg = $msg."<b>Errore! La Password può contenere solo lettere e numeri</b><br />";
		$errore=TRUE;
		};
		
		//Se nome utente e password usano caratteri ammessi procedo con la connessione ed il recupero dei dati dal DB
		if(!$errore){
		$conn = connessione();
		$sql = "SELECT Admin, Hash FROM ACCOUNT WHERE UserName='$User'";
		$result = $conn->query($sql) or die("Errore nella query MySQL: ".$conn->error);
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$admin = $row['Admin'];
				$Hash = $row['Hash'];
		}
		
		
		//Confronto l'hash della password con quella salvata nel DB a quell'username
			if ($Pass == $Hash) {
				if ($admin) { $_SESSION['Tipo'] = "Admin"; } else { $_SESSION['Tipo'] = "User"; };
				$_SESSION['User']=$User;
				header("location:../index.php");
				exit;
			} else {
				$msg = "<b>Password Sbagliata</b><br />";
			}
		}else {
				$msg = "<b>Nome Utente non trovato</b><br />";
		}
		}
		
		}
		//Controllo se è già stato effettuato il login
		if (!isset($_SESSION['User'])) {
			//Se non è stato fatto mostro il form di login
			echo '<form action="" method="post" name="Login_Form">
			<table width="400" border="0" align="center" cellpadding="10" cellspacing="5" class="Table">
			<tr>
			<td colspan="2" align="left" valign="top"><h3>Login</h3></td>
			</tr>
			<tr>
			<td align="right" valign="top">Username</td>
			<td><input name="Username" type="text" class="Input" value='; if(isset($User)){ echo $User; } echo'></td>
			</tr>
			<tr>
			<td align="right">Password</td>
			<td><input name="Password" type="password" class="Input"></td>
			</tr>
			<tr>
			<td> </td>
			<td><input name="Submit" type="submit" value="Login"></td>
			</tr>';
			if(isset($msg)){
				echo '<tr>
				<td colspan="2" align="center" valign="top">'. $msg .'</td>
				</tr>';
			}
			echo '</table></form>'; 
			} else {
			//Altrimenti avviso che il login è già stato effettuato come e mostro il nome utente
			echo 'Hai già effetuato il login come: '. $_SESSION['User'];
			}	

	?>
 
	</div>
</body>

</html>