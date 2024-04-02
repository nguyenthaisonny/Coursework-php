<?php
if (!defined('_CODE')) {
    die('Access denied...');
}

$data = [
    'titlePage' => 'Profile'
];


if (!checkLogin()) {
    reDirect('?module=auth&page=login');
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
        reDirect('?module=home&page=forum/forum');
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

    if (empty($errors)) {
        if (!empty($_FILES["profileImage"]["name"])) {

            $target_dir = './templates/img/imgProfile/';
            $profileImage = $target_dir . $_FILES["profileImage"]["name"];
            move_uploaded_file($_FILES["profileImage"]["tmp_name"], $profileImage);
            //validat image



            //handle insert to database

            $ok = 'ok';
            $dataUpdate = [
                'fullname' => $filterAll['fullname'],
                'email' => $filterAll['email'],
                'phone' => $filterAll['phone'],
                'profileImage' => $profileImage,
                'description' => $filterAll['description']






            ];
        } else {
            $userId = $_GET['id'];
            $oldImage = getRaw("SELECT profileImage FROM users WHERE id = '$userId'");
            $dataUpdate = [
                'fullname' => $filterAll['fullname'],
                'email' => $filterAll['email'],
                'phone' => $filterAll['phone'],
                'profileImage' => $oldImage['profileImage'],
                'description' => $filterAll['description']







            ];
        }

        $condition = "id = $userId";
        $updateStatus = update('users', $dataUpdate, $condition);
        if ($updateStatus) {

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
    reDirect('?module=user&page=profile/profile&id=' . $userId);
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
                <div class="card-header">Avatar</div>
                <div class="card-body text-center">
                    <!-- Profile picture image-->
                    <img width="160" height="160" class="rounded-circle" src=<?php echo !empty(getOldValue($old, 'profileImage')) ? getOldValue($old, 'profileImage') : "https://img.freepik.com/free-vector/businessman-character-avatar-isolated_24877-60111.jpg?w=826&t=st=1710127291~exp=1710127891~hmac=10efc92f9bddd8afe06fa86d74c0caf109f33b79794fd0fc982a01c8bff70758"; ?> alt="">


                    <!-- Profile info-->
                    <h6 style="margin: 16px 0 0 0;font-size: 16px; font-weight: 600;">
                        <?php echo getOldValue($old, 'fullname') ?>
                        <?php echo checkAdminInList($userId) ? '<span style="color: #20D5EC; font-size: 16px;"><i class="fa-solid fa-circle-check"></i></span>' : null; ?>

                    </h6>

                    <p class="small font-italic text-muted mb-4"><?php echo getOldValue($old, 'email') ?></p>

                    <!-- Profile picture button-->

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
                                <label class="small mb-1">Email</label>
                                <input name="email" class="form-control" type="email" placeholder="Enter your email" value=<?php echo  getOldValue($old, 'email') ?>>
                                <?php
                                echo formErr('email', '<span class="error" >', '</span>', $errors);
                                ?>
                            </div>
                            <!-- Form Group (last name)-->
                            <div class="col-md-6">
                                <label class="small mb-1">Phone</label>
                                <input name="phone" class="form-control" placeholder="Enter your phone" type="number" value=<?php echo  getOldValue($old, 'phone') ?>>
                            </div>
                            <?php

                            echo formErr('phone', '<span class="error" >', '</span>', $errors);
                            ?>

                            <div class=" gx-3" style="padding-top: 16px;">
                                <label class="small mb-1" for="inputUsername">Description</label>
                                <input name="description" type="text" class="form-control" placeholder="Description about your self ^^" value="<?php echo getOldValue($old, 'description') != 'NULL' ? getOldValue($old, 'description') : null ?>">


                            </div>

                        </div>
                        <!-- Form Row        -->
                        <div class="mb-3">
                            <label class="small mb-1" for="inputUsername">Upload avatar (JPG or PNG no larger than 5 MB)</label>

                            <input name="profileImage" class="form-control" id="inputUsername" type="file" placeholder="Choose your image profile">
                            <?php

                            echo formErr('profileImage', '<span class="error" >', '</span>', $errors);
                            ?>
                        </div>
                        <input type="hidden" name="id" value="<?php echo $userId; ?>">

                        <!-- Save changes button-->
                        <hr class="mt-0 mb-3">
                        <button class="mg-btn rounded" style="margin-top: 0;" type="submit">Save Changes</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>



<?php
layouts('footerIn')
?>