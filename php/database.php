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
        $request = 'SELECT identifiant from compte c where lower(c.identifiant) = :identifiant';
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
        
        $request = 'SELECT phrase from citation c, auteur a, siecle s where c.auteurid = a.id and c.siecleid = s.id and s.numero = :siecle and a.nom = :nom';
        $statement = $db->prepare($request);
        $statement->bindParam(':siecle', $siecle);
        $statement->bindParam(':nom', $nom);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    
?>