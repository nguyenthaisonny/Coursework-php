<?php

if (!defined('_CODE')) {
    die('Access denied...');
}

$data = [
    'titlePage' => 'Read message'
];
// if(checkAdmin()) {
//     reDirect('?module=users&action=list');
// }
$isAdmin = checkAdmin();
if (!$isAdmin) {
    reDirect('?module=home&page=forum/forum');
}
$loginToken = getSession('loginToken');
$queryToken = getRaw("SELECT userId FROM tokenlogin WHERE token = '$loginToken'");
$adminId = $queryToken['userId'];
$listMessage = getRaws("SELECT * FROM messages WHERE belong = 'user' AND toUserId = '$adminId' ORDER BY createAt DESC");






$smg = getFlashData('smg');
$smgType = getFlashData(('smg_type'));
// quey to users table






layouts('headerReadMessage', $data);
?>
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
<div class="container bootdey">
    <div class="email-app mb-4">
        <nav>
            <a href="?module=admin&page=message/newEmail" class="btn mg-btn primary">New Email</a>
            <ul class="nav">
                <li class="nav-item" style="background-color: #eee;">
                    <a class="nav-link" href="#"><i class="fa fa-inbox"></i> Inbox </a>
                </li>
                
            </ul>
        </nav>
        <main class="inbox">
            <div style="position: relative;" class="toolbar">
                
                <a id="deleteAll" href="?module=admin&page=message/deleteAllMessage" onclick="return confirm('Delete all message?')" data-toggle="tooltip" data-placement="top" title="Delete all" style="position: absolute; right: 16px; top: -12px; color: rgb(254, 44, 85); " type="button" href="">
                    <i class="fa-solid fa-delete-left" style="font-size: 26px"></i>
                </a>
            </div>
            <?php
            if (!empty($smg)) {
                getSmg($smg, $smgType);
            }
            ?>
            <ul class="messages overflow-auto" id="listMessage">
                <button id="myBtn" title="Go to top" style="border-radius: 50%;"><i class="fa-solid fa-arrow-up"></i></button>

                <?php
                if (!empty($listMessage)) :
                    $count = 0;
                    foreach ($listMessage as $item) :
                        $userId = $item['userId'];
                        $messageId = $item['id'];
                        $userDetail = getRaw("SELECT * FROM users WHERE id = '$userId'");
                        
                        $messageSubject = $item['messageSubject'];
                        $messageContent = $item['messageContent'];
                        
                        $userDetail = getRaw("SELECT fullname, email, profileImage FROM users WHERE id='$userId' ");
                        $isReply = false;
                        
                       
                        if($item['replyStatus'] == 1) {
                            $isReply = true;
                        }
                        $count++;
                ?>
                        <li style="position: relative;" class="message <?php echo $item['readStatus'] == 0 ? 'unread' : null; ?>">
                            <a style="height: 50px" href="?module=admin&page=message/replyMessage&messageId=<?php echo $item['id']; ?>">

                                <div class="header">
                                    <span class="from" style="font-size: 18px; line-height: 16px;"><i data-toggle="tooltip" title="<?php echo $isReply ? 'replied':  'no reply yet'?>" style="margin-right: 6px; font-weight:300; font-size: 16px;" class="<?php echo $isReply ? 'fa-solid fa-square-check':  'fa fa-square-o'?>"></i><?php echo $userDetail['fullname']; ?></span>
                                    <span class="date">
                                    <span class="fa fa-paper-clip"></span><?php echo formatTimeDifference($item['createAt']); ?></span>

                                </div>
                                <div class="title">
                                    <i style="margin-right: 6px; font-weight:100;" class="fa fa-star-o"></i> <?php echo $item['messageSubject']; ?>
                                </div>
                                <div class="description">
                                    <?php echo strlen($item['messageContent']) < 50 ? $item['messageContent'] :  substr($item['messageContent'], 0, 50) . "..."; ?>
                                </div>
                            </a>
                            <a style="position: absolute; top: 44%; right: 14px; color: #fff;" href="?module=admin&page=message/deleteMessage&messageId=<?php echo $item['id']; ?>" onclick="return confirm('Delete this message?')" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></a>

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
        </main>
    </div>
</div>
<script>
    $(function() {
        $('[data-toggle="tooltip"]').tooltip()
    })
    let deleteAll = document.getElementById("deleteAll");
    deleteAll.onclick = function() {
        return confirm("Delete all")
    }
   //handle scroll top
    let mybutton = document.getElementById("myBtn");
    mybutton.onclick = function() {
        topFunction();
    }
    
    window.onscroll = function() {
        scrollFunction()
    };

    function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            mybutton.style.display = "block";
        } else {
            mybutton.style.display = "none";
        }
    }

    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
        document.body.scrollTop = 0; // For Safari
        document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    }
</script>

<?php
layouts('footer');

?>