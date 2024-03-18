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
    reDirect('?module=home&action=forum');
}

$listMessage = getRaws("SELECT * FROM messages ORDER BY create_at DESC");





$smg = getFlashData('smg');
$smgType = getFlashData(('smg_type'));
// quey to users table






layouts('headerReadMessage', $data);
?>
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
<div class="container bootdey">
    <div class="email-app mb-4">
        <nav>
            <a href="page-inbox-compose.html" class="btn btn-danger btn-block">New Email</a>
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
                    <a class="nav-link" href="#"><i class="fa fa-bookmark"></i> Important</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fa fa-inbox"></i> Inbox <span class="badge badge-danger">4</span></a>
                </li>
            </ul>
        </nav>
        <main class="inbox">
            <div class="toolbar">
                <div class="btn-group">
                    <button type="button" class="btn btn-light">
                        <span class="fa fa-envelope"></span>
                    </button>
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
                <div class="btn-group float-right">
                    <button type="button" class="btn btn-light">
                        <span class="fa fa-chevron-left"></span>
                    </button>
                    <button type="button" class="btn btn-light">
                        <span class="fa fa-chevron-right"></span>
                    </button>
                </div>
            </div>
            <?php
            if (!empty($smg)) {
                getSmg($smg, $smgType);
            }
            ?>
            <ul class="messages overflow-auto">
                <?php
                if (!empty($listMessage)) :
                    $count = 0;
                    foreach ($listMessage as $item) :
                        $userId = $item['userId'];
                        $postId = $item['id'];

                        $questionCount = countRow("SELECT id FROM questions WHERE postId = '$postId'");

                        $userDetail = getRaw("SELECT fullname, email, profileImage FROM users WHERE id='$userId' ");
                        $count++;
                ?>
                        <li class="message <?php echo $item['readStatus'] == 0 ? 'unread' : null; ?>">
                            <a href="?module=admin&action=replyMessage&messageId=<?php echo $item['id']; ?>">

                                <div class="header">
                                    <span class="from" style="font-size: 18px; line-height: 16px;"><i style="margin-right: 6px; font-weight:300; font-size: 16px;" class="fa fa-square-o"></i><?php echo $item['fullnameMessage']; ?></span>
                                    <span class="date">
                                        <span class="fa fa-paper-clip"></span><?php echo formatTimeDifference($item['create_at']); ?></span>
                                </div>
                                <div class="title">
                                    <i style="margin-right: 6px; font-weight:100;" class="fa fa-star-o"></i> <?php echo $item['messageSubject']; ?>
                                </div>
                                <div class="description">
                                    <?php echo $item['messageContent']; ?>
                                </div>
                            </a>
                        </li>
                    <?php
                    endforeach;
                else :
                    ?>
                    <tr>
                        <td>
                            <div class="alert alert-danger text-center">None of Post</div>
                        </td>
                    </tr>
                <?php

                endif;
                ?>


            </ul>
        </main>
    </div>
</div>
<?php
layouts('footer');

?>