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
    $listMessageUser = getRaws("SELECT * FROM messages WHERE userId = '$userId' AND toUserId='$friendId' ORDER BY 'createAt' DESC");
    $listMessageFriend = getRaws("SELECT * FROM messages WHERE userId = '$friendId' AND toUserId='$userId'  ORDER BY 'createAt' DESC");
    $listMessage = array_merge($listMessageUser, $listMessageFriend);
    // sort listMessage to latest
    $ord = array();
    foreach ($listMessage as $key => $value) {
        $ord[] = strtotime($value['createAt']);
    }
    array_multisort($ord, SORT_ASC, $listMessage);
    
    // echo '<pre>';
    // print_r($listMessage);
    // echo '</pre>';
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
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-search"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="Search...">
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



                                $count++;
                        ?>
                                <li class="clearfix <?php echo $friendId == $_GET['friendId'] ? 'active' : null ?>">
                                    <a class="clearfix" href="?module=home&page=chat/chatWith&userId=<?php echo $userId ?>&friendId=<?php echo $friendId ?>">
                                        <img src="<?php echo $item['profileImage'] ? $item['profileImage'] :  "https://img.freepik.com/free-vector/businessman-character-avatar-isolated_24877-60111.jpg?w=826&t=st=1710127291~exp=1710127891~hmac=10efc92f9bddd8afe06fa86d74c0caf109f33b79794fd0fc982a01c8bff70758"; ?>" class="mr-3 rounded-circle" width="50" alt="User" />
                                        <div class="about">
                                            <div class="name"><?php echo $item['fullname'] ?></div>
                                            <div class="status"> <i class="fa fa-circle offline"></i> left 7 mins ago </div>
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
                                    <img src="https://bootdey.com/img/Content/avatar/avatar2.png" alt="avatar">
                                </a>
                                <div class="chat-about">
                                    <h6 class="m-b-0">Aiden Chavez</h6>
                                    <small>Last seen: 2 hours ago</small>
                                </div>
                            </div>
                            <div class="col-lg-6 hidden-sm text-right">
                                <a href="javascript:void(0);" class="btn btn-outline-secondary"><i class="fa fa-camera"></i></a>
                                <a href="javascript:void(0);" class="btn btn-outline-primary"><i class="fa fa-image"></i></a>
                                <a href="javascript:void(0);" class="btn btn-outline-info"><i class="fa fa-cogs"></i></a>
                                <a href="javascript:void(0);" class="btn btn-outline-warning"><i class="fa fa-question"></i></a>
                            </div>
                        </div>
                    </div>
                    
                    
                    <div class="chat-history" id="chat-history" style="height: 400px; overflow-y: auto;">

                        <ul class="m-b-0" >
                            <?php
                            if (!empty($listMessage)) :
                                $count = 0;
                                foreach ($listMessage as $item) :




                                    $count++;
                            ?>
                                    <li class="clearfix">

                                        <div class="<?php echo $item['userId'] == $_GET['userId'] ? 'message other-message float-right' : 'message my-message'; ?>"><?php echo $item['messageContent'] ?> </div>
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