-- Login: xkorce01, xkubic39
-- 3. Uloha do IDS 2017

-- DROP TABLES --
DROP TABLE  zamestnanec CASCADE CONSTRAINT;
DROP TABLE  rezervace CASCADE CONSTRAINT;
DROP TABLE  stul CASCADE CONSTRAINT;
DROP TABLE  objednana_polozka CASCADE CONSTRAINT;
DROP TABLE  uctenka CASCADE CONSTRAINT;
DROP TABLE  obsahuje CASCADE CONSTRAINT;
DROP TABLE  ingredience CASCADE CONSTRAINT;
DROP TABLE  polozka_menu CASCADE CONSTRAINT;
DROP TABLE  rezervace_stul CASCADE CONSTRAINT;
DROP SEQUENCE seq_rezervace;
DROP INDEX index_typ;
DROP MATERIALIZED VIEW LOG ON xkorce01.rezervace;
DROP MATERIALIZED VIEW LOG ON xkorce01.rezervace_stul;
DROP MATERIALIZED VIEW view_rezervace;
-- TIME FORMAT SPECIFICATION --
alter session set nls_date_format = 'DD.MM.YYYY hh24:mi';

-- CREATE TABLES --
CREATE TABLE zamestnanec (
id_zamestnanec INT GENERATED AS IDENTITY (START WITH 1 INCREMENT BY 1) PRIMARY KEY,
jmeno VARCHAR2(20) NOT NULL,
prijmeni VARCHAR2(30) NOT NULL,
cislo_op VARCHAR2(9) NOT NULL UNIQUE,
pracovni_uvazek VARCHAR2(20) NOT NULL,
telefon VARCHAR2(10) NULL UNIQUE,
email VARCHAR2(30) NULL, --constraint only '_%@__%.__%'
ulice VARCHAR2(30) NOT NULL,
cislo_popisne INT NOT NULL,
mesto VARCHAR2(25) NOT NULL,
psc INT NOT NULL --constraint length of cell content has to be 5
);

CREATE TABLE rezervace (
id_rezervace INT PRIMARY KEY, -- part of is shout contain date   
pocet_osob INT DEFAULT '1', --default 1 person
jmeno VARCHAR2(20) NOT NULL,
telefon VARCHAR2(10) NOT NULL,
poznamka VARCHAR2(100) NULL
);

CREATE TABLE stul (
id_stul INT GENERATED AS IDENTITY (START WITH 1 INCREMENT BY 1) PRIMARY KEY,
mistnost VARCHAR2(10) NOT NULL,
kapacita INT NOT NULL
);

CREATE TABLE rezervace_stul( 
id_rezervace INT NOT NULL,
id_stul INT NOT NULL,
datum_rezervace DATE DEFAULT sysdate NOT NULL,
doba INT DEFAULT '2' NOT NULL --default 2
);


CREATE TABLE objednana_polozka (
id_objednavka INT GENERATED AS IDENTITY (START WITH 1 INCREMENT BY 1) PRIMARY KEY,
status VARCHAR2(15) DEFAULT 'objednano' NOT NULL,  --constraint only 'objednano', 'oznaceno', 'zaplaceno'
datum DATE DEFAULT sysdate NOT NULL,
nazev VARCHAR2(50) NULL,
cena DECIMAL(8,2) NULL,
id_menu VARCHAR2(5) NOT NULL,
id_zamestnanec INT NOT NULL,
id_stul INT NOT NULL,
id_rezervace INT NULL,
id_uctenka INT NULL
);

CREATE TABLE polozka_menu (
id_menu VARCHAR2(5) PRIMARY KEY,
nazev VARCHAR2(50) NOT NULL,
popis VARCHAR2(100) NULL,
cena DECIMAL(8,2) NOT NULL,
mnozstvi_menu INT NOT NULL,
typ varchar(1) NOT NULL, --constraint only "J" for jidlo "P" for piti 
jednotka_menu VARCHAR2(3) NOT NULL --constraint only ml,l are allowed for typ = P; g, kus are allowed for  typ = J
);

CREATE TABLE uctenka (
id_uctenka INT GENERATED AS IDENTITY (START WITH 1 INCREMENT BY 1) PRIMARY KEY,
datum DATE DEFAULT sysdate NOT NULL,
suma DECIMAL(8,2) NULL, --need to compute when item is added - SUM()
id_zamestnanec INT NOT NULL
);

CREATE TABLE ingredience (
id_ingredience INT GENERATED AS IDENTITY (START WITH 1 INCREMENT BY 1) PRIMARY KEY,
nazev_ingredience VARCHAR2(30) NOT NULL
);

CREATE TABLE obsahuje ( 
id_menu VARCHAR2(5) NOT NULL,
id_ingredience INT NOT NULL,
mnozstvi INT NOT NULL,
jednotka VARCHAR2(3) NOT NULL --constraint only 'ml', 'cl', 'l', 'g', 'kg', 'kus', 'CL', 'PL'
);

-- ALTER - TABLE FOREGIN KEYS--
-- objednana_polozka
ALTER TABLE objednana_polozka ADD CONSTRAINT fk_id_menu_pol FOREIGN KEY (id_menu) REFERENCES polozka_menu(id_menu);
ALTER TABLE objednana_polozka ADD CONSTRAINT fk_id_zamestnanec_pol FOREIGN KEY (id_zamestnanec) REFERENCES zamestnanec(id_zamestnanec);
ALTER TABLE objednana_polozka ADD CONSTRAINT fk_id_stul_pol FOREIGN KEY (id_stul) REFERENCES stul(id_stul);
ALTER TABLE objednana_polozka ADD CONSTRAINT fk_id_rezervace_pol FOREIGN KEY (id_rezervace) REFERENCES rezervace(id_rezervace);
ALTER TABLE objednana_polozka ADD CONSTRAINT fk_id_uctenka_pol FOREIGN KEY (id_uctenka) REFERENCES uctenka(id_uctenka);
-- uctenka
ALTER TABLE uctenka ADD CONSTRAINT fk_id_zamestnanec_uc FOREIGN KEY (id_zamestnanec) REFERENCES zamestnanec(id_zamestnanec);
-- obsahuje
ALTER TABLE obsahuje ADD CONSTRAINT fk_id_ingredience_obs FOREIGN KEY (id_ingredience) REFERENCES ingredience(id_ingredience);
ALTER TABLE obsahuje ADD CONSTRAINT fk_id_menu_obs FOREIGN KEY (id_menu) REFERENCES polozka_menu(id_menu);
ALTER TABLE obsahuje ADD PRIMARY KEY (id_menu,id_ingredience);
-- rezervace_stul
ALTER TABLE rezervace_stul ADD CONSTRAINT fk_id_rezervace_rezst FOREIGN KEY (id_rezervace) REFERENCES rezervace(id_rezervace);
ALTER TABLE rezervace_stul ADD CONSTRAINT fk_id_stul_rezst FOREIGN KEY (id_stul) REFERENCES stul(id_stul);
ALTER TABLE rezervace_stul ADD PRIMARY KEY (id_rezervace,id_stul);

-- ALTER TABLE - CHECKS --
-- email
ALTER TABLE zamestnanec ADD CONSTRAINT chk_email CHECK (email LIKE '_%@__%.__%');
-- status
ALTER TABLE objednana_polozka ADD CONSTRAINT chk_status CHECK (status IN('objednano', 'oznaceno', 'zaplaceno'));
-- jidlo / piti
ALTER TABLE polozka_menu ADD CONSTRAINT chk_typ CHECK (typ IN('J', 'P'));
-- jednotky
ALTER TABLE polozka_menu ADD CONSTRAINT chk_jednotka_pm CHECK ((typ = 'P' AND jednotka_menu = 'ml' or jednotka_menu = 'cl' or jednotka_menu = 'dcl' or jednotka_menu = 'l') 
                                                                OR (typ = 'J' AND jednotka_menu = 'kus' or jednotka_menu = 'g'));
-- jednotky ingredience
ALTER TABLE obsahuje ADD CONSTRAINT chk_jednotka_ob CHECK (jednotka IN('ml', 'cl', 'dcl', 'l', 'g', 'kg', 'kus', 'CL', 'PL'));
-- psc
ALTER TABLE zamestnanec ADD CONSTRAINT chk_psc CHECK (LENGTH(psc) = '5');

----- TRIGGERY -----
-- doplni cenu a nazev do objednane polozky
CREATE OR REPLACE TRIGGER TRG_cena_objednavka
AFTER
INSERT
  ON objednana_polozka
BEGIN
  UPDATE objednana_polozka o SET (cena, nazev) = (SELECT p.cena, p.nazev 
              FROM polozka_menu p WHERE o.id_menu = p.id_menu)
  WHERE id_objednavka = (SELECT MAX(id_objednavka) FROM objednana_polozka);
END;
/

-- doplni cizi klice a sumu k uctence
CREATE OR REPLACE TRIGGER TRG_status_zaplaceno_castka
AFTER
INSERT 
  ON uctenka
BEGIN
  UPDATE objednana_polozka
    SET objednana_polozka.id_uctenka = (SELECT MAX(id_uctenka) FROM uctenka)
  WHERE objednana_polozka.status = 'oznaceno';
  
  UPDATE uctenka
    SET uctenka.suma = (SELECT SUM(cena) FROM objednana_polozka WHERE status = 'oznaceno')
  WHERE uctenka.id_uctenka = (SELECT MAX(id_uctenka) FROM uctenka);
   
  UPDATE objednana_polozka
    SET objednana_polozka.status = 'zaplaceno'
  WHERE objednana_polozka.status = 'oznaceno';
END;
/

-- pomocna sekvence pro generovani rezervace
CREATE SEQUENCE seq_rezervace
START WITH 1
INCREMENT BY 1;

-- trigger pro generovani PK pro rezervaci
CREATE OR REPLACE TRIGGER  TRG_cislo_rezervace
BEFORE UPDATE OR INSERT ON Rezervace
REFERENCING NEW AS NEW OLD AS OLD
FOR EACH ROW
BEGIN
       IF(:new.id_rezervace IS NULL) THEN
        SELECT (to_number(to_char(sysdate, 'YYYYMMDD'))*10+seq_rezervace.nextval) INTO :new.id_rezervace FROM DUAL;
       END IF;
END TRG_cislo_rezervace;
/

----INSERT INGDEDIENCIE
INSERT INTO ingredience (nazev_ingredience) VALUES('Brambory'); --1
INSERT INTO ingredience (nazev_ingredience) VALUES('Ryze'); --2
INSERT INTO ingredience (nazev_ingredience) VALUES('Veprove stehno'); --3
INSERT INTO ingredience (nazev_ingredience) VALUES('Hovezi svickova'); --4
INSERT INTO ingredience (nazev_ingredience) VALUES('Kruti prsa'); --5
INSERT INTO ingredience (nazev_ingredience) VALUES('Rajce'); --6
INSERT INTO ingredience (nazev_ingredience) VALUES('Penne'); --7
INSERT INTO ingredience (nazev_ingredience) VALUES('Voda'); --8
INSERT INTO ingredience (nazev_ingredience) VALUES('Malinovy sirup'); --9

INSERT INTO ingredience (nazev_ingredience) VALUES('Mouka'); --10
INSERT INTO ingredience (nazev_ingredience) VALUES('Voda'); --11
INSERT INTO ingredience (nazev_ingredience) VALUES('Pomodoro'); --12
INSERT INTO ingredience (nazev_ingredience) VALUES('Sunka'); --13
INSERT INTO ingredience (nazev_ingredience) VALUES('Mozzarella'); --14
INSERT INTO ingredience (nazev_ingredience) VALUES('Gorgonzola'); --15
INSERT INTO ingredience (nazev_ingredience) VALUES('Pecorino'); --16
INSERT INTO ingredience (nazev_ingredience) VALUES('Parmezan'); --17
INSERT INTO ingredience (nazev_ingredience) VALUES('Houby'); --18

INSERT INTO ingredience (nazev_ingredience) VALUES('Kofola'); --19
INSERT INTO ingredience (nazev_ingredience) VALUES('Fanta'); --20
INSERT INTO ingredience (nazev_ingredience) VALUES('Merlot'); --21
INSERT INTO ingredience (nazev_ingredience) VALUES('Lambrusco'); --22
INSERT INTO ingredience (nazev_ingredience) VALUES('Cinzano'); --23
INSERT INTO ingredience (nazev_ingredience) VALUES('Svijany 13�'); --24
INSERT INTO ingredience (nazev_ingredience) VALUES('Pilsner Urquell'); --25
INSERT INTO ingredience (nazev_ingredience) VALUES('citron'); --26
INSERT INTO ingredience (nazev_ingredience) VALUES('olivy'); --27


----INSERT POLOZKA_MENU
INSERT INTO polozka_menu (id_menu, nazev, popis, cena, mnozstvi_menu, typ, jednotka_menu) VALUES('KRPR1','Smazena kruti prsa s brambory','Stavnate kureci prsa v bylinkove omacce s opecenymi brambory','135','200','J','g');
INSERT INTO polozka_menu (id_menu, nazev, popis, cena, mnozstvi_menu, typ, jednotka_menu) VALUES('PEN01','Penne s rajcaty','','115','185','J','g');
INSERT INTO polozka_menu (id_menu, nazev, popis, cena, mnozstvi_menu, typ, jednotka_menu) VALUES('GRSV1','Grilovana hovezi svickova s americkymi bramborami','jidlo pro labuzniky','250','165', 'J','g');
INSERT INTO polozka_menu (id_menu, nazev, popis, cena, mnozstvi_menu, typ, jednotka_menu) VALUES('LIM01','Malinova limonada','','25','300','P','ml');
INSERT INTO polozka_menu (id_menu, nazev, popis, cena, mnozstvi_menu, typ, jednotka_menu) VALUES('PIZ01','Sunkova pizza','Pomodoro, sunka, mozarella','125','450','J','g');
INSERT INTO polozka_menu (id_menu, nazev, popis, cena, mnozstvi_menu, typ, jednotka_menu) VALUES('PIZ02','Syrova pizza','Pomodoro, mozarella, gorgonzola, parmezan, pecorino','140','500','J','g');
INSERT INTO polozka_menu (id_menu, nazev, popis, cena, mnozstvi_menu, typ, jednotka_menu) VALUES('PIZ03','Zampionova pizza','Pomodoro, sunka, funghi, mozarella','130','470','J','g');

INSERT INTO polozka_menu (id_menu, nazev, popis, cena, mnozstvi_menu, typ, jednotka_menu) VALUES('KOF03','Kofola','','20','0.3','P','l');
INSERT INTO polozka_menu (id_menu, nazev, popis, cena, mnozstvi_menu, typ, jednotka_menu) VALUES('KOF05','Kofola','','30','0.5','P','l');
INSERT INTO polozka_menu (id_menu, nazev, popis, cena, mnozstvi_menu, typ, jednotka_menu) VALUES('FAN01','Fanta','','28','330','P','ml');
INSERT INTO polozka_menu (id_menu, nazev, popis, cena, mnozstvi_menu, typ, jednotka_menu) VALUES('MER01','Merlot','','45','3','P','dcl');
INSERT INTO polozka_menu (id_menu, nazev, popis, cena, mnozstvi_menu, typ, jednotka_menu) VALUES('LAM01','Lambrusco','','40','3','P','dcl');
INSERT INTO polozka_menu (id_menu, nazev, popis, cena, mnozstvi_menu, typ, jednotka_menu) VALUES('CIN01','Cinzano','','35','3','P','dcl');
INSERT INTO polozka_menu (id_menu, nazev, popis, cena, mnozstvi_menu, typ, jednotka_menu) VALUES('SVI03','Svijany 13','','27','0.3','P','l');
INSERT INTO polozka_menu (id_menu, nazev, popis, cena, mnozstvi_menu, typ, jednotka_menu) VALUES('SVI05','Svijany 13','','35','0.5','P','l');
INSERT INTO polozka_menu (id_menu, nazev, popis, cena, mnozstvi_menu, typ, jednotka_menu) VALUES('PIL05','Pilsner Urquell','','35','0.5','P','l');
INSERT INTO polozka_menu (id_menu, nazev, popis, cena, mnozstvi_menu, typ, jednotka_menu) VALUES('VOD05','Voda s citr�nem','','35','0.5','P','l');

----INSERT OBSAHUJE
INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('KRPR1','5','120','g');
INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('KRPR1','2','80','g');
INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('PEN01','7','120','g');
INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('PEN01','6','65','g');
INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('GRSV1','4','110','g');
INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('GRSV1','1','55','g');

INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('PIZ01','10','220','g');
INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('PIZ01','11','140','ml');
INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('PIZ01','12','55','g');
INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('PIZ01','13','100','g');
INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('PIZ01','14','100','g');
INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('PIZ01','18','100','g');

INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('PIZ02','10','220','g');
INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('PIZ02','11','140','ml');
INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('PIZ02','12','55','g');
INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('PIZ02','14','100','g');
INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('PIZ02','18','100','g');

INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('PIZ03','10','220','g');
INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('PIZ03','11','140','ml');
INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('PIZ03','12','55','g');
INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('PIZ03','14','100','g');
INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('PIZ03','15','100','g');
INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('PIZ03','17','100','g');
INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('PIZ03','16','100','g');

INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('KOF03','19','0.3','l');
INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('KOF05','19','0.5','l');
INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('FAN01','20','330','ml');
INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('MER01','21','3','dcl');
INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('LAM01','22','3','dcl');
INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('CIN01','23','3','dcl');
INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('SVI03','24','0.3','l');
INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('SVI05','24','0.5','l');
INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('PIL05','25','0.5','l');
INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('VOD05','8','0.5','l');
INSERT INTO obsahuje (id_menu, id_ingredience, mnozstvi, jednotka) VALUES ('VOD05','26','1','kus');


----INSERT STUL
INSERT INTO stul (mistnost, kapacita) VALUES ('bar','100'); -- id 1
INSERT INTO stul (mistnost, kapacita) VALUES ('terasa','4');
INSERT INTO stul (mistnost, kapacita) VALUES ('terasa','4');
INSERT INTO stul (mistnost, kapacita) VALUES ('terasa','2');
INSERT INTO stul (mistnost, kapacita) VALUES ('terasa','8');
INSERT INTO stul (mistnost, kapacita) VALUES ('sala','4');
INSERT INTO stul (mistnost, kapacita) VALUES ('sala','4');
INSERT INTO stul (mistnost, kapacita) VALUES ('sala','2');
INSERT INTO stul (mistnost, kapacita) VALUES ('sala','2');
INSERT INTO stul (mistnost, kapacita) VALUES ('sala','6');
INSERT INTO stul (mistnost, kapacita) VALUES ('sala','6');
INSERT INTO stul (mistnost, kapacita) VALUES ('sala','8');

----INSERT REZERVACE
INSERT INTO rezervace (id_rezervace,pocet_osob, jmeno, telefon, poznamka) VALUES ('201703031','10','Kubis','764890543','Plati majitel');
INSERT INTO rezervace (id_rezervace,pocet_osob, jmeno, telefon) VALUES ('201703231','2','Korcek','799797369');
INSERT INTO rezervace (id_rezervace,pocet_osob, jmeno, telefon, poznamka) VALUES ('201704081','1','Kovac','0905977065','Vycistete stul');

INSERT INTO rezervace (id_rezervace,pocet_osob, jmeno, telefon, poznamka) VALUES ('201704101','1','Kubica','772036023','Bez zampionu');
INSERT INTO rezervace (id_rezervace,pocet_osob, jmeno, telefon, poznamka) VALUES ('201704102','1','Kubis','764890543','Syr navic');
INSERT INTO rezervace (id_rezervace,pocet_osob, jmeno, telefon, poznamka) VALUES ('201703032','1','Navratil','772036023','Kukurici navic');
INSERT INTO rezervace (pocet_osob, jmeno, telefon, poznamka) VALUES ('3','Novak','772036023','Platba predem');
INSERT INTO rezervace (pocet_osob, jmeno, telefon, poznamka) VALUES ('5','Kovar','772036089','');
INSERT INTO rezervace (id_rezervace,pocet_osob, jmeno, telefon, poznamka) VALUES ('201704291','1','Dubaj','782776089','');
INSERT INTO rezervace (id_rezervace,pocet_osob, jmeno, telefon, poznamka) VALUES ('201704301','1','Do Long','772776089','');

----IMSERT REZERVACE_STUL
INSERT INTO rezervace_stul (id_rezervace, id_stul, datum_rezervace, doba) VALUES ('201703031','3',TO_DATE('24.03.2017 14:30'), '5');
INSERT INTO rezervace_stul (id_rezervace, id_stul, datum_rezervace) VALUES ('201703231','7',TO_DATE('04.04.2017 12:00'));
INSERT INTO rezervace_stul (id_rezervace, id_stul, datum_rezervace,doba) VALUES ('201704081','1',TO_DATE('08.04.2017 21:00'),'3');

INSERT INTO rezervace_stul (id_rezervace, id_stul, datum_rezervace) VALUES ('201704101','1',TO_DATE('10.04.2017 14:30'));
INSERT INTO rezervace_stul (id_rezervace, id_stul, datum_rezervace) VALUES ('201704102','1',TO_DATE('12.04.2017 14:30'));
INSERT INTO rezervace_stul (id_rezervace, id_stul, datum_rezervace) VALUES ('201703032','1',TO_DATE('10.04.2017 12:00'));
INSERT INTO rezervace_stul (id_rezervace, id_stul, datum_rezervace) VALUES ('201704291','1',TO_DATE('29.04.2017 12:20'));
INSERT INTO rezervace_stul (id_rezervace, id_stul, datum_rezervace) VALUES ('201704301','1',TO_DATE('30.04.2017 12:15'));

----INSERT ZAMESTNANEC
INSERT INTO zamestnanec (jmeno, prijmeni, cislo_op, pracovni_uvazek, telefon, email, ulice, cislo_popisne, mesto, psc) VALUES ('Alojz','Nebel','654768567','kuchar','','','Berkova','67','Brno','60200');
INSERT INTO zamestnanec (jmeno, prijmeni, cislo_op, pracovni_uvazek, telefon, email, ulice, cislo_popisne, mesto, psc) VALUES ('Hildegarda','Horvathova','363720288','cisnik','786908482','hilduska@seznam.cz','Purkynova','2','Brno','60200');
INSERT INTO zamestnanec (jmeno, prijmeni, cislo_op, pracovni_uvazek, telefon, email, ulice, cislo_popisne, mesto, psc) VALUES ('Jan','Kubica','647850288','provozni','897534653','honza@seznam.cz','Namestni','75','Prostejov','79604');

----INSERT OBJEDNANA POLOZKA
INSERT INTO objednana_polozka (id_menu, id_zamestnanec, id_stul, id_rezervace) 
VALUES('KRPR1','2','5','201703031');


INSERT INTO objednana_polozka (id_menu, id_zamestnanec, id_stul, id_rezervace) 
VALUES('KRPR1','2','5','201703031');


INSERT INTO objednana_polozka (id_menu, id_zamestnanec, id_stul, id_rezervace) 
VALUES('PEN01','2','5','201703031');


INSERT INTO objednana_polozka (id_menu, id_zamestnanec, id_stul) 
VALUES('PEN01','3','2');


INSERT INTO objednana_polozka (id_menu, id_zamestnanec, id_stul, id_rezervace) 
VALUES('PIZ01','3','1','201704101');


INSERT INTO objednana_polozka (id_menu, id_zamestnanec, id_stul, id_rezervace) 
VALUES('PIZ02','3','1','201704102');


INSERT INTO objednana_polozka (id_menu, id_zamestnanec, id_stul, id_rezervace) 
VALUES('PIZ03','3','1','201703032');

INSERT INTO objednana_polozka (id_menu, id_zamestnanec, id_stul,id_rezervace) 
VALUES('PIZ01','3','1','201704291');

INSERT INTO objednana_polozka (id_menu, id_zamestnanec, id_stul,id_rezervace) 
VALUES('PIZ02','2','1','201704301');

----INSERT UCTENKA
UPDATE objednana_polozka
SET status = 'oznaceno'
WHERE id_objednavka = '1';

UPDATE objednana_polozka
SET status = 'oznaceno'
WHERE id_objednavka = '2';

INSERT INTO uctenka (id_zamestnanec) VALUES('2');

---

UPDATE objednana_polozka
SET status = 'oznaceno'
WHERE id_objednavka = '3';

INSERT INTO uctenka (id_zamestnanec, suma, datum) VALUES('2', (SELECT SUM(cena)
FROM objednana_polozka WHERE status = 'oznaceno'), '28.04.2017 15:00');

UPDATE objednana_polozka o
SET ID_UCTENKA = (SELECT MAX(id_uctenka) FROM uctenka)
WHERE o.status = 'oznaceno';

UPDATE objednana_polozka o
SET o.STATUS = 'zaplaceno'
WHERE o.status = 'oznaceno';

---

UPDATE objednana_polozka
SET status = 'oznaceno'
WHERE id_objednavka = '6';

INSERT INTO uctenka (id_zamestnanec, suma, datum) VALUES('3', (SELECT SUM(cena)
FROM objednana_polozka WHERE status = 'oznaceno'), '28.04.2017 15:00');

UPDATE objednana_polozka o
SET ID_UCTENKA = (SELECT MAX(id_uctenka) FROM uctenka)
WHERE o.status = 'oznaceno';

UPDATE objednana_polozka o
SET o.STATUS = 'zaplaceno'
WHERE o.status = 'oznaceno';

---

UPDATE objednana_polozka
SET status = 'oznaceno'
WHERE id_objednavka = '7';

INSERT INTO uctenka (id_zamestnanec, suma, datum) VALUES('2', (SELECT SUM(cena)
FROM objednana_polozka WHERE status = 'oznaceno'), '03.05.2017 15:00');

UPDATE objednana_polozka o
SET ID_UCTENKA = (SELECT MAX(id_uctenka) FROM uctenka)
WHERE o.status = 'oznaceno';

UPDATE objednana_polozka o
SET o.STATUS = 'zaplaceno'
WHERE o.status = 'oznaceno';

---

UPDATE objednana_polozka
SET status = 'oznaceno'
WHERE id_objednavka = '4';

UPDATE objednana_polozka
SET status = 'oznaceno'
WHERE id_objednavka = '5';

INSERT INTO uctenka (id_zamestnanec) VALUES('3');


---- SELECTS - 3. DU -----

----- TWO TABLES (2x) -----

--Ktery zamestanec vystavil jakou uctenku
SELECT id_uctenka AS "Uctenka", jmeno AS "Jmeno", prijmeni AS "Prijmeni", datum AS "Datum vystaveni"
FROM Zamestnanec NATURAL JOIN Uctenka;

--Vypis vsechny rezervace k dnesnimu dni a k jakemu stolu patri
-- neni pouzito z duvodu, ze netusime, kdy se bude skript testovat, tedy chceme zabranit prazdne tabulce
--SELECT id_rezervace AS "Cislo rezervace", id_stul AS "Stul", jmeno AS "Jmeno", telefon AS "Telefon",datum_rezervace AS "Cas", doba AS "Doba", pocet_osob AS "Pocet osob"
--FROM Rezervace NATURAL JOIN Rezervace_stul
--WHERE datum_rezervace >= sysdate AND datum_rezervace < trunc(sysdate+1);

--Vypis rezervacie zo dna 10.4.2017
SELECT id_rezervace AS "Cislo rezervace", id_stul AS "Stul", jmeno AS "Jmeno", telefon AS "Telefon",datum_rezervace AS "Cas", doba AS "Doba", pocet_osob AS "Pocet osob"
FROM Rezervace NATURAL JOIN Rezervace_stul
WHERE datum_rezervace >= '10.04.2017 00:00' AND datum_rezervace <= '10.04.2017 23:59'
ORDER BY datum_rezervace;

----- THREE TABLES (1x) -----

-- Prehled rezervovanych objednavek na pult (k odberu pryc)
SELECT datum_rezervace AS "Datum", jmeno AS "Jmeno", telefon AS "Telefon", nazev AS "Nazev", poznamka AS "Poznamka" 
FROM Rezervace NATURAL JOIN Objednana_polozka NATURAL JOIN Rezervace_Stul
WHERE id_stul = 1 AND status='objednano';


----- GROUP BY 2x -----

-- Pocet ingrediencii jednotlivych jedal
SELECT nazev AS "Nazev jidla", COUNT(nazev) AS "Pocet ingrediencii"
FROM Polozka_menu NATURAL JOIN Obsahuje NATURAL JOIN Ingredience
WHERE typ = 'J'
GROUP BY nazev
ORDER BY COUNT(nazev) DESC;

----- EXPLAIN PLAN and INDEX-----
EXPLAIN PLAN FOR
SELECT nazev AS "Nazev jidla", COUNT(nazev) AS "Pocet ingrediencii"
FROM Polozka_menu NATURAL JOIN Obsahuje NATURAL JOIN Ingredience
WHERE typ = 'J'
GROUP BY nazev
ORDER BY COUNT(nazev) DESC;

SELECT * FROM TABLE(DBMS_XPLAN.DISPLAY);

CREATE INDEX index_typ ON polozka_menu(typ);

EXPLAIN PLAN FOR
SELECT nazev AS "Nazev jidla", COUNT(nazev) AS "Pocet ingrediencii"
FROM Polozka_menu NATURAL JOIN Obsahuje NATURAL JOIN Ingredience
WHERE typ = 'J'
GROUP BY nazev
ORDER BY COUNT(nazev) DESC;

SELECT * FROM TABLE(DBMS_XPLAN.DISPLAY);

--Vypis zostupne najcastejsie objednane polozky a kolko dokopy utrzili
SELECT nazev AS "Nazev", COUNT(id_menu) AS "Pocet", SUM(cena) AS "Celkova cena"
FROM Objednana_polozka NATURAL JOIN Polozka_menu
GROUP BY nazev
ORDER BY COUNT(id_menu) DESC, SUM(cena) DESC;

-- EXISTS (1x) 

--Tabulka zobrazi informacie vsetkych, ktori maju rezervaciu, no do podniku nakoniec neprisli
SELECT jmeno AS "Jmeno", telefon AS "Kontakt", RE.id_rezervace AS "Cislo rezervace", datum_rezervace AS "Datum rezervace"
FROM Rezervace RE JOIN Rezervace_stul ON(RE.id_rezervace = Rezervace_stul.id_rezervace)
WHERE NOT EXISTS (
  SELECT *
  FROM Objednana_polozka O
  WHERE RE.id_rezervace=O.id_rezervace
) AND datum_rezervace < sysdate;
--
-- WHERE (1x)

-- rozpis jaka objednavka je na jake uctence
SELECT id_uctenka AS "Uctenka", nazev AS "Nazev", cena AS "Cena" FROM objednana_polozka
WHERE id_uctenka IN
(SELECT id_uctenka FROM Uctenka);


----- PROCEDURY -----

CREATE OR REPLACE PROCEDURE reset_seq( seq_rezervace in varchar2 ) IS
seq_value NUMBER;
BEGIN
    execute immediate
    'select ' || seq_rezervace || '.nextval from dual' INTO seq_value;

    execute immediate
    'alter sequence ' || seq_rezervace || ' increment by -' || seq_value || ' minvalue 0';

    execute immediate
    'select ' || seq_rezervace || '.nextval from dual' INTO seq_value;

    execute immediate
    'alter sequence ' || seq_rezervace || ' increment by 1 minvalue 0';
END reset_seq;
/
/*
SELECT seq_rezervace.nextval FROM dual;
EXECUTE reset_seq('seq_rezervace');
SELECT seq_rezervace.nextval FROM dual;
*/

CREATE OR REPLACE PROCEDURE inc_prices (perc IN INT) IS
cena_menu polozka_menu.cena%TYPE;
menu_id polozka_menu.id_menu%TYPE;
invalid_perc EXCEPTION;
CURSOR kurzor_cena is
  SELECT id_menu, cena FROM polozka_menu;
BEGIN
  IF perc <= -100 THEN
    RAISE invalid_perc;
  END IF;
  OPEN kurzor_cena;
  LOOP
  FETCH kurzor_cena into menu_id, cena_menu;
  EXIT WHEN  kurzor_cena%NOTFOUND;
  IF perc >= 0 THEN
    cena_menu := ROUND((cena_menu * (1+(perc/100))),1);
  ELSE 
    cena_menu := ROUND((cena_menu * ((100+perc)/100)),1);
  END IF;
  UPDATE polozka_menu SET cena = cena_menu
    WHERE id_menu = menu_id;
  END LOOP;
  CLOSE kurzor_cena;
  EXCEPTION
    WHEN invalid_perc THEN
      dbms_output.put_line('Percento nemuze byt mensi nez 99%!!'); 
END;
/
/*
SELECT * FROM polozka_menu;
EXECUTE inc_prices(10);
SELECT * FROM polozka_menu;
EXECUTE inc_prices(-100);
SELECT * FROM polozka_menu;
EXECUTE inc_prices(0);
SELECT * FROM polozka_menu;
*/

CREATE OR REPLACE PROCEDURE spocti_trzbu (pocatek IN DATE, konec IN DATE) IS
suma uctenka.suma%TYPE;
datum uctenka.datum%TYPE;
celkem NUMBER(10,0);
datediff DECIMAL(10,5);
pocet INT;
invalid_dates EXCEPTION;
BEGIN
  SELECT pocatek - konec INTO datediff FROM dual;
  IF datediff > 0 THEN
    RAISE invalid_dates;
  END IF;
  
  SELECT SUM(suma) , COUNT(suma) INTO celkem, pocet FROM uctenka
  WHERE datum
  BETWEEN spocti_trzbu.pocatek
  AND spocti_trzbu.konec;
  
  IF pocet = '0' THEN
    celkem := '0';
  END IF;
  
  dbms_output.put_line('Celkova trzba: ' || celkem); 
   
  EXCEPTION
    WHEN invalid_dates THEN
      dbms_output.put_line('Cas byl zadan v opacnem poradi!!');
  
END;
/

/*
SELECT * FROM uctenka;
EXECUTE spocti_trzbu('28.04.2017 00:00','28.04.2017 23:00');
EXECUTE spocti_trzbu('28.04.2017 03:00','28.04.2017 01:00');
EXECUTE spocti_trzbu('27.04.2017 00:00','27.04.2017 23:59');
*/

----- PRISTUPOVE PRAVA -----
GRANT SELECT,INSERT,DELETE,UPDATE ON stul TO xkubic39;
GRANT SELECT,INSERT,DELETE,UPDATE ON rezervace TO xkubic39;
GRANT SELECT,INSERT,DELETE,UPDATE ON rezervace_stul TO xkubic39;
GRANT SELECT,INSERT,DELETE,UPDATE ON objednana_polozka TO xkubic39;
GRANT SELECT,INSERT,UPDATE ON uctenka TO xkubic39;
GRANT SELECT ON polozka_menu TO xkubic39;

CREATE MATERIALIZED VIEW LOG ON xkorce01.rezervace_stul WITH PRIMARY KEY,ROWID;
CREATE MATERIALIZED VIEW LOG ON xkorce01.rezervace WITH PRIMARY KEY,ROWID;

CREATE MATERIALIZED VIEW view_rezervace
CACHE
BUILD IMMEDIATE
REFRESH FAST ON COMMIT 
ENABLE QUERY REWRITE
AS SELECT id_rezervace, id_stul, jmeno, telefon,datum_rezervace, doba, pocet_osob, poznamka,
rezervace_stul.rowid AS stulrowid, rezervace.rowid AS rezrowid
FROM Rezervace NATURAL JOIN Rezervace_stul;

GRANT ALL ON view_rezervace TO xkubic39;

/*
alter session set nls_date_format = 'DD.MM.YYYY hh24:mi';
SELECT * FROM xkorce01.view_rezervace;
SELECT id_rezervace, id_stul, jmeno, telefon,datum_rezervace, doba, pocet_osob
FROM xkorce01.view_rezervace
WHERE datum_rezervace >= '10.04.2017 00:00' AND datum_rezervace <= '10.04.2017 23:59'
ORDER BY datum_rezervace;

INSERT INTO xkorce01.rezervace (id_rezervace,pocet_osob, jmeno, telefon, poznamka) VALUES ('201704031','3','Kovarik','772036075','Otevreny ucet');
INSERT INTO xkorce01.rezervace_stul (id_rezervace, id_stul, datum_rezervace) VALUES ('201704031','4',TO_DATE('10.04.2017 13:20'));
SELECT * FROM xkorce01.rezervace;
COMMIT;
SELECT * FROM xkorce01.view_rezervace;
*/
