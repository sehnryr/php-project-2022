<?php
    /**
     * Main file with main function
     * @copyright TheBoysCompany
     * 
     */


    /**
     * This line will include the file "constants.php" containing all 
     * the differents variables to connect to the data base like 
     * user-name, password, ip of the server, ...
     */
    include 'constants.php';


    /**
     * This function is used to open a session from the database and return 
     * the protocol that other function need to read, write in the database  
     */
    function dbConnect(){
        $dsn = 'pgsql:dbname='.DB_NAME.';host='.DB_SERVER.';port='.DB_PORT;
        /*
        * DB_NAME, DB_SERVER, DB_PORT are variables contained in constants.php
        */
        try{
            $conn = new PDO($dsn, DB_USER, DB_PASSWORD);
            /**
             * try if it can connect to the database
             */
        } catch (PDOException $e){
            echo 'Connexion échouée : ' . $e->getMessage();
            /**
             * if the connection is not granted or they're is an error, it print an error message
             */
        }
        return $conn;
    }
    

    /**
     * 
     */
    function verification($db, $id){
        $nid = strtolower($id);
        $request = 'SELECT identifiant from compte c where lower(c.identifiant) = :identifiant';
        /**
         * the lower(...) is used for transform all the uppercase in the identifiant in lowercase
         */
        $statement = $db->prepare($request);
        $statement->bindParam(':identifiant', $nid);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        /**
         * 
         */
        if(empty($result)){
            return false;
        }else{
            return true;
        }
    }


    /**
     * 
     */
    function userConnection($db, $email, $password){
        
        $request = 'SELECT id from users u where  u.email = :email and u.password = :passwd';
        $statement = $db->prepare($request);
        $statement->bindParam(':email', $email);
        $statement->bindParam(':passwd', $password);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    function getDoctorSpeciality($db, $id){
        
        $request = 'SELECT s.name from specialities s left join doctors d on d.speciality_id = s.id where  d.id = :id ';
        $statement = $db->prepare($request);
        $statement->bindParam(':id', $id);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    function getAllAppointments($db, $id){
        $request = 'SELECT id from appointments s where  s.userid = :id';
        $statement = $db->prepare($request);
        $statement->bindParam(':id', $id);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }
?>