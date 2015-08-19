<?php 
	session_start();
	require "./functions/phpfunctions.php" 
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">

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
	<p>Ti trovi in: <span xml:lang="en">Home</span></p>

	</div>

    <div id="nav"> 
		<?php
			menu();
		?>
    </div>

	<div id="content"> 

	
	
		<?php
		print_r($_SESSION);
		
		?>
		
		
		
	</div>

	<div id="footer">
		<ul>
			<li id="footleft"><a href="chisiamo.html">Chi Siamo</a></li>
			<li id="footmid" accesskey="C"><a href="contatti.html">Contatti</a></li>
			<li id="footmid" accesskey="3"><a href="mappa.html">Mappa del sito</a></li> 
			<li id="footright"><a href="notelegali.html">Note Legali</a></li>         
		</ul> 
    </div>
</body>

</html>
