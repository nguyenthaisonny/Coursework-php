<?php
if (!defined('_CODE')) {
    die('Access denied...');
}
$data = [
    'titlePage' => 'Reply'
];


if (!checkLogin()) {
    reDirect('?module=auth&page=login');
}

$filterAll = filter();

if (getSession('loginToken')) {
    
    $userIdReply = $_GET['userIdReply'];
    $replyId = $_GET['replyId'];
    $postId = $_GET['postId'];
    $questionId = $_GET['questionId'];
    $loginToken = getSession('loginToken');
    $queryUserIdPost = getRaw("SELECT userId FROM posts WHERE id = '$postId'");
    $queryToken = getRaw("SELECT userId FROM tokenlogin WHERE token = '$loginToken'");
    $userIdLogin = $queryToken['userId'];
    $userIdPost = $queryUserIdPost['userId'];
    
    if ($userIdLogin == $userIdReply || checkAdminNotSignOut()) {

        $deleteStatus = delete('replies', "id='$replyId'");
        if ($deleteStatus) {

            setFlashData('smg', 'Delete reply successfully!');
            setFlashData('smg_type', 'success');
        } else {
            setFlashData('smg', 'System faces errors! Please try again.');
            setFlashData('smg_type', 'danger');
        }
    } else {
        setFlashData('smg', 'Error! Can not delete reply of another user.');
        setFlashData('smg_type', 'danger');
    }
    reDirect("?module=home&page=reply/question&questionId=".$questionId."&postId=".$postId."&userIdEdit=".$userIdLogin."&userIdPost=".$userIdPost);
}



$smg = getFlashData('smg');
$smgType = getFlashData('smg_type');