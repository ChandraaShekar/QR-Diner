<?php
if(!isset($_SESSION['user']) || empty($_SESSION['user'])){
    header("Location: /not-authorized");
}

require_once "php/classes/MenuHandler.php";
require_once "php/classes/OrderHandler.php";

$menuHandler = new MenuHandler();
$orderHandler = new OrderHandler();
$menuItems = $menuHandler->getDrinks();
$cartItems = $orderHandler->getItemWithName($_SESSION['user']['user_info']['uid']);
if(count($menuItems) == 0){
    header("Location: /menu");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - QR Diner</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/welcome.css">
    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
    <style>
        body, button {
            font-family: 'Quicksand', sans-serif;
        }
    </style>
</head>
<body>
        <div class="header-img">
            <img src="http://qrdiner.com/images/black1.png" width="140px" alt="">
        </div>
        <div class="chat" style="margin-top:10px;">
            <div class="chat-container">
                <div class="chat-listcontainer">
                <ul class="chat-message-list">
                </ul>
                </div>
            </div>
            <div class="scrolling-wrapper" id="scroller" style="visibility:hidden;">
                <?php 
                foreach ($menuItems as $key => $value) {
                  ?>
                
                    <div class="card">
                        <img src="<?= $value['itemImage'] ?>" alt="Avatar" style="width:100%; max-height: 100px;">
                        <div class="container">
                            <h4><?= $value['name'] ?></h4> 
                            <h5>$<?= !empty($value['offerPrice'])? $value['offerPrice'] : $value['price'] ?>/-</h5>
                            <div class="input-group">
                                <input type="button" value="-" class="button-minus" id="<?= $value['id'];?>-sub-btn" data-field="quantity">
                                <input type="number" step="1" max="" value="<?= isset($cartItems[$value['id']]) ? $cartItems[$value['id']]['itemCount'] : 0 ?>" name="quantity" class="quantity-field" disabled>
                                <input type="button" value="+" class="button-plus" id="<?= $value['id'];?>-add-btn" data-field="quantity">
                            </div>
                        </div>
                    </div>
                  <?php
                }
                ?>
            </div>
    </div>  
    <div class="bottomBtn" id="bottom-btns" style="visibility:hidden;">
        <a href="/show-orders"><button type="button" class="block btn-1">Order</button></a>
        <hr class="new3">
        <a href="/menu"><button type="button" class="block btn-2">Jump to menu</button></a>
    </div>  
</body>
<script src="/js/welcome.js"></script>
<script>
    setTimeout(
    function(){
        document.getElementById('scroller').style.visibility = "visible";
    },5000);

    setTimeout(
    function(){
        document.getElementById('bottom-btns').style.visibility = "visible";
    },6000);

    function incrementValue(e) {
        e.preventDefault();
        var fieldName = $(e.target).data('field');
        var parent = $(e.target).closest('div');
        var currentVal = parseInt(parent.find('input[name=' + fieldName + ']').val(), 10);

        if (!isNaN(currentVal)) {
            parent.find('input[name=' + fieldName + ']').val(currentVal + 1);
        } else {
            parent.find('input[name=' + fieldName + ']').val(0);
        }
    }

    function decrementValue(e) {
        e.preventDefault();
        var fieldName = $(e.target).data('field');
        var parent = $(e.target).closest('div');
        var currentVal = parseInt(parent.find('input[name=' + fieldName + ']').val(), 10);

        if (!isNaN(currentVal) && currentVal > 0) {
            parent.find('input[name=' + fieldName + ']').val(currentVal - 1);
        } else {
            parent.find('input[name=' + fieldName + ']').val(0);
        }
    }

    $('.input-group').on('click', '.button-plus', function(e) {
        incrementValue(e);
    });

    $('.input-group').on('click', '.button-minus', function(e) {
        decrementValue(e);
    });
    
	$(document).ready(function(){
		<?php
			foreach ($menuItems as $key =>$value){
                ?>
                $("#<?= $value['id'];?>-add-btn").click(function(){
                    $.post("/add-to-orders", {
                        itemId: "<?= $value['id'] ?>",
                        changeType: "add"
                    }, function(data, status){
                        console.log(`Data: ${data}\nStatus: ${status}`);
                    });
                });
                $("#<?= $value['id'] ?>-sub-btn").click(function(){
                    $.post("/add-to-orders", {
                        itemId: "<?= $value['id'] ?>",
                        changeType: "sub"
                    }, function(data, status){
                        console.log(`Data: ${data}\nStatus: ${status}`);
                    });
                });
                <?php
            }
		?>
		});
</script>


</html>