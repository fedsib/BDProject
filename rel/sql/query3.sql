/* Estrae le prenotazioni fatte tra la data del 3 settembre ed il 4 settembre mostrando nome dell'utente associato o il nome dell'istruttore e del corso */
SELECT PERSONA.Nome, PERSONA.Cognome, ACCOUNT.UserName, PRENOTAZIONE.CodCampo, PRENOTAZIONE.Data, PRENOTAZIONE.Ora, CORSO.NomeCorso 
FROM PRENOTAZIONE LEFT JOIN CORSO ON PRENOTAZIONE.CodCorso = CORSO.CodCorso JOIN PERSONA ON (PRENOTAZIONE.CodFiscale = PERSONA.CodFiscale) OR (CORSO.CodFiscale = PERSONA.CodFiscale) LEFT JOIN ACCOUNT ON PRENOTAZIONE.CodFiscale = ACCOUNT.CodFiscale
WHERE PRENOTAZIONE.Data >= '2015-09-03' AND PRENOTAZIONE.Data <= '2015-09-04'
ORDER BY PRENOTAZIONE.Data, PRENOTAZIONE.Ora
