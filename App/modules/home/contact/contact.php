<?php
if (!defined('_CODE')) {
    die('Access denied...');
}

$data = [
    'titlePage' => 'Contact'
];

if (!checkLogin()) {
    reDirect('?module=auth&page=login');
}
$loginToken = getSession('loginToken');
$queryToken = getRaw("SELECT userId FROM tokenlogin WHERE token = '$loginToken'");
$userId = $queryToken['userId'];
$userDetail = getRaw("SELECT fullname, email FROM users WHERE id = '$userId'");
if(!empty($userDetail)) {
    $old = $userDetail;
}
if(isPost()) {
    $filterAll = filter();
    $dataInsert = [
        'userId' => $userId,
        

        'messageSubject' => $filterAll['messageSubject'],
        'messageContent' => $filterAll['messageContent']

    ];
    $insertStatus = insert('messages', $dataInsert);
    if($insertStatus) {
        setFlashData('smg', 'Send message successfully!');
        setFlashData('smg_type', 'success');
    } else {
        setFlashData('smg', 'System face error! please try again.');
        setFlashData('smg_type', 'danger');
    }
}
$smg = getFlashData('smg');
$smgType = getFlashData('smg_type');
layouts('headerContact', $data)
?>
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />

<div id="contact" class="contact-area section-padding" style="padding: 50px 0 100px 0;">
	<div class="container">										
		<div class="section-title text-center">
			<h1>Get in Touch</h1>
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce vitae risus nec dui venenatis dignissim. Aenean vitae metus in augue pretium ultrices.</p>
		</div>		
        <?php
                if (!empty($smg)) {
                    getSmg($smg, $smgType);
                }
                ?>			
		<div class="row">
			<div class="col-lg-8">	
				<div class="contact">
					<form class="form" method="post" action="" onsubmit="return validation();">
						<div class="row">
							<div class="form-group col-md-6">
								<input type="text" name="fullnameMessage" class="form-control" placeholder="Name" style="cursor: not-allowed;" value="<?php echo getOldValue($old, 'fullname')?>">
							</div>
							<div class="form-group col-md-6" >
								<input type="email" name="emailMessage" class="form-control" placeholder="Email" style="cursor: not-allowed;"  value="<?php echo getOldValue($old, 'email')?>">
							</div>
							<div class="form-group col-md-12 " style="margin-top: 16px;">
								<input type="text" name="messageSubject" class="form-control" placeholder="Subject" required="required">
							</div>
							<div class="form-group col-md-12" style="margin-top: 16px;">
								<textarea rows="6" name="messageContent" class="form-control" placeholder="Your Message" required="required"></textarea>
							</div>
							<div class="col-md-12 text-center" style="margin-top: 16px;">
								<button type="submit"  class="btn btn-contact-bg" title="Submit Your Message!">Send Message</button>
							</div>
						</div>
					</form>
				</div>
			</div><!--- END COL --> 
			<div class="col-lg-4">
				<div class="single_address">
					<i class="fa fa-map-marker"></i>
					<h4>Our Address</h4>
					<p>Cong Hoa Garden, 20 Cong Hoa, Ward 12, Tan Binh, HCMC</p>
				</div>
				<div class="single_address">
					<i class="fa fa-envelope"></i>
					<h4>Send your message</h4>
					<p>Info@example.com</p>
				</div>
				<div class="single_address">
					<i class="fa fa-phone"></i>
					<h4>Call us on</h4>
					<p>09 123 456 78</p>
				</div>
				<div class="single_address">
					<i class="fa fa-clock-o"></i>
					<h4>Work Time</h4>
					<p>Mon - Fri: 08.00 - 16.00. <br>Sat: 10.00 - 14.00</p>
				</div>					
			</div><!--- END COL --> 
		</div><!--- END ROW -->
	</div><!--- END CONTAINER -->	
</div>



<?php
layouts('footerIn')
?>