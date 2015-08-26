/* Recupera la lista di tutti i corsi con i nomi degli istruttori associati */
SELECT CORSO.CodCorso, CORSO.NomeCorso, CORSO.TipoCorso, CORSO.Attivo, PERSONA.Nome, PERSONA.Cognome
FROM CORSO LEFT JOIN PERSONA ON CORSO.CodFiscale = PERSONA.CodFiscale;

/* Recupero le informazioni riguardanti il corso 4 */
SELECT CORSO.Attivo, CORSO.CodCorso, CORSO.NomeCorso, CORSO.TipoCorso, PERSONA.Nome, PERSONA.Cognome
FROM CORSO
LEFT JOIN PERSONA ON CORSO.CodFiscale = PERSONA.CodFiscale 
WHERE CORSO.CodCorso ='4';

/* Estrae le prenotazioni fatte dopo la data del 25 agosto 2015, mostrando per ognuna i dati dell'utente e della prenotazione */
SELECT PERSONA.Nome, PERSONA.Cognome, ACCOUNT.UserName, PRENOTAZIONE.CodCampo, PRENOTAZIONE.Data, PRENOTAZIONE.Ora, CORSO.NomeCorso 
FROM PRENOTAZIONE LEFT JOIN CORSO ON PRENOTAZIONE.CodCorso = CORSO.CodCorso JOIN PERSONA ON (PRENOTAZIONE.CodFiscale = PERSONA.CodFiscale) OR (CORSO.CodFiscale = PERSONA.CodFiscale) LEFT JOIN ACCOUNT ON PRENOTAZIONE.CodFiscale = ACCOUNT.CodFiscale
WHERE PRENOTAZIONE.Data >= '2015-08-25'
ORDER BY PRENOTAZIONE.Data, PRENOTAZIONE.Ora;

/* Restituisce, se c' e' una prenotazione per il campo 3 nella data 16-09-2015 alle ore 13 */
SELECT PRENOTAZIONE.Data, PRENOTAZIONE.Ora, PRENOTAZIONE.CodCampo 
FROM PRENOTAZIONE 
WHERE PRENOTAZIONE.CodCampo = '3' AND PRENOTAZIONE.Data = '2015-09-16' AND PRENOTAZIONE.Ora = '13';

/* Recupero la lista delle lezioni del corso 2 e, se disponibili  anche le prenotazioni corrispondenti */
SELECT PRENOTAZIONE.Data, PRENOTAZIONE.Ora, PRENOTAZIONE.CodCampo, PRENOTAZIONE.CodLezione
FROM PRENOTAZIONE 
WHERE PRENOTAZIONE.CodCorso ='2'
ORDER BY  PRENOTAZIONE.Data, PRENOTAZIONE.Ora;

/* Estrae le informazioni di tutti i corsi attivi con nome e cognome del rispettivo istruttore ed anche controllo se l utente attualmente loggato e iscritto al corso */
SELECT CORSO.CodCorso, CORSO.NomeCorso, CORSO.TipoCorso, ACCISC.UserName, PERSONA.Nome, PERSONA.Cognome
FROM CORSO JOIN PERSONA ON CORSO.CodFiscale = PERSONA.CodFiscale
LEFT JOIN
(SELECT ACCOUNT.UserName, ISCRITTOCORSO.CodCorso 
FROM ACCOUNT JOIN ISCRITTOCORSO ON ACCOUNT.CodFiscale = ISCRITTOCORSO.CodFiscale 
WHERE ACCOUNT.UserName='user') AS ACCISC
ON CORSO.CodCorso = ACCISC.CodCorso 
WHERE CORSO.Attivo='1'";

/* Mostra le prenotazioni dell'utente che visualizza la pagina alla data indicata */
SELECT PRENOTAZIONE.Data, PRENOTAZIONE.Ora, PRENOTAZIONE.CodCampo 
FROM ACCOUNT 
JOIN PRENOTAZIONE ON ACCOUNT.CodFiscale=PRENOTAZIONE.CodFiscale
WHERE ACCOUNT.UserName ='user' AND PRENOTAZIONE.Data >= '2015-08-20' 
ORDER BY PRENOTAZIONE.Data, PRENOTAZIONE.Ora;




