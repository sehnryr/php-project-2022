DELETE FROM appointments;
DELETE FROM doctors;
DELETE FROM specialities;
DELETE FROM users;

-- --- Populate auteur table ------------
ALTER SEQUENCE users_id_seq RESTART;
INSERT INTO users (lastname, firstname, password, phone_number, email) VALUES
('de Montesquieu', 'Charles', '1234', '0612345678', 'cm@test.com'),
('Hugo', 'Victor', '1234', '0612345678', 'vh@test.com'),
('Marx', 'Karl', '1234', '0612345678', 'km@test.com'),
('Bernard', 'Tristan', '1234', '0612345678', 'tb@test.com'),
('de La Fontaine', 'Jean', '1234', '0612345678', 'jf@test.com');

-- --- Populate specialities table ------
ALTER SEQUENCE specialities_id_seq RESTART;
INSERT INTO specialities (name) VALUES
('Généraliste'),
('Esthéticien-ne'),
('Urologue'),
('Kinésithérapeute'),
('Podologue');

-- --- Populate siecle table ------------
ALTER SEQUENCE doctors_id_seq RESTART;
INSERT INTO doctors (firstname, lastname, password, phone_number, email, postal_code, speciality_id) VALUES
('Mask', 'Masochiste', '1234', '0612345678', 'mm@test.com', 44000, 2),
('Poney', 'Play', '1234', '0612345678', 'pp@test.com', 44000, 3),
('Dominique', 'Strauss-Kahn', '1234', '0612345678', 'dsk@test.com', 44000, 4),
('Marc', 'Dutroux', '1234', '0612345678', 'md@test.com', 44000, 1);

-- -- --- Populate citation table ------------
ALTER SEQUENCE appointments_id_seq RESTART;
INSERT INTO appointments (userid, doctorid, date_time) VALUES
(1, 1, 2022-01-15),
(3, 4, 2022-02-12),
(2, 2, 2022-03-25),
(4, 1, 2022-04-05),
(5, 3, 2022-05-08),
(2, 4, 2022-06-19);