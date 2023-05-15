# time_tracker
Este o aplicatie in care se pot inregistra useri/admini, care pot loga ore pt activitati(categorii) din cadrul unor departamente predefinite.

CREATE TABLE "UTILIZATORI"
(
  "ID" bigint, AUTO_INCREMENT,
  "NUME" character(255),
  "PRENUME" character(255),
  "USERNAME" character(255),
  "PAROLA" character(255),
  "USERNAME" character(255),
  "ROL" character(25),
  "ID_DEP_UTILIZATOR" int, 
  "STATUS" character(25)
);

CREATE TABLE "DEPARTAMENTE"
(
  "ID_DEP" bigint,AUTO_INCREMENT,
  "NUME_DEP" character(255)
);

CREATE TABLE "CATEGORII"
(
  "ID_CAT" bigint,AUTO_INCREMENT,
  "NUME_CAT" character(255),
  "ID_DEPARTAMENT_CAT" int
);

CREATE TABLE "ACTIVITATI"
(
  "ID_ACT" bigint,AUTO_INCREMENT,
  "ID" int,
  "NUME_DEP" character(255),
  "NUME_CAT" character(255),
  "DATA_ACT" date,
  "ORE_LUCRATE" int,
  "ORA_LOG" timestamp,
);
