<?php
if(!defined('_CODE')) {
    die('Access denied...');
}
if(checkLogin()) {
    $token = getSession('loginToken');
    delete('tokenlogin', "token='$token'");
    removeSession('loginToken');
    reDirect('?module=auth&action=login');
}