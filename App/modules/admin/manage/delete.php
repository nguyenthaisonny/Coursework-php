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
$filterAll = filter();
if (!empty($filterAll['id'])) {
    $userId = $filterAll['id'];
    $postQuery = getRaw("SELECT id FROM posts WHERE userId = '$userId'");
    $postId = $postQuery['id'];
    $userDetail = countRow("SELECT * FROM users WHERE id='$userId'");
    if ($userDetail > 0) {
        $deleteToken = delete('tokenlogin', "userId='$userId'");
        if ($deleteToken) {
            $deleteUser = delete('users', "id='$userId'");
            $deletePost = delete('posts', "id='$postId'");
            if ($deleteUser) {
                setFlashData('smg', 'Delete success');
                setFlashData('smg_type', 'success');
            } else {
                setFlashData('smg', 'System get errors! Please try again');
                setFlashData('smg_type', 'danger');
            }
        }
    } else {
        setFlashData('smg', 'User does not exist');
        setFlashData('smg_type', 'danger');
    }
} else {
    setFlashData('smg', 'Link does not exist');
    setFlashData('smg_type', 'danger');
}
reDirect('?module=admin&page=manage/list');
