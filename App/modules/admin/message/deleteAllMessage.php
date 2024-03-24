<?php
if (!defined('_CODE')) {
    die('Access denied...');
}
$isAdmin = checkAdmin();
if(!$isAdmin) {
    reDirect('?module=home&page=forum/forum');
}
//check whether id exist
// delete login token -> delete user

    $deleteStatus = delete('messages', "1=1");
    if($deleteStatus) {
        setFlashData('smg', 'Delete successfully');
        setFlashData('smg_type', 'success');
    } else {
        setFlashData('smg', 'System faced error :(( Please try again!');
        setFlashData('smg_type', 'danger');
    }

reDirect('?module=admin&page=message/readMessage');