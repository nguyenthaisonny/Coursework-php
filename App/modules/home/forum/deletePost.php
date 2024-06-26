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
if(!empty($filterAll['postId'] && !empty($filterAll['userIdDelete']))) {

    if (getSession('loginToken')) {
        $postId = $_GET['postId'];
        
        $loginToken = getSession('loginToken');
        $queryToken = getRaw("SELECT userId FROM tokenlogin WHERE token = '$loginToken'");
        $userId = $queryToken['userId'];
        
        if ($userId == $_GET['userIdDelete'] || checkAdminNotSignOut()) {
    
            $listQuestion = getRaws("SELECT id FROM questions WHERE postId = '$postId' ");
            //delete Reply
            foreach($listQuestion as $item) {
                $questionId = $item['id'];
                $deleteReply = delete('replies', "questionId='$questionId'");
            }
            //deleteQuestion
            foreach($listQuestion as $item) {
                $questionId = $item['id'];
                $deleteQuestion = delete('questions', "id='$questionId'");
            }
            $deleteQuestion = delete('questions', "postId='$postId'");
            $deleteStatus = delete('posts', "id='$postId'");

            if ($deleteStatus && $deleteQuestion) {
    
                setFlashData('smg', 'Delete post successfully!');
                setFlashData('smg_type', 'success');
            } else {
                setFlashData('smg', 'System faces errors! Please try again.');
                setFlashData('smg_type', 'danger');
            }
        } else {
            setFlashData('smg', 'Error! Can not delete post of another user.');
            setFlashData('smg_type', 'danger');
        }
    }
    if (!empty($_GET['type'])) {
        reDirect('?module=home&page=forum/forum&type=' . $_GET['type']);
    } else {

        reDirect('?module=home&page=forum/forum');
    }
}



$smg = getFlashData('smg');
$smgType = getFlashData('smg_type');


