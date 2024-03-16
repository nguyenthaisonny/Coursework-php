<?php
if (!defined('_CODE')) {
    die('Access denied...');
}

$data = [
    'titlePage' => 'Profile'
];
if(isGet()) {
    $userId = filter()['userId'];
    $userDetail = getRaw("SELECT * FROM users WHERE id='$userId'");
    $questionCount = countRow("SELECT id FROM questions WHERE userId='$userId'");
    $replyCount = countRow("SELECT id FROM replies WHERE userId='$userId'");

}

if (!checkLogin()) {
    reDirect('?module=auth&action=login');
}
layouts('headerProfileView', $data)
?>
<section class="vh-100" style="background-color: #f4f5f7;">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100" >
      <div class="col col-lg-12 mb-4 mb-lg-0">
        <div class="card mb-3" style="border-radius: .5rem;">
          <div class="row g-0">
            <div class="col-md-4 gradient-custom text-center text-white"
              style="border-top-left-radius: .5rem; border-bottom-left-radius: .5rem;">
              <img style="margin-top: 35px; margin-bottom: 10px;" width="160" height="160" class="rounded-circle" src=<?php echo $userDetail['profileImage']?> >


              <h5><?php echo $userDetail['fullname']?></h5>
              <p><?php echo $userDetail['description']?></p>
              <i class="fa-regular fa-circle-check mb-5"></i>
              
            </div>
            <div class="col-md-8">
              <div class="card-body p-4">
                <h6>Information</h6>
                <hr class="mt-0 mb-4">
                <div class="row pt-1">
                  <div class="col-7 mb-3">
                    <h6>Email</h6>
                    <p class="text-muted"><?php echo $userDetail['email']?></p>
                  </div>
                  <div class="col-5 mb-3">
                    <h6>Phone</h6>
                    <p class="text-muted"><?php echo $userDetail['phone']?></p>
                  </div>
                </div>
                <h6>Activity</h6>
                <hr class="mt-0 mb-4">
                <div class="row pt-1">
                  <div class="col-7 mb-3">
                    <h6>Question</h6>
                    <p class="text-muted"><?php echo $questionCount?> questions created</p>
                  </div>
                  <div class="col-5 mb-3">
                    <h6>Reply</h6>
                    <p class="text-muted"><?php echo $replyCount?> replies created</p>
                  </div>
                </div>
                <div class="d-flex justify-content-start">
                  <a href="#!"><i class="fab fa-facebook-f fa-lg me-3"></i></a>
                  <a href="#!"><i class="fab fa-twitter fa-lg me-3"></i></a>
                  <a href="#!"><i class="fab fa-instagram fa-lg"></i></a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
<?php
layouts('footerIn')
?>