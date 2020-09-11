<?php

$title = "Orders List";
require_once "header.php";
require_once "php/classes/admin/orderHandler.php";
$orderHandler = new OrderHandler();

$orders = $orderHandler->getOrdersList();

?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid">
                        <h1 class="mt-5"><?= $title; ?></h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active"><?= $title; ?></li>
                        </ol>
                        <div class="card mb-4">
                            <div class="card-header"><i class="fas fa-table mr-1"></i><?= $title; ?>
                    <button class="btn btn-warning" id="downloadBtn"><i class="fas fa-download mr-1">
                    </i> Download Excel</button></div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Order ID</th>
                                                <th>Note</th>
                                                <th>Total Price</th>
                                                <th>Total Price(with Tax)</th>
                                                <th>time</th>
                                                <th class="exclude">view</th>
                                            </tr>
                                        </thead>
                                        <tfoot class="exclude">
                                            <tr>
                                                <th>#</th>
                                                <th>Order ID</th>
                                                <th>Note</th>
                                                <th>Total Price</th>
                                                <th>Total Price(with Tax)</th>
                                                <th>time</th>
                                                <th class="exclude">view</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            <?php
                                            $i = 0;
                                                foreach ($orders as $key => $order) {
                                                    ?>
                                                    <tr>
                                                        <td><?= ++$i; ?></td>
                                                        <td><?= $order['orderId'] ?></td>
                                                        <td><?= $order['note'] ?></td>
                                                        <td>$<?= $order['totalPrice'] ?>/-</td>
                                                        <td>$<?= $order['totalPriceWithTax']?>/-</td>
                                                        <td><?php 
                                                            $time = strtotime($order['time']);
                                                            $newformat = date('d M Y H:m A', $time);
                                                            echo $newformat; ?></td>
                                                        <td><a href="/order-info/<?= $order['orderId'] ?>" class="btn btn-primary">View Order</a></td>
                                                    </tr>
                                                    <?php
                                                }
                                            ?>
                                        </tbody>
                                    </table>
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
        <script src="https://cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>
        <script>
        
        $("#downloadBtn").click(function(){
                $("#dataTable").table2excel({
                    exclude: ".exclude",
                    name: "<?php echo $title; ?>",
                    filename: "<?php echo $title; ?>.xls", // do include extension
                    preserveColors: false // set to true if you want background colors and font colors preserved
                });
            });
        </script>
    </body>
</html>
