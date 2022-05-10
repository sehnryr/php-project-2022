DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS doctors CASCADE;
DROP TABLE IF EXISTS specialities CASCADE;
DROP TABLE IF EXISTS appointments CASCADE;

-- Table users
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    firstname VARCHAR(64) NOT NULL,
    lastname VARCHAR(64) NOT NULL,
    passwd VARCHAR(64) NOT NULL,
    phone_number VARCHAR(10) NOT NULL, -- Google says to never store phone numbers as numeric data
    email VARCHAR(64) NOT NULL
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
    passwd VARCHAR(64) NOT NULL,
    phone_number VARCHAR(10) NOT NULL,
    email VARCHAR(64) NOT NULL,
    postal_code NUMERIC(5,0) NOT NULL,
    specialty_id INTEGER NOT NULL,

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
