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
     * Gets the password hash of a user.
     * 
     * @param string $email
     * 
     * @return string The password hash.
     */
    private function getUserPasswordHash(
        string $email
    ): ?string {
        $email = strtolower($email);

        $request = 'SELECT password_hash FROM users 
                        WHERE email = :email';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':email', $email);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_OBJ);

        if (!$result) {
            return NULL;
        }

        return $result->password_hash;
    }

    /** 
     * Verifies the user credentials.
     * 
     * @param string $email
     * @param string $password
     * 
     * @return bool
     */
    private function verifyUserCredentials(
        string $email,
        string $password
    ): bool {
        $password_hash = $this->getUserPasswordHash($email);
        return !empty($password_hash) &&
            password_verify($password, $password_hash);
    }

    /**
     * Verifies the user access token.
     * 
     * @param string $access_token
     * 
     * @return bool
     */
    private function verifyUserAccessToken(string $access_token): bool
    {
        $request = 'SELECT * FROM users
                        WHERE access_token = :access_token';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':access_token', $access_token);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_OBJ);

        return !empty($result);
    }

    /**
     * Creates an access token if credentials are valid.
     * 
     * @param string $email
     * @param string $password
     * 
     * @return string The access_token.
     */
    public function getUserAccessToken(
        string $email,
        string $password
    ): ?string {
        if (!$this->verifyUserCredentials($email, $password)) {
            return NULL;
        }

        $email = strtolower($email);

        $access_token = hash('sha256', $email . $password . time());

        // Set access token on the user
        $request = 'UPDATE users SET access_token = :access_token
                        WHERE email = :email';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':email', $email);
        $statement->bindParam(':access_token', $access_token);
        $statement->execute();

        return $access_token;
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
        int $session_expire = 0
    ): void {
        if (!$this->verifyUserCredentials($email, $password)) {
            throw new AuthenticationException('Authentication failed.');
        }

        $email = strtolower($email);

        $access_token = hash('sha256', $email . $password . microtime(true));

        // Set session hash on the user
        $request = 'UPDATE users SET access_token = :access_token
                        WHERE email = :email';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':email', $email);
        $statement->bindParam(':access_token', $access_token);
        $statement->execute();

        $access_token = $this->getUserAccessToken($email, $password);

        switch ($session_expire) {
            case 0:
                $cookie_expire = 0;
                break;
            default:
                $cookie_expire = time() + $session_expire;
                break;
        }

        setcookie('docto_session', $access_token, $cookie_expire);
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

        $access_token = $_COOKIE['docto_session'];

        $request = 'SELECT * FROM users
                        WHERE access_token = :access_token';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':access_token', $access_token);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_OBJ);

        if (empty($result)) {
            throw new AuthenticationException('Authentication failed.');
        }
    }

    /**
     * Removes the access token from the user.
     * 
     * @param string $access_token
     * 
     * @throws AccessTokenNotFound If the access token is invalid.
     */
    public function removeUserAccessToken(string $access_token): void
    {
        if (!$this->verifyUserAccessToken($access_token)) {
            throw new AuthenticationException();
        }

        // remove access token
        $request = 'UPDATE users SET access_token = NULL
                        WHERE access_token = :access_token';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':access_token', $access_token);
        $statement->execute();
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

        $access_token = $_COOKIE['docto_session'];
        $this->removeUserAccessToken($access_token);

        setcookie('docto_session', '', time() - 3600);
    }

    /**
     * Gets the general infos of a user
     * 
     * @param string $access_token
     * 
     * @return array Array of id, firstname, lastname, phone number and email.
     */
    public function getUserInfos(string $access_token): ?array
    {
        $request = 'SELECT id, firstname, lastname, phone_number, email FROM users
                        WHERE access_token = :access_token';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':access_token', $access_token);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_OBJ);

        if (empty($result)) {
            throw new AuthenticationException();
        }

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
     * Deletes a user.
     * 
     * @param string $email
     * @param string $password
     * 
     * @throws AuthenticationException
     */
    public function deleteUser(
        string $email,
        string $password
    ): void {
        if (!$this->verifyUserCredentials($email, $password)) {
            throw new AuthenticationException();
        }

        $request = 'DELETE FROM users
                        WHERE email = :email';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':email', $email);
        $statement->execute();
    }

    /**
     * Deletes a user with its authorization token.
     * 
     * @param string $access_token
     * 
     * @throws AuthenticationException
     */
    public function deleteUserWithToken(string $access_token): void
    {
        if (!$this->verifyUserAccessToken($access_token)) {
            throw new AuthenticationException();
        }

        $request = 'DELETE FROM users
                        WHERE access_token = :access_token';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':access_token', $access_token);
        $statement->execute();
    }

    /**
     * Get all the info from doctors and their specialties.
     * 
     * @return array collection of doctors and their specialty.
     */
    public function getDoctorsAndSpecialties(): array
    {
        $request = 'SELECT * FROM doctors d
                        LEFT JOIN specialties s
                        ON d.specialty_id = s.id';

        $statement = $this->PDO->prepare($request);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return (array) $result;
    }

    /**
     * Gets the firstname and lastname of a doctor.
     * 
     * @param int $id of a doctor
     * 
     * @return array an array with firstname and lastname
     */
    public function getDoctorName(int $id): ?array
    {
        $request = 'SELECT d.firstname, d.lastname FROM doctors d
                        WHERE d.id = :id';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':id', $id);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

    /**
     * Gets the postal code of a doctor
     * 
     * @param int $id of a doctor
     * 
     * @return int the postalcode where the doctor is
     */
    public function getDoctorPCode(int $id): int
    {
        $request = 'SELECT postal_code from doctors where id = :id';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':id', $id);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_OBJ);

        return $result->postal_code;
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
                        WHERE d.id = :id';

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
     * @return array return an array of doctorid and datetime for each appointement 
     */
    public function getAllAppointments(int $id): ?array
    {
        $request = 'SELECT id, doctorid, date_time FROM appointments 
                        WHERE userid = :id';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':id', $id);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return (array) $result;
    }

    /**
     * Gets all specialities and their id
     * 
     * @return array return an array with all specialties and id
     */
    public function getAllSpecialties(): ?array
    {
        $request = 'SELECT * FROM specialties';

        $statement = $this->PDO->prepare($request);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    /**
     * Gets all the appointments free for a specialty 
     * 
     * @param string $id of a specialty
     * 
     * @return array return an array with all appointment
     */
    public function getAppointmentsForASpecialty(int $id): ?array
    {
        $request = 'SELECT a.id "appoint_id", a.date_time, d.id "doctor_id", d.firstname, d.lastname, s.name FROM appointments a 
                    LEFT JOIN doctors d ON a.doctorid = d.id LEFT JOIN specialties s ON d.specialty_id = s.id 
                    WHERE userid IS NULL AND s.id = :id';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':id', $id);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return (array) $result;
    }

    /**
     * Gets the date of the appointment
     * 
     * @param id $id of the appointment
     * 
     * @return string date of the appointment
     */
    public function getAppointmentsDate(int $id): string
    {
        $request = 'SELECT date_time from appointments a where a.id = :id';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':id', $id);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_OBJ);

        return (string) $result->date_time;
    }

    /**
     * Cancel an appointment which is not passed
     * 
     * @param id $id of the appointment
     * 
     * @return bool return true if it was made or false if it wasn't
     */
    public function cancelAppointment(int $id): bool
    {
        $date = strtotime($this->getAppointmentsDate($id));
        if($date > time()){
            $request = 'UPDATE appointments set userid = NULL where id = :id';

            $statement = $this->PDO->prepare($request);
            $statement->bindParam(':id', $id);
            $statement->execute();

            return true;
        }else{
            return false;
        }
    }

    /**
     * Gets if the appointment is already taken
     * 
     * @param id $id of the appointment
     * 
     * @return bool return true if take or false if free
     */
    public function appointmentIsTaken(int $id): bool
    {
        $request = 'SELECT userid from appointments where id = :id';

        $statement = $this->PDO->prepare($request);
        $statement->bindParam(':id', $id);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_OBJ)->userid;

        if($result != NULL){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Reserve an appointment which is not passed
     * 
     * @param id $id of the appointment
     * @param userid $userid 
     * 
     * @return bool return true if it was made or false if it wasn't
     */
    public function setAppointment(int $id, int $userid): bool
    {
        if($this->appointmentIsTaken($id)){  
            return false;
        }else{
            if(strtotime($this->getAppointmentsDate($id)) > time()){
                $request = 'UPDATE appointments set userid = :userid where id = :id';

                $statement = $this->PDO->prepare($request);
                $statement->bindParam(':userid', $userid);
                $statement->bindParam(':id', $id);
                $statement->execute();
    
                return true;
            }else{
                return false;
            }
        }
    }
}
