<?php
if(!defined('_CODE')) {
    die('Access denied...');
}

$data = [
    'titlePage' => 'Forgot password'
];
layouts('header', $data);
 
if ($_POST) {
    $filterAll = filter();
    if(!empty($filterAll['email'])) {
        $email = $filterAll['email'];
        $queryUser = getRaw("SELECT id, fullname FROM users WHERE email='$email'");
        if(!empty($queryUser)) {
            $userId = $queryUser['id'];
            $fullname = $queryUser['fullname'];
            // create forgotToken and update
            $forgotToken = sha1(uniqid().time());
            $dataUpdate = [
                'forgotToken' => $forgotToken
            ];
            $updateStatus = update('users', $dataUpdate, "id = $userId");
            if($updateStatus) {
                //create link reset password
                $linkReset = _WEB_HOST.'?module=auth&action=reset'.'&token='.$forgotToken;
                // send mail for user
                $subject = 'Request to recover your password';
                $content = 'Hi '. $fullname. '</br>';
                $content .= ' We were sent the request to recover password from you. Please click this link bellow to recover your password!'.'</br>';
                $content .= $linkReset. '</br>';
                $content .= 'Thank you!';
                $sendEmail = sendMail($email, $subject, $content);
                if($sendEmail) {
                setFlashData('smg', 'Please check your email to recover your password');
                setFlashData('smg_type', 'success');
                }
                else {
                setFlashData('smg', 'System get errors :(( Please try again email!');
                setFlashData('smg_type', 'danger');
                }
            }
            else {
                setFlashData('smg', 'System get errors :(( Please try again!');
                setFlashData('smg_type', 'danger');
            }

        } else {
            setFlashData('smg', 'This email address does not exist in system !');
            setFlashData('smg_type', 'danger');
        }
    } else {
        setFlashData('smg', 'Please type email address!');
        setFlashData('smg_type', 'danger');
    
    }
    // reDirect('?module=auth&action=reset');
}   

$smg = getFlashData('smg');
$smgType = getFlashData('smg_type');

?>

<div class="row">
    <div class="col-4" style="margin: 50px auto;">
        <h2 class="text-center text-uppercase">Forgot Password</h2>
        <?php
        if (!empty($smg)) {
            getSmg($smg, $smgType);
        }
        ?>
        <form action="" method="post">
            <div class="form-group mg-form">
                <label for="">Email</label>
                <input name="email" type="email" class="form-control" placeholder="Enter your email">
            </div>

            
            <button type="submit" class="mg-btn large primary">
                Submit
            </button>
            <hr>
            
        </form>
        

            <button class="mg-btn large rounded" >
    
                 <a href="?module=auth&action=login" >Sign in</a>
            </button>
            
            <button class="mg-btn large rounded" >
    
                <a href="?module=auth&action=register" >Sign up</a>
            </button>
        
    </div>
</div>



<?php
layouts('footer')

?>