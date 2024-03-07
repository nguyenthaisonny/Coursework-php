<?php
if(!defined('_CODE')) {
    die('Access denied...');
}



try {
    if(class_exists('PDO')) {
        $dsn = 'mysql:dbname='._DB.';host='._HOST;

        $option = [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];
        $conn = new PDO($dsn, _USER, _PASS, $option);
    }
} catch(Exception $exception) {
    echo '<div style="color: red; padding: 5px 15px; boder: 1px solid red;">';
    echo $exception -> getMessage().'<br>';
    echo '</div>';
    die();
}