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
if (isGet()) {

    $filterAll = filter();


    if (!empty($filterAll['postId'])) {
        $postId = $filterAll['postId'];
        $userIdPost = getRaw("SELECT userId FROM posts WHERE id = '$postId'")['userId'];

        // check whether exist in database
        //if exist => get info
        //if not exist => navigat to list page
        $listQuestion = getRaws("SELECT * FROM questions WHERE postId='$postId' ORDER BY update_at DESC");
        if (!empty($listQuestion)) {
            //exist
            setFlashData('listQuestion', $listQuestion);
        }
    }
}

if (isPost()) {
    $filterAll = filter();
    if (!empty($filterAll['title']) || !empty($filterAll['content'])) {
        if (getSession('loginToken')) {

            $loginToken = getSession('loginToken');
            $queryToken = getRaw("SELECT userId FROM tokenlogin WHERE token = '$loginToken'");
            $userIdLogin = $queryToken['userId'];

            $postId = $filterAll['postId'];
            //handle Image
            if (!empty($_FILES["questionImage"]['name'])) {

                $target_dir = './templates/img/imgQuestion/';
                $questionImage = $target_dir . $_FILES["questionImage"]["name"];
                move_uploaded_file($_FILES["questionImage"]["tmp_name"], $questionImage);
                $dataInsert = [

                    'title' => $filterAll['title'],
                    'content' => $filterAll['content'],
                    'update_at' => date('Y:m:d H:i:s'),
                    'postId' => $filterAll['postId'],
                    'userId' => $userIdLogin,
                    'questionImage' => $questionImage

                ];
            } else {
                $dataInsert = [

                    'title' => $filterAll['title'],
                    'content' => $filterAll['content'],
                    'update_at' => date('Y:m:d H:i:s'),
                    'postId' => $filterAll['postId'],
                    'userId' => $userIdLogin,
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
            reDirect("?module=home&page=question/post&postId=" . $postId);
        }
    }
}
$errors = getFlashData('errors');
// print_r($errors);
$smg = getFlashData('smg');
$smgType = getFlashData(('smg_type'));
$old = getFlashData('old');


if (!empty($listQuestion)) {
    $old = $listQuestion;

    // echo '<prev>';
    // print_r($old);
    // echo '</prev>';
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
                    <button class="mg-btn medium rounded " style="margin: 0 25%;">
                        <a href="?module=home&page=question/addQuestion&postId=<?php echo $postId; ?>&userIdEdit=<?php echo $userIdEdit ?>" style="padding: 0 39px;">

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


                <div class="inner-main-body p-2 p-sm-3 forum-content collapse show" id="listQuestion">
                    <button id="myBtn" title="Go to top" style="border-radius: 50%; right: 168px"><i class="fa-solid fa-arrow-up"></i></button>
                    <a style="margin-bottom: 16px" href="<?php echo _WEB_HOST; ?>/?module=home&page=forum/forum" class="btn btn-light btn-sm has-icon " data-target=".forum-content"><i class="fa-solid fa-backward"></i></a>
                    <div class="row">

                    <?php
                        if (!empty($listQuestion)) :
                            $count = 0;

                            foreach ($listQuestion as $item) :
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
                                                        <div  style="padding: 0 6px;">
                                                            <h6 style="margin: 0 ;padding: 0; font-size: 18px;font-weight:600;">
                                                                <a style="color: black;" href="?module=user&page=profile/profileView&userId=<?php echo $userId ?>">
    
                                                                    <?php echo $userDetail['fullname'] ?>
                                                                </a>
                                                            </h6>
                                                            <div class="text-muted small" style="margin: 2px 0; font-size: 12px; font-weight: 300;line-height: 12px;"><?php echo formatTimeDifference($item['update_at']); ?></div>
                                                        </div>
                                                        
                                                        <?php echo checkAdminInList($userId) ? '<span style="color: #20D5EC; font-size: 16px;"><i class="fa-solid fa-circle-check"></i></span>' : null ;?>
                                                    </div>
                                                    </div>
                                                </div>
                                                <div style="position: absolute; right: 13px; top: 13px;" class="dropdown show">
                                                    <a href="#" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i style="color:black;" class="fa-solid fa-ellipsis icon-hover"></i>
                                                    </a>

                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                        <a class="dropdown-item" href="<?php echo _WEB_HOST; ?>/?module=home&page=question/editQuestion&questionId=<?php echo $item['id'] ?>&postId=<?php echo $item['postId'] ?>" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen-to-square"></i> Edit question</a>
                                                        <a class="dropdown-item" href="<?php echo _WEB_HOST; ?>/?module=home&page=question/deleteQuestion&questionId=<?php echo $item['id'] ?>&userIdDelete=<?php echo $item['userId'] ?>&postId=<?php echo $item['postId'] ?>" onclick="return confirm('Delete this question ?')" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i> Delete question</a>
                                                    </div>
                                                </div>

                                                <div style="position: absolute; right: 12px; bottom: 44px;">
                                                    <?php echo $countReply == 0 ? null : '<a href="?module=home&page=reply/question&questionId=' . $item['id'] . '&postId=' . $item['postId'] . '" style="font-size: 14px;font-weight: 400;color: black;">' . $countReply . ' comments</a>'; ?>


                                                </div>
                                                <h5 style="margin: 0;"><a href="<?php echo _WEB_HOST; ?>/?module=home&page=reply/question&questionId=<?php echo $item['id'] ?>&postId=<?php echo $item['postId'] ?>" class="text-body"><?php echo $item['title'] ?></a></h5>
                                                <p>
                                                    <?php echo $item['content'] ?>
                                                </p>
                                                <div class="text-center">

                                                    <?php echo !empty($item['questionImage']) ? '<a href="?module=home&page=reply/question&questionId='.$item['id'].'&postId='. $item['postId'] .'" ><img style="padding-bottom: 10px" src=' . $item['questionImage'] . '  class="img-fluid" alt="Responsive image" ></a>' :  null ?>
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
        <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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



                            <input id="postId" type="hidden" name='postId' value="<?php echo $postId; ?>">
                            <div class="modal-footer">
                            </div>
                            <button type="button" class="mg-btn  rounded small">
                                <a style="padding: 12px 84px" href="<?php echo _WEB_HOST; ?>/?module=home&page=question/post&postId=<?php echo $_GET['postId'] ?>">Back</a>
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
        if (e.target.className === "modal fade") {
            window.location.href = "?module=home&page=question/post&postId=" + document.getElementById('postId').value;
        }
    }
</script>
<script>
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
        listQuestion.scrollTop = 0; // For Safari
        listQuestion.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    }
</script>



<?php
layouts('footerIn')
?>