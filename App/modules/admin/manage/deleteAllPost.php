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
$deleteStatus = delete('posts', "1=1");
if($deleteStatus) {
    setFlashData('smg', 'Delete all successfully');
    setFlashData('smg_type', 'success');
} else {
    setFlashData('smg', 'System faced errors!');
    setFlashData('smg_type', 'danger');
}
reDirect('?module=home&page=forum/forum');