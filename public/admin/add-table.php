<?php
$access_to = 3;
$title = "Add Table";
include "public/admin/phpqrcode/qrlib.php";
require_once "public/vendor/autoload.php";
use Bitly\BitlyClient;
require_once "php/classes/admin/othersHandler.php";
require_once "php/classes/admin/tableHandler.php";
$tableHandler = new TableHandler();
if(isset($_POST['submit'])){
    $tableNumber = $_POST['tableNumber'];
    $status = "";
    $msg = "";
    if(
        !empty($tableNumber)
    ){
        
        $tableCode = uniqid("TABLE", true);
        $text =  "https://". $router->subdomain[1] . BASE_URL .  "/new-user/$tableNumber/$tableCode";
        $bitlyClient = new BitlyClient('ff5d0b5e0035b542e915aa1676a7294cebb25d6d');
        $options = ['longUrl' => $text];
        $response = $bitlyClient->shorten($options);
        $imagesPath = "QRImages/";
        $file = $tableCode.".png";
        $filepath = $imagesPath . $file;
        $ecc = "L";
        $pixel_size = 10;
        $frame_size = 10;
        // echo "<pre>";
        // print_r($response);
        // print_r($response->data->url);
        // die();
        // $qrOptions = new QROptions([
        //     'version' => 5,
        //     'outputType' => QRCode::OUTPUT_IMAGE_PNG,

        // ]);
        // $qrCode = new QrCode();
        QRcode::png($text, $filepath, $ecc, $pixel_size, $frame_size);
        $q4 = $tableHandler->addNewTable($tableNumber, $tableCode, $text, $response->data->url);
        if($q4[0]){
            $status = "success";
            $msg = "New table has been successfully added.";
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
require_once "header.php";

?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h1 class="mt-5"><?php echo $title; ?></h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/tables">Tables</a></li>
                <li class="breadcrumb-item active"><?php echo $title; ?></li>
            </ol>
            <div class="card mb-4">
                <div class="card-body">
                <div id="formctrl">                         
                <form id="add_products_form" method="post">
                    <?php if(!empty($status)){ ?>
                    <div id="status">
                    <div class="alert alert-dismissible alert-<?php echo $status ?>">
                         <p><?php echo $msg ?></p>
                    </div>
                    <?php } ?>
                    </div>
                    <div class="form-group" >
                        <input class="form-control py-4" type="text" id="tableNumber" name="tableNumber" placeholder="Enter Table Number" required />
                    </div>
                    <div id="status">
                    </div>
                    <div class="form-group">
                        <input type="submit" name="submit" class="btn btn-primary btn-lg btn-block" id="submit" value="Add Table">
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