/*Se le tabelle esistono vengono eliminate*/
DROP TABLE IF EXISTS PERSONA;
DROP TABLE IF EXISTS ISTRUTTORE;
DROP TABLE IF EXISTS SOCIO;
DROP TABLE IF EXISTS ACCOUNT;
DROP TABLE IF EXISTS CORSO;
DROP TABLE IF EXISTS CAMPO;
DROP TABLE IF EXISTS LEZIONE;
DROP TABLE IF EXISTS ISCRITTOCORSO;
DROP TABLE IF EXISTS PRENOTAZIONE;

CREATE TABLE PERSONA (
 CodFiscale char(16) NOT NULL,
 Nome varchar(20) NOT NULL,
 Cognome varchar(20) NOT NULL,
 DataNasc date NOT NULL,
 LuogoNasc varchar(40) NOT NULL,
 Telefono varchar(20) DEFAULT NULL,
 Mail varchar(40) NOT NULL,
 Sesso enum('Maschio','Femmina') NOT NULL,
 PRIMARY KEY (CodFiscale),
 UNIQUE KEY Mail (Mail)
) ENGINE=InnoDB;

CREATE TABLE ISTRUTTORE (
 CodFiscale char(16) NOT NULL,
 Qualifica varchar(255) DEFAULT NULL,
 Retribuzione int NOT NULL,
 DataAssunzione date NOT NULL,
 PRIMARY KEY (CodFiscale),
 FOREIGN KEY (CodFiscale) REFERENCES PERSONA (CodFiscale) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE SOCIO (
 CodFiscale char(16) NOT NULL,
 DataIscrizione date NOT NULL,
 Livello enum('Principiante','Intermedio','Esperto') NOT NULL,
 PRIMARY KEY (CodFiscale),
 FOREIGN KEY (CodFiscale) REFERENCES PERSONA (CodFiscale) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE ACCOUNT (
 CodFiscale char(16) NOT NULL,
 UserName varchar(20) NOT NULL,
 Admin bool NOT NULL DEFAULT '0',
 Hash varchar(255) NOT NULL,
 PRIMARY KEY (CodFiscale),
 UNIQUE KEY UserName (UserName),
 FOREIGN KEY (CodFiscale) REFERENCES PERSONA (CodFiscale) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE CORSO (
 CodCorso int NOT NULL AUTO_INCREMENT,
 NomeCorso varchar(255) NOT NULL,
 TipoCorso enum('Principiante','Intermedio','Avanzato') NOT NULL,
 Attivo bool NOT NULL DEFAULT '0',
 CodFiscale char(16) DEFAULT NULL,
 PRIMARY KEY (CodCorso),
 FOREIGN KEY (CodFiscale) REFERENCES ISTRUTTORE (CodFiscale) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE CAMPO (
 CodCampo tinyint NOT NULL,
 TipoSup enum('Terra Rossa','Erba Sintetica','PlayIt') NOT NULL,
 PRIMARY KEY (CodCampo)
) ENGINE=InnoDB;

CREATE TABLE LEZIONE (
 CodLezione int NOT NULL,
 CodCorso int NOT NULL,
 PRIMARY KEY (CodCorso,CodLezione),
 FOREIGN KEY (CodCorso) REFERENCES CORSO (CodCorso) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE ISCRITTOCORSO (
 CodCorso int NOT NULL,
 CodFiscale char(16) NOT NULL,
 PRIMARY KEY (CodCorso,CodFiscale),
 FOREIGN KEY (CodCorso) REFERENCES CORSO (CodCorso) ON DELETE CASCADE ON UPDATE CASCADE,
 FOREIGN KEY (CodFiscale) REFERENCES SOCIO (CodFiscale) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE PRENOTAZIONE (
 CodCorso int DEFAULT NULL,
 CodLezione int DEFAULT NULL,
 CodFiscale char(16) DEFAULT NULL,
 CodCampo tinyint NOT NULL,
 Data date NOT NULL,
 Ora int NOT NULL,
 PRIMARY KEY (CodCampo,Data,Ora),
 FOREIGN KEY (CodCorso, CodLezione) REFERENCES LEZIONE (CodCorso, CodLezione) ON DELETE CASCADE ON UPDATE CASCADE,
 FOREIGN KEY (CodCampo) REFERENCES CAMPO (CodCampo) ON DELETE CASCADE ON UPDATE CASCADE,
 FOREIGN KEY (CodFiscale) REFERENCES PERSONA (CodFiscale) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

/*INIZIO DEI TRIGGER*/

/*Controlla che la retribuzione degli istruttori non sia inferiore a zero per nuovi inserimenti
nel caso lo sia, imposta di default una retribuzione di 800 euro*/
DROP TRIGGER IF EXISTS InserimentoRetribuzione;
DELIMITER |
CREATE TRIGGER  InserimentoRetribuzione BEFORE INSERT ON ISTRUTTORE
	FOR EACH ROW
	BEGIN
		IF NEW.Retribuzione<0
		THEN SET NEW.Retribuzione = 800;
		END IF;
	END;
|
DELIMITER ;

/*Controlla che la retribuzione degli istruttori non sia inferiore a zero, quando viene aggiornata, nel caso
lo sia, reimposta la vecchia retribuzione*/
DROP TRIGGER IF EXISTS AggiornaRetribuzione;
DELIMITER |
CREATE TRIGGER AggiornaRetribuzione BEFORE UPDATE ON ISTRUTTORE
	FOR EACH ROW
	BEGIN
		IF NEW.Retribuzione<0
		THEN SET NEW.Retribuzione = OLD.Retribuzione;
	END IF;
	END;
|
DELIMITER;
	
/*Se manca il codice fiscale dell'istruttore nel corso, il corso non e' attivo, vale per l'inserimento*/
DROP TRIGGER IF EXISTS CorsoAttivoIns;
DELIMITER |
CREATE TRIGGER  CorsoAttivoIns BEFORE INSERT ON CORSO
	FOR EACH ROW
	BEGIN
		IF NEW.CodFiscale IS NULL
		THEN SET NEW.Attivo = 0;
		END IF;
	END;
|
DELIMITER ;

/*Se manca il codice fiscale dell'istruttore nel corso, il corso non e' attivo, vale per l'aggiornamento*/
DROP TRIGGER IF EXISTS CorsoAttivoUpd;
DELIMITER |
CREATE TRIGGER  CorsoAttivoUpd BEFORE UPDATE ON CORSO
	FOR EACH ROW
	BEGIN
		IF NEW.CodFiscale IS NULL
		THEN SET NEW.Attivo = 0;
		END IF;
	END;
|
DELIMITER ;
	
/*Se un'istruttore viene eliminato, imposta il campo attivo di corso su falso (0)*/
DROP TRIGGER IF EXISTS CorsoAttivoElim;
DELIMITER |
CREATE TRIGGER CorsoAttivoElim BEFORE DELETE ON PERSONA
FOR EACH ROW
	BEGIN
		DECLARE cod int;
		SELECT COUNT(1) INTO cod FROM CORSO
        WHERE CodFiscale = OLD.CodFiscale;
		IF cod > 0 THEN
		UPDATE CORSO AS c SET c.Attivo = 0 WHERE c.CodFiscale = OLD.CodFiscale;
		END IF;
	END;
|
DELIMITER ;	

/*Ogni volta che si cancella un CodFiscale in persona bisogna fare un controllo in corso ed eventualmente cambiare Attivo*/

/*FINE DEI TRIGGER*/

/*INIZIO FUNCTION*/
/* Controlla che un utente/amministratore non abbia altre prenotazioni al momento se non la trova la aggiunge altrimenti da errore.
Viene usata nelle prenotazioni personali */

DROP FUNCTION IF EXISTS ControlloPrenotazione;
DELIMITER |

CREATE FUNCTION ControlloPrenotazione(cod CHAR(16), d DATE, h int, c int) RETURNS CHAR(100)
BEGIN	
	DECLARE presente int;
	SELECT COUNT(*) INTO presente FROM PRENOTAZIONE LEFT JOIN CORSO ON PRENOTAZIONE.CodCorso = CORSO.CodCorso 
	WHERE PRENOTAZIONE.Data = d AND PRENOTAZIONE.Ora = h AND (PRENOTAZIONE.CodFiscale = cod || CORSO.CodFiscale = cod);
	
		IF presente > 0 THEN
			RETURN CONCAT('Errore, hai gia\' una prenotazione nella data ',d,' alle ',h);
		ELSE
			INSERT INTO PRENOTAZIONE (CodFiscale, CodCampo, Data, Ora) VALUES (cod, c, d, h);
			RETURN 'Prenotazione aggiunta con successo';
		END IF;
END;
|
DELIMITER ;


/* Controlla che un utente/amministratore non abbia altre prenotazioni al momento 
se non la trova la aggiunge altrimenti da errore. Viene usata nelle aggiunta delle lezioni*/

DROP FUNCTION IF EXISTS ControlloPrenotazioneCorso;
DELIMITER |
CREATE FUNCTION ControlloPrenotazioneCorso(cod CHAR(16), d DATE, h int, c int, corso int) RETURNS CHAR(120)
BEGIN	
	DECLARE presente INT;
	DECLARE num INT;
	SELECT COUNT(*) INTO presente FROM PRENOTAZIONE LEFT JOIN CORSO ON PRENOTAZIONE.CodCorso = CORSO.CodCorso 
	WHERE PRENOTAZIONE.Data = d AND PRENOTAZIONE.Ora = h AND (PRENOTAZIONE.CodFiscale = cod || CORSO.CodFiscale = cod);
	SELECT CodLezione INTO num FROM LEZIONE WHERE CodCorso = corso ORDER BY CodLezione DESC LIMIT 1;
		IF (ISNULL(num)) THEN
			SET num = 0;
		END IF;
		IF presente > 0 THEN
			RETURN CONCAT('Errore, questo istruttore ha gia\' una prenotazione il ',d,' alle ',h);
		ELSE
			SET num = num + 1;
			INSERT INTO LEZIONE (CodCorso, CodLezione) VALUES (corso, num);
			INSERT INTO PRENOTAZIONE (CodCorso, CodLezione, CodCampo, Data, Ora) VALUES (corso, num, c, d, h);
			RETURN 'Prenotazione aggiunta con successo';
		END IF;
END;
|
DELIMITER ;


/* Estrae il livello del Corso in difficolta', lo confronta con le possibilita' e gli da un valore numerico 1-2-3,
 fa la stessa cosa per il livello di abilita' del socio. Poi confronta difficolta > abilita e ritorna un emsssaggio, 
 se e' Iscriviti il php visualizza il bottone di iscrizione
*/

DROP FUNCTION IF EXISTS PossoIscrivermi;
DELIMITER |
CREATE FUNCTION PossoIscrivermi(usr VARCHAR(40), corso int) RETURNS CHAR(120)
BEGIN 
	DECLARE difficolta char(20);
	DECLARE d int;
	DECLARE abilita char(20);
	DECLARE a int;
	SET difficolta = (SELECT TipoCorso FROM CORSO WHERE CodCorso = corso);
	IF difficolta = "Avanzato" THEN
		SET d = 3;
	ELSE
		IF difficolta = "Intermedio" THEN
			SET d = 2;
		ELSE
			SET d = 1;
		END IF;
	END IF;
	
	SET abilita = (SELECT SOCIO.Livello FROM SOCIO JOIN ACCOUNT ON SOCIO.CodFiscale = ACCOUNT.CodFiscale WHERE ACCOUNT.UserName = usr);
	IF abilita = "Avanzato" THEN
		SET a = 3;
	ELSE
		IF abilita = "Intermedio" THEN
			SET a = 2;
		ELSE
			SET a = 1;
		END IF;
	END IF;
	
	IF d > a THEN
		RETURN 'Questo corso richiede un livello di abilita\' maggiore';
	ELSE
		RETURN 'Iscriviti';
	END IF;
END;
|
DELIMITER ;

/*FINE FUNCTION*/