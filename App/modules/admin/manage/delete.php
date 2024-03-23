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
    
    $postId = getRaw("SELECT id FROM posts WHERE userId = '$userId'")['id'];
    $questionId =  getRaw("SELECT id FROM questions WHERE userId = '$userId'")['id'];
    $replyId =  getRaw("SELECT id FROM replies WHERE userId = '$userId'")['id'];
    $userDetail = countRow("SELECT id FROM users WHERE id='$userId'");
    if ($userDetail > 0) {
        $deleteToken = delete('tokenlogin', "userId='$userId'");
        if ($deleteToken) {
            //delete reply
            $listQuestion = getRaws("SELECT id FROM questions WHERE postId='$postId'");
            foreach($listQuestion as $item) {
                $questionId = $item['id'];

                $deleteReply = delete('replies', "questionId='$questionId'");
            }
            //deleteQuestion
            $listPost = getRaws("SELECT id FROM posts WHERE userId='$userId'");
            foreach($listPost as $item) {
                $postId = $item['id'];

                $deleteQuestion = delete('questions', "postId='$postId'");
            }
            // deletePost
            foreach($listPost as $item) {
                $postId = $item['id'];

               
                $deletePost = delete('posts', "id='$postId'");
            }
            $deleteUser = delete('users', "id='$userId'");
           
            if ($deleteUser) {
                setFlashData('smg', 'Delete success');
                setFlashData('smg_type', 'success');
            } else {
                setFlashData('smg', 'System get errors! Please try again'. $postId);
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
