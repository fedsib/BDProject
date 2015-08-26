/*INIZIO FUNCTION*/
/* Controlla che un utente/amministratore non abbia altre prenotazioni al momento se non la trova la aggiunge altrimenti da errore.
Viene usata nelle prenotazioni personali */

DROP FUNCTION IF EXISTS ControlloPrenotazione;
DELIMITER |
CREATE FUNCTION ControlloPrenotazione(cod CHAR(16), d DATE, h int, c int) RETURNS CHAR(100)
BEGIN	
	DECLARE presente int;
	SELECT COUNT(*) INTO presente FROM PRENOTAZIONE LEFT JOIN CORSO ON PRENOTAZIONE.CodCorso = CORSO.CodCorso LEFT JOIN ISCRITTOCORSO ON PRENOTAZIONE.CodCorso = ISCRITTOCORSO.CodCorso
	WHERE PRENOTAZIONE.Data = d AND PRENOTAZIONE.Ora = h AND (PRENOTAZIONE.CodFiscale = cod || CORSO.CodFiscale = cod || ISCRITTOCORSO.CodFiscale = cod);
	
		IF presente > 0 THEN
			RETURN CONCAT('Errore, hai gia una prenotazione nella data ',d,' alle ',h);
		ELSE
			INSERT INTO PRENOTAZIONE (CodFiscale, CodCampo, Data, Ora) VALUES (cod, c, d, h);
			RETURN 'Prenotazione aggiunta con successo';
		END IF;
END;
|
DELIMITER ;

/* Controlla che l'istruttore che tiene un corso non abbia gia' altre prenotazioni personali. Viene usata nelle aggiunta delle lezioni*/

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
			RETURN CONCAT('Errore, questo istruttore ha gia una prenotazione il ',d,' alle ',h);
		ELSE
			SET num = num + 1;
			INSERT INTO LEZIONE (CodCorso, CodLezione) VALUES (corso, num);
			INSERT INTO PRENOTAZIONE (CodCorso, CodLezione, CodCampo, Data, Ora) VALUES (corso, num, c, d, h);
			RETURN 'Prenotazione aggiunta con successo';
		END IF;
END;
|
DELIMITER ;


/* Estrae il livello del Corso in difficolta', lo confronta con i valori previsti e gli da un valore numerico 1-2-3,
 fa la stessa cosa per il livello di abilita' del socio. Poi confronta difficolta > abilita e ritorna un messsaggio, 
 se e' Iscriviti il php visualizza il bottone di iscrizione*/

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
	IF abilita = "Esperto" THEN
		SET a = 3;
	ELSE
		IF abilita = "Intermedio" THEN
			SET a = 2;
		ELSE
			SET a = 1;
		END IF;
	END IF;
	
	IF d > a THEN
		RETURN 'Questo corso richiede un livello di abilita maggiore';
	ELSE
		RETURN 'Iscriviti';
	END IF;
END;
|
DELIMITER ;

/*FINE FUNCTION*/

