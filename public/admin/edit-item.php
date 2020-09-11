<?php
$access_to = 3;
require_once "php/classes/admin/itemHandler.php";
require_once "php/classes/admin/othersHandler.php";

$item_code = $router->params[0];

$itemHandler = new ItemHandler();
$otherHandler = new OthersHandler();
// print_r($_POST);
// die();0
if(isset($_POST['submit'])){

    $item_name = $_POST['item_name'];
    $item_des = $_POST['item_description'];
    $original_price = $_POST['original_price'];
    $sale_price = $_POST['sale_price'];
    $item_quantity = $_POST['item_quantity'];
    $category = $_POST['category'];
    $itemStatus = $_POST['itemStatus'];
    $imgName = $_POST['oldImage'];
    $image = $_FILES['image'];
    $status = "";
    $msg = "";
    if(
        !empty($item_name) &&
        !empty($original_price) &&
        !empty($category)
    ){
        print_r($image);
        if(!empty($image['name'])){
            $imgName = "img/product_images/" . uniqid("IMG").$image['name'];
        }
        // die("Image: " . $imgName);
        $q = $itemHandler->updateItemInfo($item_code, $item_name, $item_des, $original_price, $sale_price, $imgName, $category, $itemStatus);
        
        if($q[0]){
            move_uploaded_file($image['tmp_name'], $imgName);
            $status = "success";
            $msg = "New item has been successfully Edited.";
        }else{
            $status = "danger";
            $msg = "There was an error, try again later.";
            $msg .= "<br>" . $q4[1];
        }
    }
    // echo json_encode(["status" => $status, "msg" => $msg]);
}


$product_info = $itemHandler->getItemInfo($item_code)[0];
$categories = $otherHandler->getFromTable('menu_categories');
// $itemStatuses = $otherHandler->getFromTable('item_status');
if(!isset($router->params[0]) || empty($router->params[0])){
    header("Location: /menu");
}
$title = "Edit Product";
require_once "header.php";

?>

<div id="layoutSidenav_content">
    <main>
        <br>
        <br>
        <br>
        <div class="container-fluid">
            <h1 class="mt-5"><?php echo $title; ?></h1>
            <div class="button" style="float: right">
                <form method="post">
                    <button type="submit" class="btn btn-danger" id="delete" name="delete-item">Delete Item</button>
                </form>
            </div>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/menu">Menu</a></li>
                <li class="breadcrumb-item"><a href="/product-info/<?php echo $product_code ?>">Product Info (<?php echo $product_info['name'] ?>)</a></li>
                <li class="breadcrumb-item active"><?php echo $title; ?></li>
            </ol>
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table mr-1">
                    </i>
                    <?php echo $title; ?>
                <div class="card-body">
                <div id="formctrl">
                                          
                <style>
                    .form-group{
                        padding:5px;
                    }
                </style>
                <div style="padding:15px;"><h1>Edit Product</h1></div>                           
                <form style="padding: 5px;" id="add_products_form" method="post" enctype="multipart/form-data">
                    <?php if(!empty($status)){ ?>
                    <div id="status">
                        <div class="alert alert-dismissible alert-<?php echo $status ?>">
                            <p><?php echo $msg ?></p>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="form-group" >
                        <input class="form-control py-4" type="text" id="item_name" name="item_name" placeholder="Enter Product Name" required value="<?php echo $product_info['name'] ?>" />
                    </div>
                    <div class="form-group" >
                        <textarea class="form-control py-4" type="text" id="item_description" name="item_description" placeholder="Enter Product Description" ><?php echo $product_info['description'] ?></textarea>
                    </div>
                    <div class="form-group" >
                        <input class="form-control py-4" type="text" id="original_price" name="original_price" placeholder="Enter Original Price" required value="<?php echo $product_info['price'] ?>" />
                    </div>
                    <div class="form-group" >
                        <input class="form-control py-4" type="text" id="sale_price" name="sale_price" placeholder="Enter Offer Price" value="<?php echo $product_info['offerPrice'] ?>" />
                    </div>
                    <div class="row">
                        <div class="col">
                            <img width="150" src="<?= $product_info['itemImage'] ?>" alt="<?= $product_info['name'] ?>">
                        </div>
                        <div class="form-group col">
                            <input type="hidden" name="oldImage" value="<?= $product_info['itemImage'] ?>">
                            <input type="file" name="image" id="image" accept="image/*" placeholder="Change Image">
                        </div>
                    </div>
                    <div class="form-group" >
                        <!-- <input class="form-control py-4" type="text" id="product_pkg_type" placeholder="Enter Package type" /> -->
                        <select id="category" name="category" class="form-control" required >
                            <option value="">Category</option>
                            <?php 
                            
                                foreach($categories as $row){
                                    ?>
                                    <option value="<?php echo $row['id'] ?>" <?php echo (strval($row['id']) == strval($product_info['category']))?  "selected" : ""; ?>><?php echo $row['name'] ?></option>
                                    <?php
                                }
                            ?>
                        </select>
                    </div>
                    <div class="form-group" >
                        <!-- <input class="form-control py-4" type="text" id="product_pkg_type" placeholder="Enter Package type" /> -->
                        <select id="itemStatus" name="itemStatus" class="form-control" >
                            <option value="">Item Status</option>
                            <?php 
                            
                                foreach($itemStatuses as $itemStatus){
                                    ?>
                                    <option value="<?php echo $itemStatus['name'] ?>" <?php echo (strval($itemStatus['name']) == strval($product_info['status']))?  "selected" : ""; ?>><?php echo $itemStatus['name'] ?></option>
                                    <?php
                                }
                            ?>
                        </select>
                    </div>
                    <div id="status">
                    </div>
                    <div class="form-group">
                        <input type="submit" name="submit" class="btn btn-primary btn-lg btn-block" id="submit" value="Save Changes">
                    </div>
                    </form>
                </div>
            </div>
            </div>
            </div>
        </div>
    </main>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.4.1.min.js" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/datatables-demo.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/babel-polyfill/6.26.0/polyfill.js"></script>
        <script src="https://cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.1/dist/jquery.validate.min.js"></script>
        <script>
            $("#downloadBtn").click(function(){
                $("#tableData").table2excel({
                    name: "<?php echo $title; ?>",
                    filename: "<?php echo $title; ?>.xls", // do include extension
                    preserveColors: false // set to true if you want background colors and font colors preserved
                });
            });
        </script>

    </body>
</html>