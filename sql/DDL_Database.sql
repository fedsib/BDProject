CREATE TABLE PERSONA (
 CodFiscale char(16) NOT NULL,
 Nome varchar(20) NOT NULL,
 Cognome varchar(20) NOT NULL,
 DataNasc date NOT NULL,
 LuogoNasc varchar(40) NOT NULL,
 Telefono varchar(20) DEFAULT NULL,
 Mail varchar(40) NOT NULL,
 Sesso enum('Maschio','Femmina') NOT NULL,
 PRIMARY KEY (CodFiscale),
 UNIQUE KEY Mail (Mail)
) ENGINE=InnoDB;


CREATE TABLE ISTRUTTORE (
 CodFiscale char(16) NOT NULL,
 Qualifica varchar(255) DEFAULT NULL,
 Retribuzione int NOT NULL,
 DataAssunzione date NOT NULL,
 PRIMARY KEY (CodFiscale),
 FOREIGN KEY (CodFiscale) REFERENCES PERSONA (CodFiscale) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE SOCIO (
 CodFiscale char(16) NOT NULL,
 DataIscrizione date NOT NULL,
 Livello enum('Principiante','Intermedio','Esperto') NOT NULL,
 PRIMARY KEY (CodFiscale),
 FOREIGN KEY (CodFiscale) REFERENCES PERSONA (CodFiscale) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE CORSO (
 CodCorso int NOT NULL AUTO_INCREMENT,
 NomeCorso varchar(255) NOT NULL,
 TipoCorso enum('Principiante','Intermedio','Avanzato') NOT NULL,
 Attivo bool NOT NULL DEFAULT '0',
 CodFiscale char(16) DEFAULT NULL,
 PRIMARY KEY (CodCorso),
 FOREIGN KEY (CodFiscale) REFERENCES ISTRUTTORE (CodFiscale) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE CAMPO (
 CodCampo tinyint NOT NULL,
 TipoSup enum('Terra Rossa','Erba Sintetica','PlayIt') NOT NULL,
 PRIMARY KEY (CodCampo)
) ENGINE=InnoDB;

CREATE TABLE LEZIONE (
 CodLezione int NOT NULL,
 CodCorso int NOT NULL,
 PRIMARY KEY (CodCorso,CodLezione),
 FOREIGN KEY (CodCorso) REFERENCES CORSO (CodCorso) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE ISCRITTOCORSO (
 CodCorso int NOT NULL,
 CodFiscale char(16) NOT NULL,
 PRIMARY KEY (CodCorso,CodFiscale),
 FOREIGN KEY (CodCorso) REFERENCES CORSO (CodCorso) ON DELETE CASCADE ON UPDATE CASCADE,
 FOREIGN KEY (CodFiscale) REFERENCES SOCIO (CodFiscale) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE PRENOTAZIONE (
 CodCorso int DEFAULT NULL,
 CodLezione int DEFAULT NULL,
 CodFiscale char(16) DEFAULT NULL,
 CodCampo tinyint NOT NULL,
 Data date NOT NULL,
 Ora int NOT NULL,
 PRIMARY KEY (CodCampo,Data,Ora),
 FOREIGN KEY (CodCorso, CodLezione) REFERENCES LEZIONE (CodCorso, CodLezione) ON DELETE CASCADE ON UPDATE CASCADE,
 FOREIGN KEY (CodCampo) REFERENCES CAMPO (CodCampo) ON DELETE CASCADE ON UPDATE CASCADE,
 FOREIGN KEY (CodFiscale) REFERENCES PERSONA (CodFiscale) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE ACCOUNT (
 CodFiscale char(16) NOT NULL,
 UserName varchar(20) NOT NULL,
 Admin bool NOT NULL DEFAULT '0',
 Hash varchar(255) NOT NULL,
 PRIMARY KEY (CodFiscale),
 UNIQUE KEY UserName (UserName),
 FOREIGN KEY (CodFiscale) REFERENCES PERSONA (CodFiscale) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;