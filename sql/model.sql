/*******************************************************************************
Create Date:    2022-04-25
Author:         Maël Grellier Neau <mael.grelneau@gmail.com>
Author:         Maxence Laurent <nano0@duck.com>
Author:         Youn Mélois <youn@melois.dev>
Description:    Creates the database tables and relations.
Usage:          psql -U postgres -d doctolibertain -a -f model.sql
                https://stackoverflow.com/a/23992045/12619942
*******************************************************************************/

DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS doctors CASCADE;
DROP TABLE IF EXISTS specialties CASCADE;
DROP TABLE IF EXISTS appointments CASCADE;

-- Table users
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    firstname VARCHAR(64) NOT NULL,
    lastname VARCHAR(64) NOT NULL,
    password_hash VARCHAR(60) NOT NULL, -- use PASSWORD_BCRYPT algo
    phone_number VARCHAR(10) NOT NULL, -- Google says to never store phone numbers as numeric data
    email VARCHAR(64) UNIQUE NOT NULL,
    session_hash VARCHAR(64)
);

-- Table specialties
CREATE TABLE specialties (
    id SERIAL PRIMARY KEY,
    name VARCHAR(64) NOT NULL
);

-- Table doctors
CREATE TABLE doctors (
    id SERIAL PRIMARY KEY,
    firstname VARCHAR(64) NOT NULL,
    lastname VARCHAR(64) NOT NULL,
    password_hash VARCHAR(60) NOT NULL, -- use PASSWORD_BCRYPT algo
    phone_number VARCHAR(10) NOT NULL,
    email VARCHAR(64) UNIQUE NOT NULL,
    postal_code NUMERIC(5,0) NOT NULL,
    specialty_id INTEGER NOT NULL,
    session_hash VARCHAR(64),

    FOREIGN KEY(specialty_id) REFERENCES specialties(id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

-- Table appointments
CREATE TABLE appointments (
    id SERIAL PRIMARY KEY,
    userid INTEGER,
    doctorid INTEGER NOT NULL,
    date_time TIMESTAMP NOT NULL,

    FOREIGN KEY(userid) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY(doctorid) REFERENCES doctors(id)
        ON UPDATE CASCADE ON DELETE CASCADE
);
