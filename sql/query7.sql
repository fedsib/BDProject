/* Mostra le prenotazioni dell'utente che visualizza la pagina alla data indicata */
SELECT PRENOTAZIONE.Data, PRENOTAZIONE.Ora, PRENOTAZIONE.CodCampo 
FROM ACCOUNT 
JOIN PRENOTAZIONE ON ACCOUNT.CodFiscale=PRENOTAZIONE.CodFiscale
WHERE ACCOUNT.UserName ='user' AND PRENOTAZIONE.Data >= '2015-08-20' 
ORDER BY PRENOTAZIONE.Data, PRENOTAZIONE.Ora;
