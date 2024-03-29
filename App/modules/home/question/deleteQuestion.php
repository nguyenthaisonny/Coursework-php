<?php
if (!defined('_CODE')) {
    die('Access denied...');
}
$data = [
    'titlePage' => 'Home'
];


if (!checkLogin()) {
    reDirect('?module=auth&page=login');
}

$filterAll = filter();

if (getSession('loginToken')) {
    $postId = $_GET['postId'];
    $userIdDelete = $_GET['userIdDelete'];
    $questionId = $_GET['questionId'];
    $queryUserIdPost = getRaw("SELECT userId FROM posts WHERE id = '$postId'");
    $userIdPost = $queryUserIdPost['userId'];
    $loginToken = getSession('loginToken');
    $queryToken = getRaw("SELECT userId FROM tokenlogin WHERE token = '$loginToken'");
    $userIdLogin = $queryToken['userId'];
    
    
    if ($userIdLogin == $userIdDelete || checkAdminNotSignOut()) {
        $listReply = getRaws("SELECT id FROM replies WHERE questionId = '$questionId'");
        foreach($listReply as $item) {
            $questionId = $item['id'];
            $deleteReplies = delete('replies', "questionId = $questionId ");
        }
        $deleteQuestion = delete('questions', "id='$questionId'");
        if ($deleteQuestion) {

            setFlashData('smg', 'Delete question successfully!');
            setFlashData('smg_type', 'success');
        } else {
            setFlashData('smg', 'System faces errors! Please try again.');
            setFlashData('smg_type', 'danger');
        }
    } else {
        setFlashData('smg', 'Error! Can not delete question of another user.');
        setFlashData('smg_type', 'danger');
    }
    if (!empty($_GET['type'])) {
        reDirect("?module=home&page=question/post&postId=".$postId."&type=". $_GET['type']);
       
    } else {

        reDirect("?module=home&page=question/post&postId=".$postId);
    }
    
}



$smg = getFlashData('smg');
$smgType = getFlashData('smg_type');