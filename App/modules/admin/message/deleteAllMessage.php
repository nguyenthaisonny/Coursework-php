<?php
if (!defined('_CODE')) {
    die('Access denied...');
}
$isAdmin = checkAdmin();
if (!$isAdmin) {
    reDirect('?module=home&page=forum/forum');
}
//check whether id exist
// delete login token -> delete user
$loginToken = getSession('loginToken');
$queryToken = getRaw("SELECT userId FROM tokenlogin WHERE token = '$loginToken'");
$adminId = $queryToken['userId'];

$deleteStatus = delete('messages', "toUserId='$adminId'");
if ($deleteStatus) {
    setFlashData('smg', 'Delete successfully');
    setFlashData('smg_type', 'success');
} else {
    setFlashData('smg', 'System faced error :(( Please try again!');
    setFlashData('smg_type', 'danger');
}

reDirect('?module=admin&page=message/readMessage');
