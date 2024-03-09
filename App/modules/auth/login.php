<?php
if (!defined('_CODE')) {
    die('Access denied...');
}
$data = [
    'titlePage' => 'Sign in'
];

if(checkLogin()) {
    reDirect('?module=home&action=dashboard');
}   
if ($_POST) {
    $filterAll = filter();
    if (!empty(trim($filterAll['email'])) && !empty(trim($filterAll['password']))) {
        $email = $filterAll['email'];
        $password = $filterAll['password'];
        $userQuery = getRaw("SELECT password, id FROM users WHERE email = '$email'");
        if (!empty($userQuery)) {
            $passwordHash = $userQuery['password'];
            $userId = $userQuery['id'];
            if (password_verify($password, $passwordHash)) {
                //create tokenlogin
                $tokenLogin = sha1(uniqid().time());
                //insert to tokenlogin table
                $dataInsert = [
                    'user_id' => $userId,
                    'token' => $tokenLogin
                ];
                $inserStatus = insert('tokenlogin', $dataInsert);

                if($inserStatus) {
                    // insert success
                    setSession('loginToken', $tokenLogin);
                    
                    reDirect('?module=home&action=dashboard');
                } else {
                    setFlashData('smg', 'Cannot sign in, please try again!');
                    setFlashData('smg_type', 'danger');
                    reDirect('?module=auth&action=login');
                }
                
            } else {
                setFlashData('smg', 'Password is not correct! Please type again !');
                setFlashData('smg_type', 'danger');
                reDirect('?module=auth&action=login');
            }
        }
    } else {
        setFlashData('smg', 'Please type email and password');
        setFlashData('smg_type', 'danger');
        reDirect('?module=auth&action=login');
    }
}

$smg = getFlashData('smg');
$smgType = getFlashData('smg_type');
layouts('header', $data);

?>

<div class="row">
    <div class="col-4" style="margin: 50px auto;">
        <h2 class="text-center text-uppercase">Sign in</h2>
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

            <div class="form-group">
                <label for="">Password</label>

                <input name="password" type="password" class="form-control" placeholder="Enter your password">
            </div>
            <button type="submit" class="mg-btn btn btn-primary btn-block">
                Sign in
            </button>
            <hr>
            <p class="text-center">
                <a href="?module=auth&action=forgot">Forgot password?</a>
            </p>
            <p class="text-center">
                <a href="?module=auth&action=register">Sign up</a>
            </p>
        </form>
    </div>
</div>



<?php
layouts('footer')

?>