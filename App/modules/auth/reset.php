<?php
if (!defined('_CODE')) {
    die('Access denied...');
}



$token = filter()['token'];

if (!empty($token)) {
    $tokenQuery = getRaw("SELECT id, fullname, email FROM users WHERE forgotToken='$token'");
    if (!empty($tokenQuery)) {
        $userId = $tokenQuery['id'];
        if (isPost()) {
            $filterAll = filter();
            $errors = [];
            //validate passwoed: required, min 8, 
            if (empty($filterAll['password'])) {
                $errors['password']['required'] = 'Password is required!';
            } else {
                if (strlen($filterAll['password']) < 7) {
                    $errors['password']['min'] = 'Password should be more than 8 characters!';
                }
            }

            //validate password confirm: required, equal to password
            if (empty($filterAll['password_confirm'])) {
                $errors['password_confirm']['required'] = 'Password confirm is required!';
            } else {
                if ($filterAll['password_confirm'] != $filterAll['password']) {
                    $errors['password_confirm']['match'] = 'Invalid password confirm!';
                }
            }
            if (empty($errors)) {
                $passwordHash = password_hash($filterAll['password'], PASSWORD_DEFAULT);
                $dataUpdate = [
                    'password' => $passwordHash,
                    'forgotToken' => NULL,
                    'update_at' => date('Y-m-d H:i:s')
                ];
                $updateStatus = update('users', $dataUpdate, "id='$userId'");
                if ($updateStatus) {
                    if (!empty(getSession('loginToken'))) {
                        $token = getSession('loginToken');
                        delete('tokenlogin', "token='$token'");
                        removeSession('loginToken');
                    }
                    reDirect('?module=auth&page=login');
                    setFlashData('smg_type', 'Success!');
                    setFlashData('smg', 'Plesase check your data again !');
                } else {
                    setFlashData('smg', 'System got errors! Plesase try again.');
                    setFlashData('smg_type', 'danger');
                }
            } else {
                setFlashData('errors', $errors);

                setFlashData('smg', 'Plesase check your data again !');
                setFlashData('smg_type', 'danger');
            }
        }

        $errors = getFlashData('errors');
        $smg = getFlashData('smg');
        $smgType = getFlashData('smg_type');

        layouts('header', ['titlePage' => 'reset']);
?>
        <div class="row">
            <div class="col-4" style="margin: 50px auto;">
                <h2 class="text-center text-uppercase">Reset password</h2>
                <form action="" method="post">
                    <?php
                    if (!empty($smg)) {
                        getSmg($smg, $smgType);
                    }
                    ?>

                    <div class="form-group mg-form">
                        <label for="">Password</label>
                        <input name="password" type="password" class="form-control" placeholder="Enter your password" value=<?php  ?>>
                        <?php
                        echo formErr('password', '<span class="error" >', '</span>', $errors);

                        ?>
                    </div>

                    <div class="form-group mg-form">
                        <label for="">Confirmed password</label>
                        <input name="password_confirm" type="password" class="form-control" placeholder="Confirm your password" value=<?php  ?>>
                        <?php
                        echo formErr('password_confirm', '<span class="error" >', '</span>', $errors);

                        ?>
                    </div>
                    <input type="hidden" name='token' value=<?php echo $token; ?>>


                    <button type="submit" class="mg-btn primary large">
                        Submit
                    </button>

                </form>
            </div>
        </div>

<?php
        layouts('footer');
    } else {
        setFlashData('smg', 'Link is not exist or out-dated!');
        setFlashData('smg_type', 'danger');
    }
} else {
    setFlashData('smg', 'Link is not exist or out-dated!');
    setFlashData('smg_type', 'danger');
}
