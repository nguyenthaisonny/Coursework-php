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
        <button class="mg-btn rounded">

            <a href="?module=auth&action=register">Sign up</a>
        </button>


    </div>
</div>



<?php
layouts('footerIn')
?>