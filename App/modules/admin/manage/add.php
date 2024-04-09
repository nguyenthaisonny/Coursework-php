<?php
if (!defined('_CODE')) {
    die('Access denied...');
}
$data = [
    'pageTitle' => 'User management'

];
// $result = countRow('SELECT * FROM users');
// echo $result;

$isAdmin = checkAdmin();
if (!$isAdmin) {
    reDirect('?module=home&page=forum/forum');
}
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
            'status' => $filterAll['status']




        ];
        $insertStatus = insert('users', $dataInsert);
        if ($insertStatus) {
            $linkActive = _WEB_HOST . '/?module=auth&page=active&token=' . $activeToken;
            setFlashData('smg', 'Success! A new user was just added');
            setFlashData('smg_type', 'success');
            reDirect('?module=admin&page=manage/list');
        } else {
            setFlashData('smg', 'System faces errors! Please try again.');
            setFlashData('smg_type', 'danger');
            reDirect('?module=admin&page=manage/add');
        }
    } else {
        setFlashData('errors', $errors);
        setFlashData('old', $filterAll);
        setFlashData('smg', 'Plesase check your data again !');
        setFlashData('smg_type', 'danger');

        reDirect('?module=admin&page=manage/add');
    }
}

layouts('header', $data);
$errors = getFlashData('errors');
// print_r($errors);
$smg = getFlashData('smg');
$smgType = getFlashData(('smg_type'));
$old = getFlashData('old');
?>

<div class="container">
    <div class="row" style="margin: 50px auto;">
        <h2 class="text-center  text-uppercase">Add user</h2>
        <?php
        if (!empty($smg)) {
            getSmg($smg, $smgType);
        }
        ?>
        <form action="" method="post">
            <div class="row">
                <div class="col">
                    <div class="form-group mg-form">
                        <label for="">Fullname</label>
                        <input name="fullname" type="fullname" class="form-control" placeholder="Enter user's name" value=<?php echo getOldValue($old, 'fullname') ?>>
                        <?php
                        echo formErr('fullname', '<span class="error" >', '</span>', $errors);
                        ?>
                    </div>
                    <div class="form-group mg-form">
                        <label for="">Email</label>
                        <input name="email" type="email" class="form-control" placeholder="Enter user's email" value=<?php echo  getOldValue($old, 'email') ?>>
                        <?php
                        echo formErr('email', '<span class="error" >', '</span>', $errors);
                        ?>
                    </div>
                    <div class="form-group mg-form">
                        <label for="">Phone number</label>
                        <input name="phone" type="number" class="form-control" placeholder="Enter user's phone" value=<?php echo  getOldValue($old, 'phone') ?>>
                        <?php

                        echo formErr('phone', '<span class="error" >', '</span>', $errors);
                        ?>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group mg-form">
                        <label for="">Password</label>
                        <input name="password" type="password" class="form-control" placeholder="Enter user's password" value=<?php echo  getOldValue($old, 'password') ?>>
                        <?php

                        echo formErr('password', '<span class="error" >', '</span>', $errors);
                        ?>
                    </div>

                    <div class="form-group mg-form">
                        <label for="">Confirmed password</label>
                        <input name="password_confirm" type="password" class="form-control" placeholder="Confirm user's password" value=<?php echo  getOldValue($old, 'password_confirm') ?>>
                        <?php

                        echo formErr('password_confirm', '<span class="error" >', '</span>', $errors);
                        ?>
                    </div>
                    <div class="form-group">
                        <label for="">Status</label>
                        <select name="status" id="" class="form-control">
                            <option value="0" <?php echo getOldValue($old, 'status') == 0 ? 'selected' : false; ?>>Not active</option>
                            <option value="1" <?php echo getOldValue($old, 'status') == 1 ? 'selected' : false; ?>>Active</option>

                        </select>
                    </div>

                </div>
            </div>
            <div style="display: flex; justify-content: space-between;">

                <button type="button" class="mg-btn medium rounded">

                    <a style="padding: 12px 292px" href="?module=admin&page=manage/list">Back</a>
                </button>
                <button type="submit" class="mg-btn medium primary">Submit</button>
            </div>

            </from>
    </div>
</div>

<?php
layouts('footer')
?>