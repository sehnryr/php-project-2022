<?php
    include 'constants.php';

    function dbConnect(){
        $dsn = 'pgsql:dbname='.DB_NAME.';host='.DB_SERVER.';port='.DB_PORT;
        try{
            $conn = new PDO($dsn, DB_USER, DB_PASSWORD);
        } catch (PDOException $e){
            echo 'Connexion échouée : ' . $e->getMessage();
        }
        return $conn;
    }

    function verification($db, $id){
        $nid = strtolower($id);
        $request = 'SELECT email from users u where lower(u.email) = :identifiant';
        $statement = $db->prepare($request);
        $statement->bindParam(':identifiant', $nid);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        if(empty($result)){
            return false;
        }else{
            return true;
        }
    }

    function userConnection($db, $email, $password){
        
        $request = 'SELECT phrase from users u where  u.email = :email and u.password = :passwd';
        $statement = $db->prepare($request);
        $statement->bindParam(':email', $email);
        $statement->bindParam(':passwd', $password);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    
?>