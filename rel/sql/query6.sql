/* Estrae le informazioni di tutti i corsi attivi con nome e cognome del rispettivo istruttore ed anche controllo se l utente attualmente loggato e iscritto al corso */
SELECT CORSO.CodCorso, CORSO.NomeCorso, CORSO.TipoCorso, ACCISC.UserName, PERSONA.Nome, PERSONA.Cognome
FROM CORSO JOIN PERSONA ON CORSO.CodFiscale = PERSONA.CodFiscale
LEFT JOIN
(SELECT ACCOUNT.UserName, ISCRITTOCORSO.CodCorso 
FROM ACCOUNT JOIN ISCRITTOCORSO ON ACCOUNT.CodFiscale = ISCRITTOCORSO.CodFiscale 
WHERE ACCOUNT.UserName='user') AS ACCISC
ON CORSO.CodCorso = ACCISC.CodCorso 
WHERE CORSO.Attivo='1';
