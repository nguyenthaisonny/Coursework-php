<?php

if (!defined('_CODE')) {
    die('Access denied...');
}
$data = [
    'titlePage' => 'Sign in'
];


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
                $tokenLogin = sha1(uniqid() . time());
                //insert to tokenlogin table
                $dataInsert = [
                    'userId' => $userId,
                    'token' => $tokenLogin
                ];
                $inserStatus = insert('tokenlogin', $dataInsert);

                if ($inserStatus) {
                    // insert success

                    setSession('loginToken', $tokenLogin);

                    reDirect('?module=home&page=forum/forum');
                } else {
                    setFlashData('smg', 'Cannot sign in, please try again!');
                    setFlashData('smg_type', 'danger');
                    reDirect('?module=auth&page=login');
                }
            } else {
                setFlashData('smg', 'Password is not correct! Please type again !');
                setFlashData('smg_type', 'danger');
                reDirect('?module=auth&page=login');
            }
        } else {
            setFlashData('smg', 'This email is not exist!');
            setFlashData('smg_type', 'danger');
            reDirect('?module=auth&page=login');
        }
    } else {
        setFlashData('smg', 'Please type email and password');
        setFlashData('smg_type', 'danger');
        reDirect('?module=auth&page=login');
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
            <button type="submit" class="mg-btn large primary">
                Sign in
            </button>


        </form>
        <button class="mg-btn large rounded">

            <a class="largeAnker" href="?module=auth&page=register">Sign up</a>
        </button>


        <hr>
        <p class="text-center">
            <a href="?module=auth&page=forgot">Forgot password?</a>
        </p>
    </div>
</div>



<?php
layouts('footer')

?>