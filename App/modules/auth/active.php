<?php
if(!defined('_CODE')) {
    die('Access denied...');
}
layouts('header', ['titlePage' => 'Active']);
$token = filter()['token'];
if(!empty($token)) {
    // check i datat base
    $queryToken = getRaw("SELECT id FROM users WHERE activeToken = '$token'");
    if(!empty($queryToken)) {
        $userId = $queryToken['id'];
        $dataUpdate = [
            'status' => 1,
            'activeToken' => null,

        ];
        $updateStatus = update('users', $dataUpdate, "id='$userId'");
        if($updateStatus) {
            setFlashData('smg', 'Password changed! You can sign in now!');
            setFlashData('smg_type', 'success');
            reDirect('?module=auth&page=login');
        }
        else {
            setFlashData('smg', 'Failed to active ! Please try again!');
            setFlashData('smg_type', 'danger');
        }

    } else {
        getSmg('Link is not exist or out-dated', 'danger');
    }
}
else {
    getSmg('Link is not exist or out-dated', 'danger');
}  


?>
<h1>ACTIVE</h1>
<?php
layouts('footer');
?>