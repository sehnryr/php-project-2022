<?php
    if(!isset($_COOKIE['docto']['email'])){
        setcookie("docto['email']", "email", time()+5*60);
    }
    
?>