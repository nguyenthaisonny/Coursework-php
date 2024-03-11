<?php
if (!defined('_CODE')) {
    die('Access denied...');
}

$data = [
    'titlePage' => 'Profile'
];


if (!checkLogin()) {
    reDirect('?module=auth&action=login');
}

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
        reDirect('?module=home&action=dasboard');
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

    // handle Image upload
    // if (isset($_FILES['profileImage'])) {
    //     $image = $_FILES['profileImage']['name'];
    // }
    $target_dir = './templates/img/';
    $profileImage = $target_dir.$_FILES["profileImage"]["name"];
    move_uploaded_file($_FILES["profileImage"]["tmp_name"], $profileImage);
    //validat image
    
        
    

    if (empty($errors)) {
        //handle insert to database

        $activeToken = sha1(uniqid() . time());
        $dataUpdate = [
            'fullname' => $filterAll['fullname'],
            'email' => $filterAll['email'],
            'phone' => $filterAll['phone'],
            'profileImage' => $profileImage






        ];
        if (!empty($filterAll['password'])) {
            $dataUpdate['password'] = password_hash($filterAll['password'], PASSWORD_DEFAULT);
        }
        $condition = "id = $userId";
        $updateStatus = update('users', $dataUpdate, $condition);
        if ($updateStatus) {
            $linkActive = _WEB_HOST . '/?module=auth&action=active&token=' . $activeToken;
            setFlashData('smg', 'Edit profile success!');
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
    reDirect('?module=user&action=profile&id=' . $userId);
}



layouts('headerProfile', $data);
$errors = getFlashData('errors');
// print_r($errors);
$smg = getFlashData('smg');
$smgType = getFlashData(('smg_type'));
$old = getFlashData('old');
$ok = getFlashData('ok');
$no = getFlashData('no');
$userDetail = getFlashData('user_detail');
echo $ok;
echo $no;
if (!empty($userDetail)) {
    $old = $userDetail;
    // echo '<prev>';
    // print_r($old);
    // echo '</prev>';
    

}


?>

<div class="container-xl px-4 mt-4">

    <hr class="mt-0 mb-4">
    <div class="row">
        <div class="col-xl-4">
            <!-- Profile picture card-->
            <div class="card mb-4 mb-xl-0">
                <div class="card-header">Profile Picture</div>
                <div class="card-body text-center">
                    <!-- Profile picture image-->
                    <img class="img-account-profile rounded-circle mb-2" src= <?php echo !empty(getOldValue($old, 'profileImage')) ? getOldValue($old, 'profileImage') : "https://img.freepik.com/free-vector/businessman-character-avatar-isolated_24877-60111.jpg?w=826&t=st=1710127291~exp=1710127891~hmac=10efc92f9bddd8afe06fa86d74c0caf109f33b79794fd0fc982a01c8bff70758" ;?> alt="">
                    

                    <!-- Profile picture help block-->
                    <div class="small font-italic text-muted mb-4">JPG or PNG no larger than 5 MB</div>
                    <!-- Profile picture upload button-->
                    <button class="mg-btn rounded" style="margin-top: 0;">Upload new image</button>
                </div>
            </div>
        </div>
        <div class="col-xl-8">
            <!-- Account details card-->
            <div class="card mb-4">
                <div class="card-header">Account Details</div>
                <?php
                if (!empty($smg)) {
                    getSmg($smg, $smgType);
                }
                ?>
                <div class="card-body">
                    <form action="" method="post" enctype="multipart/form-data">
                        <!-- Form Group (username)-->
                        <div class=" gx-3" style="padding-bottom: 16px;">
                            <label class="small mb-1" for="inputUsername">Fullname</label>
                            <input name="fullname" type="fullname" id="inputUsername" class="form-control" placeholder="Enter your name" value="<?php echo getOldValue($old, 'fullname') ?>">
                            <?php
                            echo formErr('fullname', '<span class="error" >', '</span>', $errors);
                            ?>

                        </div>
                        <div class="row gx-3 mb-3">
                            <!-- Form Row-->
                            <!-- Form Group (first name)-->
                            <div class="col-md-6">
                                <label class="small mb-1">Email Address</label>
                                <input name="email" class="form-control" type="email"  value=<?php echo  getOldValue($old, 'email') ?>>
                                <?php
                                echo formErr('email', '<span class="error" >', '</span>', $errors);
                                ?>
                            </div>
                            <!-- Form Group (last name)-->
                            <div class="col-md-6">
                                <label class="small mb-1">Phone</label>
                                <input name="phone" class="form-control"  type="number" value=<?php echo  getOldValue($old, 'phone') ?>>
                            </div>
                            <?php

                            echo formErr('phone', '<span class="error" >', '</span>', $errors);
                            ?>
                        </div>
                        <!-- Form Row        -->
                        <div class="mb-3">
                            <label class="small mb-1" for="inputUsername">Upload avatar</label>

                            <input name="profileImage" class="form-control" id="inputUsername" type="file" placeholder="Enter your username">
                            <?php

                            echo formErr('profileImage', '<span class="error" >', '</span>', $errors);
                            ?>
                        </div>
                        <input type="hidden" name="id" value="<?php echo $userId; ?>">

                        <!-- Save changes button-->
                        <hr class="mt-0 mb-3">
                        <button class="mg-btn rounded" style="margin-top: 0;" type="submit">Save changes</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>



<?php
layouts('footerIn')
?>