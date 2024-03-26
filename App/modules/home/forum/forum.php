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

$listPost = getRaws("SELECT * FROM posts ORDER BY updateAt DESC");
if (!empty($_GET['type'])) {
    $newListPost = [];
    switch ($_GET['type']) {
        case 'oldest':

            $newListPost =  getRaws("SELECT * FROM posts ORDER BY updateAt");
            break;
        case 'noneQuestion':
            foreach ($listPost as $item) {
                $postId = $item['id'];
                $questionCount = countRow("SELECT id FROM questions WHERE postId = '$postId'");

                if ($questionCount == 0) {
                    array_push($newListPost, $item);
                }
            }
            break;
        case 'popular':
            foreach ($listPost as $item) {
                $postId = $item['id'];
                $questionCount = countRow("SELECT id FROM questions WHERE postId = '$postId'");

                if ($questionCount >= 6) {
                    array_push($newListPost, $item);
                }
            }
            break;
    }
} else {
    $newListPost = $listPost;
}



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
                    <button type="button" class="mg-btn medium rounded " style="margin: 0 25%;">
                        <a class="mediumAnker" href="?module=home&page=forum/addPost<?php echo !empty($_GET['type']) ? '&type=' . $_GET['type'] : '' ?>">

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
                                                <a id='latest' href="javascript:void(0)" class="nav-link nav-link-faded has-icon <?php echo empty($_GET['type']) ? 'active' : '' ?>">Latest</a>
                                                <a id="oldest" href="javascript:void(0)" class="nav-link nav-link-faded has-icon <?php echo (!empty($_GET['type']) && $_GET['type'] == 'oldest') ? 'active' : '' ?>">Oldest</a>
                                                <a id="popular" href="javascript:void(0)" class="nav-link nav-link-faded has-icon <?php echo (!empty($_GET['type']) && $_GET['type'] == 'popular') ? 'active' : '' ?>">Popular</a>


                                                <a id="noneQuestion" href="javascript:void(0)" class="nav-link nav-link-faded has-icon <?php echo (!empty($_GET['type']) && $_GET['type'] == 'noneQuestion') ? 'active' : '' ?>">None of question</a>

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
                <div  class="inner-main-header" style="display: flex;">

                    <div class="forum-welcome" style="margin: auto;color: rgb(104, 85, 224);font-weight: 600;">
                    Welcome to our forum community <span><i style="    color: rgb(254, 44, 85); " class="fa-solid fa-heart"></i></span></div>
                    <?php echo checkAdminNotSignOut() ? '<a id="deleteAll" href="?module=admin&page=manage/deleteAllPost" data-toggle="tooltip" data-placement="top" title="Delete all" style="position: absolute; right: 26px; top: 20px; color: rgb(254, 44, 85); " type="button" href="">
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
                    if (!empty($newListPost)) :
                        $count = 0;
                        foreach ($newListPost as $item) :
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
                                            <div style="display: flex; ">

                                                <div style="padding: 0 6px;">

                                                    <h6 style="margin: 0 ;padding: 0; font-size: 18px;font-weight: 600;"><a href="?module=user&page=profile/profileView&userId=<?php echo $userId ?>" class="text-body"><?php echo $userDetail['fullname'] ?></a></h6>
                                                    <p style=" margin: 2px 0; font-size: 12px; font-weight: 300;line-height: 12px;"><?php echo  formatTimeDifference($item['updateAt']); ?></p>

                                                </div>

                                                <?php echo checkAdminInList($userId) ? '<span style="color: #20D5EC; font-size: 16px;"><i class="fa-solid fa-circle-check"></i></span>' : null; ?>
                                            </div>

                                        </div>
                                        <div style="position: absolute; right: 13px; top: 13px;" class="dropdown show">
                                            <a href="#" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i style="color:black;" class="fa-solid fa-ellipsis icon-hover"></i>
                                            </a>

                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                <a class="dropdown-item" href="<?php echo _WEB_HOST; ?>/?module=home&page=forum/editPost&postId=<?php echo $item['id'] ?><?php echo !empty($_GET['type']) ? '&type=' . $_GET['type'] : '' ?>" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen-to-square"></i> Edit post</a>
                                                <a class="dropdown-item" href="<?php echo _WEB_HOST; ?>/?module=home&page=forum/deletePost&postId=<?php echo $item['id'] ?>&userIdDelete=<?php echo $item['userId'] ?>" onclick="return confirm('Delete this post?')" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i>Delete post</a>
                                            </div>
                                        </div>

                                        <div class="media-body" style="margin-top: 8px;">
                                            <h5 style="margin: 0;"><a href="<?php echo _WEB_HOST; ?>/?module=home&page=question/post&postId=<?php echo $item['id'] ?>" class="text-body"><?php echo $item['postName'] ?></a></h5>
                                            <p style="margin-bottom: 20px;">
                                                <?php echo $item['description'] ?>
                                            </p>

                                        </div>
                                        <div>

                                            <a style="margin-right: 4px;" href="<?php echo _WEB_HOST; ?>/?module=home&page=question/post&postId=<?php echo $item['id'] ?>" class="d-inline-block text-muted">
                                                <i class="fa-solid fa-door-open icon-hover" style="font-size: 20px;"></i>

                                            </a>
                                            <span class="d-none d-sm-inline-block" style="font-size: 16px; font-weight: 300; line-height: 16px;">
                                                <a class="hover-item" href="<?php echo _WEB_HOST; ?>/?module=home&page=question/post&postId=<?php echo $item['id'] ?>"><?php echo $questionCount ?> <?php echo ($questionCount == 1 || $questionCount == 0) ? 'question' : 'questions' ?></a>
                                            </span>

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



    </div>
</div>
<script>
    //handle sort case
    const latest = document.getElementById('latest');

    latest.onclick = function(e) {

        const urlParams = new URLSearchParams('?module=home&page=forum/forum');

        window.location.search = urlParams;



    }
    const oldest = document.getElementById('oldest');

    oldest.onclick = function(e) {

        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('type', 'oldest');
        window.location.search = urlParams;



    }
    const noneQuestion = document.getElementById('noneQuestion');

    noneQuestion.onclick = function(e) {

        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('type', 'noneQuestion');
        window.location.search = urlParams;



    }
    const popular = document.getElementById('popular');

    popular.onclick = function(e) {


        const urlParams = new URLSearchParams(window.location.search);

        urlParams.set('type', 'popular');
        window.location.search = urlParams;



    }


    //handle tooltip
    $(function() {
        $('[data-toggle="tooltip"]').tooltip()
    })
    let deleteAll = document.getElementById("deleteAll");
    if(!!deleteAll) {

        deleteAll.onclick = function() {
            return confirm("Delete all")
        }
    }

    //handle scroll top
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
        listPost.scrollTo({
            top: 0,
            behavior: 'smooth'
        }) // For Safari
        listPost.documentElement.scrollTo({
            top: 0,
            behavior: 'smooth'
        }); // For Chrome, Firefox, IE and Opera
    }
</script>

<?php
layouts('footerIn')
?>