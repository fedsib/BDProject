/*Controlla che la retribuzione degli istruttori non sia inferiore a zero per nuovi inserimenti*/
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

/*Controlla che la retribuzione degli istruttori non sia inferiore a zero, quando viene aggiornata*/
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
DELIMITER ;
/*
Questa è stata un bel casino, alla fine ho messo che se minore non si cambia perché, dopo varie ricerche, risulta che quella precedente semplicemente impedisce di inserire il dato però non c'è modo dare effettivamente un errore.
E siccome abbiamo anche interfaccia web dare solo un errore che impedisce l'inserimento senza però dire niente è brutto, in questo modo comunque si impedisce di mettere un valore negativo. Stessa cosa per quella sopra dove ho ipotizzato un 800 come retribuzione minima
*/

	
/*Se manca il codice fiscale dell'istruttore nel corso, il corso non è attivo, vale per l'inserimento*/
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

/*Se manca il codice fiscale dell'istruttore nel corso, il corso non è attivo, vale per l'aggiornamento*/
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

/*
Ho dovuto cambiare questa perché, a quanto pare, non si attivano trigger quanto una foreign key viene cambiata quindi ogni volta che si cancella un CodFiscale in persona bisogna fare un controllo in corso ed eventualmente cambiare Attivo
*/