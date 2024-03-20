<?php
if (!defined('_CODE')) {
    die('Access denied...');
}
$data = [
    'titlePage' => 'Home'
];


if (!checkLogin()) {
    reDirect('?module=auth&page=login');
}
// echo $result;
$filterAll = filter();


if (!empty($filterAll['userIdEdit']) && !empty($filterAll['postId']) && !empty($filterAll['questionId']) && !empty($filterAll['userIdPost'])) {
    $userIdEdit = $filterAll['userIdEdit'];
    $postId = $filterAll['postId'];
    $questionId = $filterAll['questionId'];
    $userIdPost  = $filterAll['userIdPost'];

    

    // check whether exist in database
    //if exist => get info
    //if not exist => navigat to list page
    $questionDetail = getRaw("SELECT * FROM questions WHERE id = '$questionId'");
    $userEditDetail = getRaw("SELECT * FROM users WHERE id = '$userIdEdit'");
    $listReply = getRaws("SELECT * FROM replies WHERE questionId = '$questionId' ORDER BY update_at DESC");

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

            if (!empty($_FILES["replyImage"]['name'])) {

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
            } else {
                $dataInsert = [

                    'replyContent' => $filterAll['replyContent'],
                    'update_at' => date('Y:m:d H:i:s'),
                    'questionId' => $questionId,
                    'userId' => $userIdLogin,
                    'replyImage' => null

                ];
            }
            $insertStatus = insert('replies', $dataInsert);
            if ($insertStatus) {

                setFlashData('smg', 'A new reply was just uploaded!');
                setFlashData('smg_type', 'success');
            } else {
                setFlashData('smg', 'System faces errors! Please try again.');
                setFlashData('smg_type', 'danger');
            }
            reDirect("?module=home&page=reply/question&questionId=" . $questionId . "&postId=" . $postId . "&userIdEdit=" . $userIdEdit . "&userIdPost=" . $userIdPost);
        }
    }
}
$errors = getFlashData('errors');
// print_r($errors);
$smg = getFlashData('smg');
$smgType = getFlashData(('smg_type'));
$old = getFlashData('old');

$questionDetail = getFlashData('questionDetail');
$questionId = $questionDetail['id'];
$countReply = countRow("SELECT id FROM replies WHERE questionId='$questionId'");
$userPostDetail = getRaw("SELECT * FROM users WHERE id='$userIdPost'");

if (!empty($listReply)) {
    $old = $listReply;
}
layouts('headerPost', $data);
?>



<div class="container" style=" margin-bottom: 100px">
    <div class="main-body p-0">
        <div class="inner-wrapper">
            <!-- Inner sidebar -->
            <div class="inner-sidebar">
                <!-- Inner sidebar header -->
                <div class="inner-sidebar-header justify-content-center">
                    <!-- Button trigger modal -->
                    <button type="button" class="mg-btn medium rounded " style="margin: 0 25%;" >
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
                <button id="myBtn" title="Go to top" style="border-radius: 50%;"><i class="fa-solid fa-arrow-up"></i></button>


                <!-- Questions -->
                <div class="inner-main-body p-2 p-sm-3 forum-content collapse show" id='listReply'>
                    <a href="<?php echo _WEB_HOST; ?>/?module=home&page=question/post&postId=<?php echo $postId; ?>&userIdEdit=<?php echo $userIdPost; ?>" class="btn btn-light btn-sm has-icon " data-target=".forum-content"><i class="fa-solid fa-backward"></i></a>
                    <div class="container posts-content" style="position: relative;">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <div style="margin-bottom: 6px;">
                                            <h6 style="margin: 0; position: absolute; right: 48%;top: 14px; font-weight: 300;">Question</h6>
                                            <a href="?module=user&page=profile/profileView&userId=<?php echo $userIdEdit ?>">
                                            <img src="<?php echo !empty($userPostDetail['profileImage']) ? $userPostDetail['profileImage'] : "https://img.freepik.com/free-vector/businessman-character-avatar-isolated_24877-60111.jpg?w=826&t=st=1710127291~exp=1710127891~hmac=10efc92f9bddd8afe06fa86d74c0caf109f33b79794fd0fc982a01c8bff70758"; ?>" class="mr-3 rounded-circle" width="50">

                                            </a>
                                            <div class="media-body ml-3" style="position: absolute; left: 72px; top: 14px;">
                                                <h6 style="margin: 0 ;padding: 0; font-size: 16px">
                                                    <a style="color: black;" href="?module=user&page=profile/profileView&userId=<?php echo $userIdEdit ?>">

                                                        <?php echo $userEditDetail['fullname'] ?>
                                                    </a>
                                                </h6>
                                                <div class="text-muted small" style=" margin: 2px 0; font-size: 12px; font-weight: 300;line-height: 12px;"><?php echo formatTimeDifference($questionDetail['update_at']); ?></div>
                                            </div>
                                        </div>
                                        <div style="position: absolute; right: 13px; top: 13px;" class="dropdown show">
                                            <a href="#" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i style="color:black;" class="fa-solid fa-ellipsis icon-hover"></i>
                                            </a>

                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                <a class="dropdown-item" style="padding: 6px 7px;" href="<?php echo _WEB_HOST; ?>/?module=home&page=reply/editQuestionInReplyPage&questionId=<?php echo $questionDetail['id'] ?>&userIdEdit=<?php echo $questionDetail['userId'] ?>&userIdPost=<?php echo $userIdPost ?>&postId=<?php echo $postId ?>" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen-to-square"></i> Edit question</a>
                                                <a class="dropdown-item" style="padding: 6px 7px;" href="<?php echo _WEB_HOST; ?>/?module=home&page=reply/deleteQuestionInReplyPage&questionId=<?php echo $questionDetail['id'] ?>&userIdDelete=<?php echo $questionDetail['userId'] ?>&postId=<?php echo $questionDetail['postId'] ?>" onclick="return confirm('Delete this post?')" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i> Delete question</a>
                                            </div>
                                        </div>
                                        <div style="position: absolute; right: 12px; bottom: 44px;">
                                                <?php echo $countReply == 0 ? null : '<a  style="font-size: 14px;font-weight: 400;color: black;">' . $countReply . ' comments</a>'; ?>
                                        </div>
                                        <h5 style="margin: 0;"><a href="" class="text-body"><?php echo $questionDetail['title'] ?></a></h5>
                                        <p>
                                            <?php echo $questionDetail['content'] ?>
                                        </p>
                                        <div class="text-center">

                                            <?php echo !empty($questionDetail['questionImage']) ? '<img style="padding-bottom: 10px" src=' . $questionDetail['questionImage'] . ' class="img-fluid " alt="Responsive image" >' :  null ?>
                                        </div>
                                    </div>
                                    <div class="card-footer" style="display: flex; justify-content: space-evenly;">
                                        <a href="javascript:void(0)" class="d-inline-block text-muted">
                                            <i class="fa-regular fa-thumbs-up icon-hover" style="font-size: 26px;"></i>

                                        </a>
                                        <a style="position: relative;" href="<?php echo _WEB_HOST; ?>/?module=home&page=reply/question&questionId=<?php echo $item['id'] ?>&postId=<?php echo $item['postId'] ?>&postId=<?php echo $item['userId'] ?>&userIdPost=<?php echo $userIdPost ?>" class="d-inline-block text-muted ml-3">

                                            <i class="fa-regular fa-comment icon-hover active" style="font-size: 26px;"></i>
                                        </a>
                                        <a href="javascript:void(0)" class="d-inline-block text-muted ml-3">
                                            <i class="fa-solid fa-share icon-hover" style="font-size: 26px;"></i>
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
                                    <div class="col-lg-2">

                                    </div>
                                    <div class="col-lg-8">
                                        <div class="card mb-4">
                                            <div class="card-body">
                                                <div class="media mb-3">
                                                    <h6 style="margin: 0; position: absolute; right: 49.5%;top: 14px; font-weight: 300;">Reply</h6>
                                                    <a href="?module=user&page=profile/profileView&userId=<?php echo $userId ?>">

                                                    <img src="<?php echo !empty($userDetail['profileImage']) ? $userDetail['profileImage'] : "https://img.freepik.com/free-vector/businessman-character-avatar-isolated_24877-60111.jpg?w=826&t=st=1710127291~exp=1710127891~hmac=10efc92f9bddd8afe06fa86d74c0caf109f33b79794fd0fc982a01c8bff70758"; ?>" class="d-block ui-w-40 rounded-circle" >

                                                    </a>

                                                    <div class="media-body ml-3" style="position: absolute; left: 66px; top: 11px;">
                                                        <h6 style="margin: 0 ;padding: 0; font-size: 16px">
                                                            <a style="color:black;" href="?module=user&page=profile/profileView&userId=<?php echo $userId ?>">
                                                                <?php echo $userDetail['fullname'] ?>
                                                            </a>
                                                        </h6>
                                                        <div class="text-muted small" style="margin: 2px 0; font-size: 12px; font-weight: 300;line-height: 12px;"><?php echo formatTimeDifference($item['update_at']); ?></div>
                                                    </div>
                                                </div>
                                                <div style="position: absolute; right: 13px; top: 13px;" class="dropdown show">
                                                    <a href="#" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i style="color:black;" class="fa-solid fa-ellipsis icon-hover"></i>
                                                    </a>

                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                    <a class="dropdown-item" style="padding: 6px 7px;" href="<?php echo _WEB_HOST; ?>/?module=home&page=reply/editReply&replyId=<?php echo $item['id'] ?>&userIdEdit=<?php echo $item['userId'] ?>&postId=<?php echo $postId ?>&questionId=<?php echo $item['questionId'] ?>&userIdPost=<?php echo $userIdPost ?>" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen-to-square"></i> Edit reply</a>
                                                    <a class="dropdown-item" style="padding: 6px 7px;" href="<?php echo _WEB_HOST; ?>/?module=home&page=reply/deleteReply&replyId=<?php echo $item['id'] ?>&userIdReply=<?php echo $item['userId'] ?>&postId=<?php echo $postId ?>&questionId=<?php echo $item['questionId'] ?>" onclick="return confirm('Delete this reply?')" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i> Delete reply</a>
                                                    </div>
                                                </div>
                                                <p>
                                                    <?php echo $item['replyContent'] ?>
                                                </p>
                                                <div class="text-center">

                                                    <?php echo !empty($item['replyImage']) ? '<img src=' . $item['replyImage'] . ' class="img-fluid " alt="Responsive image" width: "200" height: "200" >'  :  null ?>
                                                </div>



                                            </div>
                                            <div class="card-footer" style="display: flex; justify-content: space-evenly;">
                                                <a href="javascript:void(0)" class="d-inline-block text-muted">
                                                    <i class="fa-regular fa-thumbs-up icon-hover" style="font-size: 26px;"></i>

                                                </a>
                                                <div></div>
                                                <a href="javascript:void(0)" class="d-inline-block text-muted ml-3">
                                                    <i class="fa-solid fa-share icon-hover" style="font-size: 26px;"></i>
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
        <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                <label class="col-form-label" required="required">Content</label>
                                <input name="replyContent" type="text" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">Image</label>
                                <input name="replyImage" class="form-control" id="inputUsername" type="file">

                            </div>

                            <input id="userIdEdit" type="hidden" name='userIdEdit' value="<?php echo $userIdEdit; ?>">
                            <input id="userIdPost" type="hidden" name='userIdPost' value="<?php echo $userIdPost; ?>">
                            <input id="questionId" type="hidden" name='questionId' value="<?php echo $questionId; ?>">

                            <input id="postId" type="hidden" name='postId' value="<?php echo $postId; ?>">
                            <div class="modal-footer">
                            </div>
                            <button type="button" class="mg-btn small rounded">
                                <a style="padding: 12px 84px"  href="?module=home&page=reply/question&questionId=<?php echo $questionId;?>&postId=<?php echo $postId;?>&userIdEdit=<?php echo $userIdEdit;?>&userIdPost=<?php echo $userIdPost;?>">Back</a>
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
    // handle modal
    var myModal = new bootstrap.Modal(document.getElementById('addModal'), {})
    myModal.show()

    document.getElementById('addModal').onclick = function(e) {
        console.log(e.target.className);
        if(e.target.className === "modal fade") {
            window.location.href = "?module=home&page=reply/question&questionId=" + document.getElementById('questionId').value + "&postId=" + document.getElementById('postId').value + "&userIdEdit=" + document.getElementById('userIdEdit').value + "&userIdPost=" + document.getElementById('userIdPost').value ;
        }
    }
</script>
<script>
    // Get the button:
    let mybutton = document.getElementById("myBtn");
    let listReply = document.getElementById("listReply");
    console.log(listReply)
    // When the user scrolls down 20px from the top of the document, show the button
    listReply.onscroll = function() {
        scrollFunction()
    };
    mybutton.onclick = function() {
        topFunction()
    };
    window.onscroll = function() {
        handleScrollWindow()
    }

    function scrollFunction() {
        console.log(document.body.scrollTop);
        if (listReply.scrollTop > 100 && window.scrollY < 300) {
            mybutton.style.display = "block";
        } else {
            mybutton.style.display = "none";
        }
    }
    function handleScrollWindow() {
    if(listReply.scrollTop > 20 && window.scrollY>100) {
        mybutton.style.display = "none";
    } else if (listReply.scrollTop > 20 && window.scrollY<100){
        mybutton.style.display = "block";
        
    }
}
    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
        listReply.scrollTop = 0; // For Safari
        listReply.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    }
</script>


<?php
layouts('footerIn')
?>