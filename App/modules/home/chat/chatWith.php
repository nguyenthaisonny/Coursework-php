<?php
if (!defined('_CODE')) {
    die('Access denied...');
}
$data = [
    'titlePage' => 'Chat room'
];

if (!checkLogin()) {
    reDirect('?module=auth&page=login');
}
if (isGet()) {
    $filterAll = filter();
    $userId = $filterAll['userId'];

    $listFriend = getRaws("SELECT * FROM users");
    $friendId = $filterAll['friendId'];
    $friendDetail = getRaw("SELECT fullname, profileImage, description FROM users WHERE id = '$friendId'");
    $listMessageUser = getRaws("SELECT * FROM messages WHERE userId = '$userId' AND toUserId='$friendId' ORDER BY 'createAt' DESC");
//     echo '<pre>';
//    print_r($friendDetail);
//    echo '</pre>';

    $listMessageFriend = getRaws("SELECT * FROM messages WHERE userId = '$friendId' AND toUserId='$userId'  ORDER BY 'createAt' DESC");
    $listMessage = array_merge($listMessageUser, $listMessageFriend);
    // sort listMessage to latest
    $ord = array();
    foreach ($listMessage as $key => $value) {
        $ord[] = strtotime($value['createAt']);
    }
    array_multisort($ord, SORT_ASC, $listMessage);
}
if (isPost()) {
    $filterAll = filter();
    $friendId = $_GET['friendId'];
    $userId = trim($_GET['userId']);

    $messageContent = $filterAll['messageContent'];
    $dataInsert = [
        'userId' => $userId,
        'messageContent' => $messageContent,
        'toUserId' => $friendId

    ];
    $insertStatus = insert('messages', $dataInsert);
    if ($insertStatus) {

        reDirect('?module=home&page=chat/chatWith&userId= ' . $userId . '&friendId=' . $friendId);
    }
}



layouts('headerRoom', $data)
?>

<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />

<div class="container">
    <div class="row clearfix">
        <div class="col-lg-12">
            <div class="card chat-app">
                <div id="plist" class="people-list">
                    <div style="display: flex; justify-content: center;">
                        <button type="button" class="mg-btn medium rounded " style="margin: 0 25%;">
                            <a class="mediumAnker" href="?module=home&page=forum/forum">

                                Back
                            </a>
                        </button>
                    </div>


                    <ul class="list-unstyled chat-list mt-2 mb-0" style="height: 400px; overflow-y: auto;">
                        <?php
                        if (!empty($listFriend)) :
                            $count = 0;

                            foreach ($listFriend as $item) :
                                $friendId = $item['id'];
                                if ($friendId == $userId) {
                                    continue;
                                }
                                $isOnline = false;
                                if (countRow("SELECT id FROM tokenlogin WHERE userId=$friendId")) {
                                    $isOnline = true;
                                }
                                $listMessageUser = getRaws("SELECT * FROM messages WHERE userId = '$userId' AND toUserId='$friendId' ORDER BY 'createAt' DESC");
                                $listMessageFriend = getRaws("SELECT * FROM messages WHERE userId = '$friendId' AND toUserId='$userId'  ORDER BY 'createAt' DESC");
                                $listMessageInFriendList = array_merge($listMessageUser, $listMessageFriend);
                                // sort listMe$listMessageInFriendList to latest
                                $ord = array();
                                foreach ($listMessageInFriendList as $key => $value) {
                                    $ord[] = strtotime($value['createAt']);
                                    if ($value['toUserId'] == $userId) {
                                        continue;
                                    }
                                }
                                array_multisort($ord, SORT_ASC, $listMessageInFriendList);
                                if (!empty($listMessageInFriendList)) {

                                    $lastMessage = $listMessageInFriendList[count($listMessageInFriendList) - 1];
                                    $myMessage = false;
                                    $readStatus = false;
                                    // check  last mess belongs to user of friend
                                    if ($lastMessage['userId'] == $userId) {
                                        $myMessage = true;
                                    } else {
                                        // check read status
                                        $lastMessageId = $lastMessage['id'];
    
                                        $queryReadStatus = getRaw("SELECT readStatus FROM messages WHERE id = $lastMessageId");
                                        if($queryReadStatus['readStatus'] == 1) {
                                            $readStatus = true;
                                           
                                        }
                                        if ($lastMessage['userId'] == $_GET['friendId'] && $lastMessage['toUserId'] == $_GET['userId']) {
                                            $readStatus = true;
                                            $dataUpdate = ['readStatus' => 1];
                                            update('messages', $dataUpdate, "id='$lastMessageId'");
                                        }
                                    }
                                } else {
                                    $lastMessage = [];
                                }





                                $count++;
                        ?>
                                <li class="clearfix <?php echo $friendId == $_GET['friendId'] ? 'active' : null ?>">
                                    <a class="clearfix" href="?module=home&page=chat/chatWith&userId=<?php echo $userId ?>&friendId=<?php echo $friendId ?>">
                                        <div style="position: relative;">
                                            <img style="position: relative;" src="<?php echo $item['profileImage'] ? $item['profileImage'] :  "https://img.freepik.com/free-vector/businessman-character-avatar-isolated_24877-60111.jpg?w=826&t=st=1710127291~exp=1710127891~hmac=10efc92f9bddd8afe06fa86d74c0caf109f33b79794fd0fc982a01c8bff70758"; ?>" class="mr-3 rounded-circle" width="50" alt="User" />

                                            <i style="position: absolute; left: 34px; top: 28px;" class="fa fa-circle <?php echo $isOnline ? 'online' : 'offline' ?>"></i>
                                        </div>

                                        <div class="about">
                                            <div style="font-weight: 600;" class="name">
                                                <?php echo $item['fullname'] ?>
                                                <?php echo checkAdminInList($friendId) ? '<span style="color: #20D5EC; font-size: 14px;"><i class="fa-solid fa-circle-check"></i></span>' : null; ?>
                                            </div>
                                            <div style="<?php echo $myMessage || $readStatus ? 'color: #65676b;': 'color: black; font-weight:600;'?>">


                                                <?php

                                                if (!empty($lastMessage)) {
                                                    if ($lastMessage['userId'] == $userId) {

                                                        echo strlen($lastMessage['messageContent']) < 14  ? 'You: ' . $lastMessage['messageContent'] :  substr($lastMessage['messageContent'], 0, 14) . "...";
                                                    } else {
                                                        echo strlen($lastMessage['messageContent']) < 14  ? $lastMessage['messageContent'] :  substr($lastMessage['messageContent'], 0, 14) . "...";
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>

                                    </a>
                                </li>
                            <?php
                            endforeach;
                        else :
                            ?>
                            <tr>
                                <td>
                                    <div class="alert alert-danger text-center">None of Friend</div>
                                </td>
                            </tr>
                        <?php

                        endif;
                        ?>

                    </ul>
                </div>
                <div class="chat">
                    <div class="chat-header clearfix">
                        <div class="row">
                            <div class="col-lg-6">
                                <a href="javascript:void(0);" data-toggle="modal" data-target="#view_info">
                                    <img src="<?php echo $friendDetail['profileImage'] ? $friendDetail['profileImage'] :  "https://img.freepik.com/free-vector/businessman-character-avatar-isolated_24877-60111.jpg?w=826&t=st=1710127291~exp=1710127891~hmac=10efc92f9bddd8afe06fa86d74c0caf109f33b79794fd0fc982a01c8bff70758"; ?>" lass="rounded-circle" style="width: 60px;" alt="User" />

                                </a>
                                <div class="chat-about">
                                    <h5 style="font-weight: 600;" class="m-b-0"><?php echo $friendDetail['fullname'] ?></h5>
                                    <small><?php echo $friendDetail['description'] ?></small>
                                </div>
                            </div>

                        </div>
                    </div>


                    <div class="chat-history" id="chat-history" style="height: 400px; overflow-y: auto;">

                        <ul class="m-b-0">
                            <?php
                            if (!empty($listMessage)) :
                                $count = 0;
                                foreach ($listMessage as $item) :


                                    

                                    $count++;
                            ?>
                                    <li class="clearfix" style="position: relative;">

                                        <div style="max-width: 400px" class="<?php echo $item['userId'] == $_GET['userId'] ? 'message other-message float-right' : 'message my-message'; ?>">
                                            <?php echo $item['messageContent'] ?>
                                        </div>
                                        <p style="<?php echo $item['userId'] == $_GET['userId'] ? 'position: absolute; right: 6px;; bottom: -18px; margin: 2px 0; font-size: 12px; color: #ccc ;line-height: 12px;' : 'position: absolute; left: 6px; bottom: -18px; margin: 2px 0; font-size: 12px; color: #ccc ;line-height: 12px;'; ?>"><?php echo  formatTimeDifference($item['createAt']); ?></p>
                                    </li>
                                <?php
                                endforeach;
                            else :
                                ?>
                                <tr>
                                    <td>
                                        <div class="alert alert-danger text-center">None of message</div>
                                    </td>
                                </tr>
                            <?php

                            endif;
                            ?>
                        </ul>

                    </div>


                    <?php
                    echo checkAdminInList($_GET['friendId']) ?
                        '
                    <div class="alert alert-warning text-center">If you still confuse please tell us in contact page or email</div>
                    '
                        :
                        '<div class="chat-message clearfix">
                        <div class="input-group mb-0">
                            <form action="" method="post" style="width: 100%;">
                                <div style="display: flex;">


                                    <div class="input-group-prepend" style="padding-right: 6px;">
                                        <button class="input-group-text">

                                            <span><i class="fa fa-send"></i></span>
                                        </button>
                                    </div>

                                    <input type="text" name="messageContent" class="form-control" placeholder="Enter text here...">
                                </div>
                            </form>
                        </div>
                    </div>'

                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // Function to scroll the chat message area to the bottom
    function scrollToBottom() {
        var chatHistory = document.getElementById('chat-history');
        chatHistory.scrollTop = chatHistory.scrollHeight;
    }

    // Call scrollToBottom function on page load
    window.onload = function() {
        scrollToBottom();
    };

    // Call scrollToBottom function after sending a new message
    document.querySelector('form').addEventListener('submit', function() {
        scrollToBottom();
    });
</script>
<?php
layouts('footer')
?>