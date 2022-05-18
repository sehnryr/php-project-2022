<?php

/**
 * PHP version 8.1.0
 * 
 * @author Maël Grellier Neau <mael.grelneau@gmail.com>
 * @author Maxence Laurent <nano0@duck.com>
 * @author Youn Mélois <youn@melois.dev>
 */

require_once 'config.php';
require_once 'library/exceptions.php';

/**
 * Collection of methods to communicate with the database.
 */
class Database
{
    protected $PDO;

    /**
     * Connect to the PostgreSQL database.
     * 
     * @throws PDOException Error thrown if the connection to 
     *                      the database failed.
     */
    public function __construct()
    {
        $db_name = DB_NAME;
        $db_server = DB_SERVER;
        $db_port = DB_PORT;

        $dsn = "pgsql:dbname={$db_name};host={$db_server};port={$db_port}";

        $this->PDO = new PDO($dsn, DB_USER, DB_PASSWORD);
    }

    /**
     * Connects the user by returning its unique id if the 
     * credentials are valid.
     * 
     * @param string $email
     * @param string $password
     * @param int $session_expire (optional) The lifetime of the session cookie in seconds.
     * 
     * @throws AuthenticationException If the authentication failed.
     */
    public function connectUser(
        string $email,
        string $password,
        int $session_expire = 60 * 60 * 4 // 4 hours in seconds
    ): void {
        try {
            $this->tryConnectUser();
        } catch (AuthenticationException $e) {
            return;
        }

        $email = strtolower($email);

        $request = 'SELECT password_hash FROM users 
                        WHERE email = :email';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':email', $email);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_OBJ);

        if (!$result || !password_verify($password, $result->password_hash)) {
            throw new AuthenticationException('Authentication failed.');
        }

        $session_hash = hash('sha256', $email . $password . time());

        // Set session hash on the user
        $request = 'UPDATE users SET session_hash = :session_hash
                        WHERE email = :email';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':email', $email);
        $statement->bindParam(':session_hash', $session_hash);
        $statement->execute();

        if ($session_expire == 0) {
            $cookie_expire = 0;
        } else {
            $cookie_expire = time() + $session_expire;
        }

        setcookie('docto_session', $session_hash, $cookie_expire);
    }

    /**
     * Tries to connect the user with its session cookie if valid.
     * 
     * @throws AuthenticationException If the authentication failed.
     */
    public function tryConnectUser(): void
    {
        if (!isset($_COOKIE['docto_session'])) {
            throw new AuthenticationException('Authentication failed.');
        }

        $session_hash = $_COOKIE['docto_session'];

        $request = 'SELECT * FROM users
                        WHERE session_hash = :session_hash';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':session_hash', $session_hash);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_OBJ);

        if ($result == NULL) {
            throw new AuthenticationException('Authentication failed.');
        }
    }

    /**
     * Disconnects the current user by resetting the session hash stored in the
     * database.
     */
    public function disconnectUser(): void
    {
        if (!isset($_COOKIE['docto_session'])) {
            return;
        }

        $session_hash = $_COOKIE['docto_session'];

        $request = 'UPDATE users SET session_hash = NULL
                        WHERE session_hash = :session_hash';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':session_hash', $session_hash);
        $statement->execute();

        setcookie('docto_session', '', time() - 3600);
    }

    /**
     * Gets the general infos of a user
     * 
     * @return array Array of firstname, lastname, phone number and email.
     */
    public function getUserInfo(): ?array
    {
        try {
            $this->tryConnectUser();
        } catch (AuthenticationException $e) {
            return NULL;
        }

        $session_hash = $_COOKIE['docto_session'];

        $request = 'SELECT firstname, lastname, phone_number, email FROM users
                        WHERE session_hash = :session_hash';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':session_hash', $session_hash);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_OBJ);

        return (array) $result;
    }

    /**
     * Create an user in the database and return a bool to result
     *
     * @param string $firstname first name
     * @param string $lastname last name
     * @param string $email 
     * @param string $phoneNumber phone number
     * @param string $password
     *
     */
    public function createUser(
        string $firstname,
        string $lastname,
        string $email,
        string $phoneNumber,
        string $password
    ) {
        // test if user already exists
        $request = 'SELECT * FROM users
                        WHERE email = :email';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':email', $email);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_OBJ);

        if ($result) {
            throw new DuplicateEmailException('Email already exists.');
        }

        $password_hash = password_hash($password, PASSWORD_BCRYPT);


        $request = 'INSERT INTO users 
                        (firstname, lastname, password_hash, phone_number, email)
                        VALUES (:firstname, :lastname, :password_hash, :phone_number, :email)';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':firstname', $firstname);
        $statement->bindParam(':lastname', $lastname);
        $statement->bindParam(':password_hash', $password_hash);
        $statement->bindParam(':phone_number', $phoneNumber);
        $statement->bindParam(':email', $email);
        $statement->execute();
    }

    /**
     * Get all the info from doctors and their specialties.
     * 
     * @return mixed TODO
     */
    public function getDoctorsAndSpecialties(): mixed
    {
        $request = 'SELECT * FROM doctors d
                        LEFT JOIN specialties s
                        ON d.specialty_id = s.id';

        $statement = $this->PDO->prepare($request);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    /**
     * Gets the name of the specialty of a doctor.
     * 
     * @param int $id
     * 
     * @return string the specialty's name.
     */
    public function getDoctorSpecialty(int $id): string
    {
        $request = 'SELECT s.name FROM specialties s 
                        LEFT JOIN doctors d 
                        ON s.id = d.specialty_id 
                        WHERE d.if = :id';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':id', $id);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_OBJ);

        return $result->name;
    }

    /**
     * Gets all the appointments associated to a user.
     * 
     * @param int $id
     * 
     * @return mixed TODO
     */
    public function getAllAppointments(int $id): mixed
    {
        $request = 'SELECT id FROM appointments 
                        WHERE userid = :id';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':id', $id);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }
}
