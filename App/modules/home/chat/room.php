<?php
if (!defined('_CODE')) {
    die('Access denied...');
}
$data = [
    'titlePage' => 'Chat room'
];

if (isGet()) {
    $filterAll = filter();
    $userId = $filterAll['userId'];
    $listFriend = getRaws("SELECT * FROM users");
}
if (!checkLogin()) {
    reDirect('?module=auth&page=login');
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
                    <ul class="list-unstyled chat-list mt-2 mb-0">

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
                                    if($value['toUserId']==$userId) {
                                        continue;
                                    }
                                }
                                array_multisort($ord, SORT_ASC, $listMessageInFriendList);
                                if(!empty($listMessageInFriendList)) {

                                    $lastMessage = $listMessageInFriendList[count($listMessageInFriendList) - 1];
                                    $myMessage = false;
                                    $readStatus = false;
                                    //check  the message belong to user or friend
                                    if($lastMessage['userId'] == $userId) {
                                        $myMessage = true;
                                    } else {
                                        //check read status
                                        $lastMessageId = $lastMessage['id'];
    
                                        $queryReadStatus = getRaw("SELECT readStatus FROM messages WHERE id = $lastMessageId");
                                        if($queryReadStatus['readStatus'] == 1) {
                                            $readStatus = true;
                                           
                                        }
                                    }
                                } else {
                                    $lastMessage = [];
                                }
                               




                                $count++;
                        ?>
                                <li class="clearfix">
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

                                                if(!empty($lastMessage)) {
                                                    if($lastMessage['userId'] == $userId) {

                                                        echo strlen($lastMessage['messageContent']) < 14  ? 'You: '. $lastMessage['messageContent'] :  substr($lastMessage['messageContent'], 0, 14) . "..."; 
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
                <div class="chat" style="height: 569px;">
                    <img src="https://messengernews.fb.com/wp-content/themes/messenger/images/messenger_logo_1200x630.jpg" class="img-fluid" alt="Responsive image">
                    <div class="chat-history">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
layouts('footer')
?>