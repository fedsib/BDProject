/* Recupero le informazioni riguardanti il corso 4 */
SELECT CORSO.Attivo, CORSO.CodCorso, CORSO.NomeCorso, CORSO.TipoCorso, PERSONA.Nome, PERSONA.Cognome
FROM CORSO
LEFT JOIN PERSONA ON CORSO.CodFiscale = PERSONA.CodFiscale 
WHERE CORSO.CodCorso ='4';
