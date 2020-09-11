<?php
if(!isset($router->params[0]) || empty($router->params[0])){
    header("Location: /tables");
}
$tableNo = $router->params[0];
require_once "php/classes/admin/tableHandler.php";
$title = "Table Info";

$tableHandler = new TableHandler();

$table_info = $tableHandler->getTableInfo($tableNo)[0];
if(isset($_POST['delete'])){
    // print_r($_POST);
    $tableHandler->deleteTable($tableNo)[0];
    header("Location: /tables");
    die();
}


require_once "header.php";

?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h1 class="mt-5"><?php echo $title; ?></h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/tables">tables</a></li>
                <li class="breadcrumb-item active"><?php echo $title; ?></li>
            </ol>
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table mr-1">
                    </i>
                    <?php echo $title; ?>
                    <button class="btn btn-warning" id="downloadBtn"><i class="fas fa-download mr-1">
                    </i> Download Excel</button>
                    <form method="post">
                        <input type="submit" name="delete" class="btn btn-danger" style="float: right;" value="Delete" onclick="return confirm('Are you sure?\nClick ok to delete the table')">
                    </form>
                    </div>
                <div class="card-body">
                    <table class="table table-bordered" id="tableData">
                        <tbody>
                            <tr>
                                <th>Table Number</th>
                                <td><?php echo $table_info['tableNumber']; ?></td>
                            </tr>
                            <tr>
                                <th>Table Code</th>
                                <td width="500"><?php echo $table_info['tableCode']; ?></td>
                            </tr>
                            <tr>
                                <th>Short URL</th>
                                <td width="500"><p class="btn-link"><?php echo $table_info['shortUrl']?></p></td>
                            </tr>
                            <tr>
                                <th>QR Image</th>
                                <td><a href="https://diner.lbits.co/QRImages/<?php echo $table_info['qrLocation']; ?>"><img src="https://diner.lbits.co/QRImages/<?php echo $table_info['qrLocation']; ?>" width="200" height="200"></a></td>
                            </tr>
                            <tr>
                                <th>Table Status</th>
                                <td><?php echo $table_info['tableStatus']; ?></td>
                            </tr>
                            <?php if(!empty($table_info['occupiedUser'])){ ?>
                                 
                                <tr>
                                    <th>Occupied User</th>
                                    <td>
                                        <a href="user-info.php?uid=<?php echo $table_info['uid']; ?>">
                                            <?php echo $table_info['name']; ?>
                                        </a>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th>Occupied User Phone</th>
                                    <td><?php echo $table_info['phone']; ?></td>
                                </tr>
                                <tr>
                                    <th>Occupied User Phone</th>
                                    <td><?php echo $table_info['email']; ?></td>
                                </tr>
                            
                            <?php } ?>
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
        </script>

    </body>
</html>