/* Restituisce, se c' e' una prenotazione per il campo 3 nella data 16-09-2015 alle ore 13 */
SELECT PRENOTAZIONE.Data, PRENOTAZIONE.Ora, PRENOTAZIONE.CodCampo 
FROM PRENOTAZIONE 
WHERE PRENOTAZIONE.CodCampo = '3' AND PRENOTAZIONE.Data = '2015-09-16' AND PRENOTAZIONE.Ora = '13';
