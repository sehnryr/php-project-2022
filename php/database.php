<?php

/**
 * PHP version 8.1.0
 * 
 * @author Maël Grellier Neau <mael.grelneau@gmail.com>
 * @author Maxence Laurent <nano0@duck.com>
 * @author Youn Mélois <youn@melois.dev>
 */


/**
 * Includes required constants to connect to the database.
 */
require_once 'constants.php';

/**
 * Includes a collection of custom exceptions.
 */
require_once 'exceptions.php';

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
     * Checks if user exists in the database by testing its unique email.
     * 
     * @param string $email
     * 
     * @return bool
     */
    public function userExists(string $email): bool
    {
        $email = strtolower($email);

        $request = 'SELECT email FROM users 
                        WHERE email = :email';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':email', $email);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return !empty($result);
    }

    /**
     * Connects the user by returning its unique id if the 
     * credentials are valid.
     * 
     * @param string $email
     * @param string $password
     * @param int $session_expire (optional) The lifetime of the session cookie in seconds.
     */
    public function connectUser(
        string $email,
        string $password,
        int $session_expire = 60 * 60 * 4 // 4 hours in seconds
    ): void {
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

        $request = 'UPDATE users SET session_hash = :session_hash
                        WHERE email = :email';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':email', $email);
        $statement->bindParam(':session_hash', $email);
        $statement->execute();

        setcookie('docto_session', $session_hash, time() + $session_expire);
    }

    /**
     * Disconnects the current user by resetting the session hash stored in the
     * database.
     * 
     * @param string $email
     * @param string $session_hash
     */
    public function disconnectUser(string $email, string $session_hash): void
    {
        $email = strtolower($email);

        $request = 'UPDATE users SET session_hash = NULL
                        WHERE email = :email
                        AND session_hash = :session_hash';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':email', $email);
        $statement->bindParam(':session_hash', $session_hash);
        $statement->execute();

        setcookie('docto_session', '', time() - 3600);
    }

    /**
     * Create an user in the database and return a bool to result
     *
     * @param string $fname first name
     * @param string $lname last name
     * @param string $password
     * @param string $phonenb phone number
     * @param string $email 
     *
     */
    public function createUser(
        string $fname,
        string $lname,
        string $password,
        string $phonenb,
        string $email
    ) {
        $request = 'INSERT INTO users (firstname, lastname, passwd, phone_number, email) 
                        VALUES (:fn, :ln, :pass, :pnb, :mail)';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':fn', $fname);
        $statement->bindParam(':ln', $fname);
        $statement->bindParam(':pass', $password);
        $statement->bindParam(':pnb', $phonenb);
        $statement->bindParam(':mail', $email);
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
