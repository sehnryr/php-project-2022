<?php
    if(!isset($_COOKIE['docto']['email'])){
        setcookie("docto", "email", time()+5*60);
    }

    include 'DoctoLibertain.html';
?>