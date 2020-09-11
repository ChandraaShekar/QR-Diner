<?php
$access_to = 3;
$title = "Add Item";
require_once "php/classes/admin/othersHandler.php";
require_once "php/classes/admin/itemHandler.php";
require_once "header.php";

// require '../../';

// use Bitly\BitlyClient;

// $bitlyClient = new BitlyClient('your-access-token');

// $options = ['longUrl' => 'http://www.example.com/a-log-url-slug/'];

// $response = $bitlyClient->shorten($options);
$othersHandler = new othersHandler();
$itemHandler = new itemHandler();

if(isset($_POST['submit'])){
    $item_name = $_POST['item_name'];
    $item_des = $_POST['item_description'];
    $original_price = $_POST['original_price'];
    $sale_price = $_POST['sale_price'];
    $category = $_POST['category'];
    $image = $_FILES['itemImage'];
    $status = "";
    $msg = "";
    if(
        !empty($item_name) &&
        !empty($original_price) &&
        !empty($category)
    ){
        $imgName = "img/product_images/" . uniqid("IMG").$image['name'];
        // $image = uniqid("IMG").$image['name'];
        $q4 = $itemHandler->addNewItem($item_name, $item_des, $original_price, $sale_price, $imgName, $category);
        if($q4[0]){
            move_uploaded_file($image['tmp_name'], $imgName);
            $status = "success";
            $msg = "New item has been successfully added to the database.";
        }else{
            $status = "danger";
            $msg = "There was an error, try again later.";
            $msg .= "<br>" . $q4[1];
        }
    }else{
        $status = "danger";
        $msg = "There was an error, try again later.";
    }
}
$categories = $othersHandler->getFromTable('menu_categories');

?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h1 class="mt-5"><?php echo $title; ?></h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/menu">Menu</a></li>
                <li class="breadcrumb-item active"><?php echo $title; ?></li>
            </ol>
            <div class="card mb-4">
                <div class="card-body">
                <div id="formctrl">
                    
                <form id="add_products_form" method="post" enctype="multipart/form-data">
                <?php if(!empty($status)){ ?>
                    <div id="status">
                    <div class="alert alert-dismissible alert-<?php echo $status ?>">
                         <p><?php echo $msg ?></p>
                    </div>
                    </div>
                <?php } ?>
                    <div class="form-group" >
                        <input class="form-control py-4" type="text" id="item_name" name="item_name" placeholder="Enter Product Name" required />
                    </div>
                    <div class="form-group" >
                        <textarea class="form-control py-4" type="text" id="item_description" name="item_description" placeholder="Enter Product Description (Optional)" ></textarea>
                    </div>
                    <div class="form-group" >
                        <input class="form-control py-4" type="text" id="original_price" name="original_price" placeholder="Enter Original Price" required  />
                    </div>
                    <div class="form-group" >
                        <input class="form-control py-4" type="text" id="sale_price" name="sale_price" placeholder="Enter Offer Price (Optional)" />
                    </div>
                    <div class="form-group">
                        <input type="file" name="itemImage" id="">
                    </div>
                    <div class="form-group">
                        <select name="category" id="category" class="form-control py-2" required>
                            <option>Category</option>
                            <?php
                            foreach ($categories as $category){
                                ?>
                                <option value="<?php echo $category['id'] ?>"><?php echo $category['name'] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="submit" class="btn btn-primary btn-lg btn-block" id="submit" value="Add Product">
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
        <script src="/js/admin/scripts.js"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
        <script src="/public/admin/assets/demo/datatables-demo.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/babel-polyfill/6.26.0/polyfill.js"></script>
        <script src="https://cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>
        <script>
            $("#downloadBtn").click(function(){
                $("#tableData").table2excel({
                    name: "<?php echo $title; ?>",
                    filename: "<?php echo $title; ?>.xls", // do include extension
                    preserveColors: false // set to true if you want background colors and font colors preserved
                });
            });

            // $("#add_products_form").validate({
            //     rules: {
            //         product_code: "required",
            //         product_name: "required",
            //         product_description: "required",
            //         original_price: "required",
            //         sale_price: "required",
            //         product_weight: "required",
            //         product_no_items: "required",
            //         pkg_type: "required",
            //         inventory_stock: "required",
            //         category: "required",
            //         product_image: "required",
            //         product_barcode: "required"
            //     },
            //     messages: {
            //         product_code: "product code is mandatory"
            //     }
            // });

            // $("#submit").click(function(){
                
            // var productCode = $("#product_code").val();
            // var productName = $("#product_name").val();
            // var productDes = $("#product_des").val();
            // var originalPrice = $("#original_price").val();
            // var salePrice = $("#Sale_price").val();
            // var pkgWeight = $("#product_weight").val();
            // var pkgQuantity = $("#product_no_items").val();
            // var inventoryStock = $("#inventory_stock").val();
            // var productBarcode = $("#product_barcode").val();
            // var productImage = $("#product_image").val();
            // var categroy = $("#category").val();
            // var formData = {
            //                 "product_code": productCode,
            //                 "product_name": productName,
            //                 "product_des": productDes,
            //                 "original_price": originalPrice,
            //                 "sale_price": salePrice,
            //                 "pkg_weight": pkgWeight,
            //                 "pkg_quantity": pkgQuantity,
            //                 "inventory_stock": inventoryStock,
            //                 "product_barcode": productBarcode,
            //                 "product_image": productImage,
            //                 "category": category
            //             };
            //         console.log(formData);
            //         $.post(
            //             "php/api/add-product.php",
            //             formData,
            //             function(data){
            //                 // var x = JSON.parse(data);
            //                 //     $("#status").html(`
            //                 //         <div class="alert alert-dismissible alert-${data.status}">
            //                 //             <p>${data.msg}</p>
            //                 //         </div>
            //                 //     `);
            //                 console.log(data);
            //             }
            //         );

            // });
        </script>

    </body>
</html>