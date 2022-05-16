<?php
    if(!isset($_COOKIE['docto']['email'])){
        setcookie("docto['email']", "email", time()+5*60);
    }
    include 'DoctoLibertain.html';
    
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL,"/DoctoLibertain.html");

    $server_output = curl_exec($ch);

    curl_close ($ch);

    echo $server_output;
?>