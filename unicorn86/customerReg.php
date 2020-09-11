<?php

require "classes/userHandler.php";

$user = new UserHandler();
$status = "";
$msg = "";
if(isset($_POST['submit'])){
	$name = $_POST['name'];
	$phone = $_POST['phone'];
	$email = $_POST['email'];
	if(!empty($name) && !empty($phone)){
		$q = $user->addUser($name, $phone, $email);
		if($q){
			header("Location: menu.php");
		}
	}else{
		$status = "warning";
		$msg = "Please Fill the Required Fields";
	}
}

?>


<!doctype html>
<html lang="en">

<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="icon" href="img/favicon.png" type="image/png">
	<title>Food Menu</title>
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="css/bootstrap.css">
	<link rel="stylesheet" href="vendors/linericon/style.css">
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<link rel="stylesheet" href="css/magnific-popup.css">
	<link rel="stylesheet" href="vendors/owl-carousel/owl.carousel.min.css">
	<link rel="stylesheet" href="vendors/lightbox/simpleLightbox.css">
	<link rel="stylesheet" href="vendors/nice-select/css/nice-select.css">
	<link rel="stylesheet" href="vendors/jquery-ui/jquery-ui.css">
	<link rel="stylesheet" href="vendors/animate-css/animate.css">
	<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600&display=swap" rel="stylesheet">
	<!-- main css -->
	<link rel="stylesheet" href="css/style.css">
	<style>
        body,input, button {
            font-family: 'Quicksand', sans-serif;
            font-size: 16px;
            font-weight: 400;
            color: #222222; }
    </style>
</head>

<body id="book-table">

	<header>
		<div class="logo d-flex justify-content-center" style="margin-top:30px;">
			<img src="img/logo.png" alt="">
		</div>
	</header>
	<div class="site-main" style="margin-top: -50px;">
		<!--================ fill in details =================-->
		<section class=" section_gap_top">
			<div class="container">
				<div class="row align-items-center justify-content-center">
					<div class="col-lg-6 offset-lg-6">
						<div class="contact-form-section" style="margin:0 auto;">
							<h3 style="text-align: center;">Fill in the Details</h3>
							<br><br>
							<?php if(!empty($status)){ ?>
							<div class="alert alert-dismissible alert-<?php echo $status; ?>">
								<button type="button" class="close" data-dismiss="alert">&times;</button>
								<?php echo $msg; ?>
							</div>
							<?php } ?>
							<form class=" contact-form text-right" id="myForm" method="POST">
								<div class="form-group col-md-12">
									<input type="text" class="form-control" id="name" name="name" placeholder="Name" onfocus="this.placeholder = ''"
									 onblur="this.placeholder = 'Name'" required>
								</div>
								<div class="form-group col-md-12">
									<input type="tel" class="form-control" id="subject" name="phone" placeholder="Phone Number" onfocus="this.placeholder = ''"
									 onblur="this.placeholder = 'Phone Number'" required>
								</div>
								<div class="form-group col-md-12">
									<input type="email" class="form-control" id="email" name="email" placeholder="Email Address (Optional)" onfocus="this.placeholder = ''"
									 onblur="this.placeholder = 'Email Address(optional)'">
								</div>
								<div class="col-lg-12 text-center" style="margin-top: 50px;">
									<button class="btn btn-dark text-uppercase" style="font-weight:700;" name="submit">Jump to Menu</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</section>
		<!--================ End Reservstion Area =================-->
		<footer class=" overlay" style="margin-top: 300px;">
			<div class="container">
				<div class="row footer-bottom justify-content-center">
					<div class="col-lg-6">
						<p class="footer-text m-0" style="text-align: center;">
Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved.
</p>
					</div>
				</div>

			</div>
		</footer>
		<!--================ Start Footer Area =================-->
	</div>

	<!-- Optional JavaScript -->
	<!-- jQuery first, then Popper.js, then Bootstrap JS -->
	<script src="js/jquery-3.2.1.min.js"></script>
	<script src="js/popper.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/stellar.js"></script>
	<script src="js/jquery.magnific-popup.min.js"></script>
	<script src="vendors/lightbox/simpleLightbox.min.js"></script>
	<script src="vendors/nice-select/js/jquery.nice-select.min.js"></script>
	<script src="vendors/owl-carousel/owl.carousel.min.js"></script>
	<script src="vendors/jquery-ui/jquery-ui.js"></script>
	<script src="js/jquery.ajaxchimp.min.js"></script>
	<script src="vendors/counter-up/jquery.waypoints.min.js"></script>
	<script src="vendors/counter-up/jquery.counterup.js"></script>
	<!--gmaps Js-->
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCjCGmQ0Uq4exrzdcL6rvxywDDOvfAu6eE"></script>
	<script src="js/gmaps.min.js"></script>
	<script src="js/theme.js"></script>
</body>

</html>