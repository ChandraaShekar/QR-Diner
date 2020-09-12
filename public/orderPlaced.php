<?php
if((!isset($_SESSION['user']) && !empty($_SESSION['user']))){
	header("Location: logout.php");
	die();
}

require_once "php/classes/MenuHandler.php";
require_once "php/classes/OrderHandler.php";
// require_once "php/paymentConfig.php";

$menuHandler = new MenuHandler();
$orderHandler = new OrderHandler();
$_SESSION['orders'] = [];
// print_r($_SESSION);
$orderList = $orderHandler->getAllOrderList();
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
	<link rel="stylesheet" href="css/bootstrap.css">
	<link rel="stylesheet" href="vendors/linericon/style.css">
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<link rel="stylesheet" href="css/magnific-popup.css">
	<link rel="stylesheet" href="vendors/owl-carousel/owl.carousel.min.css">
	<link rel="stylesheet" href="vendors/lightbox/simpleLightbox.css">
	<link rel="stylesheet" href="vendors/nice-select/css/nice-select.css">
	<link rel="stylesheet" href="vendors/jquery-ui/jquery-ui.css">
    <link rel="stylesheet" href="vendors/animate-css/animate.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
	<!-- main css -->
    <link rel="stylesheet" href="css/style.css">
    <style>
        .table-row , .table-head, h6{
            font-family: "Quicksand", sans-serif;
            font-size: 16px;
            font-weight: 600;
            color: #222222; }
         .btn, a {
            font-family: "Quicksand", sans-serif;
            font-size: 14px;
            font-weight: 600;
        }
        .footer-text,h5 {
            font-family: "Quicksand", sans-serif; 
        }
        .headings h3 {
            margin-top: 50px;
            font-family: "Quicksand", sans-serif;
            font-size:20px; 
            font-weight: 600;
        }
      
    </style>
</head>
<body>
<header>
		<div class="logo d-flex justify-content-center" style="margin-top:30px;">
			<img src="img/logo.png" alt="">
		</div>
	</header>

	<div class="container">
        <div class="headings">
			<h3 class="text-center" id="orderMsg">Loading...</h3>
            <h3 class="text-center">Order Status: <span id="status"><strong>Loading...</strong></span></h3>
			<h5 class="text-center">Sit back and relax!</h5>
			<!-- <h5 class="text-center"></h5> -->
        </div>
        <div class="section-top-border">
			<h3 class="mb-20 title_color" style="text-align: center;">Your Order</h3>
			<div class="progress-table-wrap">
				<div class="progress-table">
					<?php
					$x = 0;
					$totalOrdersPrice = 0;
					foreach ($orderList as $key => $order) {	
						?>
						
						<div class="title"><h3>Order #<?php echo ++$x; ?></h3></div>
						<div class="table-head">
							<div class="serial">#</div>
							<div class="country">Items</div>
							<div class="percentage">Price</div>
						</div>
						
						<?php
						$i = 0;
						$totalPrice = 0;
						$orderInfo = $order['orderInfo'];
						foreach ($order['orderItems'] as $key => $orderItems) {
							$price = $orderItems['itemPrice'];
							?>
							<div class="table-row">
								<div class="serial"><?php echo ++$i; ?></div>
								<div class="country"><?php echo $orderItems['name'] ?></div>
								<div class="percentage">$<?php echo $price; ?> x <?php echo $orderItems['itemCount'] ?> <br> $<?php echo $orderItems['totalPrice']; ?> </div>
							</div>
						<?php
						$totalPrice += $itemTotalPrice;
						}
						?>
					<div class="table-row">
						<div class="serial"></div>
						<div class="country">
							Sub Total: <br>
							Service Tax - <?= $tax ?>%<br>
							Total:
						</div>
						<div class="percentage" style="text-align:right;">
							$<?php echo $orderInfo['totalPrice'] ?> <br>
							$<?php echo $orderInfo['totalPriceWithTax']-$orderInfo['totalPrice'] ?>  <br>
							$<?php echo $orderInfo['totalPriceWithTax']; ?>
						</div>
					</div>
						<?php
						$totalOrdersPrice += $orderInfo['totalPriceWithTax'];
					}
					?>
				</div>
			</div>
			<hr>
			<div style="display: flex;flex-direction: row;justify-content: space-around;align-items: center;margin-bottom: 20px;">
				<a class="btn btn-warning" href="/order-again">Order More</a>
				<a class="btn btn-primary" href="/feedback">Done</a>
			</div>
		</div>
	</div>
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
			$.get("/get-order-status", function(data){
				var x = JSON.parse(data);
				var orderMsg = "";
				$("#status").html(`<strong style="${(x.tableStatus == "Payment Pending") ? 'color: red;' : (x.tableStatus == "Payment Successful")? 'color: green;' : 'color: black;'}">${x.tableStatus}</strong>`);
				if(x.tableStatus == "Reviewing"){
					orderMsg = "Your Order is being Reviewed, Please Wait.";
				}else if(x.tableStatus == "Declined"){
					orderMsg = "<span style='color: red;'>Your Order has been Declined. You can Click on Order More below to Order Again.</span>";
				}else{
					orderMsg = "<span style='color: green;'>Your Order has been Successfully Placed.</span>";
				}
				$("#orderMsg").html(orderMsg);
			});
		}, 1000);
	</script>
</body>
</html>

<!-- 

<form action="php/transaction.php" method="post">
<?php //$_SESSION['payableAmount'] = $totalOrdersPrice * 100; ?>
					<input type="hidden" value="<?php //echo $totalOrdersPrice * 100; ?>" name="paymentAmount">
					<script src="https://checkout.stripe.com/checkout.js"
						class="stripe-button"
						data-key="<?php //echo $publishKey ?>"
						data-amount="<?php //echo $totalOrdersPrice * 100; ?>"
						data-name="Unicorn Restaurant"
						data-description="Unicron Restaurant Bill Payment"
						data-image=""
						data-currency="usd"
					></script>
				</form>
 -->