

<?php
session_start();
require_once('config.php');
require_once('./includes/connectDB.php');
//phpmailer lib
require_once('./includes/phpmailer/Exception.php');
require_once('./includes/phpmailer/PHPMailer.php');
require_once('./includes/phpmailer/SMTP.php');

require_once('./includes/functions.php');
require_once('./includes/database.php');
require_once('./includes/session.php');


// sendMail('sonnynguyenthai@gmail.com', 'test mail', 'nyquadeptrai');

$module = _MODULE;
$action = _ACTION;
$page = _PAGE;
if (!empty($_GET['module'])) {

    if (is_string($_GET['module'])) {
        $module = trim($_GET['module']);
    }
}

if (!empty($_GET['action'])) {

    if (is_string($_GET['action'])) {
        $action = trim($_GET['action']);
    }
}
if (!empty($_GET['page'])) {

    if (is_string($_GET['page'])) {
        $page = trim($_GET['page']);
    }
}


$path = 'modules/' . $module . '/'. $page. '.php';
if (file_exists($path)) {

    require_once($path);
} else {
    require_once 'modules/error/404.php';
}
