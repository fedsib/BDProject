
<?php

/*Funzione per effettuare il collegamente al DB */
function connessione() {
$servername = "localhost";
$username = "mleorato";
$password = "nLjjA6dr";
$dbname="mleorato-PR";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

return $conn;
}

//Chiudo la connessione al DB se necessario
function chiusura($conn){
	$conn->close();
}

?>



<?php
//Visualizzo link a login oppure il nome utente ed il link per il logout
function loginlink(){

if (!isset($_SESSION['User'])) {
echo '<a href="login.php">Effettua Login</a>';
} else {
	echo 'Login fatto come: '. ($_SESSION['User']) .'  <a href="logout.php">Effettua Logout</a>';
};

}

?> 

<?php

//Mostro il menu del sito
function menu(){
//Parte visibile a tutti
echo '<p id="menubase">
	<ul>
		<li class="link_menu"><a href="index.php">Home</a></li> 
	</ul>
</p>';

//Parte visibile solo agli utenti che hanno effettuato il login
if (isset($_SESSION['User'])) {
	echo '<p id="menuutente">Area Utente:</p>
	<ul>
		<li class="link_menu"><a href="utenti.php">Gestione Account</a></li>';
 		if (($_SESSION['Tipo']) == "User") { echo '<li class="link_menu"><a href="corsi.php">Iscrizione Corsi</a></li>'; }
	echo '<li class="link_menu"><a href="prenotazione.php">Prenotazione Campi</a></li> 
	</ul>
</p>';
	
//Parte visibile solo agli amministratori
	if($_SESSION['Tipo'] == 'Admin')
echo '<p id="menuamministratore">Area Amministratore:</p>
	<ul>
		<li class="link_menu"><a href="gcorsi.php">Gestione Corsi</a></li> 
		<li class="link_menu"><a href="gprenotazioni.php">Gestione Prenotazioni</a></li> 
		<li class="link_menu"><a href="gcampi.php">Gestione Campi</a></li>
		<li class="link_menu"><a href="gutenti.php">Gestione Utenti</a></li>
	</ul>
</p>';
	}
}

?>
