<!-- <?php
$access_to = 4;
require_once "php/api/db/db.php";
require_once "auth.php";
$title = "Payments List";
require_once "php/includes/header.php";

$q = $db->query("SELECT order_info.*, restaurant_info.restaurant_name FROM order_info
                LEFT JOIN restaurant_info ON restaurant_info.id = order_info.restaurant_id
                WHERE order_status = '5' ORDER BY order_info.id DESC") or die(mysqli_error($db));

?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid">
                        <h1 class="mt-4"><?php echo $title; ?></h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item active"><?php echo $title; ?></li>
                        </ol>
                        <div class="card mb-4">
                            <div class="card-body">DataTables is a third party plugin that is used to generate the demo table below. For more information about DataTables, please visit the <a target="_blank" href="https://datatables.net/">official DataTables documentation</a>.</div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header"><i class="fas fa-table mr-1"></i><?php echo $title; ?></div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Order ID</th>
                                                <th>Order From</th>
                                                <th>Total Price</th>
                                                <th>Completed</th>
                                                <th>View</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>#</th>
                                                <th>Order ID</th>
                                                <th>Order From</th>
                                                <th>Total Price</th>
                                                <th>Completed</th>
                                                <th>View</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>

                                        <?php
                                            $i = 0;
                                            while($row = $q->fetch_assoc()){
                                                ?>
                                            <tr>
                                                <td><?php echo ++$i; ?></td>
                                                <td><a href="#"><?php echo $row['order_id']; ?></a></td>
                                                <td><a href="#"><?php echo$row['restaurant_name']; ?></a></td>
                                                <td>$<?php echo $row['total_price']; ?>/-</td>
                                                <td><a onClick="javascript: return confirm('Are you sure?\nClick Ok to prodceed');" href='actions/complete-payment.php?order_id=<?php echo $row['order_id']; ?>&order_status=3' class="btn btn-success" id="complete-payment">Completed</a></td>
                                                <td><a href="#" class="btn btn-primary">View</a></td>
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
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Your Website 2019</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.4.1.min.js" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/datatables-demo.js"></script>
    </body>
</html> -->
