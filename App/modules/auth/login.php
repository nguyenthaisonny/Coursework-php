<?php
if (!defined('_CODE')) {
    die('Access denied...');
}
$data = [
    'titlePage' => 'Sign in'
];

layouts('header', $data);

?>

<div class="row">
    <div class="col-4" style="margin: 50px auto;">
        <h2 class="text-center text-uppercase">Sign in</h2>
        <form action="" method="post">
            <div class="form-group mg-form">
                <label for="">Email</label>
                <input name="email" type="email" class="form-control" placeholder="Enter your email">
            </div>
            <div class="form-group">
                <label for="">Password</label>

                <input name="password" type="password" class="form-control" placeholder="Enter your password">
            </div>
            <button type="submit" class="mg-btn btn btn-primary btn-block">
                Sign in
            </button>
            <hr>
            <p class="text-center">
                <a href="?module=auth&action=forgot">Forgot password?</a>
            </p>
            <p class="text-center">
                <a href="?module=auth&action=register">Sign up</a>
            </p>
        </form>
    </div>
</div>



<?php
layouts('footer')

?>