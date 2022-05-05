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

    function userConnection(db, email, password){
        
    }
?>