<!-- <?php
if(!isset($_GET['restaurant_id']) || $_GET['restaurant_id'] == ""){
    header("Location: users.php");
}
$restaurant_id = $_GET['restaurant_id'];
require_once "php/api/db/db.php";
require_once "php/auth.php";
$title = "Restaurant Info";
require_once "php/includes/header.php";

$q = $db->query("SELECT restaurant_info.*, users.name FROM restaurant_info
                LEFT JOIN users ON users.uid = restaurant_info.created_by
                WHERE restaurant_info.id = '$restaurant_id'
                ") or die(mysqli_error($db));

$restaurant_info = $q->fetch_assoc();
// print_r($user_info);
$q2 = $db->query("SELECT * FROM users WHERE restaurant_id = '$restaurant_id' AND access_right = '3'");
$q3 = $db->query("SELECT * FROM order_info WHERE restaurant_id = '$restaurant_id'");
$q4 = $db->query("SELECT * FROM new_employee_info WHERE restaurant_id = '$restaurant_id'");
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h1 class="mt-4"><?php echo $title; ?></h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="restaurants.php">Restaurants</a></li>
                <li class="breadcrumb-item active"><?php echo $title; ?></li>
            </ol>
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table mr-1">
                    </i>
                    <?php echo $restaurant_info['restaurant_name']; ?>
                    <button class="btn btn-warning" id="downloadBtn"><i class="fas fa-download mr-1">
                    </i> Download Excel</button>
                    </div>
                <div class="card-body">
                    <table class="table table-bordered" id="dataTable">
                        <tbody>
                            <tr>
                                <th>Restaurant Name</th>
                                <td><?php echo $restaurant_info['restaurant_name']; ?></td>
                            </tr>
                            <tr>
                                <th>Created By</th>
                                <td><?php echo $restaurant_info['name']; ?></td>
                            </tr>
                            <tr>
                                <th>Mobile Number</th>
                                <td>+<?php echo $restaurant_info['contact_number']; ?></td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td><?php echo $restaurant_info['email_id']; ?></td>
                            </tr>
                            <tr>
                                <th>Shipping Address</th>
                                <td><?php echo $restaurant_info['shipping_address']; ?></td>
                            </tr>
                            <tr>
                                <th>City</th>
                                <td><?php echo $restaurant_info['city']; ?></td>
                            </tr>
                            <tr>
                                <th>State</th>
                                <td><?php echo $restaurant_info['state']; ?></td>
                            </tr>
                            <tr>
                                <th>Zip</th>
                                <td><?php echo $restaurant_info['zip']; ?></td>
                            </tr>
                            <tr>
                                <th>Created on</th>
                                <td><?php echo $restaurant_info['created_at']; ?></td>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr><th>Employees List</th></tr>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                            </tr>
                            <?php 
                                $i = 0;
                                while($row = $q2->fetch_assoc()){
                                    ?>
                                    <tr>
                                        <td><?php echo ++$i; ?></td>
                                        <td><?php echo $row['name'] ?></td>
                                        <td><?php echo $row['email'] ?></td>
                                        <td>+<?php echo $row['phone_number'] ?></td>
                                    </tr>
                                    <?php
                                }
                            ?>
                        </tbody>
                        <tbody>
                            <tr><th>Newly Added Employees</th></tr>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                            </tr>
                            <?php 
                                $i = 0;
                                while($row = $q4->fetch_assoc()){
                                    ?>
                                    <tr>
                                        <td><?php echo ++$i; ?></td>
                                        <td><?php echo $row['name'] ?></td>
                                        <td><?php echo $row['email'] ?></td>
                                        <td>+<?php echo $row['phone'] ?></td>
                                    </tr>
                                    <?php
                                }
                            ?>
                        </tbody>
                        <tbody>
                                <tr>
                                    <th>Orders</th>
                                </tr>
                                <tr>
                                    <th>#</th>
                                    <th>Order Id</th>
                                    <th>Total Items</th>
                                    <th>Total Price</th>
                                    <th>Orderd on</th>
                                </tr>
                                <?php 
                                    $i = 0;
                                    while($orders = $q3->fetch_assoc()){
                                        ?>
                                        <tr>
                                            <td><?php echo ++$i; ?></td>
                                            <td><a href="order-info.php?order_id=<?php echo $orders['order_id'] ?>" class="btn btn-link"><?php echo $orders['order_id']; ?></a></td>
                                            <td><?php echo $orders['total_items']; ?></td>
                                            <td><?php echo $orders['total_price']; ?></td>
                                            <td><?php echo $orders['date']; ?></td>
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
                $("#dataTable").table2excel({
                    name: "<?php echo $title; ?>",
                    filename: "<?php echo $title; ?>.xls", // do include extension
                    preserveColors: false // set to true if you want background colors and font colors preserved
                });
            });
        </script>

    </body>
</html> -->