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
		//Mostra il link di login presente su phpfunctions oppure il nome dell'utente ed il link di log out
			loginlink();
		?>
	</p>
	<p>Ti trovi in: Homepage</p>
	</div>

    <div id="nav"> 
		<?php
		//Mostra il menu presente nel file phpfunctions, presente su tutte le pagine
			menu();
		?>
    </div>

	<div id="content"> 

	
	
		<?php
		//Pagina iniziale del sito aperta sull'apertura e subito dopo aver effettuato il login con successo
		
		echo '<p>Progetto di Basi di Dati</p>'
		
		?>
		
		
		
	</div>

</body>

</html>
