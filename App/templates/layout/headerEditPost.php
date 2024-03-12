<?php
if (!defined('_CODE')) {
  die('Access denied...');
}

if (getSession('loginToken')) {

  $loginToken = getSession('loginToken');
  $queyToken = getRaw("SELECT userId FROM tokenlogin WHERE token = '$loginToken'");
  $userId = $queyToken['userId'];
  if (!empty($queyToken)) {
    $userId = $queyToken['userId'];
    $queryImage = getRaw("SELECT profileImage, fullname FROM users WHERE id = '$userId'");
    if (!empty($queryImage)) {
      $profileImage = $queryImage['profileImage'];
      $userName = $queryImage['fullname'];
    };
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo !empty($data['titlePage']) ? $data['titlePage'] : 'App của Ny' ?></title>
  <link rel="stylesheet" href="<?php echo _WEB_HOST_TEMPLATES ?>/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="<?php echo _WEB_HOST_TEMPLATES ?>/css/forum.css">

  <link rel="stylesheet" href="<?php echo _WEB_HOST_TEMPLATES ?>/css/style.css?ver=<?php echo rand(); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
 

</head>

<body>
  <div id="overlay"></div>
  <header class="p-3 mb-3 border-bottom">
    <div class="container">
      <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
        <a href="" class="d-flex align-items-center mb-2 mb-lg-0 text-dark text-decoration-none">
          <svg class="bi me-2" width="40" height="32" role="img" aria-label="Bootstrap">
            <use xlink:href="#bootstrap"></use>
          </svg>
        </a>


        <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
          <li><a href="?module=home&action=forum" class=" px-2 nav-item">Forum</a></li>
          <li><a href="?module=user&action=post" class=" px-2 nav-item">Post</a></li>
          <li><a href="#" class=" px-2 nav-item">Customers</a></li>
          <li><a href="#" class=" px-2 nav-item">Products</a></li>
        </ul>

        <button class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3 mg-btn primary" style="margin-top: 0;">
          Hi <?php echo $userName ?> ^^
        </button>



        <div class="menu-item dropdown text-end">
          <a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
            <img src=<?php echo !empty($profileImage) ? $profileImage : "https://img.freepik.com/free-vector/businessman-character-avatar-isolated_24877-60111.jpg?w=826&t=st=1710127291~exp=1710127891~hmac=10efc92f9bddd8afe06fa86d74c0caf109f33b79794fd0fc982a01c8bff70758";; ?> alt="mdo" width="32" height="32" class="rounded-circle">
          </a>
          <ul class="dropdown-menu text-small" aria-labelledby="dropdownUser1">

            <li><a class="dropdown-item" href="#">Settings</a></li>
            <li><a class="dropdown-item" href="?module=user&action=profile&id=<?php echo $userId ?>">Profile</a></li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item" href="?module=auth&action=logout#">Sign out</a></li>
          </ul>
        </div>
      </div>
    </div>
  </header>