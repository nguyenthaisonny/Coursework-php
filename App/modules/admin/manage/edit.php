<?php
if (!defined('_CODE')) {
    die('Access denied...');
}
$data = [
    'pageTitle' => 'Edit user'

];
$isAdmin = checkAdmin();
if (!$isAdmin) {
    reDirect('?module=home&page=forum/forum');
}
// $result = countRow('SELECT * FROM users');
// echo $result;
$filterAll = filter();
if (!empty($filterAll['id'])) {
    $userId = $filterAll['id'];

    // check whether exist in database
    //if exist => get info
    //if not exist => navigat to list page
    $userDetail = getRaw("SELECT * FROM users WHERE id='$userId'");
    if (!empty($userDetail)) {
        //exist
        setFlashData('user_detail', $userDetail);
    } else {
        reDirect('?module=admins&page=manage/list');
    }
}
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
        $userId = $filterAll['id'];
        $sql = "SELECT id FROM users WHERE email = '$email' AND id <> $userId";
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
    if (!empty($filterAll['password'])) {
        if (strlen($filterAll['password']) < 7) {
            $errors['password']['min'] = 'Password should be more than 8 characters!';
        }
        //validate password confirm: required, equal to password
        if (empty($filterAll['password_confirm'])) {
            $errors['password_confirm']['required'] = 'Password confirm is required!';
        } else {
            if ($filterAll['password_confirm'] != $filterAll['password']) {
                $errors['password_confirm']['match'] = 'Invalid password confirm!';
            }
        }
    }


    if (empty($errors)) {
        //handle insert to database

        $activeToken = sha1(uniqid() . time());
        $dataUpdate = [
            'fullname' => $filterAll['fullname'],
            'email' => $filterAll['email'],
            'phone' => $filterAll['phone'],

            'status' => $filterAll['status'],
            'update_at' => date('Y:m:d H:i:s')




        ];
        if (!empty($filterAll['password'])) {
            $dataUpdate['password'] = password_hash($filterAll['password'], PASSWORD_DEFAULT);
        }
        $condition = "id = $userId";
        $updateStatus = update('users', $dataUpdate, $condition);
        if ($updateStatus) {
            $linkActive = _WEB_HOST . '/?module=auth&page=active&token=' . $activeToken;
            setFlashData('smg', 'Edit user success!');
            setFlashData('smg_type', 'success');
        } else {
            setFlashData('smg', 'System faces errors! Please try again.');
            setFlashData('smg_type', 'danger');
        }
    } else {
        setFlashData('errors', $errors);
        setFlashData('old', $filterAll);
        setFlashData('smg', 'Plesase check your data again !');
        setFlashData('smg_type', 'danger');
    }
    reDirect('?module=admin&page=manage/edit&id=' . $userId);
}

layouts('header', $data);
$errors = getFlashData('errors');
// print_r($errors);
$smg = getFlashData('smg');
$smgType = getFlashData(('smg_type'));
$old = getFlashData('old');
$userDetail = getFlashData('user_detail');
if (!empty($userDetail)) {
    $old = $userDetail;
}

?>

<div class="container">
    <div class="row" style="margin: 50px auto;">
        <h2 class="text-center text-uppercase">Edit user</h2>
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
                </div>
                <div class="col">
                    <div class="form-group mg-form">
                        <label for="">Password</label>
                        <input name="password" type="password" class="form-control" placeholder="Enter your password (Please don't type if not change)">
                        <?php

                        echo formErr('password', '<span class="error" >', '</span>', $errors);
                        ?>
                    </div>

                    <div class="form-group mg-form">
                        <label for="">Confirmed password</label>
                        <input name="password_confirm" type="password" class="form-control" placeholder="Confirm your password (Please don't type if not change)">
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

            <input type="hidden" name="id" value=<?php echo $userId; ?>>
            <div style="display: flex; justify-content: space-between;">
                <button class="mg-btn medium rounded">
                    <a style="padding: 12px 292px" href="?module=admin&page=manage/list">Back</a>

                </button>


                <button type="submit" class="mg-btn medium primary">Submit</button>
            </div>

    </div>
</div>

<?php
layouts('footer')
?>