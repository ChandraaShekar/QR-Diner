<?php
if(!isset($router->params[0]) || empty($router->params[0])){
    header("Location: /orders");
}
$orderId = $router->params[0];
$title = "Orders Info($orderId)";
require_once "php/classes/admin/orderHandler.php";
require_once "header.php";
$orderHandler = new OrderHandler();
$order_info = $orderHandler->getOrderInfo($orderId);
$order_status = $orderHandler->getOrderStatus();

?>
<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h1 class="mt-4"><?php echo $title; ?></h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="orders.php">Orders</a></li>
                <li class="breadcrumb-item active"><?php echo $title; ?></li>
            </ol>
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table mr-1">
                    </i>
                    <?php echo $title; ?>
                    <button class="btn btn-warning" id="downloadBtn"><i class="fas fa-download mr-1">
                    </i> Download Excel</button>
                    </div>
                <div class="card-body">
                    <!-- <div class="form-group">
                        <label for="order_status">Update Order Status</label>
                        <select id="order_status" class="form-control" onChange="updateOrderStatus()">
                            <?php 
                                foreach($order_status as $orderStatus){
                                    ?>
                                    <option value="<?php echo $orderStatus['name'] ?>" <?php echo $orderStatus['name'] == $order_info['order_info']['orderStatus'] ? "selected" : ""; ?> ><?php echo $orderStatus['name'] ?></option>
                                    <?php
                                }
                            ?>
                        </select>
                    </div> -->
                    <table class="table table-bordered" id="tableData">
                        <tbody>
                            <tr>
                                <th>Order Id</th>
                                <td><?php echo $order_info['order_info']['orderId']; ?></td>
                            </tr>
                            <tr>
                                <th>Total Price</th>
                                <td>$<?php echo $order_info['order_info']['totalPrice']; ?>/-</td>
                            </tr>
                            <tr>
                                <th>Total Price (inc Tax)</th>
                                <td>$<?php echo $order_info['order_info']['totalPriceWithTax'] * 1.1; ?>/-</td>
                            </tr>
                            <tr>
                                <th>Note</th>
                                <td data-toggle="tooltip" data-placement="top" title="<?php echo $order_info['order_info']['note']; ?>"><?php echo $order_info['order_info']['note']; ?></td>
                            </tr>
                            <tr>
                                <th>Payment Status</th>
                                <td data-toggle="tooltip" data-placement="top"><?php echo $order_info['order_info']['paymentStatus']; ?></td>
                            </tr>
                            <tr>
                                <th>Table Number</th>
                                <td><?php echo $order_info['order_info']['tableNumber']; ?></td>
                            </tr>
                            <!-- Order items -->
                            <tr>
                                <th>#</th>
                                <th>Item Name</th>
                                <th>Price</th>
                                <th>Original Price</th>
                                <th>Quantity</th>
                                <th>Total price</th>
                            </tr>
                            <?php 
                                $i = 0;
                                foreach($order_info['order_items'] as $item){
                                    $price = (!empty($item['offerPrice']))? $item['offerPrice'] : $item['price'];
                                    // $offerPrice = ($row['sale_price'] == 0)? 0 : "$".$row['sale_price']."/-";
                                    ?>
                                    <tr>
                                        <td><?php echo ++$i; ?></td>
                                        <td><?php echo $item['name'] ?></td>
                                        <td>$<?php echo $price; ?>/-</td>
                                        <td>$<?php echo $item['price'] ?>/-</td>
                                        <td><?php echo $item['itemCount'] ?></td>
                                        <td>$<?php echo $item['itemCount'] * $price; ?>/-</td>
                                    </tr>
                                    <?php
                                }
                            ?>
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
            $(function () {
            $('[data-toggle="tooltip"]').tooltip()
            });
            function updateOrderStatus(){
                $.post(
                    "php/api/update-order-status.php",
                    {
                        order_id: '<?php echo $order_id ?>',
                        status: $("#order_status").val()
                    },
                    function(data){
                        alert(`${data}`);
                    }
                );
            }
        </script>
    </body>
</html>