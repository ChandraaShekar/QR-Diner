<?php
session_start();
require_once "auth.php";
if(!isset($_GET['uid']) || $_GET['uid'] == ""){
    header("Location: users.php");
}
$user_id = $_GET['uid'];
$title = "User Info";
require_once "php/includes/header.php";
require_once "classes/userHandler.php";
$userHandler = new UserHandler();
$user_info = $userHandler->getMoreInfo($user_id);

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
                    <table class="table table-bordered" id="tableData">
                        <tbody>
                            <tr>
                                <th>Name</th>
                                <td><?php echo $user_info['common_info']['name']; ?></td>
                            </tr>
                            <tr>
                                <th>Mobile Number</th>
                                <td>+<?php echo $user_info['common_info']['phone']; ?></td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td><?php echo $user_info['common_info']['email']; ?></td>
                            </tr>
                            <tr>
                                <th>Account Created on</th>
                                <td><?php 
                                $time = strtotime($user_info['common_info']['time']);
                                $newformat = date('d M Y H:m A', $time);
                                echo $newformat; ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-bordered" id="tableData">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Order Id</th>
                                <th>Total Amount</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                            
                        <tbody>
                            <?php
                            
                            foreach ($user_info['orderInfo'] as $key => $order) {
                                ?>
                                <tr>
                                    <td><?php echo $key+1; ?></td>
                                    <td><a href="order-info.php?orderId=<?php echo $order['orderId'] ?>"><?php echo $order['orderId'] ?></a></td>
                                    <td>$<?php echo $order['totalAmount'] ?>/-</td>
                                    <td><?php 
                                        $time = strtotime($order['time']);
                                        $newformat = date('d M Y H:m A', $time);
                                        echo $newformat; ?>
                                    </td>
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
        </script>

    </body>
</html>