<?php
if (!defined('_CODE')) {
    die('Access denied...');
}
$data = [
    'pageTitle' => 'Sign up'

];
// $result = countRow('SELECT * FROM users');
// echo $result;


// filter data from form
if (isPost()) {
    $filterAll = filter();

    
    $errors = []; // Array has errs
    //validate fullname: required , min 5
    if (empty($filterAll['fullname'])) {
        $errors['fullname']['required'] = 'Fullname is required!';
    } else {
        if (strlen($filterAll['fullname'] < 6)) {
            $errors['fullname']['min'] = 'Fullname should be more than 5 characters!';
        }
    }

    //Email Validate: required, true notation, check email existed
    if (empty($filterAll['email'])) {
        $errors['email']['required'] = 'Email is required!';
    } else {
        $email = $filterAll['email'];
        $sql = "SELECT id FROM users WHERE email = '$email'";
        if (countRow($sql) > 0) {
            $errors['email']['unique'] = 'This email has already existed!';
        }
    }
    //valide phone number: required, true notation
    if (empty($filterAll['phone'])) {
        $errors['phone']['required'] = 'Phone number is required!';
    } else {
        if (!isPhone($filterAll['phone'])) {
            $errors['phone']['isPhone'] = 'Invalid phone number';
        }
    }
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
        //handle insert to database
        
        $activeToken = sha1(uniqid() . time());
        $dataInsert = [
            'fullname' => $filterAll['fullname'],
            'email' => $filterAll['email'],
            'phone' => $filterAll['phone'],
            'password' => password_hash($filterAll['password'], PASSWORD_DEFAULT),
            'activeToken' => $activeToken,




        ];
        $insertStatus = insert('users', $dataInsert);
        if ($insertStatus) {
            $linkActive = _WEB_HOST . '/?module=auth&action=active&token=' . $activeToken;
            setFlashData('smg', 'Sign up success!');
            setFlashData('smg_type', 'success');
            // send mail verify
            $subject = $filterAll['fullname'] . ' [verify your account!] ';
            $content = 'Hi ' . $filterAll['fullname'] . '</br>';
            $content .= ' .PLease click this link below to active your account! ' . '</br>';
            $content .= $linkActive . '</br>';
            $content .= ' Thanks';

            $sendMail = sendMail($filterAll['email'], $subject, $content);
            if ($sendMail) {
                setFlashData('smg', 'Sign up success! Please check your email to active your account ^^');
                setFlashData('smg_type', 'success');
                reDirect('?module=auth&action=login');
            } else {
                setFlashData('smg', 'Opps, The system get some errors :(( Please try again! ');
                setFlashData('smg_type', 'danger');
                
            }
        } else {
            setFlashData('smg', 'Sign up fail, please try again!');
            setFlashData('smg_type', 'danger');
            
        }
        
    } else {
        setFlashData('errors', $errors);
        setFlashData('old', $filterAll);
        setFlashData('smg', 'Plesase check your data again !');
        setFlashData('smg_type', 'danger');

        reDirect('?module=auth&action=register');
    }
}

layouts('header', $data);
$errors = getFlashData('errors');
// print_r($errors);
$smg = getFlashData('smg');
$smgType = getFlashData(('smg_type'));
$old = getFlashData('old');
?>

<div class="row">
    <div class="col-4" style="margin: 50px auto;">
        <h2 class="text-center text-uppercase">Sign up</h2>
        <form action="" method="post">
            <?php
            if (!empty($smg)) {
                getSmg($smg, $smgType);
            }
            ?>
            <div class="form-group mg-form">
                <label for="">Fullname</label>
                <input name="fullname" type="fullname" class="form-control" placeholder="Enter your name" value=<?php echo getOldValue($old, 'fullname') ?>>
                <?php
                echo formErr('fullname', '<span class="error" >', '</span>', $errors);
                ?>
            </div>
            <div class="form-group mg-form">
                <label for="">Email</label>
                <input name="email" type="email" class="form-control" placeholder="Enter your email" value=<?php echo  getOldValue($old, 'email') ?>>
                <?php
                echo formErr('email', '<span class="error" >', '</span>', $errors);
                ?>
            </div>
            <div class="form-group mg-form">
                <label for="">Phone number</label>
                <input name="phone" type="number" class="form-control" placeholder="Enter your phone" value=<?php echo  getOldValue($old, 'phone') ?>>
                <?php

                echo formErr('phone', '<span class="error" >', '</span>', $errors);
                ?>
            </div>
            <div class="form-group mg-form">
                <label for="">Password</label>
                <input name="password" type="password" class="form-control" placeholder="Enter your password" value=<?php echo  getOldValue($old, 'password') ?>>
                <?php

                echo formErr('password', '<span class="error" >', '</span>', $errors);
                ?>
            </div>

            <div class="form-group mg-form">
                <label for="">Confirmed password</label>
                <input name="password_confirm" type="password" class="form-control" placeholder="Confirm your password" value=<?php echo  getOldValue($old, 'password_confirm') ?>>
                <?php

                echo formErr('password_confirm', '<span class="error" >', '</span>', $errors);
                ?>
            </div>

            <button type="submit" class="mg-btn primary">
                Sign up
            </button>
            <hr>

            <p class="text-center">
                <a href="?module=auth&action=login">I already had an account  <span>❤</span> </a>
            </p>
        </form>
    </div>
</div>

<?php
layouts('footer')
?>