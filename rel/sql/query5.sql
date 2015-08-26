/* Recupero la lista delle lezioni del corso 2 e, se disponibili  anche le prenotazioni corrispondenti */
SELECT PRENOTAZIONE.Data, PRENOTAZIONE.Ora, PRENOTAZIONE.CodCampo, PRENOTAZIONE.CodLezione
FROM PRENOTAZIONE 
WHERE PRENOTAZIONE.CodCorso ='2'
ORDER BY  PRENOTAZIONE.Data, PRENOTAZIONE.Ora;
