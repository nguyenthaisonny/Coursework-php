<?php
if(!defined('_CODE')) {
    die('Access denied...');
}?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo !empty($data['titlePage']) ? $data['titlePage'] : 'App của Ny' ?></title>
    <link rel="stylesheet" href="<?php echo _WEB_HOST_TEMPLATES?>/css/bootstrap.min.css">
    <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.js"></script>
    <link rel="stylesheet" href="<?php echo _WEB_HOST_TEMPLATES?>/css/style.css?ver=<?php echo rand();?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
</head>
<body>
    

