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
    $userIdEdit = $filterAll['userIdEdit'];
    $postId = $filterAll['postId'];
    setFlashData('userIdEdit', $userIdEdit);
    setFlashData('postId', $postId);
    // check whether exist in database
    //if exist => get info
    //if not exist => navigat to list page
    $listQuestion = getRaws("SELECT * FROM questions WHERE postId='$postId' ORDER BY update_at DESC");
    if (!empty($listQuestion)) {
        //exist
        setFlashData('listQuestion', $listQuestion);
    }
}

if (isPost()) {
    $filterAll = filter();
    if (!empty($filterAll['title']) && !empty($filterAll['content']) && !empty($filterAll['content']) ) {
        if (getSession('loginToken')) {

            $loginToken = getSession('loginToken');
            $queyToken = getRaw("SELECT userId FROM tokenlogin WHERE token = '$loginToken'");
            $userId = $queyToken['userId'];
            $userIdEdit = $filterAll['userIdEdit'];
            $postId = $filterAll['postId'];
            //handle Image
            $target_dir = './templates/img/imgQuestion/';
            $questionImage = $target_dir .$_FILES["questionImage"]["name"];
            move_uploaded_file($_FILES["questionImage"]["tmp_name"], $questionImage);
            $dataInsert = [

                'title' => $filterAll['title'],
                'content' => $filterAll['content'],
                'update_at' => date('Y:m:d H:i:s'),
                'postId' => $filterAll['postId'],
                'userId' => $userId,
                'questionImage' => $questionImage

            ];
            $insertStatus = insert('questions', $dataInsert);
            if ($insertStatus) {

                setFlashData('smg', 'A new question was just uploaded!'. $_FILES["questionImage"]["name"]);
                setFlashData('smg_type', 'success');
            } else {
                setFlashData('smg', 'System faces errors! Please try again.');
                setFlashData('smg_type', 'danger');
            }
            reDirect("?module=home&action=post&postId=" . $postId . "&userIdEdit=" . $userIdEdit);
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
$listQuestion = getFlashData('listQuestion');
$postId = getFlashData('postId');
$userIdEdit = getFlashData('userIdEdit');
if (!empty($listQuestion)) {
    $old = $listQuestion;

    // echo '<prev>';
    // print_r($old);
    // echo '</prev>';
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
                    <button type="button" class="mg-btn medium rounded " style="margin: 0 25%;" data-toggle="modal" data-target="#newQuestionModel">
                        New question <i class="fa-solid fa-plus"></i>
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
                                                <a href="javascript:void(0)" class="nav-link nav-link-faded has-icon active">All Questions</a>
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


                <!-- list questions -->
                <div id='postCollapse' class="inner-main-body p-2 p-sm-3 forum-content collapse">
                    <a href="<?php echo _WEB_HOST; ?>/?module=home&action=forum" class="btn btn-light btn-sm mb-3 has-icon " data-target=".forum-content"><i class="fa-solid fa-backward"></i></a>
                    <div class="container posts-content" style="position: relative;">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="card mb-4">
                                            <div class="card-body">
                                                <div class="media mb-3">
                                                    <img src="<?php echo $userDetail['profileImage'] ?>" class="d-block ui-w-40 rounded-circle" alt="">
                                                    <div class="media-body ml-3" style="position: absolute; left: 66px; top: 11px;">
                                                        <?php echo $userDetail['fullname'] ?>
                                                        <div class="text-muted small">Latest: <?php echo $item['update_at'] != 'NULL' ? $item['create_at'] : $item['update_at']; ?></div>
                                                    </div>
                                                </div>
                                                <div style="position: absolute; right: 14px; top: 13px;">

                                                    <a style="padding: 6px 7px;" href="<?php echo _WEB_HOST; ?>/?module=home&action=editPost&postId=<?php echo $item['id'] ?>&userIdEdit=<?php echo $item['userId'] ?>" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen-to-square"></i></a>
                                                    <a style="padding: 6px 7px;" href="<?php echo _WEB_HOST; ?>/?module=home&action=deleteQuestion&questionId=<?php echo $item['id'] ?>&userIdDelete=<?php echo $item['userId'] ?>&postId=<?php echo $item['postId'] ?>" onclick="return confirm('Delete this post?')" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></a>
                                                </div>
                                                <h5 style="margin: 0;"><a href="<?php echo _WEB_HOST; ?>/?module=home&action=question&postId=<?php echo $item['id'] ?>&userIdEdit=<?php echo $item['userId'] ?>&questionId=<?php echo $item['id'] ?>" class="text-body"><?php echo $item['title'] ?></a></h5>
                                                <p>
                                                    <?php echo $item['content'] ?>
                                                </p>
                                                <?php echo $item['questionImage'] ? '<a href="javascript:void(0)" class="ui-rect ui-bg-cover" style="background-image: url(' . $item['questionImage'] . ');"></a>' :  null ?>

                                            </div>
                                            <div class="card-footer">
                                                <a href="javascript:void(0)" class="d-inline-block text-muted">
                                                    <strong>123</strong> <small class="align-middle">Likes</small>
                                                </a>
                                                <a href="javascript:void(0)" class="d-inline-block text-muted ml-3">
                                                    <strong>12</strong> <small class="align-middle">Comments</small>
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
                    if (!empty($listQuestion)) :
                        $count = 0;
                        foreach ($listQuestion as $item) :
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
                                                    <img src="<?php echo $userDetail['profileImage'] ?>" class="d-block ui-w-40 rounded-circle" alt="">
                                                    <div class="media-body ml-3" style="position: absolute; left: 66px; top: 11px;">
                                                        <?php echo $userDetail['fullname'] ?>
                                                        <div class="text-muted small">Latest: <?php echo $item['update_at'] != 'NULL' ? $item['create_at'] : $item['update_at']; ?></div>
                                                    </div>
                                                </div>
                                                <div style="position: absolute; right: 14px; top: 13px;">

                                                    <a style="padding: 6px 7px;" href="<?php echo _WEB_HOST; ?>/?module=home&action=editPost&postId=<?php echo $item['id'] ?>&userIdEdit=<?php echo $item['userId'] ?>" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen-to-square"></i></a>
                                                    <a style="padding: 6px 7px;" href="<?php echo _WEB_HOST; ?>/?module=home&action=deleteQuestion&questionId=<?php echo $item['id'] ?>&userIdDelete=<?php echo $item['userId'] ?>&postId=<?php echo $item['postId'] ?>" onclick="return confirm('Delete this post?')" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></a>
                                                </div>
                                                <h5 style="margin: 0;"><a href="<?php echo _WEB_HOST; ?>/?module=home&action=question&postId=<?php echo $item['id'] ?>&userIdEdit=<?php echo $item['userId'] ?>&questionId=<?php echo $item['id'] ?>" class="text-body"><?php echo $item['title'] ?></a></h5>
                                                <p>
                                                    <?php echo $item['content'] ?>
                                                </p>
                                                <?php echo $item['questionImage'] ? '<a href="javascript:void(0)" class="ui-rect ui-bg-cover" style="background-image: url(' . $item['questionImage'] . ');"></a>' :  null ?>

                                            </div>
                                            <div class="card-footer">
                                                <a href="javascript:void(0)" class="d-inline-block text-muted">
                                                    <strong>123</strong> <small class="align-middle">Likes</small>
                                                </a>
                                                <a href="javascript:void(0)" class="d-inline-block text-muted ml-3">
                                                    <strong>12</strong> <small class="align-middle">Comments</small>
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
                                <div class="alert alert-danger text-center">None of question</div>
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
        <div class="modal fade" id="newQuestionModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" style="text-align: center;" id="exampleModalLabel">New question</h5>

                    </div>
                    <div class="modal-body">
                        <form method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label class="col-form-label">Title:</label>
                                <input name="title" type="text" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">Content</label>
                                <input name="content" type="text" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">Image</label>
                                <input name="questionImage" class="form-control" id="inputUsername" type="file" placeholder="Choose your image profile">

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