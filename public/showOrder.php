<?php
require_once "php/classes/MenuHandler.php";
require_once "php/classes/OrderHandler.php";
session_start();
if((!isset($_SESSION['user']) && !empty($_SESSION['user']))){
	header('Location: /menu');
	die("");
}
$menuHandler = new MenuHandler();
$orderHandler = new OrderHandler();

$tax = $orderHandler->getTax();

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
	<link rel="stylesheet" href="/css/bootstrap.css">
	<link rel="stylesheet" href="/vendors/linericon/style.css">
	<link rel="stylesheet" href="/css/font-awesome.min.css">
	<link rel="stylesheet" href="/css/magnific-popup.css">
	<link rel="stylesheet" href="/vendors/owl-carousel/owl.carousel.min.css">
	<link rel="stylesheet" href="/vendors/lightbox/simpleLightbox.css">
	<link rel="stylesheet" href="/vendors/nice-select/css/nice-select.css">
	<link rel="stylesheet" href="/vendors/jquery-ui/jquery-ui.css">
    <link rel="stylesheet" href="/vendors/animate-css/animate.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
	<!-- main css -->
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .table-row , .table-head, .serial, .country, th, h6{
            font-family: "Quicksand", sans-serif;
            font-size: 16px;
            font-weight: 600;
			color: #222222; 
		}
			
		.headconfirm {
			font-family: "Quicksand", sans-serif;
            font-size: 18px;
            font-weight: 600;
			color: #222222;
		}
			
         .btn {
            font-family: "Quicksand", sans-serif;
            font-size: 16px;
            font-weight: 600;
        }
        .footer-text {
            font-family: "Quicksand", sans-serif; 
        }
	  
		textarea {
			font-family: "Quicksand", sans-serif;
		}
    </style>
</head>

<body>

	<!--================ Start Header Menu Area =================-->
	<header>
		<div class="logo d-flex justify-content-center" style="margin-top:30px;">
			<img src="img/logo.png" alt="">
		</div>
	</header>	

	<div class="site-main" style="margin-top: -30px;">
		<!--================ Start Home Banner Area =================-->
				<div class="section-top-border">
					<h3 class="mb-20 headconfirm" style="text-align: center; ">Confirm Order</h3>
					<div class="progress-table-wrap">
						<table class="progress-table">
							<tr class="table-head">
								<th class="serial">#</th>
								<th class="country">Items</th>
								<th class="price" style="font-weight: 500;">Price</th>
							</tr>
							<tbody id="bill-body">
								<!-- <center><img src="https://www.menuelsharkia.com/static/images/loading-foods.gif" width="100" height="100" alt="Loading..."></center> -->
								<tr>
									<td>
										<center><img src="https://www.menuelsharkia.com/static/images/loading-foods.gif" width="100" height="100"alt="Loading..."></center>
									</td>
								</tr>
							</tbody>
						</table>
                    </div>
                    <hr>
				</div>
				<?php if($_SESSION['user']['user_info']['isRoot'] == "true"){ ?>
				<form action="/place-order" method="post">
					<div class="form-group" style="width:90%;margin:20px auto;">
						<textarea class="form-control" placeholder="Add Notes" name="order-note" id="exampleFormControlTextarea1" rows="2"></textarea>
					</div>
					<div style="display: flex;flex-direction: row;justify-content: center;align-items: center;margin-top: 10px;">
						<a class="btn btn-warning" href="/menu">EDIT ORDER</a>
						<input class="btn" type="submit" name="submit" style="background-color:#ef4565;color:white;margin-left: 20px;" value="Confirm Order">	
                	</div>
				</form>
				<?php }else{
					?>
					<div style="display: flex;flex-direction: row;justify-content: center;align-items: center;margin-top: 10px;">
						<a href="/menu" class="btn btn-warning">Go Back</a>
					</div>
					<?php
				} ?>
		<!--================ Start Footer Area =================-->
		<footer class=" overlay">
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
	<script src="/js/jquery-3.2.1.min.js"></script>
	<script src="/js/popper.js"></script>
	<script src="/js/bootstrap.min.js"></script>
	<script src="/js/stellar.js"></script>
	<script src="/js/jquery.magnific-popup.min.js"></script>
	<script src="/vendors/lightbox/simpleLightbox.min.js"></script>
	<script src="/vendors/nice-select/js/jquery.nice-select.min.js"></script>
	<script src="/vendors/owl-carousel/owl.carousel.min.js"></script>
	<script src="/vendors/jquery-ui/jquery-ui.js"></script>
	<script src="/js/jquery.ajaxchimp.min.js"></script>
	<script src="/vendors/counter-up/jquery.waypoints.min.js"></script>
	<script src="/vendors/counter-up/jquery.counterup.js"></script>
	<script src="/js/mail-script.js"></script>
	<!--gmaps Js-->
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCjCGmQ0Uq4exrzdcL6rvxywDDOvfAu6eE"></script>
	<script src="js/gmaps.min.js"></script>
	<script src="js/theme.js"></script>
	<script>
		setInterval(function() {
			var prevData;
			$.get("/get-orders", function(data){
				// alert(JSON.parse(data));
				// console.log(data);
				var jsonData = JSON.parse(data);
				if(prevData != data){
					prevData = data;
					$("#bill-body").html("");
					var totalPrice = 0;
					$.each(jsonData, function(key, val){
						var itemPrice = (val['offerPrice'])? val['offerPrice'] : val['price'];
						var itemTotal = itemPrice * val['itemCount'];
						$("#bill-body").append(`<tr class="table-row">
								<td class="serial">${key + 1}</td>
								<td class="country">${val['name']}</td>
								<td class="price"> ${val['itemCount']} x $${itemPrice}/- <br>$${itemTotal}/-</td>
							</tr>`);
							totalPrice += itemTotal;
					});
					$("#bill-body").append(`<tr class="table-row">
								<td class="serial"></td>
								<td class="country">Sub Total</td>
								<td class="price">$${Math.round(totalPrice * 100) / 100}/-</td>
							</tr>`);
					$("#bill-body").append(`<tr class="table-row">
								<td class="serial"></td>
								<td class="country">Service Tax (<?= $tax ?>%)</td>
								<td class="price">$${Math.round(totalPrice * (<?= $tax ?> / 100) * 100) / 100}/-</td>
							</tr>`);
					$("#bill-body").append(`<tr class="table-row">
								<td class="serial"></td>
								<td class="country">Total Price</td>
								<td class="price">$${Math.round(totalPrice * (1 + (<?= $tax ?> / 100)) * 100) / 100}/-</td>
							</tr>`);
				}
			});
		}, 1000);
	</script>
</body>

</html>