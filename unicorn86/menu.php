<?php
session_start();
require_once "classes/userHandler.php";
require_once "classes/menuHandler.php";
require_once "classes/orderHandller.php";
if(!isset($_SESSION['user'])){
	header("Location: index.php");
	die("Not Registerd");
}
function getUserIpAddr(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        return $_SERVER['HTTP_CLIENT_IP'];
    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
        return $_SERVER['REMOTE_ADDR'];
    }
}

$main = new userHandler();
$orderHandler = new OrderHandler();
$menu = new MenuHandler();
$x = $main->getAllowedIps(getUserIpAddr());
if(!$x){
    header("Location: index.php");
    die("You need to connect to the our Restaurant WiFi to access this Feature");
}
$cartItems = $orderHandler->getItemWithName($_SESSION['user']['user_info']['uid']);
$menuItems = $menu->getMenuItems();
?>
<!doctype html>
<html lang="en">

<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="icon" href="img/favicon.png" type="image/png">
	<title>Food Menu</title>
	
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
	<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"> -->

	<!-- main css -->
	<link rel="stylesheet" href="css/style.css">
	<style>
		body{
			background-color:#fffffe;
		}
		h1,h2,h3,h4,h5,h6,body .menu_list p,button, span {
			font-family: 'Quicksand', sans-serif;
		}
		h1,h2,h3,h4,h5,h6 {
			color: #094067;
		}
		p {
			color: #5f6c7b;
		}
		body,input, button {
            font-family: 'Quicksand', sans-serif;
            font-size: 16px;
            /* font-weight: 400; */
            color: #222222; }
		.menu_list ul li {
			margin-top: 10px;
		} 
		.minusplus {
			font-size:10px;display:block;
		}  
		.col-lg-5 {
			margin-top: 10px;
			margin-bottom: 20px;
		}
		.menu_list h4 {
			font-weight: bolder;
		}
		.scrolling-wrapper {
			overflow-x: scroll;
			overflow-y: hidden;
			white-space: nowrap;
		}
		.scrolling-wrapper li {
				display: inline-block;
			}
		.scrolling-wrapper::-webkit-scrollbar {
 				 display: none; }
	</style>
</head>

<body>
	<!-- <div class="menu-header">
		<div class="logo d-flex justify-content-center">
			<img src="img/logo.png" alt="">
		</div>
	</div>
	 -->
	<div class="site-main">
		<a href="logout.php" class="btn btn-primary" style="float: right;">Logout</a>
		<?php if($_SESSION['user']['user_info']['isRoot'] == "false"){ ?>
		<a href="showOrder.php" class="btn btn-primary" style="float: right;">Go to Orders</a>
		<?php } ?>
		<div>
			<p style="font-size: 14px;margin-top: 20px;margin-left: 10px;font-style: italic;">Scroll to left</p>
			<div class="scrolling-wrapper">
				<ul class="nav nav-tabs" id="myClassicTab" role="tablist">
					<?php
					$i = 0;
					foreach ($menuItems as $key => $row) {
						++$i;
						?>
					<li class="nav-item">
						<a class="nav-link <?php echo ($i == 1 )? 'active show' : ''; ?>" id="<?php echo implode("", explode(" ", $key)); ?>-tab-classic" data-toggle="tab" href="#<?php echo implode("", explode(" ", $key)); ?>-classic" role="tab" aria-controls="<?php echo implode("", explode(" ", $key)); ?>-classic" aria-selected="<?php echo ($i == 1 )? 'true' : 'false'; ?>"><?php echo $key ?></a>
					</li>
						<?php
					}

					?>
				</ul>
			</div>
			
	<!-- <hr> -->
	


	<div class="tab-content border-right border-bottom border-left rounded-bottom" id="myClassicTabContent">
		<?php 
		$i = 0;
			foreach ($menuItems as $key => $row) {
				++$i;
		?>
		<div class="tab-pane fade <?php echo ($i == 1 )? 'active show' : ''; ?>" id="<?php echo implode("", explode(" ", $key)); ?>-classic" role="tabpanel" aria-labelledby="<?php echo implode("", explode(" ", $key)); ?>-tab-classic">
			<div class="container">
				<div class="row menu_inner">
					<div class="col-lg-5" style="margin-bottom: 20px;">
						<div class="menu_list">
							<!-- <h4 style="text-decoration: underline;margin-bottom: 10px;"><?php echo $key?></h4> -->
							<ul class="list">
								<?php
								foreach ($row as $itemkey => $itemvalue) {
								?>

								
								<li>
									<div class="plus" style="display:flex;">
										<img src="<?php echo $itemvalue['itemImage'] ?>" alt="image" class="food-img" />
										<h4 style="margin-top: 5px; font-weight:600;"><?php echo $itemvalue['name'] ?>
											<?php if(!empty($itemvalue['offerPrice'])){ ?>
												<br>
												<span style="text-decoration: line-through;">$<?php echo $itemvalue['price'] ?>/-</span>
												<span style="position: absolute;right: 10px;padding-left:10px;">$<?php echo $itemvalue['offerPrice'] ?>/-</span>
											<?php }else{ ?>
												<br>
												<span style="position: absolute;right: 10px;padding-left:10px;">$<?php echo $itemvalue['price'] ?>/-</span>
											<?php } ?>
										</h4>
									</div>
									<?php
									if(!empty($itemvalue['description'])){
										?>
										<p>(<?php echo $itemvalue['description'] ?>)</p>
										<?php
									}
									
									?>
									<?php
									if($_SESSION['user']['user_info']['isRoot'] == 'true'){
									?>
									<div style="margin-left: 20px; float: right;">
										<a class="minus btn-small btn" style="background-color:#2cb67d;color:white;" href="#" id="<?php echo $itemvalue['id']; ?>-sub-btn"><i class="fa fa-minus minusplus"></i></a>
										<span class="label" style="font-size:16px;font-weight: 600;color: black;" id="<?php echo $itemvalue['id']; ?>-item-count"><?php echo (!empty($cartItems[$itemvalue['name']]))? $cartItems[$itemvalue['name']]['itemCount'] : 0; ?></span>
										<a class="plus btn-small btn" style="background-color:#2cb67d;color:white;" href="#" id="<?php echo $itemvalue['id']; ?>-add-btn"><i class="fa fa-plus minusplus"></i></a>
									</div>
									<?php
									}
									?>
								</li>
								<hr>
									<?php
								}
								?>
								
							</ul>
						</div>
					</div>
				</div>
			</div>
		  </div>
		<?php

		}
		
		?>
		</div>
  </div>

	<?php
	if($_SESSION['user']['user_info']['isRoot'] == "true"){
		?>
		
	<div class="center" style="margin:0 auto;text-align: center;align-content: center;">
		<a type="submit" href="./showOrder.php" class="btn btn-lg fixedBtn" style="background-color:#ef4565;color:white;font-weight:700;position: fixed;bottom: 0px;
		right: 0px; left: 0; ">
			Place Order
		</a>
	</div>
		<?php
	}
	?>
	<br> <br>

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
	<script src="js/mail-script.js"></script>
	<script src="js/menu.js"></script>
	<!--gmaps Js-->
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCjCGmQ0Uq4exrzdcL6rvxywDDOvfAu6eE"></script>
	<script src="js/gmaps.min.js"></script>
	<script src="js/theme.js"></script>
	<script>
		$(document).ready(function(){
		<?php
			foreach ($menuItems as $key =>$value){
				foreach ($value as $itemKey => $itemValue) {
					?>
					$("#<?php echo $itemValue['id'];?>-add-btn").click(function(){
						$.post("actions/add-to-orders.php", {
							itemName: "<?php echo $itemValue['name'] ?>",
							changeType: "add"
						}, function(data, status){
							console.log(`Data: ${data}\nStatus: ${status}`);
						});
					});
					$("#<?php echo $itemValue['id'] ?>-sub-btn").click(function(){
						$.post("actions/add-to-orders.php", {
							itemName: "<?php echo $itemValue['name'] ?>",
							changeType: "sub"
						}, function(data, status){
							console.log(`Data: ${data}\nStatus: ${status}`);
						});
					});
					<?php
				}
			}
		?>
		});
	</script>
</body>

</html>