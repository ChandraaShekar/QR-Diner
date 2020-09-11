<?php
if(!isset($router->params[0]) || $router->params[0] == ""){
    header("Location: items.php");
}
$item_code = $router->params[0];
require_once "php/classes/admin/itemHandler.php";
$title = "Product Info";
require_once "header.php";

$itemHandler = new ItemHandler();

$product_info = $itemHandler->getItemInfo($item_code)[0];


?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h1 class="mt-4"><?php echo $title; ?></h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="products.php">Products</a></li>
                <li class="breadcrumb-item active"><?php echo $title; ?></li>
            </ol>
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table mr-1">
                    </i>
                    <?php echo $title; ?>
                    <button class="btn btn-warning" id="downloadBtn"><i class="fas fa-download mr-1">
                    </i> Download Excel</button>
                    
                    <a href="/edit-item/<?php echo $item_code ?>" class="btn btn-primary" id="downloadBtn" style="float: right;"><i class="fas fa-edit mr-1">
                    </i>Edit</a>
                    </div>
                <div class="card-body">
                    <table class="table table-bordered" id="tableData">
                        <tbody>
                            <tr>
                                <th>Item Name</th>
                                <td><?php echo $product_info['name']; ?></td>
                            </tr>
                            <tr>
                                <th>Item Description</th>
                                <td width="500"><?php echo $product_info['description']; ?></td>
                            </tr>
                            <tr>
                                <th>Item Image</th>
                                <td width="500"><img width="150" src="<?= $product_info['itemImage']; ?>" alt="<?= $product_info['name']; ?>"></td>
                            </tr>
                            <tr>
                                <th>Original Price</th>
                                <td>$<?php echo $product_info['price']; ?>/-</td>
                            </tr>
                            <tr>
                                <th>Offer Price</th>
                                <td><?= !empty($product_info['offerPrice'])? "$" . $product_info['offerPrice'] . "/-" : 'None' ?></td>
                            </tr>
                            <tr>
                                <th>Category</th>
                                <td><?php echo $product_info['categoryName']; ?></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td><?php echo $product_info['status']; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>
</div>
<iframe id="txtArea1" style="display:none"></iframe>

<script src="https://code.jquery.com/jquery-3.4.1.min.js" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/datatables-demo.js"></script>
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
        </script>

    </body>
</html>