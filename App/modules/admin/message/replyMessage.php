<?php

if (!defined('_CODE')) {
    die('Access denied...');
}

$data = [
    'titlePage' => 'Reply message'
];

$isAdmin = checkAdmin();
if (!$isAdmin) {
    reDirect('?module=home&page=forum/forum');
}
$messageId = $_GET['messageId'];
$messageDetail = getRaw("SELECT * FROM messages WHERE id = '$messageId'");

$userId = $messageDetail['userId'];
$userDetail = getRaw("SELECT fullname, email, profileImage FROM users WHERE id = '$userId'");
if (empty($userDetail)) {
    setFlashData('smg', 'User is not exist!');
    setFlashData('smg_type', 'danger');
}
$dataUpdate = ['readStatus' => 1];
$updateStatus = update('messages', $dataUpdate, "id='$messageId'");
setSession('userDetail', $userDetail);


if (isPost()) {
    $loginToken = getSession('loginToken');
    $queryToken = getRaw("SELECT userId FROM tokenlogin WHERE token = '$loginToken'");
    $adminId = $queryToken['userId'];
    $messageDetail = getRaw("SELECT * FROM messages WHERE id = '$messageId'");
    $userDetail = getRaw("SELECT id, fullname, email, profileImage FROM users WHERE id = '$userId'");
    $adminDetail = getRaw("SELECT fullname, email FROM users WHERE id = '$adminId'");

    if (empty($userDetail)) {
        setFlashData('smg', 'User is not exist!');
        setFlashData('smg_type', 'danger');
    } else {

        $filterAll = filter();
        //insert to database

        $dataInsert = [

            'userId' => $adminId,
            'messageSubject' => $messageDetail['messageSubject'],
            'messageContent' => $filterAll['replyContent'],
            'belong' => 'admin',
            'toUserId' => $userId

        ];
        $insertStatus = insert('messages', $dataInsert);
        //update replystatus
        $dataUpdate = [
            'replyStatus' => 1
        ];
        $updateStatus = update('messages', $dataUpdate, "id = '$messageId'");
        //send mail
        $replyContent = $filterAll['replyContent'];

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
            
            <p>Hello, ' . $userDetail['fullname'] . '</p>
            <h3>Your message you sent us are: </h3>
            <h5>Subject: ' . $messageDetail['messageSubject'] . '</h5>
            <p>' . $messageDetail['messageContent'] . '</p>
            <h3>We would like to answer as follows: </h3>
            
            <p>' . $replyContent . '</p>
            
            
        </div>
    </body>
    </html>';




        $subject = 'Message from admin of Nguyen Thai Sonny forum';

        $sendMail = sendMail($userDetail['email'], $subject, $content);
        if ($sendMail && $dataInsert) {
            setFlashData('smg', 'Reply has been sent');
            setFlashData('smg_type', 'success');
            reDirect('?module=admin&page=message/readMessage');
        } else {
            setFlashData('smg', 'Opps, The system get some errors :(( Please try again! ');
            setFlashData('smg_type', 'danger');
        }
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


        <main class="message">
            <a href="?module=admin&page=message/readMessage" class="btn mg-btn rounded">Back</a>
            <div class="toolbar">

            </div>
            <?php
            if (!empty($smg)) {
                getSmg($smg, $smgType);
            }
            ?>
            <div class="details">
                <div class="title">Subject: <?php echo $messageDetail['messageSubject'] ?></div>
                <div class="header">
                    <img class="avatar" src="<?php echo !empty($userDetail['profileImage']) ? $userDetail['profileImage'] : "https://img.freepik.com/free-vector/businessman-character-avatar-isolated_24877-60111.jpg?w=826&t=st=1710127291~exp=1710127891~hmac=10efc92f9bddd8afe06fa86d74c0caf109f33b79794fd0fc982a01c8bff70758"; ?>">
                    <div class="from">
                        <span style="font-size: 16px; font-weight:600; color: black;"><?php echo !empty($userDetail['fullname']) ? $userDetail['fullname'] : "Not found!" ?></span>
                        <?php echo !empty($userDetail['email']) ? $userDetail['email'] : "Not found!" ?>
                    </div>
                    <div class="date"><?php echo formatTimeDifference($messageDetail['createAt']) ?></div>
                </div>
                <div class="content">
                    <blockquote>
                        <p style="word-wrap: break-word;">
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
                        <button tabindex="3" type="submit" class="btn mg-btn primary">Send Message</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
<?php
layouts('footer');

?>