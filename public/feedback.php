<?php

require_once "php/classes/UserHandler.php";

$userHandler = new UserHandler();
if(isset($_POST['submit'])){
    $restaurantRating = $_POST['rate'];
    $serviceRating = $_POST['rate2'];
    $restaurantReview = $_POST['review1'];
    $serviceReview = $_POST['review2'];
    if($userHandler->getUserFeedback($restaurantRating, $serviceRating, $restaurantReview, $serviceReview)){
        header("Location: /thankyou");
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
        *{
            margin: 0;
            padding: 0;
        }
		body{
			background-color:#fffffe;
		}
		h1,h2,h3,h4,h5,h6,body .menu_list p,button, span {
			font-family: 'Quicksand', sans-serif;
		}
		p {
			color: #5f6c7b;
		}
		body,input, button {
            font-family: 'Quicksand', sans-serif;
            /* font-weight: 400; */
         }
        h4{
            font-weight: bold;
            padding:0 20px;
        }
         
		h6 {
            font-size:16px;
			font-weight: bold;
		}
    </style>
    <script type="text/javascript"> 
        window.history.forward(); 
        function noBack() { 
            window.history.forward(); 
        } 
    </script>
</head>

<body>
    <form method="post">
        <div class="text-center" style="margin-top:50px;">
            <h4 class="text-primary">Thank you for dining with us!</h4>
            <h6 style="margin-top:40px;">Please rate the restaurant</h6>
            <div class="rate">
                <input type="radio" id="star5" name="rate" value="5" />
                <label for="star5" title="text">5 stars</label>
                <input type="radio" id="star4" name="rate" value="4" />
                <label for="star4" title="text">4 stars</label>
                <input type="radio" id="star3" name="rate" value="3" />
                <label for="star3" title="text">3 stars</label>
                <input type="radio" id="star2" name="rate" value="2" />
                <label for="star2" title="text">2 stars</label>
                <input type="radio" id="star1" name="rate" value="1" />
                <label for="star1" title="text">1 star</label>
            </div>
            <div class="form-group" style="width:90%;margin:0 auto;">
                <textarea class="form-control" placeholder="Tell us about your experience!" name="review1" id="exampleFormControlTextarea1" rows="2"></textarea>
            </div>
        </div>

        <div class="text-center" style="margin-top:50px;">
            <h6>Please rate our digital menu</h6>
            <div class="rate2">
                <input type="radio" id="star-two-5" name="rate2" value="5" />
                <label for="star-two-5" title="text">5 stars</label>
                <input type="radio" id="star-two-4" name="rate2" value="4" />
                <label for="star-two-4" title="text">4 stars</label>
                <input type="radio" id="star-two-3" name="rate2" value="3" />
                <label for="star-two-3" title="text">3 stars</label>
                <input type="radio" id="star-two-2" name="rate2" value="2" />
                <label for="star-two-2" title="text">2 stars</label>
                <input type="radio" id="star-two-1" name="rate2" value="1" />
                <label for="star-two-1" title="text">1 star</label>
            </div>
            <div class="form-group" style="width:90%;margin:0 auto;">
                <textarea class="form-control" placeholder="We would love to hear from you!" name="review2" id="exampleFormControlTextarea1" rows="2"></textarea>
            </div>
        </div>
        <div style="margin: 30px 18px 20px 18px;">
            <input type="submit" value="submit" name="submit" class="btn btn-primary btn-block">
        </div>
    </form>
</body>


</html>