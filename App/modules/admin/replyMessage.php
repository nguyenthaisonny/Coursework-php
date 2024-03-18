<?php

if (!defined('_CODE')) {
    die('Access denied...');
}

$data = [
    'titlePage' => 'Read message'
];

$isAdmin = checkAdmin();
if (!$isAdmin) {
    reDirect('?module=home&action=forum');
}
$messageId = $_GET['messageId'];
$messageDetail = getRaw("SELECT * FROM messages WHERE id = '$messageId'");

$userId = $messageDetail['userId'];
$userDetail = getRaw("SELECT fullname, email, profileImage FROM users WHERE id = '$userId'");
$dataUpdate = ['readStatus' => 1];
$updateStatus = update('messages', $dataUpdate, "id='$messageId'");
setSession('userDetail', $userDetail);

if (isPost()) {
    $messageDetail = getRaw("SELECT * FROM messages WHERE id = '$messageId'");
    $userDetail = getRaw("SELECT fullname, email, profileImage FROM users WHERE id = '$userId'");

    $filterAll = filter();
    $replyContent = $filterAll['replyContent'];
    $subject = $userDetail['fullname'] . ' [Reply] ';
    $content = 'Hi ' . $filterAll['fullname'] . '<br>';
    $content .= '- Your message: ' . '<br>';
    $content .= '+ Subject: '. $messageDetail['messageSubject'] . '.';
    $content .= '+ Content: '. $messageDetail['messageContent'] . '<br>';
    $content .= '- Our answer is: ' . '<br>';
    $content .= $replyContent . '<br>';




    $content .= 'Thanks for your contribution <span>❤</span>';

    $sendMail = sendMail($userDetail['email'], $subject, $content);
    if ($sendMail) {
        setFlashData('smg', 'Reply has been sent');
        setFlashData('smg_type', 'success');
        reDirect('?module=admin&action=readMessage');
    } else {
        setFlashData('smg', 'Opps, The system get some errors :(( Please try again! ');
        setFlashData('smg_type', 'danger');
    }
}



$smg = getFlashData('smg');
$smgType = getFlashData(('smg_type'));
// quey to users table






layouts('headerReplyMessage', $data);
?>
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
<div class="container">
    <div class="email-app mb-4">
        <nav>
            <a href="?module=admin&action=readMessage" class="btn mg-btn rounded">Back</a>
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fa fa-inbox"></i> Inbox <span class="badge badge-danger">4</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fa fa-star"></i> Stared</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fa fa-rocket"></i> Sent</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fa fa-trash-o"></i> Trash</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fa fa-bookmark"></i> Important<span class="badge badge-info">5</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fa fa-inbox"></i> Inbox <span class="badge badge-danger">4</span></a>
                </li>
            </ul>
        </nav>
        <main class="message">
            <div class="toolbar">
                <div class="btn-group">
                    <button type="button" class="btn btn-light">
                        <span class="fa fa-star"></span>
                    </button>
                    <button type="button" class="btn btn-light">
                        <span class="fa fa-star-o"></span>
                    </button>
                    <button type="button" class="btn btn-light">
                        <span class="fa fa-bookmark-o"></span>
                    </button>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-light">
                        <span class="fa fa-mail-reply"></span>
                    </button>
                    <button type="button" class="btn btn-light">
                        <span class="fa fa-mail-reply-all"></span>
                    </button>
                    <button type="button" class="btn btn-light">
                        <span class="fa fa-mail-forward"></span>
                    </button>
                </div>
                <button type="button" class="btn btn-light">
                    <span class="fa fa-trash-o"></span>
                </button>
                <div class="btn-group">
                    <button type="button" class="btn btn-light dropdown-toggle" data-toggle="dropdown">
                        <span class="fa fa-tags"></span>
                        <span class="caret"></span>
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#">add label <span class="badge badge-danger"> Home</span></a>
                        <a class="dropdown-item" href="#">add label <span class="badge badge-info"> Job</span></a>
                        <a class="dropdown-item" href="#">add label <span class="badge badge-success"> Clients</span></a>
                        <a class="dropdown-item" href="#">add label <span class="badge badge-warning"> News</span></a>
                    </div>
                </div>
            </div>
            <?php
            if (!empty($smg)) {
                getSmg($smg, $smgType);
            }
            ?>
            <div class="details">
                <div class="title"><?php echo $messageDetail['messageSubject'] ?></div>
                <div class="header">
                    <img class="avatar" src="<?php echo $userDetail['profileImage'] ?>">
                    <div class="from">
                        <span><?php echo $userDetail['fullname'] ?></span>
                        <?php echo $userDetail['email'] ?>
                    </div>
                    <div class="date"><?php echo formatTimeDifference($messageDetail['create_at']) ?></div>
                </div>
                <div class="content">
                    <blockquote>
                        <p>
                            <?php echo $messageDetail['messageContent'] ?>
                        </p>
      
                    </blockquote>
                </div>
                <!-- <div class="attachments">
                    <div class="attachment">
                        <span class="badge badge-danger">zip</span> <b>bootstrap.zip</b> <i>(2,5MB)</i>
                        <span class="menu">
                            <a href="#" class="fa fa-search"></a>
                            <a href="#" class="fa fa-share"></a>
                            <a href="#" class="fa fa-cloud-download"></a>
                        </span>
                    </div>
                    <div class="attachment">
                        <span class="badge badge-info">txt</span> <b>readme.txt</b> <i>(7KB)</i>
                        <span class="menu">
                            <a href="#" class="fa fa-search"></a>
                            <a href="#" class="fa fa-share"></a>
                            <a href="#" class="fa fa-cloud-download"></a>
                        </span>
                    </div>
                    <div class="attachment">
                        <span class="badge badge-success">xls</span> <b>spreadsheet.xls</b> <i>(984KB)</i>
                        <span class="menu">
                            <a href="#" class="fa fa-search"></a>
                            <a href="#" class="fa fa-share"></a>
                            <a href="#" class="fa fa-cloud-download"></a>
                        </span>
                    </div>
                </div> -->
                <form method="post" action="">

                    <div class="form-group">
                        <textarea class="form-control" name="replyContent" rows="12" placeholder="Click here to reply"></textarea>
                    </div>
                    <div class="form-group" style="margin-top: 16px">
                        <button tabindex="3" type="submit" class="btn mg-btn primary">Send message</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
<?php
layouts('footer');

?>