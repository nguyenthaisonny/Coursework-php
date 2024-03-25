<?php
if (!defined('_CODE')) {
    die('Access denied...');
}
$data = [
    'titlePage' => 'Question'
];


if (!checkLogin()) {
    reDirect('?module=auth&page=login');
}
// echo $result;
if (isGet()) {

    $filterAll = filter();


    if (!empty($filterAll['postId'])) {
        $postId = $filterAll['postId'];
        $userIdPost = getRaw("SELECT userId FROM posts WHERE id = '$postId'")['userId'];


        $listQuestion = getRaws("SELECT * FROM questions WHERE postId='$postId' ORDER BY updateAt DESC");
        if (!empty($_GET['type'])) {
            $newListQuestion = [];
            switch ($_GET['type']) {
                case 'oldest':

                    $newListQuestion = getRaws("SELECT * FROM questions  WHERE postId='$postId' ORDER BY updateAt");
                    break;

                case 'popular':
                    foreach ($listQuestion as $item) {
                        $questionId = $item['id'];
                        $replyCount = countRow("SELECT id FROM replies WHERE questionId = '$questionId'");

                        if ($replyCount >= 6) {
                            array_push($newListQuestion, $item);
                        }
                    }
                    break;
                case 'noReplyYet':
                    foreach ($listQuestion as $item) {
                        $questionId = $item['id'];

                        $loginToken = getSession('loginToken');
                        $queryToken = getRaw("SELECT userId FROM tokenlogin WHERE token = '$loginToken'");
                        $userIdLogin = $queryToken['userId'];
                        $countUserLoginReply = countRow("SELECT id FROM replies WHERE questionId='$questionId' AND userId='$userIdLogin'");

                        if ($countUserLoginReply == 0) {
                            array_push($newListQuestion, $item);
                        }
                    }
                    break;
                case 'noneReply':
                    foreach ($listQuestion as $item) {
                        $questionId = $item['id'];
                        $replyCount = countRow("SELECT id FROM replies WHERE questionId = '$questionId'");

                        if ($replyCount == 0) {
                            array_push($newListQuestion, $item);
                        }
                    }
                    break;
            }
        } else {
            $newListQuestion = $listQuestion;
        }
    }
}

if (isPost()) {
    $filterAll = filter();
    if (!empty($filterAll['title']) || !empty($filterAll['content'])) {
        if (getSession('loginToken')) {

            $loginToken = getSession('loginToken');
            $queyToken = getRaw("SELECT userId FROM tokenlogin WHERE token = '$loginToken'");
            $userId = $queyToken['userId'];
            $userIdEdit = $filterAll['userIdEdit'];
            $postId = $filterAll['postId'];
            //handle Image
            if (!empty($_FILES["questionImage"]['name'])) {

                $target_dir = './templates/img/imgQuestion/';
                $questionImage = $target_dir . $_FILES["questionImage"]["name"];
                move_uploaded_file($_FILES["questionImage"]["tmp_name"], $questionImage);
                $dataInsert = [

                    'title' => $filterAll['title'],
                    'content' => $filterAll['content'],
                    'updateAt' => date('Y:m:d H:i:s'),
                    'postId' => $filterAll['postId'],
                    'userId' => $userId,
                    'questionImage' => $questionImage

                ];
            } else {
                $dataInsert = [

                    'title' => $filterAll['title'],
                    'content' => $filterAll['content'],
                    'updateAt' => date('Y:m:d H:i:s'),
                    'postId' => $filterAll['postId'],
                    'userId' => $userId,
                    'questionImage' => null

                ];
            }
            $insertStatus = insert('questions', $dataInsert);
            if ($insertStatus) {

                setFlashData('smg', 'A new question was just uploaded!' . $_FILES["questionImage"]["name"]);
                setFlashData('smg_type', 'success');
            } else {
                setFlashData('smg', 'System faces errors! Please try again.');
                setFlashData('smg_type', 'danger');
            }
            reDirect("?module=home&page=question/post&postId=" . $postId . "&userIdEdit=" . $userIdEdit);
        }
    }
}

$errors = getFlashData('errors');
// print_r($errors);
$smg = getFlashData('smg');
$smgType = getFlashData(('smg_type'));



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

                    <button type="button" class="mg-btn medium rounded " style="margin: 0 25%; ">
                        <a style="padding: 12px 38px;" href="?module=home&page=question/addQuestion&postId=<?php echo $postId; ?><?php echo !empty($_GET['type']) ? '&type=' . $_GET['type'] : '' ?>">

                            New question <i class="fa-solid fa-plus"></i>
                        </a>
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
                                                <a id='latest' href="javascript:void(0)" class="nav-link nav-link-faded has-icon <?php echo empty($_GET['type']) ? 'active' : '' ?>">Latest</a>
                                                <a id="oldest" href="javascript:void(0)" class="nav-link nav-link-faded has-icon <?php echo (!empty($_GET['type']) && $_GET['type'] == 'oldest') ? 'active' : '' ?>">Oldest</a>
                                                <a id="popular" href="javascript:void(0)" class="nav-link nav-link-faded has-icon <?php echo (!empty($_GET['type']) && $_GET['type'] == 'popular') ? 'active' : '' ?>">Popular</a>
                                                <a id="noneReply" href="javascript:void(0)" class="nav-link nav-link-faded has-icon <?php echo (!empty($_GET['type']) && $_GET['type'] == 'noneReply') ? 'active' : '' ?>">None of reply</a>

                                                <a id="noReplyYet" href="javascript:void(0)" class="nav-link nav-link-faded has-icon <?php echo (!empty($_GET['type']) && $_GET['type'] == 'noReplyYet') ? 'active' : '' ?>">No replies yet</a>



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

                   

                </div>
                <?php
                if (!empty($smg)) {
                    getSmg($smg, $smgType);
                }
                ?>


                <!-- list questions -->
                <div class="inner-main-body p-2 p-sm-3 forum-content collapse show" id="listQuestion">
                    <button id="myBtn" title="Go to top" style="border-radius: 50%; right: 168px"><i class="fa-solid fa-arrow-up"></i></button>
                    <a style="margin-bottom: 16px" href="<?php echo _WEB_HOST; ?>/?module=home&page=forum/forum" class="btn btn-light btn-sm has-icon " data-target=".forum-content"><i class="fa-solid fa-backward"></i></a>
                    <div class="row">

                        <?php
                        if (!empty($newListQuestion)) :
                            $count = 0;

                            foreach ($newListQuestion as $item) :
                                $userId = $item['userId'];
                                $questionId = $item['id'];
                                $countReply = countRow("SELECT id FROM replies WHERE questionId='$questionId'");
                                $userDetail = getRaw("SELECT fullname, email, profileImage FROM users WHERE id='$userId' ");

                                $count++;

                        ?>



                                <div class="col-lg-6 mb-4">

                                    <div class="card ">
                                        <div class="card-body">
                                            <div style="margin-bottom: 6px;">
                                                <a href="?module=user&page=profile/profileView&userId=<?php echo $userId ?>">

                                                    <img src="<?php echo !empty($userDetail['profileImage']) ? $userDetail['profileImage'] : "https://img.freepik.com/free-vector/businessman-character-avatar-isolated_24877-60111.jpg?w=826&t=st=1710127291~exp=1710127891~hmac=10efc92f9bddd8afe06fa86d74c0caf109f33b79794fd0fc982a01c8bff70758"; ?>" class="mr-3 rounded-circle" width="50">
                                                </a>
                                                <div class="media-body ml-3" style="position: absolute; left: 72px; top: 14px;">
                                                    <div style="display: flex; align-items: flex-start;">
                                                        <div style="padding: 0 6px;">
                                                            <h6 style="margin: 0 ;padding: 0; font-size: 18px;font-weight:600;">
                                                                <a style="color: black;" href="?module=user&page=profile/profileView&userId=<?php echo $userId ?>">

                                                                    <?php echo $userDetail['fullname'] ?>
                                                                </a>
                                                            </h6>
                                                            <div class="text-muted small" style="margin: 2px 0; font-size: 12px; font-weight: 300;line-height: 12px;"><?php echo formatTimeDifference($item['updateAt']); ?></div>
                                                        </div>

                                                        <?php echo checkAdminInList($userId) ? '<span style="color: #20D5EC; font-size: 16px;"><i class="fa-solid fa-circle-check"></i></span>' : null; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div style="position: absolute; right: 13px; top: 13px;" class="dropdown show">
                                                <a href="#" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i style="color:black;" class="fa-solid fa-ellipsis icon-hover"></i>
                                                </a>

                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                    <a class="dropdown-item" href="<?php echo _WEB_HOST; ?>/?module=home&page=question/editQuestion&questionId=<?php echo $item['id'] ?>&postId=<?php echo $item['postId'] ?><?php echo !empty($_GET['type']) ? '&type=' . $_GET['type'] : '' ?>" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen-to-square"></i> Edit question</a>
                                                    <a class="dropdown-item" href="<?php echo _WEB_HOST; ?>/?module=home&page=question/deleteQuestion&questionId=<?php echo $item['id'] ?>&userIdDelete=<?php echo $item['userId'] ?>&postId=<?php echo $item['postId'] ?>" onclick="return confirm('Delete this question ?')" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i> Delete question</a>
                                                </div>
                                            </div>

                                            <div style="position: absolute; right: 12px; bottom: 44px;">
                                                <?php
                                                if ($countReply >= 0) {
                                                    if ($countReply == 0) {
                                                        echo null;
                                                    } else if ($countReply == 1) {
                                                        echo '<a class="hover-item" href="?module=home&page=reply/question&questionId=' . $item['id'] . '&postId=' . $item['postId'] . '" style="font-size: 14px;font-weight: 400;">' . $countReply . ' reply</a>';
                                                    } else {
                                                        echo '<a class="hover-item" href="?module=home&page=reply/question&questionId=' . $item['id'] . '&postId=' . $item['postId'] . '" style="font-size: 14px;font-weight: 400;">' . $countReply . ' replies</a>';
                                                    }
                                                }
                                                ?>


                                            </div>
                                            <h5 style="margin: 0;"><a href="<?php echo _WEB_HOST; ?>/?module=home&page=reply/question&questionId=<?php echo $item['id'] ?>&postId=<?php echo $item['postId'] ?>" class="text-body"><?php echo $item['title'] ?></a></h5>
                                            <p>
                                                <?php echo $item['content'] ?>
                                            </p>
                                            <div class="text-center">

                                                <?php echo !empty($item['questionImage']) ? '<a href="?module=home&page=reply/question&questionId=' . $item['id'] . '&postId=' . $item['postId'] . '" ><img style="padding-bottom: 10px" src=' . $item['questionImage'] . '  class="img-fluid" alt="Responsive image" ></a>' :  null ?>
                                            </div>


                                        </div>
                                        <div class="card-footer" style="display: flex; justify-content: space-evenly;">
                                            <a href="javascript:void(0)" class="d-inline-block text-muted">
                                                <i class="fa-regular fa-thumbs-up icon-hover" style="font-size: 26px;"></i>

                                            </a>
                                            <a style="position: relative;" href="<?php echo _WEB_HOST; ?>/?module=home&page=reply/question&questionId=<?php echo $item['id'] ?>&postId=<?php echo $item['postId'] ?>" class="d-inline-block text-muted ml-3">

                                                <i class="fa-regular fa-comment icon-hover" style="font-size: 26px;"></i>
                                            </a>
                                            <a href="javascript:void(0)" class="d-inline-block text-muted ml-3">
                                                <i class="fa-solid fa-share icon-hover" style="font-size: 26px;"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>


                            <?php

                            endforeach; ?>
                    </div>
                <?php
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
                                <input name="title" type="text" class="form-control" required="required">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">Content</label>
                                <input name="content" type="text" class="form-control" required="required">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">Image</label>
                                <input name="questionImage" class="form-control" id="inputUsername" type="file" placeholder="Choose your image profile">

                            </div>

                            <input type="hidden" name='userIdEdit' value="<?php echo $userIdEdit; ?>">

                            <input id='postId' type="hidden" name='postId' value="<?php echo $postId; ?>">
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
    //handle click sort case
    const latest = document.getElementById('latest');

    latest.onclick = function(e) {

        const urlParams = new URLSearchParams('?module=home&page=question/post&postId=' + document.getElementById('postId').value);



        window.location.search = urlParams;



    }
    const oldest = document.getElementById('oldest');

    oldest.onclick = function(e) {

        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('type', 'oldest');
        window.location.search = urlParams;



    }

    const popular = document.getElementById('popular');

    popular.onclick = function(e) {


        const urlParams = new URLSearchParams(window.location.search);

        urlParams.set('type', 'popular');
        window.location.search = urlParams;



    }
    const noReplyYet = document.getElementById('noReplyYet');

    noReplyYet.onclick = function(e) {


        const urlParams = new URLSearchParams(window.location.search);

        urlParams.set('type', 'noReplyYet');
        window.location.search = urlParams;



    }
    const noneReply = document.getElementById('noneReply');

    noneReply.onclick = function(e) {


        const urlParams = new URLSearchParams(window.location.search);

        urlParams.set('type', 'noneReply');
        window.location.search = urlParams;



    }
    // Get the button:
    let mybutton = document.getElementById("myBtn");
    let listQuestion = document.getElementById("listQuestion");
    console.log(listQuestion)
    // When the user scrolls down 20px from the top of the document, show the button
    listQuestion.onscroll = function() {
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
        if (listQuestion.scrollTop > 100 && window.scrollY < 300) {
            mybutton.style.display = "block";
        } else {
            mybutton.style.display = "none";
        }
    }

    function handleScrollWindow() {
        if (listQuestion.scrollTop > 20 && window.scrollY > 100) {
            mybutton.style.display = "none";
        } else if (listQuestion.scrollTop > 20 && window.scrollY < 100) {
            mybutton.style.display = "block";

        }
    }
    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
        listQuestion.scrollTo({
            top: 0,
            behavior: 'smooth'
        }) // For Safari
        listQuestion.documentElement.scrollTo({
            top: 0,
            behavior: 'smooth'
        }); // For Chrome, Firefox, IE and Opera
    }
</script>



<?php
layouts('footerIn')
?>