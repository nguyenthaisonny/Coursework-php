<?php
if (!defined('_CODE')) {
    die('Access denied...');
}
$data = [
    'titlePage' => 'Home'
];


if (!checkLogin()) {
    reDirect('?module=auth&action=login');
}
// echo $result;
$filterAll = filter();


if (!empty($filterAll['userIdEdit']) && !empty($filterAll['postId'])) {
    $userEditId = $filterAll['userIdEdit'];
    $postId = $filterAll['postId'];

    // check whether exist in database
    //if exist => get info
    //if not exist => navigat to list page
    $listQuestion = getRaws("SELECT * FROM questions WHERE postId='$postId'");
    if (!empty($listQuestion)) {
        //exist
        setFlashData('listQuestion', $listQuestion);
    } else {
        reDirect('?module=home&action=forum');
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
$errors = getFlashData('errors');
// print_r($errors);
$smg = getFlashData('smg');
$smgType = getFlashData(('smg_type'));
$old = getFlashData('old');
$ok = getFlashData('ok');
$no = getFlashData('no');
$listQuestion = getFlashData('listQuestion');

if (!empty($listQuestion)) {
    $old = $listQuestion;
    
    echo '<prev>';
    print_r($old);
    echo '</prev>';
    

}
layouts('headerForum', $data);
?>



<div class="container">
    <div class="main-body p-0">
        <div class="inner-wrapper">
            <!-- Inner sidebar -->
            <div class="inner-sidebar">
                <!-- Inner sidebar header -->
                <div class="inner-sidebar-header justify-content-center">
                    <!-- Button trigger modal -->
                    <button type="button" class="mg-btn medium rounded " style="margin: 0 25%;" data-toggle="modal" data-target="#newPostModel">
                        Upload <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
                <!-- /Inner sidebar header -->

                <!-- Inner sidebar body -->
                <div class="inner-sidebar-body p-0">
                    <div class="p-3 h-100" data-simplebar="init">
                        <div class="simplebar-wrapper" style="margin: -16px;">
                            <div class="simplebar-height-auto-observer-wrapper">
                                <div class="simplebar-height-auto-observer"></div>
                            </div>
                            <div class="simplebar-mask">
                                <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                                    <div class="simplebar-content-wrapper" style="height: 100%; overflow: hidden scroll;">
                                        <div class="simplebar-content" style="padding: 16px;">
                                            <nav class="nav nav-pills nav-gap-y-1 flex-column">
                                                <a href="javascript:void(0)" class="nav-link nav-link-faded has-icon active">All Threads</a>
                                                <a href="javascript:void(0)" class="nav-link nav-link-faded has-icon">Popular this week</a>
                                                <a href="javascript:void(0)" class="nav-link nav-link-faded has-icon">Popular all time</a>
                                                <a href="javascript:void(0)" class="nav-link nav-link-faded has-icon">Solved</a>
                                                <a href="javascript:void(0)" class="nav-link nav-link-faded has-icon">Unsolved</a>
                                                <a href="javascript:void(0)" class="nav-link nav-link-faded has-icon">No replies yet</a>
                                            </nav>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="simplebar-placeholder" style="width: 234px; height: 292px;"></div>
                        </div>
                        <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                            <div class="simplebar-scrollbar" style="width: 0px; display: none;"></div>
                        </div>
                        <div class="simplebar-track simplebar-vertical" style="visibility: visible;">
                            <div class="simplebar-scrollbar" style="height: 151px; display: block; transform: translate3d(0px, 0px, 0px);"></div>
                        </div>
                    </div>
                </div>
                <!-- /Inner sidebar body -->
            </div>
            <!-- /Inner sidebar -->

            <!-- Inner main -->
            <div class="inner-main">
                <!-- Inner main header -->
                <div class="inner-main-header">
                    <a class="nav-link nav-icon rounded-circle nav-link-faded mr-3 d-md-none" href="#" data-toggle="inner-sidebar"><i class="material-icons">arrow_forward_ios</i></a>
                    <select class="custom-select custom-select-sm w-auto mr-1">
                        <option selected="">Latest</option>
                        <option value="1">Popular</option>
                        <option value="3">Solved</option>
                        <option value="3">Unsolved</option>
                        <option value="3">No Replies Yet</option>
                    </select>

                </div>
                <?php
                if (!empty($smg)) {
                    getSmg($smg, $smgType);
                }
                ?>
                <!-- /Inner main header -->

                <!-- Inner main body -->

                <!-- Forum List -->
                <div class="inner-main-body p-2 p-sm-3 collapse forum-content">
                    <?php
                    if (!empty($listQuestion)) :
                        $count = 0;
                        foreach ($listQuestion as $item) :
                            $userId = $item['userId'];
                            $userDetail = getRaw("SELECT fullname, email, profileImage FROM users WHERE id='$userId' ");
                            $count++;
                    ?>
                            <div class="card mb-2">


                                <div class="card-body p-2 p-sm-3" style="display: flex;justify-content: space-between;">
                                    <div class="media forum-item">
                                        <div style="display: flex;align-items: center;">

                                            <a href="#" data-toggle="collapse" data-target=".forum-content"><img src="<?php echo $userDetail['profileImage'] ?>" class="mr-3 rounded-circle" width="50" alt="User" /></a>
                                            <div style="padding-left: 6px;">
                                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                                    <h6 style="margin: 0;padding: 0"><a href="#" data-toggle="collapse" data-target=".forum-content" class="text-body"><?php echo $userDetail['fullname'] ?></a></h6>
                                                    <p style="margin: 0; font-size: 14px; padding-top: 4px; margin-left: 6px; font-weight: 300;"><?php echo $item['create_at']; ?></p>
                                                </div>
                                                <p style="margin: 0"><?php echo $userDetail['email'] ?></p>
                                            </div>
                                        </div>
                                        <div class="media-body" style="margin-top: 10px;">
                                            <h6><a href="#" data-toggle="collapse" data-target=".forum-content" class="text-body"><?php echo $item['postName'] ?></a></h6>
                                            <p class="text-secondary">
                                                <?php echo $item['description'] ?>
                                            </p>

                                            <div>

                                                <span class="d-none d-sm-inline-block"><i class="far fa-eye"></i> 19</span>
                                                <span><i class="far fa-comment ml-2"></i> 3</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-muted small text-center align-self-center" style="display: flex; flex-direction: column; justify-content: space-between">

                                        <div>

                                            <a href="<?php echo _WEB_HOST; ?>/?module=home&action=editPost&postId=<?php echo $item['id'] ?>$userIdEdit=<?php echo $item['userId'] ?>" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen-to-square"></i></a>
                                            <a href="<?php echo _WEB_HOST; ?>/?module=home&action=deletePost&postId=<?php echo $item['id'] ?>&userIdDelete=<?php echo $item['userId'] ?>" onclick="return confirm('Delete this post?')" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        <?php
                        endforeach;
                    else :
                        ?>
                        <tr>
                            <td colspan="7">
                                <div class="alert alert-danger text-center">None of Post</div>
                            </td>
                        </tr>
                    <?php

                    endif;
                    ?>
                    <ul class="pagination pagination-sm pagination-circle justify-content-center mb-0">
                        <li class="page-item disabled">
                            <span class="page-link has-icon"><i class="material-icons">chevron_left</i></span>
                        </li>
                        <li class="page-item"><a class="page-link" href="javascript:void(0)">1</a></li>
                        <li class="page-item active"><span class="page-link">2</span></li>
                        <li class="page-item"><a class="page-link" href="javascript:void(0)">3</a></li>
                        <li class="page-item">
                            <a class="page-link has-icon" href="javascript:void(0)"><i class="material-icons">chevron_right</i></a>
                        </li>
                    </ul>
                </div>
                <!-- /Forum List -->

                <!-- Forum Detail -->
                <div id='postCollapse' class="inner-main-body p-2 p-sm-3 forum-content collapse">
                    <a href="<?php echo _WEB_HOST; ?>/?module=home&action=forum" class="btn btn-light btn-sm mb-3 has-icon " data-target=".forum-content"><i class="fa fa-arrow-left mr-2"></i>Back</a>
                    <?php
                    if (!empty($listQuestion)) :
                        $count = 0;
                        foreach ($listQuestion as $item) :
                            $userId = $item['userId'];
                            $userDetail = getRaw("SELECT fullname, email, profileImage FROM users WHERE id='$userId' ");
                            $count++;
                    ?>
                            <div class="card mb-2">
                                <div class="card-body">
                                    <div class="media forum-item">
                                        <div style="display:flex; align-items: center;">

                                            <a href="javascript:void(0)" class="card-link">
                                                <img src="https://bootdey.com/img/Content/avatar/avatar1.png" class="rounded-circle" width="50" alt="User" />
                                               
                                            </a>
                                            <div  style="margin-left: 8px;">
                                                <h6 style="margin: 0;padding: 0"><a href="#"  class="text-body"><?php echo $userDetail['fullname'] ?></a></h6>
                                                <p style="margin: 0; font-size: 14px; font-weight: 300;"><?php echo $userDetail['email'] ?></p>
                                            </div>
                                        </div>
                                        <small class="text-muted ml-2" style="font-size: 16px; font-weight:300;"><?php echo $item['create_at']; ?></small>
                                        <div class="media-body ml-3">
                                            <h5 class="mt-1"><?php echo $item['title']; ?></h5>
                                            <div class="mt-3 font-size-sm">
                                                <div><?php echo $item['content']; ?></div>
                                            </div>
                                        </div>
                                        <div class="text-muted small text-center">
                                            <span class="d-none d-sm-inline-block"><i class="far fa-eye"></i> 19</span>
                                            <span><i class="far fa-comment ml-2"></i> 3</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
                        endforeach;
                    else :
                        ?>
                        <tr>
                            <td colspan="7">
                                <div class="alert alert-danger text-center">None of Post</div>
                            </td>
                        </tr>
                    <?php

                    endif;
                    ?>
                    <div class="card mb-2 ">
                        <div class="card-body">
                            <div class="media forum-item">
                                <a href="javascript:void(0)" class="card-link">
                                    <img src="https://bootdey.com/img/Content/avatar/avatar2.png" class="rounded-circle" width="50" alt="User" />
                                    <small class="d-block text-center text-muted">Pro</small>
                                </a>
                                <div class="media-body ml-3">
                                    <a href="javascript:void(0)" class="text-secondary">drewdan</a>
                                    <small class="text-muted ml-2">1 hour ago</small>
                                    <div class="mt-3 font-size-sm">
                                        <p>What exactly doesn't work with your ajax calls?</p>
                                        <p>Also, WebSockets are a great solution for realtime data on a forum. Laravel offers this out of the box using broadcasting</p>
                                    </div>
                                    <button class="btn btn-xs text-muted has-icon"><i class="fa fa-heart" aria-hidden="true"></i>1</button>
                                    <a href="javascript:void(0)" class="text-muted small">Reply</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Forum Detail -->

                <!-- /Inner main body -->
            </div>
            <!-- /Inner main -->
        </div>

        <!-- New Thread Modal -->
        <div class="modal fade" id="newPostModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" style="text-align: center;" id="exampleModalLabel">New Post</h5>

                    </div>
                    <div class="modal-body">
                        <form method="post">
                            <div class="form-group">
                                <label class="col-form-label">Title:</label>
                                <input name="postName" type="text" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">Description</label>
                                <input name="description" type="text" class="form-control">
                            </div>
                            <div class="modal-footer">
                            </div>
                            <button type="button" class="mg-btn  rounded " data-dismiss="modal">Close</button>
                            <button type="submit" class="mg-btn  primary" style="margin-left: 60px;">Upload</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<script>
    var myCollapse = document.getElementById('postCollapse')
    var bsCollapse = new bootstrap.Collapse(myCollapse, {




    })
</script>

<?php
layouts('footerIn')
?>