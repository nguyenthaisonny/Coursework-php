<?php
if(!defined('_CODE')) {
    die('Access denied...');
}
$data = [
    'titlePage' => 'Home'
];


if(!checkLogin()) {
    reDirect('?module=auth&action=login');
}
layouts('headerIn', $data);
?>
<h1>dashboard</h1>



<?php 
layouts('footerIn')
?>