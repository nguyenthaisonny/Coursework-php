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
$filterAll = filter();
if (!empty($filterAll['userIdEdit']) && !empty($filterAll['postId'])) {
    $postId = $filterAll['postId'];
    $userIdEdit = $filterAll['userIdEdit'];
    setFlashData('userIdEdit', $userIdEdit);
    setFlashData('postId', $postId);
    // check whether exist in database
    //if exist => get info
    //if not exist => navigat to list page
    $postDetail = getRaw("SELECT postName, description FROM posts WHERE id = '$postId'");
    setFlashData('postDetail', $postDetail);
}


if (isPost()) {
    $filterAll = filter();

    if (!empty($filterAll['postName']) || !empty($filterAll['description'])) {
        if (getSession('loginToken')) {
            $postId = $_GET['postId'];
            $loginToken = getSession('loginToken');
            $queryToken = getRaw("SELECT userId, id FROM tokenlogin WHERE token = '$loginToken'");
            $userIdLogin = $queryToken['userId'];
            $userIdEdit = $_GET['userIdEdit'];

            $dataUpdate = [
                'postName' => $filterAll['postName'],
                'description' => $filterAll['description'],
                'update_at' => date('Y:m:d H:i:s'),

            ];
            if (($userIdLogin == $userIdEdit) || checkAdminNotSignOut()) {

                $updateStatus = update('posts', $dataUpdate, "id='$postId'");
                if ($updateStatus) {

                    setFlashData('smg', 'This post was just updated');
                    setFlashData('smg_type', 'success');
                } else {
                    setFlashData('smg', 'System faces errors! Please try again.');
                    setFlashData('smg_type', 'danger');
                }
            } else {
                setFlashData('smg', 'Error! Can not edit post of another user.');
                setFlashData('smg_type', 'danger');
            }
        }
        reDirect('?module=home&action=forum');
    }
}
$listPost = getRaws("SELECT * FROM posts ORDER BY update_at DESC");


$smg = getFlashData('smg');
$smgType = getFlashData('smg_type');
$postId = getFlashData('postId');
$userIdEdit = getFlashData('userIdEdit');
$postDetail = getFlashData('postDetail');
// print_r($postDetail);
if (!empty($postDetail)) {
    $old = $postDetail;

    // echo '<prev>';
    // print_r($old);
    // echo '</prev>';
}

layouts('headerEditPost', $data);
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
                        New post <i class="fa-solid fa-plus"></i>
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
                <div class="inner-main-body p-2 p-sm-3 collapse forum-content show">
                    <?php
                    if (!empty($listPost)) :
                        $count = 0;
                        foreach ($listPost as $item) :
                            $userId = $item['userId'];
                            $postId = $item['id'];
                            $questionCount = countRow("SELECT id FROM questions WHERE postId = '$postId'");
                            $userDetail = getRaw("SELECT fullname, email, profileImage FROM users WHERE id='$userId' ");
                            $count++;
                    ?>
                            <div class="card mb-2" style="position: relative;">

                                
                                <div class="card-body p-2 p-sm-3" style="display: flex;justify-content: space-between;">
                                    <div class="media forum-item">
                                        <div style="display: flex;align-items: flex-start;">

                                            <a href="?module=user&action=profileView&userId=<?php echo $userId?>"  ><img  src="<?php echo $userDetail['profileImage'] ?>" class="mr-3 rounded-circle" width="50" alt="User" /></a>
                                            <div style="padding-left: 6px;">

                                                <h6 style="margin: 0 ;padding: 0; font-size: 16px"><a  href="?module=user&action=profileView&userId=<?php echo $userId?>" class="text-body"><?php echo $userDetail['fullname'] ?></a></h6>
                                                <p style=" margin: 2px 0; font-size: 12px; font-weight: 300;line-height: 12px;">Latest: <?php echo $item['update_at'] != 'NULL' ? $item['create_at'] : $item['update_at']; ?></p>
                                            </div>

                                        </div>
                                        <div style="position: absolute; right: 13px; top: 13px;">

                                            <a href="<?php echo _WEB_HOST; ?>/?module=home&action=editPost&postId=<?php echo $item['id'] ?>&userIdEdit=<?php echo $item['userId'] ?>" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen-to-square"></i></a>
                                            <a href="<?php echo _WEB_HOST; ?>/?module=home&action=deletePost&postId=<?php echo $item['id'] ?>&userIdDelete=<?php echo $item['userId'] ?>" onclick="return confirm('Delete this post?')" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></a>
                                        </div>
                                        <div class="media-body" style="margin-top: 4px;">
                                            <h5 style="margin: 0;"><a href="<?php echo _WEB_HOST; ?>/?module=home&action=post&postId=<?php echo $item['id'] ?>&userIdEdit=<?php echo $item['userId'] ?>" class="text-body"><?php echo $item['postName'] ?></a></h5>
                                            <p style="margin-bottom: 20px;">
                                                <?php echo $item['description'] ?>
                                            </p>

                                        </div>
                                        <div >

                                            <a style="margin-right: 4px;" href="<?php echo _WEB_HOST; ?>/?module=home&action=post&postId=<?php echo $item['id'] ?>&userIdEdit=<?php echo $item['userId'] ?>" class="d-inline-block text-muted">
                                                <i class="fa-solid fa-door-open icon-hover"  style="font-size: 20px;"></i>

                                            </a>
                                            <span class="d-none d-sm-inline-block" style="font-size: 16px; font-weight: 300; line-height: 16px;"><?php echo $questionCount?> questions</span>
                                            
                                        </div>
                                    </div>
                                    <div class="text-muted small text-center align-self-center" style="display: flex; flex-direction: column; justify-content: space-between">


                                    </div>

                                </div>
                            </div>
                        <?php
                        endforeach;
                    else :
                        ?>
                        <tr>
                            <td>
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
                <div class="inner-main-body p-2 p-sm-3 collapse forum-content">
                    <a href="#" class="btn btn-light btn-sm has-icon" data-toggle="collapse" data-target=".forum-content"><i class="fa fa-arrow-left mr-2"></i>Back</a>
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="media forum-item">
                                <a href="javascript:void(0)" class="card-link">
                                    <img src="https://bootdey.com/img/Content/avatar/avatar1.png" class="rounded-circle" width="50" alt="User" />
                                    <small class="d-block text-center text-muted">Newbie</small>
                                </a>
                                <div class="media-body ml-3">
                                    <a href="javascript:void(0)" class="text-secondary">Mokrani</a>
                                    <small class="text-muted ml-2">1 hour ago</small>
                                    <h5 class="mt-1">Realtime fetching data</h5>
                                    <div class="mt-3 font-size-sm">
                                        <p>Hellooo :)</p>
                                        <p>
                                            I'm newbie with laravel and i want to fetch data from database in realtime for my forum anaytics and i found a solution with ajax but it dosen't work if any one have a simple solution it will be
                                            helpful
                                        </p>
                                        <p>Thank</p>
                                    </div>
                                </div>
                                <div class="text-muted small text-center">
                                    <span class="d-none d-sm-inline-block"><i class="far fa-eye"></i> 19</span>
                                    <span><i class="far fa-comment ml-2"></i> 3</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-2">
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

        <div class="overlay">
            <div class="overlay-content">

            </div>
        </div>
        <!-- Edit Thread Modal -->
        <div class="modal fade" id="editmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" style="text-align: center;" id="exampleModalLabel">Edit Post</h5>

                    </div>
                    <div class="modal-body">
                        <form method="post">
                            <div class="form-group">
                                <label class="col-form-label">Title</label>
                                <input name="postName" type="text" class="form-control" value="<?php echo  getOldValue($old, 'postName') ?>">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">Description</label>
                                <input name="description" type="text" class="form-control" value="<?php echo  getOldValue($old, 'description') ?>">
                            </div>


                            <div class="modal-footer">
                            </div>
                            <button type="button" class="mg-btn small rounded">
                                <a href="<?php echo _WEB_HOST; ?>/?module=home&action=forum">Back</a>
                            </button>
                            <button type="submit" class="mg-btn  primary" style="margin-left: 60px;">Upload</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>

<script>
    var myModal = new bootstrap.Modal(document.getElementById('editmodal'), {})
    myModal.show()
</script>
<?php
layouts('footerIn')
?>