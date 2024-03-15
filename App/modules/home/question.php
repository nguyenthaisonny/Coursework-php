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


if (!empty($filterAll['userIdEdit']) && !empty($filterAll['postId']) && !empty($filterAll['questionId']) && !empty($filterAll['userIdPost'])) {
    $userIdEdit = $filterAll['userIdEdit'];
    $postId = $filterAll['postId'];
    $questionId = $filterAll['questionId'];
    $userIdPost  = $filterAll['userIdPost'];

    setFlashData('userIdPost', $userIdPost);
    setFlashData('userIdEdit', $userIdEdit);
    setFlashData('postId', $postId);
    setFlashData('questionId', $questionId);

    // check whether exist in database
    //if exist => get info
    //if not exist => navigat to list page
    $questionDetail = getRaw("SELECT * FROM questions WHERE id = '$questionId'");
    $userEditDetail = getRaw("SELECT * FROM users WHERE id = '$userIdEdit'");
    $listReply = getRaws("SELECT * FROM replies WHERE questionId = '$questionId'");

    if (!empty($listReply)) {
        //exist
        setFlashData('listReply', $listReply);
    }
    if (!empty($userEditDetail)) {
        //exist
        setFlashData('userEditDetail', $userEditDetail);
    }

    if (!empty($questionDetail)) {
        //exist
        setFlashData('questionDetail', $questionDetail);
    }
}

if (isPost()) {
    $filterAll = filter();
    if (!empty($filterAll['replyContent']) || !empty($filterAll['contentImage'])) {
        if (getSession('loginToken')) {

            $loginToken = getSession('loginToken');
            $queyToken = getRaw("SELECT userId FROM tokenlogin WHERE token = '$loginToken'");
            $userIdLogin = $queyToken['userId'];
            $userIdEdit = $filterAll['userIdEdit'];
            $postId = $filterAll['postId'];
            $questionId = $_GET['questionId'];
            $userIdPost = $_GET['userIdPost'];

            //handle Image
            $target_dir = './templates/img/imgReply/';
            $replyImage = $target_dir . $_FILES["replyImage"]["name"];
            move_uploaded_file($_FILES["replyImage"]["tmp_name"], $replyImage);
            $dataInsert = [

                'replyContent' => $filterAll['replyContent'],
                'update_at' => date('Y:m:d H:i:s'),
                'questionId' => $questionId,
                'userId' => $userIdLogin,
                'replyImage' => $replyImage

            ];
            $insertStatus = insert('replies', $dataInsert);
            if ($insertStatus) {

                setFlashData('smg', 'A new reply was just uploaded!');
                setFlashData('smg_type', 'success');
            } else {
                setFlashData('smg', 'System faces errors! Please try again.');
                setFlashData('smg_type', 'danger');
            }
            reDirect("?module=home&action=question&questionId=" . $questionId . "&postId=" . $postId . "&userIdEdit=" . $userIdEdit . "&userIdPost=" . $userIdPost);
        }
    }
}
$errors = getFlashData('errors');
// print_r($errors);
$smg = getFlashData('smg');
$smgType = getFlashData(('smg_type'));
$old = getFlashData('old');
$ok = getFlashData('ok');
$no = getFlashData('no');
$questionDetail = getFlashData('questionDetail');
$userEditDetail = getFlashData('userEditDetail');
$listReply = getFlashData(('listReply'));
$userIdPost = getFlashData('userIdPost');
$postId = getFlashData('postId');
$userIdEdit = getFlashData('userIdEdit');
if (!empty($listReply)) {
    $old = $listReply;
}
layouts('headerPost', $data);
?>



<div class="container">
    <div class="main-body p-0">
        <div class="inner-wrapper">
            <!-- Inner sidebar -->
            <div class="inner-sidebar">
                <!-- Inner sidebar header -->
                <div class="inner-sidebar-header justify-content-center">
                    <!-- Button trigger modal -->
                    <button type="button" class="mg-btn medium rounded " style="margin: 0 25%;" data-toggle="modal" data-target="#newReply">
                        New Reply <i class="fa-solid fa-plus"></i>
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
                                                <a href="javascript:void(0)" class="nav-link nav-link-faded has-icon active">All Replies</a>
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


                <!-- Questions -->
                <div id='postCollapse' class="inner-main-body p-2 p-sm-3 forum-content collapse">
                    <a href="<?php echo _WEB_HOST; ?>/?module=home&action=post&postId=<?php echo $postId; ?>&userIdEdit=<?php echo $userIdPost; ?>" class="btn btn-light btn-sm mb-3 has-icon " data-target=".forum-content"><i class="fa-solid fa-backward"></i></a>
                    <div class="container posts-content" style="position: relative;">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <div class="media mb-3">
                                            <h6 style="margin: 0; position: absolute; right: 50%;top: 14px; font-weight: 300;">Question</h6>
                                            <img src="<?php echo $userEditDetail['profileImage'] ?>" class="d-block ui-w-40 rounded-circle" alt="">
                                            <div class="media-body ml-3" style="position: absolute; left: 66px; top: 11px;">
                                                <h6 style="margin: 0 ;padding: 0; font-size: 16px">

                                                    <?php echo $userEditDetail['fullname'] ?>
                                                </h6>
                                                <div class="text-muted small">Latest: <?php echo $questionDetail['update_at'] != 'NULL' ? $questionDetail['create_at'] : $questionDetail['update_at']; ?></div>
                                            </div>
                                        </div>
                                        <div style="position: absolute; right: 14px; top: 13px;">

                                            <a style="padding: 6px 7px;" href="<?php echo _WEB_HOST; ?>/?module=home&action=editPost&postId=<?php echo $questionDetail['id'] ?>&userIdEdit=<?php echo $questionDetail['userId'] ?>" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen-to-square"></i></a>
                                            <a style="padding: 6px 7px;" href="<?php echo _WEB_HOST; ?>/?module=home&action=deleteQuestion&questionId=<?php echo $questionDetail['id'] ?>&userIdDelete=<?php echo $questionDetail['userId'] ?>&postId=<?php echo $questionDetail['postId'] ?>" onclick="return confirm('Delete this post?')" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></a>
                                        </div>
                                        <h5 style="margin: 0;"><a href="" class="text-body"><?php echo $questionDetail['title'] ?></a></h5>
                                        <p>
                                            <?php echo $questionDetail['content'] ?>
                                        </p>
                                        <?php echo $questionDetail['questionImage'] ? '<a href="javascript:void(0)" class="ui-rect ui-bg-cover" style="background-image: url(' . $questionDetail['questionImage'] . ');"></a>' :  null ?>

                                    </div>
                                    <div class="card-footer" style="display: flex; justify-content: space-evenly;">
                                        <a href="javascript:void(0)" class="d-inline-block text-muted">
                                            <i class="fa-regular fa-thumbs-up icon-hover" style="font-size: 20px;"></i>

                                        </a>
                                        <a href="<?php echo _WEB_HOST; ?>/?module=home&action=question&questionId=<?php echo $item['id'] ?>&postId=<?php echo $item['postId'] ?>&userIdEdit=<?php echo $item['userId'] ?>&userIdPost=<?php echo $userIdPost ?>" class="d-inline-block text-muted ml-3">

                                            <i class="fa-regular fa-comment icon-hover" style="font-size: 20px;"></i>
                                        </a>
                                        <a href="javascript:void(0)" class="d-inline-block text-muted ml-3">
                                            <i class="fa-solid fa-share icon-hover" style="font-size: 20px;"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <?php
                    if (!empty($listReply)) :
                        $count = 0;
                        foreach ($listReply as $item) :
                            $userId = $item['userId'];

                            $userDetail = getRaw("SELECT fullname, email, profileImage FROM users WHERE id='$userId' ");
                            $count++;
                    ?>
                            <div class="container posts-content" style="position: relative;">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="card mb-4">
                                            <div class="card-body">
                                                <div class="media mb-3">
                                                    <h6 style="margin: 0; position: absolute; right: 50%;top: 14px; font-weight: 300;">Reply</h6>
                                                    <img src="<?php echo $userDetail['profileImage'] ?>" class="d-block ui-w-40 rounded-circle" alt="">

                                                    <div class="media-body ml-3" style="position: absolute; left: 66px; top: 11px;">
                                                        <h6 style="margin: 0 ;padding: 0; font-size: 16px">

                                                            <?php echo $userDetail['fullname'] ?>
                                                        </h6>
                                                        <div class="text-muted small">Latest: <?php echo $item['update_at'] != 'NULL' ? $item['create_at'] : $item['update_at']; ?></div>
                                                    </div>
                                                </div>
                                                <div style="position: absolute; right: 14px; top: 13px;">

                                                    <a style="padding: 6px 7px;" href="<?php echo _WEB_HOST; ?>/?module=home&action=editPost&postId=<?php echo $item['id'] ?>&userIdEdit=<?php echo $item['userId'] ?>" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen-to-square"></i></a>
                                                    <a style="padding: 6px 7px;" href="" onclick="return confirm('Delete this post?')" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></a>
                                                </div>

                                                <p>
                                                    <?php echo $item['replyContent'] ?>
                                                </p>
                                                <?php echo $item['replyImage'] ? '<a href="javascript:void(0)" class="ui-rect ui-bg-cover" style="background-image: url(' . $item['replyImage'] . ');"></a>' :  null ?>



                                            </div>
                                            <div class="card-footer">
                                                <a href="javascript:void(0)" class="d-inline-block text-muted">
                                                    <strong>123</strong> <small class="align-middle">Likes</small>
                                                </a>
                                                <a href="javascript:void(0)" class="d-inline-block text-muted ml-3">
                                                    <strong>12</strong> <small class="align-middle">
                                                        <a href=""></a>
                                                    </small>
                                                </a>
                                                <a href="javascript:void(0)" class="d-inline-block text-muted ml-3">
                                                    <small class="align-middle">Repost</small>
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        <?php
                        endforeach;
                    else :
                        ?>
                        <tr>
                            <td>
                                <div class="alert alert-danger text-center">None of reply</div>
                            </td>
                        </tr>
                    <?php

                    endif;
                    ?>

                </div>
                <!-- /Forum Detail -->


                <!-- /Inner main body -->
            </div>
            <!-- /Inner main -->
        </div>

        <!-- New Question Modal -->
        <div class="modal fade" id="newReply" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" style="text-align: center;" id="exampleModalLabel">New reply</h5>

                    </div>
                    <div class="modal-body">
                        <form method="post" enctype="multipart/form-data">
                            <!-- <div class="form-group">
                                <label class="col-form-label">Title</label>
                                <input name="title" type="text" class="form-control">
                            </div> -->
                            <div class="form-group">
                                <label class="col-form-label">Content</label>
                                <input name="replyContent" type="text" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">Image</label>
                                <input name="replyImage" class="form-control" id="inputUsername" type="file" placeholder="Choose your image profile">

                            </div>

                            <input type="hidden" name='userIdEdit' value="<?php echo $userIdEdit; ?>">

                            <input type="hidden" name='postId' value="<?php echo $postId; ?>">
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