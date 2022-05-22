/*******************************************************************************
Create Date:    2022-05-06
Author:         Maël Grellier Neau <mael.grelneau@gmail.com>
Author:         Maxence Laurent <nano0@duck.com>
Author:         Youn Mélois <youn@melois.dev>
Description:    Populates the tables of the database.
Usage:          psql -U postgres -d doctolibertain -a -f data.sql
                https://stackoverflow.com/a/23992045/12619942
*******************************************************************************/

DELETE FROM appointments;
DELETE FROM doctors;
DELETE FROM specialties;
DELETE FROM users;

-- Populate auteur table
ALTER SEQUENCE users_id_seq RESTART;
INSERT INTO users (lastname, firstname, password_hash, phone_number, email) VALUES
('de Montesquieu', 'Charles', '$2y$10$IOfwEyrZYTCoBOhX1O8hPuAAtBhikQg94vboI1gKzMSVQdOjGwBNO', '0612345678', 'cm@test.com'),
('Hugo', 'Victor', '$2y$10$IOfwEyrZYTCoBOhX1O8hPuAAtBhikQg94vboI1gKzMSVQdOjGwBNO', '0612345678', 'vh@test.com'),
('Marx', 'Karl', '$2y$10$IOfwEyrZYTCoBOhX1O8hPuAAtBhikQg94vboI1gKzMSVQdOjGwBNO', '0612345678', 'km@test.com'),
('Bernard', 'Tristan', '$2y$10$IOfwEyrZYTCoBOhX1O8hPuAAtBhikQg94vboI1gKzMSVQdOjGwBNO', '0612345678', 'tb@test.com'),
('de La Fontaine', 'Jean', '$2y$10$IOfwEyrZYTCoBOhX1O8hPuAAtBhikQg94vboI1gKzMSVQdOjGwBNO', '0612345678', 'jf@test.com');

-- Populate specialties table
ALTER SEQUENCE specialties_id_seq RESTART;
INSERT INTO specialties (name) VALUES
('Généraliste'),
('Esthéticien-ne'),
('Urologue'),
('Kinésithérapeute'),
('Podologue'),
('Chirurgien-ne plasticien-ne'),
('Gynécologue');

-- Populate siecle table
ALTER SEQUENCE doctors_id_seq RESTART;
INSERT INTO doctors (firstname, lastname, password_hash, phone_number, email, postal_code, specialty_id) VALUES
('Mask', 'Masochiste', '$2y$10$IOfwEyrZYTCoBOhX1O8hPuAAtBhikQg94vboI1gKzMSVQdOjGwBNO', '0612345678', 'mm@test.com', 44000, 2),
('Poney', 'Play', '$2y$10$IOfwEyrZYTCoBOhX1O8hPuAAtBhikQg94vboI1gKzMSVQdOjGwBNO', '0612345678', 'pp@test.com', 44000, 3),
('Dominique', 'Strauss-Kahn', '$2y$10$IOfwEyrZYTCoBOhX1O8hPuAAtBhikQg94vboI1gKzMSVQdOjGwBNO', '0612345678', 'dsk@test.com', 44000, 4),
('Marc', 'Dutroux', '$2y$10$IOfwEyrZYTCoBOhX1O8hPuAAtBhikQg94vboI1gKzMSVQdOjGwBNO', '0612345678', 'md@test.com', 44000, 1),
('Louison', 'Diguer', '$2y$10$IOfwEyrZYTCoBOhX1O8hPuAAtBhikQg94vboI1gKzMSVQdOjGwBNO', '0612345678', 'ld@test.com', 44000, 6),
('Martin', 'Lobel', '$2y$10$IOfwEyrZYTCoBOhX1O8hPuAAtBhikQg94vboI1gKzMSVQdOjGwBNO', '0612345678', 'ml@test.com', 44000, 7);

-- Populate appointments table with lock rdv
ALTER SEQUENCE appointments_id_seq RESTART;
INSERT INTO appointments (userid, doctorid, date_time) VALUES
(1, 1, '2022-01-15'),
(3, 4, '2022-02-12'),
(2, 2, '2022-03-25'),
(4, 1, '2022-04-05'),
(5, 3, '2022-05-08'),
(2, 4, '2022-06-19');

-- Populate appointments table with free rdv
INSERT INTO appointments(doctorid, date_time) VALUES
(1, '2022-06-25'),
(2, '2022-06-25'),
(4, '2022-06-25'),
(5, '2022-06-25'),
(6, '2022-06-25'),
(6, '2022-06-05');