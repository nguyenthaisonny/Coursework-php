<?php
if (!defined('_CODE')) {
    die('Access denied...');
}
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// func to import 
function layouts($layoutName = 'header', $data = [])
{
    if (file_exists(_WEB_PATH_TEMPLATES . '/layout/' . $layoutName . '.php')) {

        require_once(_WEB_PATH_TEMPLATES . '/layout/' . $layoutName . '.php');
    }
}

// mail transfer func
function sendMail($to, $subject, $content)
{
    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = _EMAIL;                     //SMTP username
        $mail->Password   = _EMAILPASSWORD;                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom(_EMAIL, 'Sonny');
        $mail->addAddress($to);     //Add a recipient
        //Content
        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $content;
        //SSL certificate
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
            );

        $sendMail = $mail->send();
        if ($sendMail) {
            return $sendMail;
        }
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Check Get method
function isGet()
{
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        return true;
    }
    return false;
}

// Check Post method
function isPost()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        return true;
    }
    return false;
}
// filter data func
function filter()
{
    $filteredArray = [];
    if (isGet()) {
        //handle data before render 
        if (!empty($_GET)) {
            foreach ($_GET as $key => $value) {
                $key = strip_tags($key);
                if (is_array($value)) {
                    $filteredArray[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
                } else {
                    $filteredArray[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                }
            }
        }
    }
    if (isPost()) {
        //handle data before submit
        if (!empty($_POST)) {
            foreach ($_POST as $key => $value) {
                $key = strip_tags($key);
                if (is_array($value)) {
                    $filteredArray[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
                } else {
                    $filteredArray[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                }
            }
        }
    }
    return $filteredArray;
}

//check email validate
function isEmail($email)
{
    $checkEmail = filter_var($email, FILTER_VALIDATE_EMAIL);

    return $checkEmail;
}
// check interger
function isNumberInt($number)
{
    $checkNumber = filter_var($number, FILTER_VALIDATE_INT);
    return $checkNumber;
}
//check float 
function isNumberFloat($number)
{
    $checkNumber = filter_var($number, FILTER_VALIDATE_FLOAT);
    return $checkNumber;
}
//Check Phone Number
function isPhone($phone)
{
    //frist number is 0
    $checkZero = false;
    if ($phone[0] == '0') {
        $checkZero = true;
        $phone = substr($phone, 1);
    }
    //lenggth rest is 9
    $checkNumber = false;
    if (isNumberInt($phone) && strlen($phone) == 9) {
        $checkNumber = true;
    }

    if ($checkZero && $checkNumber) {
        return true;
    }
    return false;
}
//Alert smg
function getSmg($smg, $type = 'success')
{
    echo '<div style="text-align: center;" class="alert alert-' . $type . '">';
    echo $smg;
    echo '</div>';
}

// direct func
function reDirect($path = 'index.php')
{
    header("Location: $path");
    exit();
}
// echo err
function formErr($fileName, $beforeHtml = '', $afterHtml = '', $errors)
{
    // if(!empty($fileName)) {

    return !empty($errors[$fileName]) ? $beforeHtml . reset($errors[$fileName]) . $afterHtml : null;

    // }
    // echo null;

}
function getOldValue($old, $fileName)
{

    return !empty($old[$fileName]) ? $old[$fileName] : null;
}
//check login func
function checkLogin()
{
    $checkLogin = false;
    if (getSession('loginToken')) {
        
        $loginToken = getSession('loginToken');
        $queryToken = getRaw("SELECT userId FROM tokenlogin WHERE token = '$loginToken'");
        $userId = $queryToken['userId'];
        $queryStatus = getRaw("SELECT status FROM users WHERE id = '$userId'");
        if (!empty($queryToken) && $queryStatus['status'] == 1) {
            $checkLogin = true;
        } else {
            if($queryStatus['status'] == 0) {

                setFlashData('smg', 'Your account has not been active! Please check your email again.');
                setFlashData('smg_type', 'danger');
            }
            removeSession('loginToken');
        }
    }
    return $checkLogin;

}

function checkAdmin() {
    $checkAdmin = false;
    if (getSession('loginToken')) {
        
        $loginToken = getSession('loginToken');
        $queyToken = getRaw("SELECT userId FROM tokenlogin WHERE token = '$loginToken'");
        $userId = $queyToken['userId'];
        $queryStatus = getRaw("SELECT role FROM users WHERE id = '$userId'");
        if (!empty($queyToken) && $queryStatus['role'] == 'admin') {
            $checkAdmin = true;
        } else {
            if($queryStatus['role'] != 'admin') {

                setFlashData('smg', 'You can not access this page!');
                setFlashData('smg_type', 'danger');
            }
            removeSession('loginToken');
        }
    }
    return $checkAdmin;
}
function checkAdminNotSignOut() {
    $checkAdmin = false;
    if (getSession('loginToken')) {
        
        $loginToken = getSession('loginToken');
        $queyToken = getRaw("SELECT userId FROM tokenlogin WHERE token = '$loginToken'");
        $userId = $queyToken['userId'];
        $queryStatus = getRaw("SELECT role FROM users WHERE id = '$userId'");
        if (!empty($queyToken) && $queryStatus['role'] == 'admin') {
            $checkAdmin = true;
        } 
    }
    return $checkAdmin;
}
function checkAdminInList($userId) {
    $checkAdmin = false;
    $query = getRaw("SELECT role FROM users WHERE id='$userId'");
    $role = $query['role'];
    if($role == 'admin') {
        $checkAdmin = true;
    }
    return $checkAdmin;
    
}
function formatTimeDifference($inputDate) {
    // Convert input date to a DateTime object
    $inputDateTime = new DateTime($inputDate);
    
    // Get current date and time
    $currentDateTime = new DateTime();
    
    // Check if input date is in the same year
    if ($inputDateTime->format('Y') === $currentDateTime->format('Y')) {
        // Calculate the time difference
        $interval = $inputDateTime->diff($currentDateTime);
        
        // Check the time difference
        if ($interval->y > 0) {
            return $interval->y . " year(s) ago";
        } elseif ($interval->m > 0) {
            return $interval->m . " month(s) ago";
        } elseif ($interval->d > 0) {
            return $interval->d . " day(s) ago";
        } elseif ($interval->h > 0) {
            return $interval->h . " hour(s) ago";
        } elseif ($interval->i > 0) {
            return $interval->i . " minute(s) ago";
        } else {
            return "Just now";
        }
    } else {
        // If not in the same year
        return $inputDateTime->format('Y');
    }
}

