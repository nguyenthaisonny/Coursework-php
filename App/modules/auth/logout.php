<?php
if(!defined('_CODE')) {
    die('Access denied...');
}
if(checkLogin()) {
    $token = getSession('loginToken');
    delete('tokenlogin', "token='$token'");
    session_destroy();
    reDirect('?module=auth&page=login');
}