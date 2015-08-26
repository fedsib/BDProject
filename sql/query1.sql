/* Recupera la lista di tutti i corsi con i nomi degli istruttori associati */
SELECT CORSO.CodCorso, CORSO.NomeCorso, CORSO.TipoCorso, CORSO.Attivo, PERSONA.Nome, PERSONA.Cognome
FROM CORSO LEFT JOIN PERSONA ON CORSO.CodFiscale = PERSONA.CodFiscale;
