<?php

if (!defined('_CODE')) {
    die('Access denied...');
}

$data = [
    'titlePage' => 'New email'
];
// if(checkAdmin()) {
//     reDirect('?module=users&action=list');
// }
$isAdmin = checkAdmin();
if (!$isAdmin) {
    reDirect('?module=home&page=forum/forum');
}


if (isPost()) {


    $filterAll = filter();
    $replyContent = $filterAll['replyContent'];
    $replySubject = $filterAll['replySubject'];
    $emailTarget = $filterAll['emailTarget'];



    $content = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f4f4f4;
            }
            .container {
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
                background-color: #fff;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
            h1 {
                color: #333;
            }
            p {
                color: #666;
            }
            .button {
                display: inline-block;
                padding: 10px 20px;
                background-color: #007bff;
                color: #fff;
                text-decoration: none;
                border-radius: 5px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            
            <p>Hello,</p>
            <p>'.$replyContent.'</p>
            <p>Warm regards, </p>
            <p>Nguyen Thai Sonny</p>
            <p>Admin of Nguyen Thai Sonny Forum</p>

            
        </div>
    </body>
    </html>';




    $sendMail = sendMail($emailTarget, $replySubject, $content);
    if ($sendMail) {
        setFlashData('smg', 'Reply has been sent');
        setFlashData('smg_type', 'success');
        reDirect('?module=admin&page=message/readMessage');
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
            <a href="?module=admin&page=message/readMessage" class="btn mg-btn rounded">Back</a>
            <ul class="nav">
                <li class="nav-item" style="background-color: #eee;">
                    <a class="nav-link" href="#"><i class="fa fa-inbox"></i> Inbox </a>
                </li>

            </ul>
        </nav>
        <main class="message">
            <div class="toolbar">
                <!-- <div class="btn-group">
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
                </div> -->
            </div>
            <?php
            if (!empty($smg)) {
                getSmg($smg, $smgType);
            }
            ?>
            <div class="details">

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
                        <label for="">To: </label>
                        <input name="emailTarget" class="form-control" placeholder="example: example@example.com" required="required">
                    </div>
                    <div class="form-group" style="margin-top: 10px;">
                        <label for="">Subject: </label>
                        <input name="replySubject" class="form-control" placeholder="Write subject" required="required">
                    </div>
                    <div class="form-group" style="margin-top: 10px;">
                        <label for="">Content: </label>
                        <textarea class="form-control" name="replyContent" rows="12" placeholder="Write something" required="required"></textarea>
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