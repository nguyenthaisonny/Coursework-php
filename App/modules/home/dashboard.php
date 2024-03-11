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
if (isGet()) {
    $filterAll = filter();
    
    if (!empty($filterAll['postName']) || !empty($filterAll['description'])) {
        if (getSession('loginToken')) {

            $loginToken = getSession('loginToken');
            $queyToken = getRaw("SELECT user_id FROM tokenlogin WHERE token = '$loginToken'");
            $userId = $queyToken['user_id'];
            $dataInsert = [
                'postName' => $filterAll['postName'],
                'description' => $filterAll['description'],
                'update_at' => date('Y:m:d H:i:s')
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
        reDirect('?module=home&action=dashboard');

    } 
}
$listPosts = getRaws("SELECT * FROM posts ORDER BY update_at DESC");

print_r($listPosts);
$smg = getFlashData('smg');
$smgType = getFlashData('smg_type');

layouts('headerIn', $data);
?>
<div class="row">
    <div class="col-4" style="margin: 50px auto;">
        <h2 class="text-center text-uppercase">WELCOME!</h2>
        <?php
        if (!empty($smg)) {
            getSmg($smg, $smgType);
        }
        ?>

        <!-- Button trigger modal -->
        <button type="button" class="mg-btn medium rounded " style="margin: 0 25%;" data-toggle="modal" data-target="#exampleModal">
            Upload <i class="fa-solid fa-plus"></i>
        </button>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" style="text-align: center;" id="exampleModalLabel">New Post</h5>

                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group">
                                <label class="col-form-label">Title:</label>
                                <input name="postName" type="text" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">Description</label>
                                <input name="description" type="text" class="form-control">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="mg-btn  rounded " data-dismiss="modal">Close</button>
                                <button type="submit" class="mg-btn  primary" style="margin-left: 60px;">Upload</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>



        <?php
        layouts('footerIn')
        ?>