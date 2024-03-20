<?php
if (!defined('_CODE')) {
    die('Access denied...');
}
$data = [
    'titlePage' => 'Forum'
];


if (!checkLogin()) {
    reDirect('?module=auth&page=login');
}

if (isPost()) {
    $filterAll = filter();

    if (!empty($filterAll['postName']) || !empty($filterAll['description'])) {
        if (getSession('loginToken')) {

            $loginToken = getSession('loginToken');
            $queyToken = getRaw("SELECT userId FROM tokenlogin WHERE token = '$loginToken'");
            $userId = $queyToken['userId'];
            $dataInsert = [
                'postName' => $filterAll['postName'],
                'description' => $filterAll['description'],
                'update_at' => date('Y:m:d H:i:s'),
                'userId' => $userId
            ];
            $insertStatus = insert('posts', $dataInsert);
            if ($insertStatus) {

                setFlashData('smg', 'A new Post was just uploaded!');
                setFlashData('smg_type', 'success');
            } else {
                setFlashData('smg', 'System faces errors! Please try again.');
                setFlashData('smg_type', 'danger');
            }
        }
        reDirect('?module=home&page=forum/forum');
    }
}
$listPost = getRaws("SELECT * FROM posts ORDER BY update_at DESC");

$smg = getFlashData('smg');
$smgType = getFlashData('smg_type');

layouts('headerForum', $data);
?>



<div class="container" style=" margin-bottom: 100px">
    <div class="main-body p-0">
        <div class="inner-wrapper">
            <!-- Inner sidebar -->
            <div class="inner-sidebar">
                <!-- Inner sidebar header -->
                <div class="inner-sidebar-header justify-content-center">
                    <!-- Button trigger modal -->
                    <button href="?module=home&page=forum/addPost" type="button" class="mg-btn medium rounded " style="margin: 0 25%;">
                    <a href="?module=home&page=forum/addPost" style="padding: 0 50px;">

                        New post <i class="fa-solid fa-plus"></i>
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
                                                <a href="" class="nav-link nav-link-faded has-icon active">All Threads</a>
                                                <a href="" class="nav-link nav-link-faded has-icon">Popular this week</a>
                                                <a href="" class="nav-link nav-link-faded has-icon">Popular all time</a>
                                                <a href="" class="nav-link nav-link-faded has-icon">Solved</a>
                                                <a href="" class="nav-link nav-link-faded has-icon">Unsolved</a>
                                                <a href="" class="nav-link nav-link-faded has-icon">No replies yet</a>
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
                <div class="inner-main-header" style="position: relative;">

                    <select class="custom-select custom-select-sm w-auto mr-1">
                        <option selected="">Latest</option>
                        <option value="1">Popular</option>
                        <option value="3">Solved</option>
                        <option value="3">Unsolved</option>
                        <option value="3">No Replies Yet</option>
                    </select>
                    <?php echo checkAdminNotSignOut() ? '<a id="deleteAll" href="?module=admin&page=manage/deleteAllPost" data-toggle="tooltip" data-placement="top" title="Delete all" style="position: absolute; right: 36px; top: 20px; color: rgb(254, 44, 85); " type="button" href="">
                    <i  class="fa-solid fa-delete-left" style="font-size: 26px"></i>
                    </a>' : null; ?>
                   

                </div>
                <?php
                if (!empty($smg)) {
                    getSmg($smg, $smgType);
                }
                ?>
                <!-- /Inner main header -->

                <!-- Inner main body -->

                <!-- Forum List -->
                <ul class="inner-main-body p-2 p-sm-3 collapse forum-content show" id='listPost'>
                    <button id="myBtn" title="Go to top" style="border-radius: 50%;"><i class="fa-solid fa-arrow-up"></i></button>

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

                                            <a href="?module=user&page=profile/profileView&userId=<?php echo $userId ?>"><img src="<?php echo $userDetail['profileImage'] ? $userDetail['profileImage'] :  "https://img.freepik.com/free-vector/businessman-character-avatar-isolated_24877-60111.jpg?w=826&t=st=1710127291~exp=1710127891~hmac=10efc92f9bddd8afe06fa86d74c0caf109f33b79794fd0fc982a01c8bff70758"; ?>" class="mr-3 rounded-circle" width="50" alt="User" /></a>
                                            <div style="padding-left: 6px;">

                                                <h6 style="margin: 0 ;padding: 0; font-size: 16px"><a href="?module=user&page=profile/profileView&userId=<?php echo $userId ?>" class="text-body"><?php echo $userDetail['fullname'] ?></a></h6>
                                                <p style=" margin: 2px 0; font-size: 12px; font-weight: 300;line-height: 12px;"><?php echo  formatTimeDifference($item['update_at']); ?></p>

                                            </div>

                                        </div>
                                        <div style="position: absolute; right: 13px; top: 13px;">

                                            <a href="<?php echo _WEB_HOST; ?>/?module=home&page=forum/editPost&postId=<?php echo $item['id'] ?>&userIdEdit=<?php echo $item['userId'] ?>" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen-to-square"></i></a>
                                            <a href="<?php echo _WEB_HOST; ?>/?module=home&page=forum/deletePost&postId=<?php echo $item['id'] ?>&userIdDelete=<?php echo $item['userId'] ?>" onclick="return confirm('Delete this post?')" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></a>
                                        </div>
                                        <div class="media-body" style="margin-top: 4px;">
                                            <h5 style="margin: 0;"><a href="<?php echo _WEB_HOST; ?>/?module=home&page=question/post&postId=<?php echo $item['id'] ?>&userIdEdit=<?php echo $item['userId'] ?>" class="text-body"><?php echo $item['postName'] ?></a></h5>
                                            <p style="margin-bottom: 20px;">
                                                <?php echo $item['description'] ?>
                                            </p>

                                        </div>
                                        <div>

                                            <a style="margin-right: 4px;" href="<?php echo _WEB_HOST; ?>/?module=home&page=question/post&postId=<?php echo $item['id'] ?>&userIdEdit=<?php echo $item['userId'] ?>" class="d-inline-block text-muted">
                                                <i class="fa-solid fa-door-open icon-hover" style="font-size: 20px;"></i>

                                            </a>
                                            <span class="d-none d-sm-inline-block" style="font-size: 16px; font-weight: 300; line-height: 16px;"><?php echo $questionCount ?> questions</span>

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
                                <div class="alert alert-danger text-center">None of Post</div>
                            </td>
                        </tr>
                    <?php

                    endif;
                    ?>

                </ul>
                <!-- /Forum List -->

                <!-- Forum Detail -->

                <!-- /Forum Detail -->

                <!-- /Inner main body -->
            </div>
            <!-- /Inner main -->
        </div>

        <!-- New Thread Modal -->
        <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" style="text-align: center;" id="exampleModalLabel">New Post</h5>

                    </div>
                    <div class="modal-body">
                        <form method="post">
                            <div class="form-group">
                                <label class="col-form-label">Title:</label>
                                <input name="postName" type="text" class="form-control " required="required">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">Description</label>
                                <input name="description" type="text" class="form-control" required="required">
                            </div>
                            <div class="modal-footer">
                            </div>
                            <button type="button" class="mg-btn small rounded">
                                <a style="padding: 12px 84px" href="<?php echo _WEB_HOST; ?>/?module=home&page=forum/forum">Back</a>
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
            window.location.href = '?module=home&page=forum/forum';
        }
    }
</script>
<script>
    $(function() {
        $('[data-toggle="tooltip"]').tooltip()
    })
    let deleteAll = document.getElementById("deleteAll");
    deleteAll.onclick = function() {
        return confirm("Delete all")
    }
    // Get the button:

    let mybutton = document.getElementById("myBtn");
    let listPost = document.getElementById("listPost");
    // When the user scrolls down 20px from the top of the document, show the button
    listPost.onscroll = function() {
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
        if (listPost.scrollTop > 20 && window.scrollY < 100) {
            mybutton.style.display = "block";
        } else {
            mybutton.style.display = "none";
        }
    }

    function handleScrollWindow() {
        if (listPost.scrollTop > 20 && window.scrollY > 100) {
            mybutton.style.display = "none";
        } else if (listPost.scrollTop > 20 && window.scrollY < 100) {
            mybutton.style.display = "block";

        }
    }
    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
        listPost.scrollTop = 0; // For Safari
        listPost.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    }
</script>

<?php
layouts('footerIn')
?>