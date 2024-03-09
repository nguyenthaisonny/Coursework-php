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
    <link rel="stylesheet" href="<?php echo _WEB_HOST_TEMPLATES?>/css/style.css?ver=<?php echo rand();?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    

</head>
<body>
<header class="p-3 mb-3 border-bottom">
    <div class="container">
      <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
        <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-dark text-decoration-none">
          <svg class="bi me-2" width="40" height="32" role="img" aria-label="Bootstrap"><use xlink:href="#bootstrap"></use></svg>
        </a>

        
        <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
          <li><a href="#" class=" px-2 nav-item">Overview</a></li>
          <li><a href="#" class=" px-2 nav-item">Inventory</a></li>
          <li><a href="#" class=" px-2 nav-item">Customers</a></li>
          <li><a href="#" class=" px-2 nav-item">Products</a></li>
        </ul>

        <button class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3 primary" >
            Add Question
        </button>
        
        

        <div class="menu-item dropdown text-end">
          <a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="https://github.com/mdo.png" alt="mdo" width="32" height="32" class="rounded-circle">
          </a>
          <ul class="dropdown-menu text-small" aria-labelledby="dropdownUser1">
            
            <li><a class="dropdown-item" href="#">Settings</a></li>
            <li><a class="dropdown-item" href="#">Profile</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="?module=auth&action=logout#">Sign out</a></li>
          </ul>
        </div>
      </div>
    </div>
  </header> 
