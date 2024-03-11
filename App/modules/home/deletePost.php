<?php
if (!defined('_CODE')) {
    die('Access denied...');
}
$data = [
    'titlePage' => 'Home'
];


if (!checkLogin()) {
    reDirect('?module=auth&action=login');
}

$filterAll = filter();


if (getSession('loginToken')) {
    $postId = $_GET['postId'];
    $loginToken = getSession('loginToken');
    $queyToken = getRaw("SELECT userId FROM tokenlogin WHERE token = '$loginToken'");
    $userId = $queyToken['userId'];
    
    if ($userId = $_GET['userIdDelete'] || checkAdminNotSignOut()) {

        $deleteStatus = delete('posts', "id='$postId'");
        if ($deleteStatus) {

            setFlashData('smg', 'Delete post successfully!');
            setFlashData('smg_type', 'success');
        } else {
            setFlashData('smg', 'System faces errors! Please try again.');
            setFlashData('smg_type', 'danger');
        }
    } else {
        setFlashData('smg', 'Error! Can not edit post of another user.');
        setFlashData('smg_type', 'danger');
    }
}
reDirect('?module=home&action=forum');



$smg = getFlashData('smg');
$smgType = getFlashData('smg_type');


